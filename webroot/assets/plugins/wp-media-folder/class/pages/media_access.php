<div class="content-box content-wpmf-media-access">
    <div class="cboption">
        <h3 class="title"><?php _e('Access management', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_active_media" type="checkbox" name="cb_option_active_media" class="cb_option" id="cb_option_active_media" <?php if ($wpmf_active_media == 1) echo 'checked' ?> value="<?php echo @$wpmf_active_media ?>">
            <?php _e('Display only media by User/User role (admins will always have access to all/own media)', 'wpmf'); ?>
        </p>
        <p>
            <select name="wpmf_create_folder">
                <option <?php selected($wpmf_create_folder, 'user'); ?> value="user"><?php _e('By user', 'wpmf') ?></option>
                <option <?php selected($wpmf_create_folder, 'role'); ?> value="role"><?php _e('By role', 'wpmf') ?></option>
            </select>
            <?php _e('Auto create one folder per Editor or User role', 'wpmf'); ?>
        </p>
        <p class="description"><?php _e('This option gives the possibility to activate a media access limitation per user or per user role', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_active_media" value="<?php echo $wpmf_active_media; ?>">
    </div>
</div>
