<?php

declare(strict_types=1);

namespace VDMembership\Tests\Configuration;

use PHPUnit\Framework\TestCase;
use VDMembership\Configuration\SettingsRepository;

class SettingsRepositoryTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['_vd_test_options'] = [];
    }

    public function test_default_values_are_empty_strings(): void
    {
        $this->assertSame('', SettingsRepository::get_db_host());
        $this->assertSame('', SettingsRepository::get_db_name());
        $this->assertSame('', SettingsRepository::get_db_user());
        $this->assertSame('', SettingsRepository::get_db_password());
    }

    public function test_has_credentials_false_when_no_settings_saved(): void
    {
        $this->assertFalse(SettingsRepository::has_credentials());
    }

    public function test_save_persists_all_fields(): void
    {
        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => 'secret',
        ]);

        $this->assertSame('localhost', SettingsRepository::get_db_host());
        $this->assertSame('mydb', SettingsRepository::get_db_name());
        $this->assertSame('root', SettingsRepository::get_db_user());
        $this->assertSame('secret', SettingsRepository::get_db_password());
    }

    public function test_has_credentials_true_when_all_fields_filled(): void
    {
        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => 'secret',
        ]);

        $this->assertTrue(SettingsRepository::has_credentials());
    }

    public function test_has_credentials_true_when_password_is_empty(): void
    {
        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => '',
        ]);

        $this->assertTrue(SettingsRepository::has_credentials());
    }

    public function test_has_credentials_false_when_user_is_missing(): void
    {
        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => '',
            'db_password' => 'secret',
        ]);

        $this->assertFalse(SettingsRepository::has_credentials());
    }

    public function test_password_preserved_when_empty_value_submitted(): void
    {
        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => 'original',
        ]);

        SettingsRepository::save([
            'db_host'     => 'localhost',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => '',
        ]);

        $this->assertSame('original', SettingsRepository::get_db_password());
    }

    public function test_save_sanitizes_fields(): void
    {
        SettingsRepository::save([
            'db_host'     => '  <b>localhost</b>  ',
            'db_name'     => 'mydb',
            'db_user'     => 'root',
            'db_password' => 'secret',
        ]);

        $this->assertSame('localhost', SettingsRepository::get_db_host());
    }
}
