<?php

namespace App\Services;

use App\Models\User;
use App\Notifications\SystemNotification;

class SystemNotificationService
{
    public function notifyAdmins(
        string $title,
        string $message,
        ?string $url = null,
        string $type = 'info',
        string $icon = 'bi-bell-fill'
    ): void {
        User::where('role', 'admin')->each(function (User $admin) use ($title, $message, $url, $type, $icon) {
            $admin->notify(new SystemNotification($title, $message, $url, $type, $icon));
        });
    }

    public function notifyUser(
        ?User $user,
        string $title,
        string $message,
        ?string $url = null,
        string $type = 'info',
        string $icon = 'bi-bell-fill'
    ): void {
        if (! $user) {
            return;
        }

        $user->notify(new SystemNotification($title, $message, $url, $type, $icon));
    }
}
