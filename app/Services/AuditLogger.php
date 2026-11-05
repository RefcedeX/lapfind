<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLogger
{
    /**
     * Record an audit log entry.
     *
     * @param  string  $action  Specific action slug (e.g. 'auth.login', 'alternative.create')
     * @param  string  $category  Category group ('auth', 'catalog', 'criterion', 'user', 'spk', 'security')
     * @param  string  $description  Human-readable description
     * @param  array<string, mixed>|null  $properties  Additional metadata/payload
     * @param  User|null  $user  Specific user, or current authenticated user by default
     */
    public static function log(
        string $action,
        string $category,
        string $description,
        ?array $properties = null,
        ?User $user = null
    ): AuditLog {
        $currentUser = $user ?? Auth::user();

        return AuditLog::create([
            'user_id' => $currentUser?->id,
            'user_name' => $currentUser?->name ?? ($properties['attempted_name'] ?? 'Tamu (Guest)'),
            'user_email' => $currentUser?->email ?? ($properties['attempted_email'] ?? null),
            'user_role' => $currentUser?->role ?? 'guest',
            'action' => $action,
            'category' => $category,
            'description' => $description,
            'ip_address' => Request::ip() ?? '127.0.0.1',
            'user_agent' => Request::userAgent(),
            'method' => Request::method() ?? 'CLI',
            'url' => Request::fullUrl() ?? null,
            'properties' => $properties,
            'created_at' => now(),
        ]);
    }

    /**
     * Record an authentication audit event.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function auth(string $action, string $description, ?array $properties = null, ?User $user = null): AuditLog
    {
        return self::log($action, 'auth', $description, $properties, $user);
    }

    /**
     * Record a catalog/laptop audit event.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function catalog(string $action, string $description, ?array $properties = null): AuditLog
    {
        return self::log($action, 'catalog', $description, $properties);
    }

    /**
     * Record a criteria audit event.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function criterion(string $action, string $description, ?array $properties = null): AuditLog
    {
        return self::log($action, 'criterion', $description, $properties);
    }

    /**
     * Record an SPK evaluation audit event.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function spk(string $action, string $description, ?array $properties = null): AuditLog
    {
        return self::log($action, 'spk', $description, $properties);
    }

    /**
     * Record a security / access control event.
     *
     * @param  array<string, mixed>|null  $properties
     */
    public static function security(string $action, string $description, ?array $properties = null): AuditLog
    {
        return self::log($action, 'security', $description, $properties);
    }
}
