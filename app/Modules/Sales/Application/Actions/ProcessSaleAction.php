<?php

declare(strict_types=1);

namespace App\Modules\Sales\Application\Actions;

use App\Modules\CashRegister\Domain\Repositories\CashShiftRepositoryInterface;
use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Receivables\Domain\Models\AccountsReceivable;
use App\Modules\Sales\Application\DTOs\ProcessSaleDTO;
use App\Modules\Sales\Domain\Exceptions\CustomerRequiredForCreditSaleException;
use App\Modules\Sales\Domain\Exceptions\EmptySaleCartException;
use App\Modules\Sales\Domain\Exceptions\InsufficientProductStockException;
use App\Modules\Sales\Domain\Models\Sale;
use App\Modules\Sales\Domain\Repositories\SaleRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class ProcessSaleAction
{
    public function __construct(
        private SaleRepositoryInterface $saleRepository,
        private CashShiftRepositoryInterface $cashShiftRepository,
    ) {}

    /**
     * @throws EmptySaleCartException
     * @throws CustomerRequiredForCreditSaleException
     * @throws InsufficientProductStockException
     */
    public function execute(ProcessSaleDTO $dto): Sale
    {
        // 1. Validar que el carrito contenga al menos un producto
        if (empty($dto->items)) {
            throw EmptySaleCartException::create();
        }

        // 2. Regla de negocio: Ventas a crédito requieren cliente obligatoriamente
        if ($dto->paymentMethod === 'credit' && empty($dto->customerId)) {
            throw CustomerRequiredForCreditSaleException::create();
        }

        return DB::transaction(function () use ($dto): Sale {
            // 3. Bloquear registros de productos para validación y decremento atómico
            $productIds = array_map(fn ($item) => $item->productId, $dto->items);
            $products = Product::query()
                ->whereIn('id', $productIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($dto->items as $item) {
                /** @var Product|null $product */
                $product = $products->get($item->productId);

                if (! $product || $product->quantity < $item->quantity) {
                    $productName = $product ? $product->name : "ID {$item->productId}";
                    $available = $product ? (int) $product->quantity : 0;

                    throw InsufficientProductStockException::forProduct(
                        productName: $productName,
                        availableStock: $available,
                        requestedQuantity: $item->quantity
                    );
                }
            }

            // 4. Resolver usuario y turno activo de caja
            $userId = $dto->userId ?? (string) (auth()->user->id ?? DB::table('users')->first()?->id);
            $cashShiftId = $dto->cashShiftId;

            if (empty($cashShiftId) && ! empty($userId)) {
                $activeShift = $this->cashShiftRepository->findOpenShiftByUserId($userId);
                $cashShiftId = $activeShift?->id;
            }

            // 5. Cálculos financieros
            $subtotal = $dto->calculateSubtotal();
            $total = $dto->calculateTotal();
            $invoiceNumber = 'NX-' . strtoupper(Str::random(8));

            // Si es crédito, el efectivo ingresado a caja es la inicial (si la hubo)
            $initialPayment = round(min(max(0.0, $dto->initialPayment), $total), 4);
            $cashReceived = $dto->paymentMethod === 'credit' ? $initialPayment : $total;

            $saleData = [
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $dto->customerId,
                'created_by'     => $userId,
                'cash_shift_id'  => $cashShiftId,
                'sale_date'      => now(),
                'status'         => 'completed',
                'subtotal'       => $subtotal,
                'total_discount' => min($dto->totalDiscount, $subtotal),
                'total'          => $total,
                'exchange_rate'  => $dto->exchangeRate,
                'cash_received'  => $cashReceived,
                'change'         => 0.0,
                'payment_method' => $dto->paymentMethod,
                'notes'          => $dto->notes,
            ];

            $itemsData = array_map(fn ($item) => $item->toArray(), $dto->items);

            // 6. Persistir la venta e ítems
            $sale = $this->saleRepository->createSaleWithItems($saleData, $itemsData);

            // 7. Si la venta es a crédito, generar cuenta por cobrar y renglones
            if ($dto->paymentMethod === 'credit') {
                $balance = round($total - $initialPayment, 4);

                /** @var AccountsReceivable $receivable */
                $receivable = AccountsReceivable::query()->create([
                    'sale_id'     => $sale->id,
                    'customer_id' => $dto->customerId,
                    'sale_date'   => $sale->sale_date,
                    'due_date'    => now()->addDays(30), // Plazo estándar de 30 días
                    'total'       => $total,
                    'paid_amount' => $initialPayment,
                    'balance'     => $balance,
                ]);

                // Renglones de la cuenta por cobrar (capturando cost_price del catálogo para análisis de margen)
                $receivableItems = [];
                foreach ($dto->items as $item) {
                    /** @var Product $product */
                    $product = $products->get($item->productId);
                    $costPrice = (float) ($product->cost_price ?? 0.0);

                    $receivableItems[] = [
                        'accounts_receivable_id' => $receivable->id,
                        'product_id'             => $item->productId,
                        'quantity'               => $item->quantity,
                        'cost_price'             => $costPrice,
                        'unit_price'             => $item->price,
                        'discount'               => 0.0,
                        'final_price'            => $item->price,
                        'subtotal'               => $item->subtotal(),
                    ];
                }

                $receivable->items()->createMany($receivableItems);

                // Si aportó abono inicial, registrar el comprobante de amortización
                if ($initialPayment > 0.0) {
                    $receivable->payments()->create([
                        'payment_date'   => now(),
                        'amount_usd'     => $initialPayment,
                        'exchange_rate'  => $dto->exchangeRate,
                        'payment_method' => 'Efectivo (Inicial POS)',
                        'notes'          => 'Abono inicial registrado automáticamente al procesar venta.',
                    ]);
                }
            }

            // 8. Descontar existencias físicas en el catálogo
            foreach ($dto->items as $item) {
                Product::query()
                    ->where('id', $item->productId)
                    ->decrement('quantity', $item->quantity);
            }

            return $sale;
        });
    }
}