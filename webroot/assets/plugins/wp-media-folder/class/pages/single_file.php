<div class="content-box content-wpmf-single-file">
    <div class="cboption">
        <p><input data-label="wpmf_option_singlefile" type="checkbox" class="cb_option" <?php if ($option_singlefile == 1) echo 'checked' ?> value="<?php echo @$option_singlefile ?>">
            <?php _e('Enable single file design', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Apply single file design with below parameters when insert file to post / page', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_singlefile" value="<?php echo $option_singlefile; ?>">
    </div>
    <hr class="wpmf_setting_line">
    <div class="wpmf_group_color">
        <div class="wpmf_group_singlefile">
            <label class="control-label" for="singlebg"><?php _e('Background color', 'wpmf') ?></label>
            <input name="wpmf_color_singlefile[bgdownloadlink]" type="text" value="<?php echo $wpmf_color_singlefile->bgdownloadlink ?>" class="inputbox input-block-level wp-color-field-bg wp-color-picker">
        </div>

        <div class="wpmf_group_singlefile">
            <label class="control-label" for="singlebg"><?php _e('Hover color', 'wpmf') ?></label>
            <input name="wpmf_color_singlefile[hvdownloadlink]" type="text" value="<?php echo $wpmf_color_singlefile->hvdownloadlink ?>" class="inputbox input-block-level wp-color-field-hv wp-color-picker">
        </div>

        <div class="wpmf_group_singlefile">
            <label class="control-label" for="singlebg"><?php _e('Font color', 'wpmf') ?></label>
            <input name="wpmf_color_singlefile[fontdownloadlink]" type="text" value="<?php echo $wpmf_color_singlefile->fontdownloadlink ?>" class="inputbox input-block-level wp-color-field-font wp-color-picker">
        </div>

        <div class="wpmf_group_singlefile">
            <label class="control-label" for="singlebg"><?php _e('Hover font color', 'wpmf') ?></label>
            <input name="wpmf_color_singlefile[hoverfontcolor]" type="text" value="<?php echo $wpmf_color_singlefile->hoverfontcolor ?>" class="inputbox input-block-level wp-color-field-hvfont wp-color-picker">
        </div>
    </div>
    
    <hr class="wpmf_setting_line">
    
    <div class="cboption">
        <h3 class="title"><?php _e('Lightbox on single image', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_lightboximage" type="checkbox" name="cb_option_lightboximage" class="cb_option" id="cb_option_lightboximage" <?php if ($option_lightboximage == 1) echo 'checked' ?> value="<?php echo @$option_lightboximage ?>">
            <?php _e('Enable the single image lightbox feature', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Add a lightbox option on each image of your WordPress content', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_lightboximage" value="<?php echo $option_lightboximage; ?>">
    </div>
</div>

<?php
wp_enqueue_style('wp-color-picker');
wp_enqueue_script('wp-color-picker');
?>