<?php

declare(strict_types=1);

namespace VDMembership\Infrastructure\Database;

use VDMembership\Domain\Member;
use VDMembership\Domain\MemberRepositoryInterface;

class MysqlMemberRepository implements MemberRepositoryInterface
{
    private const TABLE = 'ugyfel';

    public static function findAll(): array
    {
        $db = self::connection();

        $rows = $db->get_results('SELECT * FROM `' . self::TABLE . '` ORDER BY `ugyfel_nev`', ARRAY_A);

        if ($db->last_error) {
            throw new \RuntimeException($db->last_error);
        }

        return array_map(fn(array $row) => Member::fromRow($row), $rows ?? []);
    }

    public static function findById(int $id): ?Member
    {
        $db = self::connection();

        $row = $db->get_row(
            $db->prepare('SELECT * FROM `' . self::TABLE . '` WHERE `ugyfel` = %d', $id),
            ARRAY_A
        );

        if ($db->last_error) {
            throw new \RuntimeException($db->last_error);
        }

        return $row ? Member::fromRow($row) : null;
    }

    public static function create(Member $member): int
    {
        $db = self::connection();

        $result = $db->get_row('SELECT MAX(`ugyfel`) AS max_id FROM `' . self::TABLE . '`', ARRAY_A);
        $new_id = ($result && $result['max_id'] !== null) ? (int) $result['max_id'] + 1 : 1;

        $data = [
            'ugyfel'       => $new_id,
            'ugyfel_nev'   => $member->ugyfel_nev,
            'lenykori'     => $member->lenykori,
            'dat_szul'     => $member->dat_szul,
            'szulhely'     => $member->szulhely,
            'anya'         => $member->anya,
            'cim_irsz'     => $member->cim_irsz,
            'cim_varos'    => $member->cim_varos,
            'cim_cim'      => $member->cim_cim,
            'telefon'      => $member->telefon,
            'mobil'        => $member->mobil,
            'emil'         => $member->emil,
            'dat_belep'    => $member->dat_belep,
            'figyelmeztet' => 'N',
            'figy_dat'     => $member->figy_dat,
            'figy_szoveg'  => $member->figy_szoveg,
            'dij'          => $member->dij,
            'honap'        => $member->honap,
            'megjegyzes'   => $member->megjegyzes,
            'statusz'      => $member->statusz ?? 1,
        ];

        // Format order matches $data key order above
        $formats = ['%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%s', '%d'];

        $inserted = $db->insert(self::TABLE, $data, $formats);

        if ($inserted === false || $db->last_error) {
            throw new \RuntimeException($db->last_error ?: 'Failed to insert member.');
        }

        $member->ugyfel = $new_id;
        return $new_id;
    }

    public static function update(Member $member): bool
    {
        $db = self::connection();

        // figyelmeztet, generalva, and esedekes are never modified by the plugin
        $data = [
            'ugyfel_nev'  => $member->ugyfel_nev,
            'lenykori'    => $member->lenykori,
            'dat_szul'    => $member->dat_szul,
            'szulhely'    => $member->szulhely,
            'anya'        => $member->anya,
            'cim_irsz'    => $member->cim_irsz,
            'cim_varos'   => $member->cim_varos,
            'cim_cim'     => $member->cim_cim,
            'telefon'     => $member->telefon,
            'mobil'       => $member->mobil,
            'emil'        => $member->emil,
            'dat_belep'   => $member->dat_belep,
            'figy_dat'    => $member->figy_dat,
            'figy_szoveg' => $member->figy_szoveg,
            'dij'         => $member->dij,
            'honap'       => $member->honap,
            'megjegyzes'  => $member->megjegyzes,
            'statusz'     => $member->statusz,
        ];

        $formats = ['%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%f', '%d', '%s', '%d'];

        $result = $db->update(self::TABLE, $data, ['ugyfel' => $member->ugyfel], $formats, ['%d']);

        if ($result === false || $db->last_error) {
            throw new \RuntimeException($db->last_error ?: 'Failed to update member.');
        }

        return true;
    }

    public static function softDelete(int $id): bool
    {
        $db = self::connection();

        $result = $db->update(self::TABLE, ['statusz' => 0], ['ugyfel' => $id], ['%d'], ['%d']);

        if ($result === false || $db->last_error) {
            throw new \RuntimeException($db->last_error ?: 'Failed to delete member.');
        }

        return true;
    }

    private static function connection(): \wpdb
    {
        $db = ExternalDatabaseConnection::get();
        if ($db === null) {
            throw new \RuntimeException(
                'No database connection: ' . (ExternalDatabaseConnection::get_error() ?? '')
            );
        }
        return $db;
    }
}
