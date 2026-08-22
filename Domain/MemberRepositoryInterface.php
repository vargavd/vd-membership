<?php

declare(strict_types=1);

namespace VDMembership\Domain;

interface MemberRepositoryInterface
{
    public static function findAll(): array;
    public static function findById(int $id): ?Member;
    public static function create(Member $member): int;
    public static function update(Member $member): bool;
    public static function softDelete(int $id): bool;
}
