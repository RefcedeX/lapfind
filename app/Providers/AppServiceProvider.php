<?php

namespace App\Providers;

use App\Services\AuditLogger;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Audit trail: Berhasil Login
        Event::listen(Login::class, function (Login $event) {
            AuditLogger::auth(
                action: 'auth.login',
                description: "Pengguna \"{$event->user->name}\" berhasil masuk ke sistem.",
                properties: [
                    'guard' => $event->guard,
                    'remember' => $event->remember,
                ],
                user: $event->user
            );
        });

        // 2. Audit trail: Berhasil Logout
        Event::listen(Logout::class, function (Logout $event) {
            if ($event->user) {
                AuditLogger::auth(
                    action: 'auth.logout',
                    description: "Pengguna \"{$event->user->name}\" keluar dari sistem.",
                    user: $event->user
                );
            }
        });

        // 3. Audit trail: Percobaan Login Gagal (Security Alert)
        Event::listen(Failed::class, function (Failed $event) {
            $attemptedEmail = $event->credentials['email'] ?? 'tidak diketahui';
            AuditLogger::security(
                action: 'auth.failed',
                description: "Percobaan login gagal untuk akun: {$attemptedEmail}.",
                properties: [
                    'attempted_email' => $attemptedEmail,
                    'guard' => $event->guard,
                ],
                user: $event->user
            );
        });
    }
}
