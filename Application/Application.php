<?php

declare(strict_types=1);

namespace VDMembership\Application;

use VDMembership\Configuration\SettingsRepository;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;

class Application
{
    private static ?string $db_error = null;
    private static bool $db_credentials_given = false;
    private static bool $db_connected = false;

    /**
     * Entry point called from the main plugin file.
     */
    public static function bootstrap(string $plugin_file): void
    {
        register_activation_hook($plugin_file, [self::class, 'activate']);
        register_deactivation_hook($plugin_file, [self::class, 'deactivate']);

        add_action('admin_init',   [self::class, 'test_db_connection']);
        add_action('admin_notices', [self::class, 'display_notices']);
    }

    public static function activate(): void
    {
        // Nothing to set up on activation for now
    }

    public static function deactivate(): void
    {
        // Nothing to clean up on deactivation for now
    }

    public static function test_db_connection(): void
    {

        self::$db_credentials_given = SettingsRepository::has_credentials();
        
        if (!self::$db_credentials_given) {
            return;
        }

        if (ExternalDatabaseConnection::get() === null) {
            self::$db_error = ExternalDatabaseConnection::get_error();
        } else {
            self::$db_connected = true;
        }
    }

    public static function display_notices(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $notices = []; // array of [ 'type' => 'error'|'success', 'message' => string ]

        if (!self::$db_credentials_given) {
            $notices[] = [
                'type' => 'warning',
                'message' => '<strong>VD Membership:</strong> '
                    . esc_html__('Database credentials are not fully configured.', 'vd-membership'),
            ];
        }

        if (self::$db_error !== null) {
            $notices[] = [
                'type' => 'error',
                'message' => sprintf(
                    '<strong>VD Membership – Database error:</strong> %s',
                    esc_html(self::$db_error)
                ),
            ];
        }
        
        if (self::$db_connected) {
            $notices[] = [
                'type' => 'success',
                'message' => '<strong>VD Membership:</strong> '
                    . esc_html__('Connected to the external database successfully.', 'vd-membership'),
            ];
        }

        foreach ($notices as $notice) {
            echo '<div class="notice notice-' . $notice['type'] . ' is-dismissible"><p>'
                . wp_kses_post($notice['message']) . '</p></div>';
        }

        // Transient-based notices from POST operations will be displayed here in later steps
    }

    /** Resets in-request state — used in tests. */
    public static function reset(): void
    {
        self::$db_error            = null;
        self::$db_credentials_given = false;
        self::$db_connected        = false;
    }
}
