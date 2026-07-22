<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function getValue(string $key, mixed $default = null): mixed
    {
        return Cache::remember('setting.'.$key, 3600, fn () => static::where('key', $key)->value('value') ?? $default);
    }

    public static function putValue(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? (int) $value : $value, 'group' => $group]);
        Cache::forget('setting.'.$key);
    }
}
