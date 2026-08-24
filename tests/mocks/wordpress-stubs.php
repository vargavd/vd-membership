<?php

// Global WordPress stubs
namespace {
    if (!defined('ARRAY_A')) {
        define('ARRAY_A', 'ARRAY_A');
    }
    if (!defined('OBJECT')) {
        define('OBJECT', 'OBJECT');
    }

    if (!class_exists('wpdb')) {
        class wpdb
        {
            public mixed $dbh = null;
            public string $last_error = '';

            public function __construct(string $user, string $pass, string $name, string $host)
            {
                $this->dbh = $GLOBALS['_vd_test_wpdb_dbh'] ?? null;
            }

            public function set_charset(mixed $dbh, string $charset = '', string $collate = ''): void {}

            public int $insert_id = 0;

            public function prepare(string $query, mixed ...$args): string
            {
                return $query;
            }

            public function get_results(string $query, string $output = OBJECT): array
            {
                $this->last_error = $GLOBALS['_vd_test_wpdb_last_error'] ?? '';
                return $GLOBALS['_vd_test_wpdb_get_results'] ?? [];
            }

            public function get_row(string $query, string $output = OBJECT, int $y = 0): mixed
            {
                $this->last_error = $GLOBALS['_vd_test_wpdb_last_error'] ?? '';
                return $GLOBALS['_vd_test_wpdb_get_row'] ?? null;
            }

            public function insert(string $table, array $data, mixed $format = null): int|false
            {
                $this->last_error = $GLOBALS['_vd_test_wpdb_last_error'] ?? '';
                if ($this->last_error) {
                    return false;
                }
                $this->insert_id = $GLOBALS['_vd_test_wpdb_insert_id'] ?? 0;
                return $GLOBALS['_vd_test_wpdb_insert_result'] ?? false;
            }

            public function update(string $table, array $data, array $where, mixed $format = null, mixed $where_format = null): int|false
            {
                $this->last_error = $GLOBALS['_vd_test_wpdb_last_error'] ?? '';
                if ($this->last_error) {
                    return false;
                }
                return $GLOBALS['_vd_test_wpdb_update_result'] ?? false;
            }
        }
    }

    if (!function_exists('get_option')) {
        function get_option(string $key, mixed $default = false): mixed
        {
            return $GLOBALS['_vd_test_options'][$key] ?? $default;
        }
    }

    if (!function_exists('update_option')) {
        function update_option(string $key, mixed $value): bool
        {
            $GLOBALS['_vd_test_options'][$key] = $value;
            return true;
        }
    }

    if (!function_exists('sanitize_text_field')) {
        function sanitize_text_field(string $str): string
        {
            return trim(strip_tags($str));
        }
    }

    if (!function_exists('current_user_can')) {
        function current_user_can(string $capability): bool
        {
            return $GLOBALS['_vd_test_can_manage_options'] ?? false;
        }
    }

    if (!function_exists('wp_kses_post')) {
        function wp_kses_post(string $str): string
        {
            return $str;
        }
    }

    if (!function_exists('esc_html')) {
        function esc_html(string $str): string
        {
            return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('esc_html__')) {
        function esc_html__(string $text, string $domain = 'default'): string
        {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }
    }

    if (!function_exists('set_transient')) {
        function set_transient(string $key, mixed $value, int $expiry = 0): bool
        {
            $GLOBALS['_vd_test_transients'][$key] = $value;
            return true;
        }
    }

    if (!function_exists('get_transient')) {
        function get_transient(string $key): mixed
        {
            return $GLOBALS['_vd_test_transients'][$key] ?? false;
        }
    }

    if (!function_exists('delete_transient')) {
        function delete_transient(string $key): bool
        {
            unset($GLOBALS['_vd_test_transients'][$key]);
            return true;
        }
    }
}

// Namespace-scoped stubs so ExternalDatabaseConnection never touches a real DB in tests
namespace VDMembership\Infrastructure\Database {
    function mysqli_connect(string $host, string $user, string $pass, string $name): mixed
    {
        return $GLOBALS['_vd_test_mysqli_result'] ?? false;
    }

    function mysqli_connect_error(): ?string
    {
        return $GLOBALS['_vd_test_mysqli_error'] ?? 'Connection refused';
    }

    function mysqli_close(mixed $link): bool
    {
        return true;
    }
}

