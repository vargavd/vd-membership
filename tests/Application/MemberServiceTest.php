<?php

declare(strict_types=1);

namespace VDMembership\Tests\Application;

use PHPUnit\Framework\TestCase;
use VDMembership\Application\MemberService;
use VDMembership\Domain\Member;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;

class MemberServiceTest extends TestCase
{
    protected function setUp(): void
    {
        $GLOBALS['_vd_test_options'] = [
            'vd_membership_settings' => [
                'db_host'     => 'localhost',
                'db_name'     => 'testdb',
                'db_user'     => 'root',
                'db_password' => '',
            ],
        ];
        $GLOBALS['_vd_test_mysqli_result']   = true;
        $GLOBALS['_vd_test_wpdb_dbh']        = new \stdClass();
        $GLOBALS['_vd_test_wpdb_last_error'] = '';
        $GLOBALS['_vd_test_transients']      = [];
        unset(
            $GLOBALS['_vd_test_wpdb_get_results'],
            $GLOBALS['_vd_test_wpdb_get_row'],
            $GLOBALS['_vd_test_wpdb_insert_result'],
            $GLOBALS['_vd_test_wpdb_update_result']
        );
        ExternalDatabaseConnection::reset();
    }

    // --- helpers ---

    private function makeRow(array $overrides = []): array
    {
        return array_merge([
            'ugyfel' => '1', 'ugyfel_nev' => null, 'lenykori' => null,
            'dat_szul' => null, 'szulhely' => null, 'anya' => null,
            'cim_irsz' => null, 'cim_varos' => null, 'cim_cim' => null,
            'telefon' => null, 'mobil' => null, 'emil' => null,
            'dat_belep' => null, 'figyelmeztet' => null, 'figy_dat' => null,
            'figy_szoveg' => null, 'dij' => null, 'honap' => null,
            'generalva' => null, 'esedekes' => null, 'megjegyzes' => null,
            'statusz' => null,
        ], $overrides);
    }

    private function notices(): array
    {
        return $GLOBALS['_vd_test_transients'][MemberService::TRANSIENT_KEY] ?? [];
    }

    // --- get_all_members ---

    public function test_get_all_members_returns_member_array(): void
    {
        $GLOBALS['_vd_test_wpdb_get_results'] = [
            $this->makeRow(['ugyfel' => '1', 'ugyfel_nev' => 'Kiss Péter', 'statusz' => '1']),
            $this->makeRow(['ugyfel' => '2', 'ugyfel_nev' => 'Nagy Éva',   'statusz' => '0']),
        ];

        $result = MemberService::get_all_members();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Member::class, $result[0]);
        $this->assertSame('Kiss Péter', $result[0]->ugyfel_nev);
        $this->assertEmpty($this->notices());
    }

    public function test_get_all_members_stores_error_transient_on_db_failure(): void
    {
        $GLOBALS['_vd_test_mysqli_result'] = false;

        $result = MemberService::get_all_members();

        $this->assertSame([], $result);
        $this->assertCount(1, $this->notices());
        $this->assertSame('error', $this->notices()[0]['type']);
    }

    // --- get_member ---

    public function test_get_member_returns_member_when_found(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row'] = $this->makeRow(['ugyfel' => '5', 'ugyfel_nev' => 'Teszt Elek']);

        $result = MemberService::get_member(5);

        $this->assertInstanceOf(Member::class, $result);
        $this->assertSame(5, $result->ugyfel);
        $this->assertEmpty($this->notices());
    }

    public function test_get_member_returns_null_without_transient_when_not_found(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']    = null;
        $GLOBALS['_vd_test_wpdb_last_error'] = '';

        $result = MemberService::get_member(999);

        $this->assertNull($result);
        $this->assertEmpty($this->notices());
    }

    public function test_get_member_stores_error_transient_on_db_failure(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Query failed';

        $result = MemberService::get_member(1);

        $this->assertNull($result);
        $this->assertCount(1, $this->notices());
        $this->assertSame('error', $this->notices()[0]['type']);
        $this->assertStringContainsString('Query failed', $this->notices()[0]['message']);
    }

    // --- create_member ---

    public function test_create_member_returns_validation_errors_when_invalid(): void
    {
        $member = new Member(0); // ugyfel_nev missing

        $errors = MemberService::create_member($member);

        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('kötelező', $errors[0]);
        $this->assertEmpty($this->notices()); // no transient on validation failure
    }

    public function test_create_member_stores_success_transient_on_success(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']      = ['max_id' => '3'];
        $GLOBALS['_vd_test_wpdb_insert_result'] = 1;

        $member = new Member(0);
        $member->ugyfel_nev = 'Új Tag';

        $errors = MemberService::create_member($member);

        $this->assertSame([], $errors);
        $this->assertCount(1, $this->notices());
        $this->assertSame('success', $this->notices()[0]['type']);
        $this->assertStringContainsString('létrehozva', $this->notices()[0]['message']);
    }

    public function test_create_member_stores_error_transient_on_db_failure(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']    = ['max_id' => '1'];
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Insert error';

        $member = new Member(0);
        $member->ugyfel_nev = 'Új Tag';

        $errors = MemberService::create_member($member);

        $this->assertSame([], $errors);
        $this->assertCount(1, $this->notices());
        $this->assertSame('error', $this->notices()[0]['type']);
        $this->assertStringContainsString('Insert error', $this->notices()[0]['message']);
    }

    // --- update_member ---

    public function test_update_member_returns_validation_errors_when_invalid(): void
    {
        $member = new Member(1);
        $member->ugyfel_nev = '';

        $errors = MemberService::update_member($member);

        $this->assertNotEmpty($errors);
        $this->assertEmpty($this->notices());
    }

    public function test_update_member_stores_success_transient_on_success(): void
    {
        $GLOBALS['_vd_test_wpdb_update_result'] = 1;

        $member = new Member(1);
        $member->ugyfel_nev = 'Módosított Név';

        $errors = MemberService::update_member($member);

        $this->assertSame([], $errors);
        $this->assertCount(1, $this->notices());
        $this->assertSame('success', $this->notices()[0]['type']);
        $this->assertStringContainsString('módosítva', $this->notices()[0]['message']);
    }

    public function test_update_member_stores_error_transient_on_db_failure(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Update failed';

        $member = new Member(1);
        $member->ugyfel_nev = 'Módosított Név';

        $errors = MemberService::update_member($member);

        $this->assertSame([], $errors);
        $this->assertCount(1, $this->notices());
        $this->assertSame('error', $this->notices()[0]['type']);
    }

    // --- delete_member ---

    public function test_delete_member_stores_success_transient(): void
    {
        $GLOBALS['_vd_test_wpdb_update_result'] = 1;

        MemberService::delete_member(5);

        $this->assertCount(1, $this->notices());
        $this->assertSame('success', $this->notices()[0]['type']);
        $this->assertStringContainsString('törölve', $this->notices()[0]['message']);
    }

    public function test_delete_member_stores_error_transient_on_db_failure(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Delete failed';

        MemberService::delete_member(5);

        $this->assertCount(1, $this->notices());
        $this->assertSame('error', $this->notices()[0]['type']);
        $this->assertStringContainsString('Delete failed', $this->notices()[0]['message']);
    }
}
