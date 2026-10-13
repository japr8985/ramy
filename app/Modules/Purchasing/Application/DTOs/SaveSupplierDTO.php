<?php

declare(strict_types=1);

namespace App\Modules\Purchasing\Application\DTOs;

final readonly class SaveSupplierDTO
{
    /**
     * @param array<int, string> $productIds
     */
    public function __construct(
        public string $name,
        public string $contactPerson,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $notes = null,
        public array $productIds = [],
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) ($data['name'] ?? '')),
            contactPerson: trim((string) ($data['contact_person'] ?? '')),
            email: isset($data['email']) && trim((string) $data['email']) !== ''
                ? trim((string) $data['email'])
                : null,
            phone: isset($data['phone']) && trim((string) $data['phone']) !== ''
                ? trim((string) $data['phone'])
                : null,
            address: isset($data['address']) && trim((string) $data['address']) !== ''
                ? trim((string) $data['address'])
                : null,
            notes: isset($data['notes']) && trim((string) $data['notes']) !== ''
                ? trim((string) $data['notes'])
                : null,
            productIds: array_map('strval', (array) ($data['selected_products'] ?? $data['product_ids'] ?? [])),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name'           => $this->name,
            'contact_person' => $this->contactPerson,
            'email'          => $this->email,
            'phone'          => $this->phone,
            'address'        => $this->address,
            'notes'          => $this->notes,
        ];
    }
}