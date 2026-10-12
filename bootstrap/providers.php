<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\FortifyServiceProvider::class,
    App\Modules\Identity\Infrastructure\Provider\IdentityServiceProvider::class,
    App\Modules\Settings\Infrastructure\Provider\SettingsServiceProvider::class,
    App\Modules\Catalog\Infrastructure\Provider\CatalogServiceProvider::class,
];
