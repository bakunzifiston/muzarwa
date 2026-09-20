<?php

namespace App\Services;

use App\Models\ContactChannel;
use App\Models\SiteSetting;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class SiteSettingsService
{
    private const TTL = 3600;
    private const CACHE_KEY = 'site_settings_all';

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function (): array {
            return SiteSetting::all()->pluck('value', 'key')->all();
        });
    }

    public function get(string $key, ?string $default = null): ?string
    {
        return $this->all()[$key] ?? $default;
    }

    public function set(string $key, ?string $value, string $group = 'general', string $type = 'text'): void
    {
        SiteSetting::setValue($key, $value, $group, $type);
        $this->flush();
    }

    public function setMany(array $pairs, string $group = 'general'): void
    {
        foreach ($pairs as $key => $value) {
            SiteSetting::setValue($key, $value, $group);
        }
        $this->flush();
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /** Returns all active channels of a type, ordered by sort_order then id. */
    public function channels(string $type): Collection
    {
        return ContactChannel::active()->ofType($type)->orderBy('sort_order')->orderBy('id')->get();
    }

    /** Returns the primary channel of a type, or the first active one. */
    public function primaryChannel(string $type): ?ContactChannel
    {
        return ContactChannel::active()->ofType($type)
            ->orderByDesc('is_primary')
            ->orderBy('sort_order')
            ->first();
    }
}
