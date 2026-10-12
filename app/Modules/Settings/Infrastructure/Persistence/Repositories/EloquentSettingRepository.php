<?php

declare(strict_types=1);

namespace App\Modules\Settings\Infrastructure\Persistence\Repositories;

use App\Modules\Settings\Domain\Models\Setting;
use App\Modules\Settings\Domain\Repositories\SettingRepositoryInterface;
use Illuminate\Support\Facades\DB;

final class EloquentSettingRepository implements SettingRepositoryInterface
{
    public function get(string $key, ?string $default = null): ?string
    {
        return Setting::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public function getAllAsKeyValue(): array
    {
        return Setting::query()
            ->pluck('value', 'key')
            ->toArray();
    }

    public function setMany(array $settings): void
    {
        DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                Setting::query()->updateOrCreate(
                    ['key' => $key],
                    ['value' => $value]
                );
            }
        });
    }

    public function forget(string $key): void
    {
        Setting::query()->where('key', $key)->delete();
    }
}