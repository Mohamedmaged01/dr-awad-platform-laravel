<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Central feature-permission gate for the admin area.
 *
 * Every dashboard menu item ("feature") ships with a default set of roles.
 * The admin can override any (role, feature) pair from Settings → Permissions;
 * overrides are stored in the `feature_permissions` setting as a JSON map of
 * "role:feature" => bool. `admin` always has full access (can't be locked out).
 */
class Access
{
    /** feature name => default allowed roles, taken from the menu config. */
    public static function defaults(): array
    {
        return collect(config('clinic.dashboard_menu'))
            ->mapWithKeys(fn ($item) => [$item['name'] => $item['roles']])
            ->all();
    }

    /** Stored admin overrides: ["role:feature" => bool]. */
    public static function overrides(): array
    {
        return json_decode(Setting::get('feature_permissions', '{}'), true) ?: [];
    }

    /** Effective permission for a role on a feature. */
    public static function allows(string $role, string $feature): bool
    {
        if ($role === 'admin') {
            return true;
        }

        $overrides = self::overrides();
        $key = "$role:$feature";
        if (array_key_exists($key, $overrides)) {
            return (bool) $overrides[$key];
        }

        return in_array($role, self::defaults()[$feature] ?? [], true);
    }

    /** Flip the stored override for a (role, feature) pair; returns the new state. */
    public static function toggle(string $role, string $feature): bool
    {
        $overrides = self::overrides();
        $overrides["$role:$feature"] = ! self::allows($role, $feature);
        Setting::put('feature_permissions', json_encode($overrides, JSON_UNESCAPED_UNICODE), 'permissions');

        return $overrides["$role:$feature"];
    }

    /** Longest-prefix match of an admin request path to a menu feature name. */
    public static function featureForPath(string $path): ?string
    {
        $path = '/' . trim($path, '/');

        // Write routes whose path doesn't share a prefix with their menu item.
        $aliases = ['/admin/invoices' => 'payments'];
        foreach ($aliases as $prefix => $feature) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return $feature;
            }
        }

        $best = null;
        $bestLen = -1;

        foreach (config('clinic.dashboard_menu') as $item) {
            $href = $item['href'];
            if ($path === $href || str_starts_with($path, rtrim($href, '/') . '/')) {
                if (strlen($href) > $bestLen) {
                    $best = $item['name'];
                    $bestLen = strlen($href);
                }
            }
        }

        return $best;
    }

    /** Menu items a role may see, in config order. */
    public static function allowedMenu(string $role): array
    {
        return collect(config('clinic.dashboard_menu'))
            ->filter(fn ($item) => self::allows($role, $item['name']))
            ->values()->all();
    }
}
