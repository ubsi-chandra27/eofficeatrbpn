<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class RegistrationSettings
{
    public static function isStaffEnabled(): bool
    {
        if (!Cache::has('registration.allow_staff')) {
            $value = Setting::getValue('registration.allow_staff', null);
            Cache::forever('registration.allow_staff', $value);
        }

        $setting = Cache::get('registration.allow_staff');

        if ($setting === null) {
            return (bool) config('registration.allow_staff');
        }

        return (bool) $setting;
    }

    public static function putStaffEnabled(bool $enabled): void
    {
        Setting::putValue('registration.allow_staff', $enabled ? '1' : '0');
        Cache::forever('registration.allow_staff', $enabled ? '1' : '0');
    }
}
