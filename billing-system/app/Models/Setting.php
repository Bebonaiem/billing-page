<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public static function get(string $key, $default = null, string $group = 'general')
    {
        $cacheKey = "settings.{$group}.{$key}";

        return Cache::remember($cacheKey, 3600, function () use ($key, $default, $group) {
            $setting = self::where('group', $group)->where('key', $key)->first();

            if (!$setting) {
                return $default;
            }

            return self::castValue($setting->value, $setting->type);
        });
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'string'): self
    {
        $setting = self::updateOrCreate(
            ['group' => $group, 'key' => $key],
            ['value' => self::prepareValue($value, $type), 'type' => $type]
        );

        Cache::forget("settings.{$group}.{$key}");

        return $setting;
    }

    protected static function castValue($value, string $type)
    {
        return match ($type) {
            'boolean' => (bool) $value,
            'integer' => (int) $value,
            'float' => (float) $value,
            'json' => json_decode($value, true),
            default => $value,
        };
    }

    protected static function prepareValue($value, string $type): string
    {
        return match ($type) {
            'boolean' => $value ? '1' : '0',
            'json' => json_encode($value),
            default => (string) $value,
        };
    }

    public static function getGroup(string $group): array
    {
        return self::where('group', $group)
            ->get()
            ->mapWithKeys(function ($setting) {
                return [$setting->key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    public static function getPublic(): array
    {
        return self::where('is_public', true)
            ->get()
            ->mapWithKeys(function ($setting) {
                $key = "{$setting->group}.{$setting->key}";
                return [$key => self::castValue($setting->value, $setting->type)];
            })
            ->toArray();
    }

    /**
     * Legacy method for backward compatibility
     */
    public static function getValue(string $key, $default = null)
    {
        return self::get($key, $default);
    }
}
