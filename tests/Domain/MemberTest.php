<?php

declare(strict_types=1);

namespace VDMembership\Tests\Domain;

use PHPUnit\Framework\TestCase;
use VDMembership\Domain\Member;

class MemberTest extends TestCase
{
    public function test_constructor_sets_ugyfel(): void
    {
        $member = new Member(42);

        $this->assertSame(42, $member->ugyfel);
    }

    public function test_constructor_defaults_all_fields_to_null(): void
    {
        $member = new Member(1);

        $this->assertNull($member->ugyfel_nev);
        $this->assertNull($member->lenykori);
        $this->assertNull($member->dat_szul);
        $this->assertNull($member->szulhely);
        $this->assertNull($member->anya);
        $this->assertNull($member->cim_irsz);
        $this->assertNull($member->cim_varos);
        $this->assertNull($member->cim_cim);
        $this->assertNull($member->telefon);
        $this->assertNull($member->mobil);
        $this->assertNull($member->emil);
        $this->assertNull($member->dat_belep);
        $this->assertNull($member->figyelmeztet);
        $this->assertNull($member->figy_dat);
        $this->assertNull($member->figy_szoveg);
        $this->assertNull($member->dij);
        $this->assertNull($member->honap);
        $this->assertNull($member->generalva);
        $this->assertNull($member->esedekes);
        $this->assertNull($member->megjegyzes);
        $this->assertNull($member->statusz);
    }

    public function test_fromRow_maps_all_fields_and_casts_types(): void
    {
        $row = [
            'ugyfel'       => '7',
            'ugyfel_nev'   => 'Kovács János',
            'lenykori'     => 'Kiss',
            'dat_szul'     => '1980-03-15',
            'szulhely'     => 'Budapest',
            'anya'         => 'Szabó Mária',
            'cim_irsz'     => '1234',
            'cim_varos'    => 'Pécs',
            'cim_cim'      => 'Fő utca 1.',
            'telefon'      => '06-1-234-5678',
            'mobil'        => '+36301234567',
            'emil'         => 'kovacs@example.com',
            'dat_belep'    => '2010-06-01',
            'figyelmeztet' => 'N',
            'figy_dat'     => '2024-01-01',
            'figy_szoveg'  => 'Figyelmeztető szöveg',
            'dij'          => '5500.5',
            'honap'        => '3',
            'generalva'    => null,
            'esedekes'     => null,
            'megjegyzes'   => 'Megjegyzés',
            'statusz'      => '1',
        ];

        $member = Member::fromRow($row);

        $this->assertSame(7, $member->ugyfel);
        $this->assertSame('Kovács János', $member->ugyfel_nev);
        $this->assertSame('Kiss', $member->lenykori);
        $this->assertSame('1980-03-15', $member->dat_szul);
        $this->assertSame('Budapest', $member->szulhely);
        $this->assertSame('Szabó Mária', $member->anya);
        $this->assertSame('1234', $member->cim_irsz);
        $this->assertSame('Pécs', $member->cim_varos);
        $this->assertSame('Fő utca 1.', $member->cim_cim);
        $this->assertSame('06-1-234-5678', $member->telefon);
        $this->assertSame('+36301234567', $member->mobil);
        $this->assertSame('kovacs@example.com', $member->emil);
        $this->assertSame('2010-06-01', $member->dat_belep);
        $this->assertSame('N', $member->figyelmeztet);
        $this->assertSame('2024-01-01', $member->figy_dat);
        $this->assertSame('Figyelmeztető szöveg', $member->figy_szoveg);
        $this->assertSame(5500.5, $member->dij);
        $this->assertSame(3, $member->honap);
        $this->assertNull($member->generalva);
        $this->assertNull($member->esedekes);
        $this->assertSame('Megjegyzés', $member->megjegyzes);
        $this->assertSame(1, $member->statusz);
    }

    public function test_fromRow_maps_null_values_to_null(): void
    {
        $row = [
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
        ];

        $member = Member::fromRow($row);

        $this->assertSame(1, $member->ugyfel);
        $this->assertNull($member->ugyfel_nev);
        $this->assertNull($member->dij);
        $this->assertNull($member->honap);
        $this->assertNull($member->statusz);
    }

    public function test_properties_are_mutable(): void
    {
        $member = new Member(5);
        $member->ugyfel_nev = 'Teszt Elek';
        $member->statusz = 1;

        $this->assertSame('Teszt Elek', $member->ugyfel_nev);
        $this->assertSame(1, $member->statusz);
    }
}
