<?php

declare(strict_types=1);

namespace VDMembership\Admin;

class AdminMenu
{
    public const SLUG_ROOT     = 'vd-membership';
    public const SLUG_MEMBERS  = 'vd-membership';          // same as root (first submenu)
    public const SLUG_NEW      = 'vd-membership-new';
    public const SLUG_EDIT     = 'vd-membership-edit';
    public const SLUG_SETTINGS = 'vd-membership-settings';

    public static function register(): void
    {
        add_menu_page(
            'VD Membership',
            'VD Membership',
            'manage_options',
            self::SLUG_ROOT,
            [MembersPage::class, 'render'],
            'dashicons-groups',
            30
        );

        // First submenu renames the auto-created root item to 'Tagok'
        add_submenu_page(
            self::SLUG_ROOT,
            'Tagok – VD Membership',
            'Tagok',
            'manage_options',
            self::SLUG_MEMBERS,
            [MembersPage::class, 'render']
        );

        $new_hook = add_submenu_page(
            self::SLUG_ROOT,
            'Új tag – VD Membership',
            'Új tag',
            'manage_options',
            self::SLUG_NEW,
            [NewMemberPage::class, 'render']
        );
        add_action('load-' . $new_hook, [NewMemberPage::class, 'init']);

        // Register edit page; accessed via table row links, not directly navigated to
        $edit_hook = add_submenu_page(
            self::SLUG_ROOT,
            'Tag szerkesztése – VD Membership',
            'Tag szerkesztése',
            'manage_options',
            self::SLUG_EDIT,
            [EditMemberPage::class, 'render']
        );
        add_action('load-' . $edit_hook, [EditMemberPage::class, 'init']);

        add_submenu_page(
            self::SLUG_ROOT,
            'Beállítások – VD Membership',
            'Beállítások',
            'manage_options',
            self::SLUG_SETTINGS,
            [SettingsPage::class, 'render']
        );
    }
}
