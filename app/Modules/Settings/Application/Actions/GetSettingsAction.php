<?php

declare(strict_types=1);

namespace App\Modules\Settings\Application\Actions;

use App\Modules\Settings\Domain\Repositories\SettingRepositoryInterface;

final readonly class GetSettingsAction
{
    public function __construct(
        private SettingRepositoryInterface $repository,
    ) {}

    /**
     * Retorna las configuraciones actuales formateadas para el cliente web.
     *
     * @return array<string, mixed>
     */
    public function execute(): array
    {
        $settings = $this->repository->getAllAsKeyValue();

        return [
            'business_name'  => $settings['business_name'] ?? '',
            'tax_id'         => $settings['tax_id'] ?? '',
            'currency'       => $settings['currency'] ?? 'USD',
            'exchange_rate'  => $settings['bcv_rate'] ?? '1.00',
            'invoice_footer' => $settings['invoice_footer'] ?? '',
        ];
    }
}