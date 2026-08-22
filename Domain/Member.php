<?php

declare(strict_types=1);

namespace VDMembership\Domain;

class Member
{
    public ?string $ugyfel_nev   = null;
    public ?string $lenykori     = null;
    public ?string $dat_szul     = null;
    public ?string $szulhely     = null;
    public ?string $anya         = null;
    public ?string $cim_irsz     = null;
    public ?string $cim_varos    = null;
    public ?string $cim_cim      = null;
    public ?string $telefon      = null;
    public ?string $mobil        = null;
    public ?string $emil         = null;
    public ?string $dat_belep    = null;
    public ?string $figyelmeztet = null;
    public ?string $figy_dat     = null;
    public ?string $figy_szoveg  = null;
    public ?float  $dij          = null;
    public ?int    $honap        = null;
    public ?string $generalva    = null;
    public ?string $esedekes     = null;
    public ?string $megjegyzes   = null;
    public ?int    $statusz      = null;

    public function __construct(public int $ugyfel) {}

    public static function fromRow(array $row): self
    {
        $m = new self((int) $row['ugyfel']);

        $m->ugyfel_nev   = isset($row['ugyfel_nev'])    ? (string) $row['ugyfel_nev']   : null;
        $m->lenykori     = isset($row['lenykori'])      ? (string) $row['lenykori']     : null;
        $m->dat_szul     = isset($row['dat_szul'])      ? (string) $row['dat_szul']     : null;
        $m->szulhely     = isset($row['szulhely'])      ? (string) $row['szulhely']     : null;
        $m->anya         = isset($row['anya'])          ? (string) $row['anya']         : null;
        $m->cim_irsz     = isset($row['cim_irsz'])      ? (string) $row['cim_irsz']    : null;
        $m->cim_varos    = isset($row['cim_varos'])     ? (string) $row['cim_varos']    : null;
        $m->cim_cim      = isset($row['cim_cim'])       ? (string) $row['cim_cim']      : null;
        $m->telefon      = isset($row['telefon'])       ? (string) $row['telefon']      : null;
        $m->mobil        = isset($row['mobil'])         ? (string) $row['mobil']        : null;
        $m->emil         = isset($row['emil'])          ? (string) $row['emil']         : null;
        $m->dat_belep    = isset($row['dat_belep'])     ? (string) $row['dat_belep']    : null;
        $m->figyelmeztet = isset($row['figyelmeztet'])  ? (string) $row['figyelmeztet'] : null;
        $m->figy_dat     = isset($row['figy_dat'])      ? (string) $row['figy_dat']     : null;
        $m->figy_szoveg  = isset($row['figy_szoveg'])   ? (string) $row['figy_szoveg']  : null;
        $m->dij          = isset($row['dij'])           ? (float)  $row['dij']          : null;
        $m->honap        = isset($row['honap'])         ? (int)    $row['honap']        : null;
        $m->generalva    = isset($row['generalva'])     ? (string) $row['generalva']    : null;
        $m->esedekes     = isset($row['esedekes'])      ? (string) $row['esedekes']     : null;
        $m->megjegyzes   = isset($row['megjegyzes'])    ? (string) $row['megjegyzes']   : null;
        $m->statusz      = isset($row['statusz'])       ? (int)    $row['statusz']      : null;

        return $m;
    }
}
