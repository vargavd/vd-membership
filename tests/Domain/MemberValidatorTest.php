<?php

declare(strict_types=1);

namespace VDMembership\Tests\Domain;

use PHPUnit\Framework\TestCase;
use VDMembership\Domain\Member;
use VDMembership\Domain\MemberValidator;

class MemberValidatorTest extends TestCase
{
    private function validMember(): Member
    {
        $m = new Member(1);
        $m->ugyfel_nev = 'Kovács János';
        return $m;
    }

    // --- ugyfel_nev (required) ---

    public function test_valid_member_passes(): void
    {
        $this->assertSame([], MemberValidator::validate($this->validMember()));
    }

    public function test_null_ugyfel_nev_fails(): void
    {
        $m = new Member(1);
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('kötelező', $errors[0]);
    }

    public function test_empty_ugyfel_nev_fails(): void
    {
        $m = new Member(1);
        $m->ugyfel_nev = '   ';
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('kötelező', $errors[0]);
    }

    public function test_ugyfel_nev_at_max_length_passes(): void
    {
        $m = $this->validMember();
        $m->ugyfel_nev = str_repeat('a', 50);
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_ugyfel_nev_over_max_length_fails(): void
    {
        $m = $this->validMember();
        $m->ugyfel_nev = str_repeat('a', 51);
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('50', $errors[0]);
    }

    // --- varchar length checks ---

    public function test_cim_irsz_over_6_fails(): void
    {
        $m = $this->validMember();
        $m->cim_irsz = '1234567';
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('6', $errors[0]);
    }

    public function test_cim_irsz_at_6_passes(): void
    {
        $m = $this->validMember();
        $m->cim_irsz = '123456';
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_megjegyzes_over_1024_fails(): void
    {
        $m = $this->validMember();
        $m->megjegyzes = str_repeat('x', 1025);
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('1024', $errors[0]);
    }

    public function test_null_optional_fields_pass(): void
    {
        $m = $this->validMember();
        // all other fields remain null
        $this->assertSame([], MemberValidator::validate($m));
    }

    // --- date validation ---

    public function test_valid_dat_szul_passes(): void
    {
        $m = $this->validMember();
        $m->dat_szul = '1980-03-15';
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_invalid_date_format_fails(): void
    {
        $m = $this->validMember();
        $m->dat_szul = '15-03-1980';
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Születési dátum', $errors[0]);
    }

    public function test_impossible_date_fails(): void
    {
        $m = $this->validMember();
        $m->dat_belep = '2024-02-30';
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('Belépési dátum', $errors[0]);
    }

    public function test_null_date_passes(): void
    {
        $m = $this->validMember();
        $m->dat_szul  = null;
        $m->dat_belep = null;
        $m->figy_dat  = null;
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_empty_string_date_passes(): void
    {
        $m = $this->validMember();
        $m->dat_szul = '';
        $this->assertSame([], MemberValidator::validate($m));
    }

    // --- email ---

    public function test_valid_email_passes(): void
    {
        $m = $this->validMember();
        $m->emil = 'kovacs@example.com';
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_invalid_email_fails(): void
    {
        $m = $this->validMember();
        $m->emil = 'not-an-email';
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('e-mail', $errors[0]);
    }

    public function test_null_email_passes(): void
    {
        $m = $this->validMember();
        $m->emil = null;
        $this->assertSame([], MemberValidator::validate($m));
    }

    // --- honap ---

    public function test_honap_null_passes(): void
    {
        $m = $this->validMember();
        $m->honap = null;
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_honap_1_passes(): void
    {
        $m = $this->validMember();
        $m->honap = 1;
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_honap_12_passes(): void
    {
        $m = $this->validMember();
        $m->honap = 12;
        $this->assertSame([], MemberValidator::validate($m));
    }

    public function test_honap_0_fails(): void
    {
        $m = $this->validMember();
        $m->honap = 0;
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('hónap', $errors[0]);
    }

    public function test_honap_13_fails(): void
    {
        $m = $this->validMember();
        $m->honap = 13;
        $errors = MemberValidator::validate($m);
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('hónap', $errors[0]);
    }

    // --- figyelmeztet is ignored ---

    public function test_figyelmeztet_any_value_does_not_cause_error(): void
    {
        $m = $this->validMember();
        $m->figyelmeztet = 'Y';
        $this->assertSame([], MemberValidator::validate($m));
    }

    // --- multiple errors ---

    public function test_multiple_violations_return_multiple_errors(): void
    {
        $m = new Member(1);
        // ugyfel_nev missing + invalid email + honap out of range
        $m->ugyfel_nev = '';
        $m->emil       = 'bad';
        $m->honap      = 99;
        $errors = MemberValidator::validate($m);
        $this->assertGreaterThanOrEqual(3, count($errors));
    }
}
