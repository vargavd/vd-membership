<?php

declare(strict_types=1);

namespace VDMembership\Admin;

use VDMembership\Application\MemberService;
use VDMembership\Domain\Member;

class NewMemberPage
{
    private static array   $errors      = [];
    private static ?Member $member_data = null;

    /**
     * Hooked on load-{page} — fires before any output, so wp_redirect() works here.
     */
    public static function init(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            self::process_post();
        }
    }

    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        if (self::$member_data !== null) {
            $member = self::$member_data;
        } else {
            $member = new Member(0);
            $member->statusz = 1; // default new members to active
        }

        $mode              = 'new';
        $validation_errors = self::$errors;

        include __DIR__ . '/../templates/admin/member-form.php';
    }

    private static function process_post(): void
    {
        check_admin_referer('vd_membership_new_nonce');

        $member = new Member(0);
        self::fill_from_post($member);

        $errors = MemberService::create_member($member);

        if (empty($errors)) {
            wp_redirect(admin_url('admin.php?page=' . AdminMenu::SLUG_MEMBERS));
            exit;
        }

        self::$errors      = $errors;
        self::$member_data = $member;
    }

    private static function fill_from_post(Member $member): void
    {
        $member->ugyfel_nev  = sanitize_text_field($_POST['ugyfel_nev']   ?? '') ?: null;
        $member->lenykori    = sanitize_text_field($_POST['lenykori']     ?? '') ?: null;
        $member->dat_szul    = sanitize_text_field($_POST['dat_szul']     ?? '') ?: null;
        $member->szulhely    = sanitize_text_field($_POST['szulhely']     ?? '') ?: null;
        $member->anya        = sanitize_text_field($_POST['anya']         ?? '') ?: null;
        $member->cim_irsz    = sanitize_text_field($_POST['cim_irsz']    ?? '') ?: null;
        $member->cim_varos   = sanitize_text_field($_POST['cim_varos']   ?? '') ?: null;
        $member->cim_cim     = sanitize_text_field($_POST['cim_cim']     ?? '') ?: null;
        $member->telefon     = sanitize_text_field($_POST['telefon']     ?? '') ?: null;
        $member->mobil       = sanitize_text_field($_POST['mobil']       ?? '') ?: null;
        $member->emil        = sanitize_email($_POST['emil']             ?? '') ?: null;
        $member->dat_belep   = sanitize_text_field($_POST['dat_belep']   ?? '') ?: null;
        $member->figy_dat    = sanitize_text_field($_POST['figy_dat']    ?? '') ?: null;
        $member->figy_szoveg = sanitize_text_field($_POST['figy_szoveg'] ?? '') ?: null;
        $member->dij         = ($_POST['dij'] ?? '') !== '' ? (float) $_POST['dij'] : null;
        $member->honap       = ($_POST['honap'] ?? '') !== '' ? intval($_POST['honap']) : null;
        $member->megjegyzes  = sanitize_textarea_field($_POST['megjegyzes'] ?? '') ?: null;
        $member->statusz     = intval($_POST['statusz'] ?? 1);
        // figyelmeztet is not set here; MysqlMemberRepository::create() always inserts 'N'
    }
}
