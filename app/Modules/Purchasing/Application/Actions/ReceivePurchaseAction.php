<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\Actions;

use App\Modules\Catalog\Domain\Models\Product;
use App\Modules\Purchasing\Application\DTOs\ProcessPurchaseDTO;
use App\Modules\Purchasing\Domain\Exceptions\EmptyPurchaseCartException;
use App\Modules\Purchasing\Domain\Models\Purchase;
use App\Modules\Purchasing\Domain\Repositories\PurchaseRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class ReceivePurchaseAction
{
    public function __construct(
        private PurchaseRepositoryInterface $purchaseRepository,
    ) {}

    /**
     * @throws EmptyPurchaseCartException
     */
    public function execute(ProcessPurchaseDTO $dto): Purchase
    {
        if (empty($dto->items)) {
            throw EmptyPurchaseCartException::create();
        }

        return DB::transaction(function () use ($dto): Purchase {
            $invoiceNumber = $dto->invoiceNumber ?: 'FAC-' . strtoupper(Str::random(8));
            $total = $dto->calculateTotal();
            $userId = $dto->createdBy ?? (string) (auth()->user->id ?? DB::table('users')->first()?->id);

            $purchaseData = [
                'invoice_number' => $invoiceNumber,
                'supplier_id'    => $dto->supplierId,
                'purchase_date'  => $dto->purchaseDate,
                'due_date'       => $dto->dueDate,
                'total'          => $total,
                'status'         => 'received',
                'notes'          => $dto->notes,
                'proof_image'    => $dto->proofImage,
                'created_by'     => $userId,
            ];

            $itemsData = array_map(fn ($item) => $item->toArray(), $dto->items);

            // 1. Persistir orden y renglones
            $purchase = $this->purchaseRepository->recordAndReceive($purchaseData, $itemsData);

            // 2. Incrementar stock de productos y actualizar costos en Catalog
            foreach ($dto->items as $item) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->find($item->productId);

                if ($product) {
                    $updatePayload = [
                        'cost_price' => $item->unitPrice,
                    ];

                    if ($item->sellingPrice !== null && $item->sellingPrice > 0) {
                        $updatePayload['selling_price'] = $item->sellingPrice;
                    }

                    $product->increment('quantity', $item->quantity);
                    $product->update($updatePayload);
                }
            }

            return $purchase;
        });
    }
}