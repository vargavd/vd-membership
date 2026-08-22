<?php

declare(strict_types=1);

namespace VDMembership\Admin;

use VDMembership\Application\MemberService;
use VDMembership\Configuration\SettingsRepository;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;

class SettingsPage
{
    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $db_host = SettingsRepository::get_db_host();
        $db_name = SettingsRepository::get_db_name();
        $db_user = SettingsRepository::get_db_user();

        include __DIR__ . '/../templates/admin/settings.php';
    }

    public static function handle_post(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }

        check_admin_referer('vd_membership_settings_nonce');

        $action = sanitize_text_field($_POST['vd_action'] ?? '');

        if ($action === 'test_connection') {
            self::process_test_connection();
        } else {
            self::process_save();
        }

        wp_redirect(admin_url('admin.php?page=' . AdminMenu::SLUG_SETTINGS));
        exit;
    }

    private static function process_save(): void
    {
        SettingsRepository::save($_POST);
        ExternalDatabaseConnection::reset();
        self::add_notice('success', 'Beállítások elmentve.');
    }

    private static function process_test_connection(): void
    {
        // Save the submitted values first so the test reflects the current form
        SettingsRepository::save($_POST);
        ExternalDatabaseConnection::reset();

        $db = ExternalDatabaseConnection::get();

        if ($db !== null) {
            self::add_notice('success', 'Kapcsolat az adatbázishoz sikeres!');
        } else {
            self::add_notice('error', 'Kapcsolat sikertelen: ' . ExternalDatabaseConnection::get_error());
        }
    }

    private static function add_notice(string $type, string $message): void
    {
        $notices = get_transient(MemberService::TRANSIENT_KEY);
        if (!is_array($notices)) {
            $notices = [];
        }
        $notices[] = ['type' => $type, 'message' => $message];
        set_transient(MemberService::TRANSIENT_KEY, $notices, 60);
    }
}
