<?php

declare(strict_types=1);

namespace VDMembership\Configuration;

class SettingsRepository
{
    private const OPTION_KEY = 'vd_membership_settings';

    private static function all(): array
    {
        $defaults = [
            'db_host'     => '',
            'db_name'     => '',
            'db_user'     => '',
            'db_password' => '', 
        ];
        $stored = get_option(self::OPTION_KEY, []);

        return array_merge($defaults, is_array($stored) ? $stored : []);
    }

    public static function get_db_host(): string
    {
        return self::all()['db_host'];
    }

    public static function get_db_name(): string
    {
        return self::all()['db_name'];
    }

    public static function get_db_user(): string
    {
        return self::all()['db_user'];
    }

    public static function get_db_password(): string
    {
        return self::all()['db_password'];
    }

    public static function has_credentials(): bool
    {
        $s = self::all();

        return ($s['db_host'] !== '') && ($s['db_name'] !== '') && ($s['db_user'] !== ''); // <- password can be empty
    }

    /**
     * Persists settings; password is only updated when a non-empty value is supplied.
     */
    public static function save(array $data): bool
    {
        $existing = self::all();

        $updated = [
            'db_host' => sanitize_text_field($data['db_host'] ?? $existing['db_host']),
            'db_name' => sanitize_text_field($data['db_name'] ?? $existing['db_name']),
            'db_user' => sanitize_text_field($data['db_user'] ?? $existing['db_user']),
            'db_password' => !empty($data['db_password'])
                ? sanitize_text_field($data['db_password'])
                : $existing['db_password'],
        ];

        return update_option(self::OPTION_KEY, $updated);
    }
}
