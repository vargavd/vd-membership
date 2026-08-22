<?php

declare(strict_types=1);

namespace VDMembership\Application;

use VDMembership\Domain\Member;
use VDMembership\Domain\MemberValidator;
use VDMembership\Infrastructure\Database\MysqlMemberRepository;

class MemberService
{
    public const TRANSIENT_KEY     = 'vd_membership_notices';
    private const TRANSIENT_EXPIRY = 60;

    /** Returns all members; stores an error transient and returns [] on DB failure. */
    public static function get_all_members(): array
    {
        try {
            return MysqlMemberRepository::findAll();
        } catch (\RuntimeException $e) {
            self::add_notice('error', 'Adatbázis hiba: ' . $e->getMessage());
            return [];
        }
    }

    /** Returns the member or null; stores an error transient on DB failure. */
    public static function get_member(int $id): ?Member
    {
        try {
            return MysqlMemberRepository::findById($id);
        } catch (\RuntimeException $e) {
            self::add_notice('error', 'Adatbázis hiba: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Validates and creates a member.
     *
     * Returns [] when the operation is handled (success or DB error — check transient for the notice).
     * Returns a non-empty string[] of validation error messages when validation fails (caller re-renders form).
     */
    public static function create_member(Member $member): array
    {
        $errors = MemberValidator::validate($member);
        if (!empty($errors)) {
            return $errors;
        }

        try {
            MysqlMemberRepository::create($member);
            self::add_notice('success', 'A tag sikeresen létrehozva.');
        } catch (\RuntimeException $e) {
            self::add_notice('error', 'Adatbázis hiba: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Validates and updates a member.
     *
     * Same return convention as create_member().
     */
    public static function update_member(Member $member): array
    {
        $errors = MemberValidator::validate($member);
        if (!empty($errors)) {
            return $errors;
        }

        try {
            MysqlMemberRepository::update($member);
            self::add_notice('success', 'A tag sikeresen módosítva.');
        } catch (\RuntimeException $e) {
            self::add_notice('error', 'Adatbázis hiba: ' . $e->getMessage());
        }

        return [];
    }

    /** Soft-deletes a member; always stores a success or error transient. */
    public static function delete_member(int $id): void
    {
        try {
            MysqlMemberRepository::softDelete($id);
            self::add_notice('success', 'A tag sikeresen törölve.');
        } catch (\RuntimeException $e) {
            self::add_notice('error', 'Adatbázis hiba: ' . $e->getMessage());
        }
    }

    private static function add_notice(string $type, string $message): void
    {
        $notices = get_transient(self::TRANSIENT_KEY);
        if (!is_array($notices)) {
            $notices = [];
        }
        $notices[] = ['type' => $type, 'message' => $message];
        set_transient(self::TRANSIENT_KEY, $notices, self::TRANSIENT_EXPIRY);
    }
}
