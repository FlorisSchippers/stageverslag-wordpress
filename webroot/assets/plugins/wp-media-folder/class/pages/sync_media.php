<div class="content-box content-wpmf-media-sync">
    <div class="btnoption">
        <h3 class="title"><?php _e('Sync WordPress media with server folders', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_sync_media" type="checkbox" name="cb_option_sync_media" class="cb_option" id="cb_option_sync_media" <?php if ($option_sync_media == 1) echo 'checked' ?> value="<?php echo @$option_sync_media ?>">
            <?php _e('Activate the sync', 'wpmf') ?>
        </p>
        <input type="hidden" name="wpmf_option_sync_media" value="<?php echo $option_sync_media; ?>">
        <div>
            <lable><?php _e('Sync delay', 'wpmf') ?></lable>
            <input name="input_time_sync" class="input_time_sync" value="<?php echo $time_sync ?>">
            <lable><?php _e('minutes', 'wpmf') ?></lable>
        </div>
        <hr>
        <div>
            <div class="wrap_dir_name_ftp">
                <div id="wpmf_foldertree_sync"></div>

            </div>

            <div class="wrap_dir_name_categories">
                <div id="wpmf_foldertree_categories"></div>

            </div>
        </div>
        <div class="time_sync" style="margin-top: 10px;">
            <div class="input_dir">
                <input type="text" name="dir_name_ftp" class="input_sync dir_name_ftp" readonly value="" >
                <input type="text" name="dir_name_categories" class="input_sync dir_name_categories" readonly data-id_category="0" value="">
            </div>

            <input type="button" class="button btn_addsync_media" value="<?php _e('Add', 'wpmf') ?>" >
            <input type="button" class="button btn_deletesync_media" value="<?php _e('Delete selected', 'wpmf') ?>" >
        </div>

        <table class="wp-list-table widefat striped wp-list-table-sync">
            <tr>
                <td style="width: 1%"><input id="cb-select-all" type="checkbox"></td>
                <td style="width: 40%"><?php _e('Directory FTP', 'wpmf') ?></td>
                <td style="width: 40%"><?php _e('Folder category', 'wpmf') ?></td>
            </tr>
            <?php if (!empty($wpmf_list_sync_media)): ?>
                <?php foreach ($wpmf_list_sync_media as $k => $v): ?>
                    <tr data-id="<?php echo $k ?>">
                        <td><input id="cb-select-<?php echo $k ?>" type="checkbox" name="post[]" value="<?php echo $k ?>"></td>
                        <td><?php echo $v['folder_ftp'] ?></td>
                        <td><?php echo @$this->breadcrumb_category[$k] ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </table>
    </div>
</div>
