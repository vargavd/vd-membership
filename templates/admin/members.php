<?php
/**
 * Members list template.
 *
 * Variables provided by MembersPage::render():
 *   Member[] $members
 */
if (!defined('ABSPATH')) {
    exit;
}
?>
<div class="wrap">
    <h1 class="wp-heading-inline"><?php echo esc_html__('Tagok', 'vd-membership'); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=' . \VDMembership\Admin\AdminMenu::SLUG_NEW)); ?>"
       class="page-title-action">
        <?php echo esc_html__('Új tag', 'vd-membership'); ?>
    </a>
    <hr class="wp-header-end">

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" style="width:4em"><?php echo esc_html__('ID', 'vd-membership'); ?></th>
                <th scope="col"><?php echo "ugyfel_nev"; ?></th>
                <th scope="col"><?php echo "dat_szul"; ?></th>
                <th scope="col"><?php echo "cim_varos"; ?></th>
                <th scope="col"><?php echo "telefon"; ?></th>
                <th scope="col"><?php echo "mobil"; ?></th>
                <th scope="col"><?php echo "dat_belep"; ?></th>
                <th scope="col" style="width:6em"><?php echo esc_html__('Állapot', 'vd-membership'); ?></th>
                <th scope="col" style="width:12em"><?php echo esc_html__('Műveletek', 'vd-membership'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
            <tr>
                <td colspan="8"><?php echo esc_html__('Nincsenek tagok a rendszerben.', 'vd-membership'); ?></td>
            </tr>
            <?php else: ?>
            <?php foreach ($members as $member): ?>
            <tr>
                <td><?php echo esc_html((string) $member->ugyfel); ?></td>
                <td><?php echo esc_html($member->ugyfel_nev ?? '—'); ?></td>
                <td><?php echo esc_html($member->dat_szul ?? '—'); ?></td>
                <td><?php echo esc_html($member->cim_varos ?? '—'); ?></td>
                <td><?php echo esc_html($member->telefon ?? '—'); ?></td>
                <td><?php echo esc_html($member->mobil ?? '—'); ?></td>
                <td><?php echo esc_html($member->dat_belep ?? '—'); ?></td>
                <td>
                    <?php if ($member->statusz == 1): ?>
                        <strong><?php echo esc_html__('Aktív', 'vd-membership'); ?></strong>
                    <?php else: ?>
                        <span style="color:#888"><?php echo esc_html__('Inaktív', 'vd-membership'); ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?php echo esc_url(admin_url('admin.php?page=' . \VDMembership\Admin\AdminMenu::SLUG_EDIT . '&ugyfel=' . (int) $member->ugyfel)); ?>">
                        <?php echo esc_html__('Szerkesztés', 'vd-membership'); ?>
                    </a>
                    <?php if ($member->statusz == 1): ?>
                    &nbsp;|&nbsp;
                    <form method="post"
                          action="<?php echo esc_url(admin_url('admin-post.php')); ?>"
                          style="display:inline">
                        <?php wp_nonce_field('vd_membership_members_nonce'); ?>
                        <input type="hidden" name="action"    value="vd_membership_members">
                        <input type="hidden" name="vd_action" value="delete">
                        <input type="hidden" name="ugyfel"    value="<?php echo (int) $member->ugyfel; ?>">
                        <button type="submit"
                                class="button-link delete"
                                onclick="return confirm('<?php echo esc_js(__('Biztosan inaktiválod ezt a tagot?', 'vd-membership')); ?>')">
                            <?php echo esc_html__('Inaktiválás', 'vd-membership'); ?>
                        </button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
