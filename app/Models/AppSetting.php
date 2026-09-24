<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * Get a setting by key with fallback default.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = static::find($key);
        if (! $setting) {
            return $default;
        }

        $decoded = json_decode($setting->value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
    }

    /**
     * Set/Update a setting by key.
     */
    public static function set(string $key, mixed $value): void
    {
        $val = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        static::updateOrCreate(['key' => $key], ['value' => $val]);
    }

    /**
     * Return all engine configuration settings.
     */
    public static function getEngineConfig(): array
    {
        return [
            'spin_duration' => (float) static::get('spin_duration', 5),
            'min_rotations' => (int) static::get('min_rotations', 6),
            'easing_type' => static::get('easing_type', 'cubic-ease-out'),
            'auto_remove_winner' => filter_var(static::get('auto_remove_winner', false), FILTER_VALIDATE_BOOLEAN),
            'event_title' => static::get('event_title', 'Spinwheel Sanggar Seni Harisma'),
            'sub_title' => static::get('sub_title', 'Interactive Grand Competition Wheel'),
            'batik_pattern_opacity' => (float) static::get('batik_pattern_opacity', 0.15),
            'sound_enabled' => filter_var(static::get('sound_enabled', true), FILTER_VALIDATE_BOOLEAN),
        ];
    }
}
