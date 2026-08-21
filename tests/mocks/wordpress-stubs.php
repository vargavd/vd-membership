<?php

// Global WordPress stubs
namespace {
    if (!class_exists('wpdb')) {
        class wpdb
        {
            public mixed $dbh = null;
            public string $last_error = '';

            public function __construct(string $user, string $pass, string $name, string $host)
            {
                $this->dbh = $GLOBALS['_vd_test_wpdb_dbh'] ?? null;
            }

            public function set_charset(mixed $dbh, string $charset, string $collate = ''): void {}
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

