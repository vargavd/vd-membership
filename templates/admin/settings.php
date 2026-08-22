<?php
/**
 * Settings page template.
 *
 * Variables provided by SettingsPage::render():
 *   string $db_host
 *   string $db_name
 *   string $db_user
 */
if (!defined('ABSPATH')) {
    exit;
}

?>
<div class="wrap">
    <h1><?php echo esc_html__('VD Membership – Beállítások', 'vd-membership'); ?></h1>

    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <?php wp_nonce_field('vd_membership_settings_nonce'); ?>
        <input type="hidden" name="action" value="vd_membership_settings">

        <h2><?php echo esc_html__('Külső adatbázis hitelesítő adatok', 'vd-membership'); ?></h2>

        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="db_host"><?php echo esc_html__('Kiszolgáló', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text"
                           id="db_host"
                           name="db_host"
                           value="<?php echo esc_attr($db_host); ?>"
                           class="regular-text"
                           placeholder="localhost">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="db_name"><?php echo esc_html__('Adatbázis neve', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text"
                           id="db_name"
                           name="db_name"
                           value="<?php echo esc_attr($db_name); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="db_user"><?php echo esc_html__('Felhasználónév', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text"
                           id="db_user"
                           name="db_user"
                           value="<?php echo esc_attr($db_user); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="db_password"><?php echo esc_html__('Jelszó', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="password"
                           id="db_password"
                           name="db_password"
                           value=""
                           class="regular-text"
                           autocomplete="new-password">
                    <p class="description">
                        <?php echo esc_html__('Hagyd üresen, ha nem kívánod módosítani.', 'vd-membership'); ?>
                    </p>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" name="vd_action" value="save" class="button button-primary">
                <?php echo esc_html__('Mentés', 'vd-membership'); ?>
            </button>
            <button type="submit" name="vd_action" value="test_connection" class="button">
                <?php echo esc_html__('Kapcsolat tesztelése', 'vd-membership'); ?>
            </button>
        </p>
    </form>
</div>
