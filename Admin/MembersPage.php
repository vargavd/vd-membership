<?php

declare(strict_types=1);

namespace VDMembership\Admin;

use VDMembership\Application\MemberService;

class MembersPage
{
    public static function render(): void
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $members = MemberService::get_all_members();

        include __DIR__ . '/../templates/admin/members.php';
    }

    public static function handle_post(): void
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }

        check_admin_referer('vd_membership_members_nonce');

        $vd_action = sanitize_text_field($_POST['vd_action'] ?? '');

        if ($vd_action === 'delete') {
            $id = intval($_POST['ugyfel'] ?? 0);
            if ($id > 0) {
                MemberService::delete_member($id);
            }
        }

        wp_redirect(admin_url('admin.php?page=' . AdminMenu::SLUG_MEMBERS));
        exit;
    }
}
