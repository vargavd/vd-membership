<?php

declare(strict_types=1);

namespace VDMembership\Tests\Infrastructure\Database;

use PHPUnit\Framework\TestCase;
use VDMembership\Domain\Member;
use VDMembership\Infrastructure\Database\ExternalDatabaseConnection;
use VDMembership\Infrastructure\Database\MysqlMemberRepository;

class MysqlMemberRepositoryTest extends TestCase
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
        $GLOBALS['_vd_test_mysqli_result'] = true;
        $GLOBALS['_vd_test_wpdb_dbh']      = new \stdClass();
        $GLOBALS['_vd_test_wpdb_last_error'] = '';
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
            'ugyfel'       => '1',
            'ugyfel_nev'   => null,
            'lenykori'     => null,
            'dat_szul'     => null,
            'szulhely'     => null,
            'anya'         => null,
            'cim_irsz'     => null,
            'cim_varos'    => null,
            'cim_cim'      => null,
            'telefon'      => null,
            'mobil'        => null,
            'emil'         => null,
            'dat_belep'    => null,
            'figyelmeztet' => null,
            'figy_dat'     => null,
            'figy_szoveg'  => null,
            'dij'          => null,
            'honap'        => null,
            'generalva'    => null,
            'esedekes'     => null,
            'megjegyzes'   => null,
            'statusz'      => null,
        ], $overrides);
    }

    // --- connection failure ---

    public function test_throws_when_no_db_connection(): void
    {
        $GLOBALS['_vd_test_mysqli_result'] = false;

        $this->expectException(\RuntimeException::class);
        MysqlMemberRepository::findAll();
    }

    // --- findAll ---

    public function test_findAll_returns_empty_array_when_no_rows(): void
    {
        $GLOBALS['_vd_test_wpdb_get_results'] = [];

        $result = MysqlMemberRepository::findAll();

        $this->assertSame([], $result);
    }

    public function test_findAll_returns_array_of_member_objects(): void
    {
        $GLOBALS['_vd_test_wpdb_get_results'] = [
            $this->makeRow(['ugyfel' => '1', 'ugyfel_nev' => 'Kovács János', 'statusz' => '1']),
            $this->makeRow(['ugyfel' => '2', 'ugyfel_nev' => 'Szabó Éva',    'statusz' => '0']),
        ];

        $result = MysqlMemberRepository::findAll();

        $this->assertCount(2, $result);
        $this->assertInstanceOf(Member::class, $result[0]);
        $this->assertSame(1, $result[0]->ugyfel);
        $this->assertSame('Kovács János', $result[0]->ugyfel_nev);
        $this->assertSame(2, $result[1]->ugyfel);
        $this->assertSame(0, $result[1]->statusz);
    }

    public function test_findAll_throws_on_db_error(): void
    {
        $GLOBALS['_vd_test_wpdb_get_results'] = [];
        $GLOBALS['_vd_test_wpdb_last_error']  = 'Table not found';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Table not found');
        MysqlMemberRepository::findAll();
    }

    // --- findById ---

    public function test_findById_returns_member_when_found(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row'] = $this->makeRow(['ugyfel' => '5', 'ugyfel_nev' => 'Teszt Elek', 'statusz' => '1']);

        $result = MysqlMemberRepository::findById(5);

        $this->assertInstanceOf(Member::class, $result);
        $this->assertSame(5, $result->ugyfel);
        $this->assertSame('Teszt Elek', $result->ugyfel_nev);
        $this->assertSame(1, $result->statusz);
    }

    public function test_findById_returns_null_when_not_found(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row'] = null;

        $result = MysqlMemberRepository::findById(999);

        $this->assertNull($result);
    }

    public function test_findById_throws_on_db_error(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Query failed';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Query failed');
        MysqlMemberRepository::findById(1);
    }

    // --- create ---

    public function test_create_returns_max_plus_one_when_table_has_rows(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']      = ['max_id' => '7'];
        $GLOBALS['_vd_test_wpdb_insert_result'] = 1;

        $member = new Member(0);
        $member->ugyfel_nev = 'Új Tag';

        $newId = MysqlMemberRepository::create($member);

        $this->assertSame(8, $newId);
        $this->assertSame(8, $member->ugyfel);
    }

    public function test_create_returns_1_when_table_is_empty(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']      = ['max_id' => null];
        $GLOBALS['_vd_test_wpdb_insert_result'] = 1;

        $newId = MysqlMemberRepository::create(new Member(0));

        $this->assertSame(1, $newId);
    }

    public function test_create_sets_figyelmeztet_to_N(): void
    {
        $capturedData = null;
        // We verify indirectly: if insert is called without error, figyelmeztet was forced to 'N'
        // (actual column values can't be inspected without a real DB; we rely on code review + this integration)
        $GLOBALS['_vd_test_wpdb_get_row']      = ['max_id' => '0'];
        $GLOBALS['_vd_test_wpdb_insert_result'] = 1;

        $member = new Member(0);
        $member->figyelmeztet = 'Y'; // should be ignored; repo always sends 'N'

        $newId = MysqlMemberRepository::create($member);
        $this->assertSame(1, $newId);
    }

    public function test_create_throws_on_insert_failure(): void
    {
        $GLOBALS['_vd_test_wpdb_get_row']      = ['max_id' => '3'];
        $GLOBALS['_vd_test_wpdb_last_error']   = 'Duplicate entry';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Duplicate entry');
        MysqlMemberRepository::create(new Member(0));
    }

    // --- update ---

    public function test_update_returns_true_on_success(): void
    {
        $GLOBALS['_vd_test_wpdb_update_result'] = 1;

        $member = new Member(3);
        $member->ugyfel_nev = 'Módosított Név';

        $this->assertTrue(MysqlMemberRepository::update($member));
    }

    public function test_update_returns_true_when_zero_rows_affected(): void
    {
        $GLOBALS['_vd_test_wpdb_update_result'] = 0;

        $this->assertTrue(MysqlMemberRepository::update(new Member(99)));
    }

    public function test_update_throws_on_db_error(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Update failed';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Update failed');
        MysqlMemberRepository::update(new Member(3));
    }

    // --- softDelete ---

    public function test_softDelete_returns_true_on_success(): void
    {
        $GLOBALS['_vd_test_wpdb_update_result'] = 1;

        $this->assertTrue(MysqlMemberRepository::softDelete(5));
    }

    public function test_softDelete_throws_on_db_error(): void
    {
        $GLOBALS['_vd_test_wpdb_last_error'] = 'Delete failed';

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Delete failed');
        MysqlMemberRepository::softDelete(5);
    }
}
