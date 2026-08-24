<?php

declare(strict_types=1);

namespace VDMembership\Infrastructure\Database;

use VDMembership\Configuration\SettingsRepository;

class ExternalDatabaseConnection
{
    private static ?\wpdb $connection = null;
    private static ?string $error = null;

    public static function get(): ?\wpdb
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        self::$error = null;

        $host = SettingsRepository::get_db_host();
        $user = SettingsRepository::get_db_user();
        $pass = SettingsRepository::get_db_password();
        $name = SettingsRepository::get_db_name();

        // Pre-check with mysqli to prevent wpdb from calling wp_die() on connection failure
        try {
            $test = mysqli_connect($host, $user, $pass, $name);
        } catch (\mysqli_sql_exception $e) {
            self::$error = $e->getMessage();
            return null;
        }

        if (!$test) {
            self::$error = mysqli_connect_error() ?: 'Could not connect to the external database.';
            return null;
        }

        mysqli_close($test);

        $db = new \wpdb($user, $pass, $name, $host);
        // $db->set_charset($db->dbh, 'latin2', 'latin2_hungarian_ci'); <- this did not read the accented characters correctly, so we will use the default charset instead
        $db->set_charset($db->dbh);

        self::$connection = $db;
        return self::$connection;
    }

    public static function get_error(): ?string
    {
        return self::$error;
    }

    /** Resets cached connection state — used in tests and settings saves. */
    public static function reset(): void
    {
        self::$connection = null;
        self::$error = null;
    }
}
