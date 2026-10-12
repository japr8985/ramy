<?php

declare(strict_types=1);

namespace App\Modules\Settings\Application\DTOs;

final readonly class UpdateSettingsDTO
{
    public function __construct(
        public string $businessName,
        public string $taxId,
        public string $currency,
        public string $exchangeRate,
        public ?string $invoiceFooter = null,
    ) {}

    /**
     * Construye la instancia a partir de la data validada del Request.
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            businessName: (string) $data['business_name'],
            taxId: (string) $data['tax_id'],
            currency: (string) ($data['currency'] ?? 'USD'),
            exchangeRate: (string) $data['exchange_rate'],
            invoiceFooter: isset($data['invoice_footer']) && $data['invoice_footer'] !== '' 
                ? (string) $data['invoice_footer'] 
                : null,
        );
    }

    /**
     * Transforma el DTO al mapa clave-valor esperado en base de datos.
     * Nota: Mapea 'exchangeRate' a la clave 'bcv_rate' para mantener retrocompatibilidad.
     *
     * @return array<string, string|null>
     */
    public function toDatabaseArray(): array
    {
        return [
            'business_name'  => $this->businessName,
            'tax_id'         => $this->taxId,
            'currency'       => $this->currency,
            'bcv_rate'       => $this->exchangeRate,
            'invoice_footer' => $this->invoiceFooter,
        ];
    }
}