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
            [SettingsPage::class, 'render'],  // updated to MembersPage in step 9.2
            'dashicons-groups',
            30
        );

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
