<?php

declare(strict_types=1);

namespace App\Modules\Parties\Application\DTOs;

final readonly class UpdateCustomerDTO
{
    public function __construct(
        public string $id,
        public string $rifCi,
        public string $name,
        public string $type = 'Individual',
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $address = null,
        public ?string $notes = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            rifCi: trim((string) $data['rif_ci']),
            name: trim((string) $data['name']),
            type: (string) ($data['type'] ?? 'Individual'),
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
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'rif_ci'  => $this->rifCi,
            'name'    => $this->name,
            'type'    => $this->type,
            'email'   => $this->email,
            'phone'   => $this->phone,
            'address' => $this->address,
            'notes'   => $this->notes,
        ];
    }
}