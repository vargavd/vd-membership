<?php
/**
 * Shared member form template (edit and new).
 *
 * Variables provided by EditMemberPage::render() / NewMemberPage::render():
 *   Member      $member            — the member to edit, or a blank Member(0) for new
 *   string      $mode              — 'edit' | 'new'
 *   string[]    $validation_errors — list of error strings; empty = no errors
 */
if (!defined('ABSPATH')) {
    exit;
}

$is_edit   = $mode === 'edit';
$page_title = $is_edit
    ? __('Tag szerkesztése', 'vd-membership')
    : __('Új tag', 'vd-membership');
?>
<div class="wrap">
    <h1><?php echo esc_html($page_title); ?></h1>
    <a href="<?php echo esc_url(admin_url('admin.php?page=' . \VDMembership\Admin\AdminMenu::SLUG_MEMBERS)); ?>">
        &larr; <?php echo esc_html__('Vissza a tagok listájához', 'vd-membership'); ?>
    </a>

    <?php if (!empty($validation_errors)): ?>
    <div class="notice notice-error" style="margin-top:1em">
        <ul style="margin:.5em 0 .5em 1.5em;list-style:disc">
            <?php foreach ($validation_errors as $err): ?>
            <li><?php echo esc_html($err); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <form method="post" style="margin-top:1.5em">
        <?php wp_nonce_field($is_edit ? 'vd_membership_edit_nonce' : 'vd_membership_new_nonce'); ?>
        <?php if ($is_edit): ?>
        <input type="hidden" name="ugyfel" value="<?php echo (int) $member->ugyfel; ?>">
        <?php endif; ?>

        <?php /* ---- Személyes adatok ---- */ ?>
        <h2 class="title"><?php echo esc_html__('Személyes adatok', 'vd-membership'); ?></h2>
        <table class="form-table" role="presentation">

            <?php if ($is_edit): ?>
            <tr>
                <th scope="row"><?php echo esc_html__('Azonosító', 'vd-membership'); ?></th>
                <td><input type="text" value="<?php echo (int) $member->ugyfel; ?>" class="small-text" readonly disabled></td>
            </tr>
            <?php endif; ?>

            <tr>
                <th scope="row">
                    <label for="ugyfel_nev"><?php echo esc_html__('Név', 'vd-membership'); ?> <span style="color:red">*</span></label>
                </th>
                <td>
                    <input type="text" id="ugyfel_nev" name="ugyfel_nev"
                           value="<?php echo esc_attr($member->ugyfel_nev ?? ''); ?>"
                           class="regular-text" required>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="lenykori"><?php echo esc_html__('Leánykori név', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="lenykori" name="lenykori"
                           value="<?php echo esc_attr($member->lenykori ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="dat_szul"><?php echo esc_html__('Születési dátum', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="date" id="dat_szul" name="dat_szul"
                           value="<?php echo esc_attr($member->dat_szul ?? ''); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="szulhely"><?php echo esc_html__('Születési hely', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="szulhely" name="szulhely"
                           value="<?php echo esc_attr($member->szulhely ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="anya"><?php echo esc_html__('Anyja neve', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="anya" name="anya"
                           value="<?php echo esc_attr($member->anya ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
        </table>

        <?php /* ---- Elérhetőség ---- */ ?>
        <h2 class="title"><?php echo esc_html__('Elérhetőség', 'vd-membership'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="cim_irsz"><?php echo esc_html__('Irányítószám', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="cim_irsz" name="cim_irsz"
                           value="<?php echo esc_attr($member->cim_irsz ?? ''); ?>"
                           class="small-text" maxlength="6">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cim_varos"><?php echo esc_html__('Város', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="cim_varos" name="cim_varos"
                           value="<?php echo esc_attr($member->cim_varos ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="cim_cim"><?php echo esc_html__('Cím', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="cim_cim" name="cim_cim"
                           value="<?php echo esc_attr($member->cim_cim ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="telefon"><?php echo esc_html__('Telefon', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="telefon" name="telefon"
                           value="<?php echo esc_attr($member->telefon ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="mobil"><?php echo esc_html__('Mobil', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="mobil" name="mobil"
                           value="<?php echo esc_attr($member->mobil ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="emil"><?php echo esc_html__('E-mail', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="email" id="emil" name="emil"
                           value="<?php echo esc_attr($member->emil ?? ''); ?>"
                           class="regular-text">
                </td>
            </tr>
        </table>

        <?php /* ---- Tagság ---- */ ?>
        <h2 class="title"><?php echo esc_html__('Tagság', 'vd-membership'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="dat_belep"><?php echo esc_html__('Belépés dátuma', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="date" id="dat_belep" name="dat_belep"
                           value="<?php echo esc_attr($member->dat_belep ?? ''); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="dij"><?php echo esc_html__('Tagdíj', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="number" id="dij" name="dij" step="any"
                           value="<?php echo $member->dij !== null ? esc_attr((string) $member->dij) : ''; ?>"
                           class="small-text">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="honap"><?php echo esc_html__('Hónap', 'vd-membership'); ?></label>
                </th>
                <td>
                    <select id="honap" name="honap">
                        <option value=""><?php echo esc_html__('— Üres —', 'vd-membership'); ?></option>
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?php echo $i; ?>" <?php selected($member->honap, $i); ?>><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="statusz"><?php echo esc_html__('Állapot', 'vd-membership'); ?></label>
                </th>
                <td>
                    <select id="statusz" name="statusz">
                        <option value="1" <?php selected($member->statusz, 1); ?>><?php echo esc_html__('Aktív', 'vd-membership'); ?></option>
                        <option value="0" <?php selected($member->statusz, 0); ?>><?php echo esc_html__('Inaktív', 'vd-membership'); ?></option>
                    </select>
                </td>
            </tr>
        </table>

        <?php /* ---- Figyelmeztetés ---- */ ?>
        <h2 class="title"><?php echo esc_html__('Figyelmeztetés', 'vd-membership'); ?></h2>
        <table class="form-table" role="presentation">
            <?php if ($is_edit): ?>
            <tr>
                <th scope="row"><?php echo esc_html__('Figyelmeztet', 'vd-membership'); ?></th>
                <td>
                    <input type="text" value="<?php echo esc_attr($member->figyelmeztet ?? ''); ?>"
                           class="small-text" readonly disabled>
                    <p class="description"><?php echo esc_html__('Rendszer által kezelt mező.', 'vd-membership'); ?></p>
                </td>
            </tr>
            <?php endif; ?>
            <tr>
                <th scope="row">
                    <label for="figy_dat"><?php echo esc_html__('Figyelmeztetés dátuma', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="date" id="figy_dat" name="figy_dat"
                           value="<?php echo esc_attr($member->figy_dat ?? ''); ?>">
                </td>
            </tr>
            <tr>
                <th scope="row">
                    <label for="figy_szoveg"><?php echo esc_html__('Figyelmeztetés szövege', 'vd-membership'); ?></label>
                </th>
                <td>
                    <input type="text" id="figy_szoveg" name="figy_szoveg"
                           value="<?php echo esc_attr($member->figy_szoveg ?? ''); ?>"
                           class="large-text">
                </td>
            </tr>
        </table>

        <?php /* ---- Megjegyzés ---- */ ?>
        <h2 class="title"><?php echo esc_html__('Megjegyzés', 'vd-membership'); ?></h2>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row">
                    <label for="megjegyzes"><?php echo esc_html__('Megjegyzés', 'vd-membership'); ?></label>
                </th>
                <td>
                    <textarea id="megjegyzes" name="megjegyzes" class="large-text" rows="5"><?php echo esc_textarea($member->megjegyzes ?? ''); ?></textarea>
                </td>
            </tr>
        </table>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php echo esc_html($is_edit ? __('Mentés', 'vd-membership') : __('Létrehozás', 'vd-membership')); ?>
            </button>
            <a href="<?php echo esc_url(admin_url('admin.php?page=' . \VDMembership\Admin\AdminMenu::SLUG_MEMBERS)); ?>"
               class="button">
                <?php echo esc_html__('Mégse', 'vd-membership'); ?>
            </a>
        </p>
    </form>
</div>
