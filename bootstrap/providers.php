<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Modules\Identity\Infrastructure\Provider\IdentityServiceProvider::class,
    App\Modules\Settings\Infrastructure\Provider\SettingsServiceProvider::class,
    App\Modules\Catalog\Infrastructure\Provider\CatalogServiceProvider::class,
    App\Modules\Parties\Infrastructure\Provider\PartiesServiceProvider::class,
    App\Modules\Sales\Infrastructure\Provider\SalesServiceProvider::class,
    App\Modules\CashRegister\Infrastructure\Provider\CashRegisterServiceProvider::class,
    App\Modules\Receivables\Infrastructure\Provider\ReceivablesServiceProvider::class,
    App\Modules\Purchasing\Infrastructure\Provider\PurchasingServiceProvider::class,
];
