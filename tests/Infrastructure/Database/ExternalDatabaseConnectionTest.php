<?php

declare(strict_types=1);

namespace VDMembership\Tests\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;

class ExternalDatabaseConnectionTest extends TestCase
{
        protected function setUp(): void
        {
            $GLOBALS['_vd_test_options'] = [
                'vd_membership_settings' => [
                    'db_host'     => 'localhost',
                    'db_name'     => 'testdb',
                    'db_user'     => 'root',
                    'db_password' => 'secret',
                ],
            ];
            $GLOBALS['_vd_test_mysqli_result'] = false;
            unset($GLOBALS['_vd_test_mysqli_error'], $GLOBALS['_vd_test_wpdb_dbh']);
            ExternalDatabaseConnection::reset();
        }

        // ----------------------------------------------
        // get Tests
        // ----------------------------------------------
        public function test_get_returns_null_when_connection_fails(): void
        {
            $GLOBALS['_vd_test_mysqli_result'] = false;
            $GLOBALS['_vd_test_mysqli_error'] = 'Access denied for user';

            $result = ExternalDatabaseConnection::get();

            $this->assertNull($result);
            $this->assertSame('Access denied for user', ExternalDatabaseConnection::get_error());
        }

        public function test_get_returns_wpdb_instance_when_connection_succeeds(): void
        {
            $GLOBALS['_vd_test_mysqli_result'] = true;
            $GLOBALS['_vd_test_wpdb_dbh'] = new \stdClass();

            $result = ExternalDatabaseConnection::get();

            $this->assertInstanceOf(\wpdb::class, $result);
            $this->assertNull(ExternalDatabaseConnection::get_error());
        }

        public function test_get_connection_is_cached_on_success(): void
        {
            $GLOBALS['_vd_test_mysqli_result'] = true;
            $GLOBALS['_vd_test_wpdb_dbh'] = new \stdClass();

            $first  = ExternalDatabaseConnection::get();
            $second = ExternalDatabaseConnection::get();

            $this->assertSame($first, $second);
        }

        public function test_get_returns_cached_connection_without_reconnecting(): void
        {
            $GLOBALS['_vd_test_mysqli_result'] = true;
            $GLOBALS['_vd_test_wpdb_dbh'] = new \stdClass();
            $cached = ExternalDatabaseConnection::get();

            // Simulate the DB going down after the first connection
            $GLOBALS['_vd_test_mysqli_result'] = false;
            $GLOBALS['_vd_test_mysqli_error'] = 'Connection lost';

            // Must return the cached instance, not null, because mysqli_connect is never called again
            $this->assertSame($cached, ExternalDatabaseConnection::get());
            $this->assertNull(ExternalDatabaseConnection::get_error());
        }

        // ----------------------------------------------
        // reset Tests
        // ----------------------------------------------
        public function test_reset_clears_cached_connection(): void
        {
            $GLOBALS['_vd_test_mysqli_result'] = true;
            $GLOBALS['_vd_test_wpdb_dbh'] = new \stdClass();

            ExternalDatabaseConnection::get();
            ExternalDatabaseConnection::reset();

            $GLOBALS['_vd_test_mysqli_result'] = false;
            $GLOBALS['_vd_test_mysqli_error'] = 'Refused after reset';

            $this->assertNull(ExternalDatabaseConnection::get());
            $this->assertSame('Refused after reset', ExternalDatabaseConnection::get_error());
        }
}
