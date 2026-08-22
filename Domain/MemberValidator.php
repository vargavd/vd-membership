<?php

declare(strict_types=1);

namespace VDMembership\Domain;

class MemberValidator
{
    /**
     * Returns a list of human-readable error strings; empty array means valid.
     */
    public static function validate(Member $member): array
    {
        $errors = [];

        // ugyfel_nev is the only required field
        if ($member->ugyfel_nev === null || trim($member->ugyfel_nev) === '') {
            $errors[] = 'A név megadása kötelező.';
        } elseif (mb_strlen($member->ugyfel_nev) > 50) {
            $errors[] = 'A név legfeljebb 50 karakter lehet.';
        }

        self::check_length($errors, $member->lenykori,    50,   'Leánykori név');
        self::check_length($errors, $member->szulhely,    50,   'Születési hely');
        self::check_length($errors, $member->anya,        50,   'Anyja neve');
        self::check_length($errors, $member->cim_irsz,    6,    'Irányítószám');
        self::check_length($errors, $member->cim_varos,   25,   'Város');
        self::check_length($errors, $member->cim_cim,     50,   'Cím');
        self::check_length($errors, $member->telefon,     25,   'Telefon');
        self::check_length($errors, $member->mobil,       25,   'Mobil');
        self::check_length($errors, $member->emil,        50,   'E-mail');
        self::check_length($errors, $member->figy_szoveg, 255,  'Figyelmeztetés szövege');
        self::check_length($errors, $member->megjegyzes,  1024, 'Megjegyzés');

        self::check_date($errors, $member->dat_szul,  'Születési dátum');
        self::check_date($errors, $member->dat_belep, 'Belépési dátum');
        self::check_date($errors, $member->figy_dat,  'Figyelmeztetési dátum');

        if ($member->emil !== null && $member->emil !== '') {
            if (!filter_var($member->emil, FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Érvénytelen e-mail cím.';
            }
        }

        if ($member->honap !== null && ($member->honap < 1 || $member->honap > 12)) {
            $errors[] = 'A hónap értéke 1 és 12 között kell legyen.';
        }

        return $errors;
    }

    private static function check_length(array &$errors, ?string $value, int $max, string $label): void
    {
        if ($value !== null && mb_strlen($value) > $max) {
            $errors[] = "{$label} legfeljebb {$max} karakter lehet.";
        }
    }

    private static function check_date(array &$errors, ?string $value, string $label): void
    {
        if ($value === null || $value === '') {
            return;
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            $errors[] = "Érvénytelen {$label} (ÉÉÉÉ-HH-NN).";
            return;
        }
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $errors[] = "Érvénytelen {$label} (ÉÉÉÉ-HH-NN).";
        }
    }
}
