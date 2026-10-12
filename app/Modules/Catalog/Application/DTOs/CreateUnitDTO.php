<?php

declare(strict_types=1);

namespace App\Modules\Catalog\Application\DTOs;

final readonly class CreateUnitDTO
{
    public function __construct(
        public string $name,
        public string $symbol,
    ) {}

    /**
     * Construye la instancia a partir del arreglo validado del request.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            name: trim((string) $data['name']),
            symbol: trim((string) $data['symbol']),
        );
    }

    /**
     * @return array{name: string, symbol: string}
     */
    public function toArray(): array
    {
        return [
            'name'   => $this->name,
            'symbol' => $this->symbol,
        ];
    }
}