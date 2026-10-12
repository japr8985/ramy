<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

final readonly class UpdateCategoryDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public ?string $description = null,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(string $id, array $data): self
    {
        return new self(
            id: $id,
            name: trim((string) $data['name']),
            description: isset($data['description']) && trim((string) $data['description']) !== ''
                ? trim((string) $data['description'])
                : null,
        );
    }

    /**
     * @return array{name: string, description: string|null}
     */
    public function toArray(): array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}