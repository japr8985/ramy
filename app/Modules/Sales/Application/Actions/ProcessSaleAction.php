<?php

declare(strict_types=1);

namespace App\Modules\Sales\Application\Actions;

use App\Modules\Catalog\Domain\Models\Product;
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
    ) {}

    /**
     * @throws EmptySaleCartException
     * @throws CustomerRequiredForCreditSaleException
     * @throws InsufficientProductStockException
     */
    public function execute(ProcessSaleDTO $dto): Sale
    {
        // 1. Validar carrito no vacío
        if (empty($dto->items)) {
            throw EmptySaleCartException::create();
        }

        // 2. Validar que ventas a crédito tengan cliente asociado
        if ($dto->paymentMethod === 'credit' && empty($dto->customerId)) {
            throw CustomerRequiredForCreditSaleException::create();
        }

        return DB::transaction(function () use ($dto): Sale {
            // 3. Bloquear y verificar stock de cada producto en inventario
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

            // 4. Calcular totales
            $subtotal = $dto->calculateSubtotal();
            $total = $dto->calculateTotal();
            $invoiceNumber = 'NX-' . strtoupper(Str::random(8));

            // Si es crédito, el dinero en caja recibido es la inicial; si no, es el total pagado
            $cashReceived = $dto->paymentMethod === 'credit' ? $dto->initialPayment : $total;

            $saleData = [
                'invoice_number' => $invoiceNumber,
                'customer_id'    => $dto->customerId,
                'created_by'     => $dto->userId ?? auth()->user->id() ?? DB::table('users')->first()?->id,
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

            // 5. Crear la venta con sus renglones mediante el repositorio
            $sale = $this->saleRepository->createSaleWithItems($saleData, $itemsData);

            // 6. Descontar inventario en Catalog
            foreach ($dto->items as $item) {
                Product::query()
                    ->where('id', $item->productId)
                    ->decrement('quantity', $item->quantity);
            }

            return $sale;
        });
    }
}