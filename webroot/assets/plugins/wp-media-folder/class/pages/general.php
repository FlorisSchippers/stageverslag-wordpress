<div class="content-box content-wpmf-general content-active">
    <div class="cboption">
        <h3 class="title"><?php _e('WP Media Folder on frontend', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_mediafolder" type="checkbox" name="cb_option_mediafolder" class="cb_option" id="cb_option_mediafolder" <?php if ($option_mediafolder == 1) echo 'checked' ?> value="<?php echo @$option_mediafolder ?>">
            <?php _e('Activate WP Media Folder on frontend (public side)', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Useful for frontend page builders', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_mediafolder" value="<?php echo $option_mediafolder; ?>">
    </div>
    
    <hr class="wpmf_setting_line">
    
    <div class="cboption">
        <h3 class="title"><?php _e('Number of files in folders', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_countfiles" type="checkbox" name="cb_option_countfiles" class="cb_option" id="cb_option_countfiles" <?php if ($option_countfiles == 1) echo 'checked' ?> value="<?php echo @$option_countfiles ?>">
            <?php _e('Display the media number in each folder', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Add the number of media that are placed directly in each folder', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_countfiles" value="<?php echo $option_countfiles; ?>">
    </div>
    
        
    <hr class="wpmf_setting_line">
    
    <?php if (current_user_can('manage_options')): ?>
        <div class="btnoption">
            <h3 class="title"><?php _e('Import categories', 'wpmf'); ?></h3>
            <a href="#" class="button <?php if ($btn_import_categories && $btn_import_categories == 'yes') echo 'button-primary'; ?>" id="wmpfImpoBtn"><?php _e('Import WP media categories', 'wpmf') ?></a>
            <span class="spinner" style="float: left;display:none"></span>
            <span class="wpmf_info_update"><?php _e('Settings saved.', 'wpmf') ?></span>
        </div>
        <p style="margin-left: 10px;" class="description"><?php _e('Import current media and post categories as media folders', 'wpmf'); ?></p>

        <hr class="wpmf_setting_line">
    <?php endif; ?>
    <div class="cboption">
        <h3 class="title"><?php _e('Media search', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_searchall" type="checkbox" name="cb_option_searchall" class="cb_option" id="cb_option_searchall" <?php if ($option_searchall == 1) echo 'checked' ?> value="<?php echo @$option_searchall ?>">
            <?php _e('Search through all media folders', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Search through all media or only in the current folder', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_searchall" value="<?php echo $option_searchall; ?>">
    </div>
</div>

<!--------------------------------------- Override image and filter ----------------------------------->

<div class="content-box content-wpmf-general">
    <div class="cboption">
        <h3 class="title"><?php _e('Replace file function', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_override" type="checkbox" name="cb_option_override" class="cb_option" id="cb_option_override" <?php if ($option_override == 1) echo 'checked' ?> value="<?php echo @$option_override ?>">
            <?php _e('Override file', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Possibility to replace an existing file by another one.', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_override" value="<?php echo $option_override; ?>">
    </div>

    <hr class="wpmf_setting_line">
    <div class="cboption">
        <h3 class="title"><?php _e('Duplicate file function', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_option_duplicate" type="checkbox" name="cb_option_duplicate" class="cb_option" id="cb_option_duplicate" <?php if ($option_duplicate == 1) echo 'checked' ?> value="<?php echo @$option_duplicate ?>">
            <?php _e('Duplicate file', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Add a button to duplicate a media from the media manager', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_option_duplicate" value="<?php echo $option_duplicate; ?>">
    </div>

    <hr class="wpmf_setting_line">
    <div class="cboption">
        <h3 class="title"><?php _e('Fillter and order feature', 'wpmf'); ?></h3>
        <p><input data-label="wpmf_useorder" type="checkbox" class="cb_option" <?php if (isset($useorder) && $useorder == 1) echo 'checked' ?> value="<?php echo @$useorder ?>">
            <?php _e('Enable the fillter and order feature', 'wpmf') ?>
        </p>
        <p class="description"><?php _e('Additional filters will be added in the media views.', 'wpmf'); ?></p>
        <input type="hidden" name="wpmf_useorder" value="<?php echo $useorder; ?>">
    </div>
</div>

<!--------------------------------------- End Override image and filter ----------------------------------->

<!---------------------------------------  Fillter and order ----------------------------------->

<div class="content-box wpmf-config-gallery content-wpmf-general">
    <div class="box-select">
        <div id="wpmf_fillterdimension" class="div_list">
            <ul class="wpmf_fillterdimension">
                <li class="div_list_child accordion-section control-section control-section-default open">
                    <h3 class="accordion-section-title wpmf-section-title dimension_title" data-title="filldimension" tabindex="0"><?php _e('List default fillter size', 'wpmf') ?></h3>
                    <ul class="content_list_filldimension">
                        <?php
                        if (count($a_dimensions) > 0):
                            foreach ($a_dimensions as $a_dimension):
                                ?>
                                <li class="customize-control customize-control-select item_dimension" style="display: list-item;" data-value="<?php echo $a_dimension; ?>">
                                    <input type="checkbox" name="dimension[]" value="<?php echo $a_dimension ?>" <?php if (in_array($a_dimension, $array_s_de) == true) echo 'checked' ?> >
                                    <span><?php echo $a_dimension; ?></span>
                                    <i class="zmdi zmdi-delete wpmf-delete" data-label="dimension" data-value="<?php echo $a_dimension; ?>" title="<?php _e('Remove dimension', 'wpmf'); ?>"></i>
                                    <i class="zmdi zmdi-edit wpmf-md-edit" data-label="dimension" data-value="<?php echo $a_dimension; ?>" title="<?php _e('Edit dimension', 'wpmf'); ?>"></i>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>

                        <li class="customize-control customize-control-select dimension" style="display: list-item;">
                            <div style="width: 100%;float: left;">
                                <span><?php _e('Width', 'wpmf'); ?></span>
                                <input name="wpmf_width_dimension" min="0" class="small-text wpmf_width_dimension" type="number">
                                <span><?php _e('Height', 'wpmf'); ?></span>
                                <input name="wpmf_height_dimension" min="0" class="small-text wpmf_height_dimension" type="number">
                            </div>
                            <span><?php _e('(unit : px)', 'wpmf'); ?></span>
                        </li>

                        <li style="display: list-item;margin:10px 0px 0px 0px">
                            <span name="add_dimension" id="add_dimension" class="button add_dimension"><?php _e('Add new size', 'wpmf'); ?></span>
                            <span name="edit_dimension" data-label="dimension" id="edit_dimension" class="button wpmfedit edit_dimension" style="display: none;"><?php _e('Save', 'wpmf'); ?></span>
                            <span id="can_dimension" class="button wpmf_can" data-label="dimension" style="display: none;"><?php _e('Cancel', 'wpmf'); ?></span>
                        </li>
                    </ul>
                    <p class="description"><?php _e('Image dimension filtering available in filter. Display image with a dimension and above.', 'wpmf'); ?></p>
                </li>
            </ul>
        </div>

        <div id="wpmf_fillterweights" class="div_list">
            <ul class="wpmf_fillterweight">
                <li class="div_list_child accordion-section control-section control-section-default open">
                    <h3 class="accordion-section-title wpmf-section-title sizes_title" data-title="fillweight" tabindex="0"><?php _e('List default fillter weight', 'wpmf') ?></h3>
                    <ul class="content_list_fillweight">
                        <?php
                        if (count($a_weights) > 0):
                            foreach ($a_weights as $a_weight):
                                $labels = explode('-', $a_weight[0]);
                                if ($a_weight[1] == 'kB') {
                                    $label = ($labels[0] / 1024) . ' kB-' . ($labels[1] / 1024) . ' kB';
                                } else {
                                    $label = ($labels[0] / (1024 * 1024)) . ' MB-' . ($labels[1] / (1024 * 1024)) . ' MB';
                                }
                                ?>
                                <li class="customize-control customize-control-select item_weight" style="display: list-item;" data-value="<?php echo $a_weight[0]; ?>" data-unit="<?php echo $a_weight[1]; ?>">
                                    <input type="checkbox" name="weight[]" value="<?php echo $a_weight[0] . ',' . $a_weight[1] ?>" data-unit="<?php echo $a_weight[1]; ?>" <?php if (in_array($a_weight, $array_s_we) == true) echo 'checked' ?> >
                                    <span><?php echo $label; ?></span>
                                    <i class="zmdi zmdi-delete wpmf-delete" data-label="weight" data-value="<?php echo $a_weight[0]; ?>" data-unit="<?php echo $a_weight[1]; ?>" title="<?php _e('Remove weight', 'wpmf'); ?>"></i>
                                    <i class="zmdi zmdi-edit wpmf-md-edit" data-label="weight" data-value="<?php echo $a_weight[0]; ?>" data-unit="<?php echo $a_weight[1]; ?>" title="<?php _e('Edit weight', 'wpmf'); ?>"></i>
                                </li>
                                <?php
                            endforeach;
                        endif;
                        ?>

                        <li class="customize-control customize-control-select weight" style="display: list-item;">
                            <div style="width: 100%;float: left;">
                                <span><?php _e('Min', 'wpmf'); ?></span>
                                <input name="wpmf_min_weight" min="0" class="small-text wpmf_min_weight" type="number">
                                <span><?php _e('Max', 'wpmf'); ?></span>
                                <input name="wpmf_max_weight" min="0" class="small-text wpmf_max_weight" type="number">
                            </div>
                            <span style="margin-top: 10px;float: left;"><?php _e('Unit :', 'wpmf'); ?>
                                <select class="wpmfunit" data-label="weight">
                                    <option value="kB"><?php _e('kB', 'wpmf'); ?></option>
                                    <option value="MB"><?php _e('MB', 'wpmf'); ?></option>
                                </select>
                            </span>

                        </li>

                        <li style="display: list-item;margin:10px 0px 0px 0px;float: left;">
                            <span name="add_weight" id="add_weight" class="button add_weight"><?php _e('Add weight', 'wpmf'); ?></span>
                            <span name="edit_weight" data-label="weight" id="edit_weight" class="button wpmfedit edit_weight" style="display: none;"><?php _e('Save', 'wpmf'); ?></span>
                            <span id="can_dimension" class="button wpmf_can" data-label="weight" style="display: none"><?php _e('Cancel', 'wpmf'); ?></span>
                        </li>
                    </ul>
                    <p class="description"><?php _e('Select weight range which you would like to display in media library filter', 'wpmf'); ?></p>
                </li>
            </ul>
        </div>
    </div>
</div>

<!--------------------------------------- End Fillter and order ----------------------------------->

<!--------------------------------------- End advanced params ----------------------------------->
