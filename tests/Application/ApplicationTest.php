<?php

declare(strict_types=1);

namespace VDMembership\Tests\Application;

use PHPUnit\Framework\TestCase;
use VDMembership\Application\Application;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;

class ApplicationTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['_vd_test_options']             = [];
        $GLOBALS['_vd_test_mysqli_result']        = false;
        $GLOBALS['_vd_test_can_manage_options']   = true;
        $GLOBALS['_vd_test_transients']          = [];
        unset($GLOBALS['_vd_test_mysqli_error'], $GLOBALS['_vd_test_wpdb_dbh']);
        ExternalDatabaseConnection::reset();
        Application::reset();
    }

    // Helper: populate the minimum required credentials (password optional per has_credentials)
    private function setCredentials(array $overrides = []): void
    {
        $GLOBALS['_vd_test_options']['vd_membership_settings'] = array_merge([
            'db_host'     => 'localhost',
            'db_name'     => 'testdb',
            'db_user'     => 'root',
            'db_password' => '',
        ], $overrides);
    }

    // ----------------------------------------------
    // test_db_connection + display_notices (warning)
    // ----------------------------------------------

    public function test_shows_warning_when_no_credentials_saved(): void
    {
        Application::test_db_connection();

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-warning', $output);
    }

    public function test_shows_warning_when_credentials_incomplete(): void
    {
        $this->setCredentials(['db_user' => '']); // user missing → has_credentials returns false

        Application::test_db_connection();

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-warning', $output);
    }

    // ----------------------------------------------
    // test_db_connection + display_notices (error)
    // ----------------------------------------------

    public function test_shows_error_notice_when_connection_fails(): void
    {
        $this->setCredentials();
        $GLOBALS['_vd_test_mysqli_result'] = false;
        $GLOBALS['_vd_test_mysqli_error']  = 'Access denied for user';

        Application::test_db_connection();

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-error', $output);
        $this->assertStringContainsString('Access denied for user', $output);
    }

    // ----------------------------------------------
    // test_db_connection + display_notices (success)
    // ----------------------------------------------

    public function test_shows_success_notice_when_connection_succeeds(): void
    {
        $this->setCredentials();
        $GLOBALS['_vd_test_mysqli_result'] = true;
        $GLOBALS['_vd_test_wpdb_dbh']      = new \stdClass();

        Application::test_db_connection();

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-success', $output);
    }

    public function test_shows_success_notice_when_password_is_empty_and_connection_succeeds(): void
    {
        $this->setCredentials(['db_password' => '']); // password optional
        $GLOBALS['_vd_test_mysqli_result'] = true;
        $GLOBALS['_vd_test_wpdb_dbh']      = new \stdClass();

        Application::test_db_connection();

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-success', $output);
    }

    // ----------------------------------------------
    // display_notices — access control
    // ----------------------------------------------

    public function test_shows_no_output_for_non_admin(): void
    {
        $GLOBALS['_vd_test_can_manage_options'] = false;

        Application::test_db_connection(); // would produce warning (no credentials)

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertSame('', $output);
    }

    // ----------------------------------------------
    // Transient-based notices
    // ----------------------------------------------

    public function test_displays_transient_success_notice(): void
    {
        $GLOBALS['_vd_test_transients']['vd_membership_notices'] = [
            ['type' => 'success', 'message' => 'Mentés sikeres.'],
        ];

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-success', $output);
        $this->assertStringContainsString('Mentés sikeres.', $output);
    }

    public function test_displays_transient_error_notice(): void
    {
        $GLOBALS['_vd_test_transients']['vd_membership_notices'] = [
            ['type' => 'error', 'message' => 'Adatbázis hiba: connection refused'],
        ];

        ob_start();
        Application::display_notices();
        $output = ob_get_clean();

        $this->assertStringContainsString('notice-error', $output);
        $this->assertStringContainsString('Adatbázis hiba', $output);
    }

    public function test_transient_is_deleted_after_display(): void
    {
        $GLOBALS['_vd_test_transients']['vd_membership_notices'] = [
            ['type' => 'success', 'message' => 'OK'],
        ];

        ob_start();
        Application::display_notices();
        ob_get_clean();

        $this->assertFalse(isset($GLOBALS['_vd_test_transients']['vd_membership_notices']));
    }

    public function test_transient_not_consumed_for_non_admin(): void
    {
        $GLOBALS['_vd_test_can_manage_options']                  = false;
        $GLOBALS['_vd_test_transients']['vd_membership_notices'] = [
            ['type' => 'success', 'message' => 'Should persist'],
        ];

        ob_start();
        Application::display_notices();
        ob_get_clean();

        $this->assertNotEmpty($GLOBALS['_vd_test_transients']['vd_membership_notices']);
    }
}
