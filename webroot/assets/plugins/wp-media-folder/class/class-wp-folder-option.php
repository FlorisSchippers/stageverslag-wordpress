<?php
require_once( WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/class-media-folder.php' );
if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
    require_once( WP_PLUGIN_DIR . '/wp-media-folder-addon/class/wpmfGoogle.php' );
    require_once( WP_PLUGIN_DIR . '/wp-media-folder-addon/class/wpmfDropbox.php' );
    require_once( WP_PLUGIN_DIR . '/wp-media-folder-addon/class/wpmfHelper.php' );
}
class Media_Folder_Option {

    public $breadcrumb_category = array();
    public $result_gennerate_thumb = '';
    public $type_import = array('jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tiff', 'tif', 'ico', '7z', 'bz2', 'gz', 'rar', 'tgz', 'zip', 'csv', 'doc', 'docx', 'ods', 'odt', 'pdf', 'pps', 'ppt', 'pptx', 'rtf', 'txt', 'xls', 'xlsx', 'psd', 'tif', 'tiff', 'mid', 'mp3', 'mp4', 'ogg', 'wma', '3gp', 'avi', 'flv', 'm4v', 'mkv', 'mov', 'mpeg', 'mpg', 'swf', 'vob', 'wmv');
    public $default_time_sync = 60;

    function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_enqueue_scripts', array($this, 'loadAdminScripts'));
        add_action('admin_enqueue_scripts', array($this, 'wpmf_heartbeat_enqueue'));
        add_action('admin_head', array($this, 'wpmf_admin_head'));
        add_action('admin_footer', array($this, 'wpmf_foldertree'));
        add_filter('heartbeat_received', array($this, 'wpmf_heartbeat_received'), 10, 2);
        add_action('admin_init', array($this, 'add_settings_option'));
        add_action('wp_ajax_update_opt', array($this, 'update_opt'));

        if (defined('NGG_PLUGIN_VERSION')) {
            if (!get_option('wpmf_import_nextgen_gallery', false)) {
                add_action('admin_notices', array($this, 'wpmf_whow_notice'), 3);
            }
        }

        add_action('wp_ajax_import_gallery', array($this, 'import_gallery'));
        add_action('wp_ajax_import_categories', array($this, 'wpmf_impo_taxo'));
        add_action('wp_ajax_wpmf_add_dimension', array($this, 'add_dimension'));
        add_action('wp_ajax_wpmf_remove_dimension', array($this, 'remove_dimension'));
        add_action('wp_ajax_wpmf_add_weight', array($this, 'add_weight'));
        add_action('wp_ajax_wpmf_remove_weight', array($this, 'remove_weight'));
        add_action('wp_ajax_wpmf_edit', array($this, 'edit'));
        add_action('wp_ajax_wpmf_get_folder', array($this, 'wpmf_get_folder'));
        add_action('wp_ajax_wpmf_import_folder', array($this, 'wpmf_import_folder'));
        add_action('wp_ajax_wpmfjao_checked', array($this, 'wpmfjao_checked'));
        add_action('wp_ajax_wpmf_add_syncmedia', array($this, 'wpmf_add_syncmedia'));
        add_action('wp_ajax_wpmf_remove_syncmedia', array($this, 'wpmf_remove_syncmedia'));
        add_action('wp_ajax_wpmf_regeneratethumbnail', array($this, 'wpmf_regeneratethumbnail'));
        add_action('wp_ajax_wpmf_syncmedia', array($this, 'wpmf_syncmedia'));
        add_action('wp_ajax_wpmf_import_size_filetype', array($this, 'wpmf_import_size_filetype'));
    }


    function wpmf_import_size_filetype(){
        global $wpdb;
        $attachments = $wpdb->get_results("SELECT ID FROM " . $wpdb->prefix . "posts as posts
               WHERE   posts.post_type     = 'attachment' AND NOT EXISTS ( SELECT meta_key FROM " . $wpdb->prefix . "postmeta as post_meta WHERE meta_key='wpmf_size' AND post_meta.post_id = posts.ID)");
        $i = 0;
        foreach ($attachments as $attachment) {
            if($i < 50){
                $wpmf_size_filetype = wpmf_get_sizefiletype($attachment->ID);
                $size = $wpmf_size_filetype['size'];
                $ext = $wpmf_size_filetype['ext'];
                if (!get_post_meta($attachment->ID, 'wpmf_size')) {
                    update_post_meta($attachment->ID , 'wpmf_size' ,$size);
                }

                if (!get_post_meta($attachment->ID, 'wpmf_filetype')) {
                    update_post_meta($attachment->ID , 'wpmf_filetype' ,$ext);
                }
            }else{
                wp_send_json(false);
            }
            $i++;
        }
        update_option('_wpmf_import_size_notice_flag', 'yes');
        wp_send_json(true);
    }

    public function wpmf_admin_head() {
        if (isset($_SESSION['wpmf_dir_checked'])) {
            unset($_SESSION['wpmf_dir_checked']);
        }
    }
    
    /* Display folder tree for FTp import function and Sync function */
    public function wpmf_foldertree() {
        global $current_screen;
        if ($current_screen->base == 'settings_page_option-folder') {
            $include_folders = isset($_SESSION['wpmf_dir_checked']) ? $_SESSION['wpmf_dir_checked'] : '';
            $selected_folders = explode(',', $include_folders);
            ?>
            <script>
                var curFolders = <?php echo json_encode($selected_folders); ?>;
                jQuery(document).ready(function ($) {
                    var sdir = '/';
                    $('#wpmf_foldertree_categories').jaofiletreecategories({
                        script: ajaxurl,
                        usecheckboxes: false,
                        showroot: '<?php _e('Media Library', 'wpmf'); ?>'
                    });

                    $('#wpmf_foldertree_sync').jaofiletreesync({
                        script: ajaxurl,
                        usecheckboxes: false,
                        showroot: '/'
                    });

                    $('#wpmf_foldertree').jaofiletreeftp({
                        script: ajaxurl,
                        usecheckboxes: true,
                        showroot: '/',
                        oncheck: function (elem, checked, type, file) {
                            var dir = file;
                            if (file.substring(file.length - 1) == sdir) {
                                file = file.substring(0, file.length - 1);
                            }
                            if (file.substring(0, 1) == sdir) {
                                file = file.substring(1, file.length);
                            }
                            if (checked) {
                                if (file != "" && curFolders.indexOf(file) == -1) {
                                    curFolders.push(file);
                                }
                            } else {

                                if (file != "" && !$(elem).next().hasClass('pchecked')) {
                                    temp = [];
                                    for (i = 0; i < curFolders.length; i++) {
                                        curDir = curFolders[i];
                                        if (curDir.indexOf(file) !== 0) {
                                            temp.push(curDir);
                                        }
                                    }
                                    curFolders = temp;
                                } else {
                                    var index = curFolders.indexOf(file);
                                    if (index > -1) {
                                        curFolders.splice(index, 1);
                                    }
                                }
                            }

                        }
                    });

                    jQuery('#wpmf_foldertree').bind('afteropen', function () {
                        jQuery(jQuery('#wpmf_foldertree').jaofiletreeftp('getchecked')).each(function () {
                            curDir = this.file;
                            if (curDir.substring(curDir.length - 1) == sdir) {
                                curDir = curDir.substring(0, curDir.length - 1);
                            }
                            if (curDir.substring(0, 1) == sdir) {
                                curDir = curDir.substring(1, curDir.length);
                            }
                            if (curFolders.indexOf(curDir) == -1) {
                                curFolders.push(curDir);
                            }
                        })
                        spanCheckInit();

                    })

                    spanCheckInit = function () {
                        $("span.check").unbind('click');
                        $("span.check").bind('click', function () {
                            $(this).removeClass('pchecked');
                            $(this).toggleClass('checked');
                            if ($(this).hasClass('checked')) {
                                $(this).prev().prop('checked', true).trigger('change');
                                ;
                            } else {
                                $(this).prev().prop('checked', false).trigger('change');
                                ;
                            }
                            setParentState(this);
                            setChildrenState(this);
                        });
                    }

                    setParentState = function (obj) {
                        var liObj = $(obj).parent().parent();
                        var noCheck = 0, noUncheck = 0, totalEl = 0;
                        liObj.find('li span.check').each(function () {

                            if ($(this).hasClass('checked')) {
                                noCheck++;
                            } else {
                                noUncheck++;
                            }
                            totalEl++;
                        })

                        if (totalEl == noCheck) {
                            liObj.parent().children('span.check').removeClass('pchecked').addClass('checked');
                            liObj.parent().children('input[type="checkbox"]').prop('checked', true).trigger('change');
                        } else if (totalEl == noUncheck) {
                            liObj.parent().children('span.check').removeClass('pchecked').removeClass('checked');
                            liObj.parent().children('input[type="checkbox"]').prop('checked', false).trigger('change');
                        } else {
                            liObj.parent().children('span.check').removeClass('checked').addClass('pchecked');
                            liObj.parent().children('input[type="checkbox"]').prop('checked', false).trigger('change');
                        }

                        if (liObj.parent().children('span.check').length > 0) {
                            setParentState(liObj.parent().children('span.check'));
                        }
                    }

                    setChildrenState = function (obj) {
                        if ($(obj).hasClass('checked')) {
                            $(obj).parent().find('li span.check').removeClass('pchecked').addClass("checked");
                            $(obj).parent().find('li input[type="checkbox"]').prop('checked', true).trigger('change');
                        } else {
                            $(obj).parent().find('li span.check').removeClass("checked");
                            $(obj).parent().find('li input[type="checkbox"]').prop('checked', false).trigger('change');
                        }

                    }
                })
            </script>   
            <?php
        }
    }

    /* Ajax checked folder tree ( tab FTP import ) */
    public function wpmfjao_checked() {
        if (isset($_POST['dir_checked'])) {
            $_SESSION['wpmf_dir_checked'] = $_POST['dir_checked'];
            wp_send_json($_SESSION['wpmf_dir_checked']);
        }
    }
    
    /* Insert a attachment to database */
    public function wpmf_insert_attachment_metadata($upload_path, $upload_url, $file, $content, $mime_type, $ext, $term_id) {
        remove_filter('wp_generate_attachment_metadata', array($GLOBALS['wp_media_folder'], 'wpmf_after_upload'));
        $list_imported = get_option('wpms_list_imported');
        if(is_array($list_imported) && !in_array($term_id.'_'.$file, $list_imported) || empty($list_imported)){
            if(!empty($list_imported) && is_array($list_imported)){
                $list_imported[] = $term_id.'_'.$file;
            }else{
                $list_imported = array($term_id.'_'.$file);
            }
            $file = wp_unique_filename($upload_path, $file);
            $upload = file_put_contents($upload_path . '/' . $file, $content);
            if ($upload) {
                $attachment = array(
                    'guid' => $upload_url . '/' . $file,
                    'post_mime_type' => $mime_type,
                    'post_title' => str_replace('.' . $ext, '', $file),
                    'post_status' => 'inherit'
                );

                $image_path = $upload_path . '/' . $file;
                // Insert attachment
                $attach_id = wp_insert_attachment($attachment, $image_path);
                $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
                wp_update_attachment_metadata($attach_id, $attach_data);
                // set attachment to term
                wp_set_object_terms((int) $attach_id, (int) $term_id, WPMF_TAXO, false);
                update_option('wpms_list_imported', $list_imported);
            }
            return true;
        }
        return false;
    }
    
    /* Scan folder to insert term and attachment */
    public function add_scandir_folder($dir, $folder_name, $parent, $precent) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT $wpdb->terms.term_id FROM $wpdb->terms,$wpdb->term_taxonomy WHERE taxonomy=%s AND name=%s AND parent=$parent AND $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id", array(WPMF_TAXO, $folder_name));
        $term_id = $wpdb->get_results($sql);
        $i = 0;
        
        if (empty($term_id)) {
            $inserted = wp_insert_term($folder_name, WPMF_TAXO, array('parent' => $parent, 'slug' => sanitize_title($folder_name) . WPMF_TAXO));
            $term_id_insert = $inserted['term_id'];
        } else {
            $term_id_insert = $term_id[0]->term_id;
        }
        //14 Fire And Flame 120x120x3cm --- 800,- Startpreis -- Schätzpreis 2100,--
        // List files and directories inside $dir path
        $files = scandir($dir); 
        $files = array_diff($files, array('..', '.'));
        if (count($files) > 0) {
            $info = pathinfo($dir);
            if (empty($info['extension'])) {
                // loop list files and directories
                foreach ($files as $file) {
                    
                    if ($i >= 3) {
                        wp_send_json(array('status' => 'error time', 'precent' => $precent)); // run again ajax
                    } else {
                        if (!is_file($dir . '/' . $file)) { // is directory
                            $this->add_scandir_folder($dir . '/' . $file, str_replace('  ', ' ', $file), $term_id_insert, $precent);
                        } else {
                            // is file
                            $upload_dir = wp_upload_dir();
                            $info_file = wp_check_filetype($dir . '/' . $file);
                            if (!empty($info_file) && !empty($info_file['ext']) && in_array(strtolower($info_file['ext']), $this->type_import)) {
                                $content = @file_get_contents($dir . '/' . $file);
                                $file = sanitize_file_name($file);
                                // check file exist , if not exist then insert file
                                $check_exist = $this->wpmf_check_exist_post('/' . $file, $term_id_insert);
                                if ($check_exist == 0) {
                                    $check = $this->wpmf_insert_attachment_metadata($upload_dir['path'], $upload_dir['url'], $file, $content, $info_file['type'], $info_file['ext'], $term_id_insert);
                                    if($check) $i++;
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    /* Ajax add a row to lists sync media */
    public function wpmf_add_syncmedia() {
        if (isset($_POST['folder_category']) && isset($_POST['folder_ftp'])) {
            $folder_ftp = str_replace('\\', '/', stripcslashes($_POST['folder_ftp']));
            $folder_category = $_POST['folder_category'];
            
            $lists = get_option('wpmf_list_sync_media');
            if (is_array($lists) && !empty($lists)) {
                $lists[$folder_category] = array('folder_ftp' => $folder_ftp);
            } else {
                $lists = array();
                $lists[$folder_category] = array('folder_ftp' => $folder_ftp);
            }

            update_option('wpmf_list_sync_media', $lists);
            wp_send_json(array('folder_category' => $folder_category, 'folder_ftp' => $folder_ftp));
        }
    }

    /* Ajax remove a row to lists sync media */
    public function wpmf_remove_syncmedia() {
        $lists = get_option('wpmf_list_sync_media');

        if (isset($_POST['key']) && $_POST['key'] != '') {
            foreach (explode(',', $_POST['key']) as $key) {
                if (isset($lists[$key]))
                    unset($lists[$key]);
            }
            update_option('wpmf_list_sync_media', $lists);
            wp_send_json(explode(',', $_POST['key']));
        }
        wp_send_json(false);
    }
    
    /* This function do import from FTP to media library */
    public function wpmf_import_folder() {
        if (current_user_can('install_plugins')) {
            if (isset($_POST['wpmf_list_import']) && $_POST['wpmf_list_import'] != '') {
                $lists = explode(',', $_POST['wpmf_list_import']);
                $i = 0;
                // get count files and directories in folder
                foreach ($lists as $list) {
                    $root = str_replace('/wp-content', '', WP_CONTENT_DIR) . $list;
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root)) as $filename) {
                        $info_file = wp_check_filetype((string) $filename);
                        if (!is_file((string) $filename)) {
                            $i++;
                        } else {
                            if (!empty($info_file['ext']) && in_array(strtolower($info_file['ext']), $this->type_import)) {
                                $i++;
                            }
                        }
                    }
                }

                $precent = (100 * 3) / $i;
                foreach ($lists as $list) {

                    if ($list != '/') {
                        $root = str_replace('/wp-content', '', WP_CONTENT_DIR) . $list;
                        $info = pathinfo($list);
                        $filename = $info['filename'];
                        $parent = 0;
                        $this->add_scandir_folder($root, $filename, $parent, $precent);
                    }
                }
            }
        }
    }
    
    /* This function do validate path */
    public function wpmf_validate_path($path) {
        return rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $path), '/');
    }
    
    /* get term to display folder tree */
    public function wpmf_get_folder() {
        $uploads_dir = wp_upload_dir();
        $uploads_dir_path = $uploads_dir['path'];
        $include_folders = isset($_SESSION['wpmf_dir_checked']) ? $_SESSION['wpmf_dir_checked'] : '';
        $selected_folders = explode(',', $include_folders);
        $path = $this->wpmf_validate_path(ABSPATH);
        $dir = $_REQUEST['dir'];

        $return = $dirs = array();
        if (@file_exists($path . $dir)) {
            $files = scandir($path . $dir);
            $files = array_diff($files, array('..', '.'));
            natcasesort($files);
            if (count($files) > 0) {
                $baseDir = ltrim(rtrim(str_replace(DIRECTORY_SEPARATOR, '/', $dir), '/'), '/');
                if ($baseDir != '')
                    $baseDir .= '/';
                
                foreach ($files as $file) {
                    if (@file_exists($path . $dir . $file) && is_dir($path . $dir . $file) && ($path . $dir . $file != $this->wpmf_validate_path($uploads_dir_path))) {
                        if (in_array($baseDir . $file, $selected_folders)) {
                            
                            $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file, 'checked' => true);
                        } else {
                            $hasSubFolderSelected = false;
                            foreach ($selected_folders as $selected_folder) {
                                if (strpos($selected_folder, $baseDir . $file) === 1) {
                                    $hasSubFolderSelected = true;
                                }
                            }

                            if ($hasSubFolderSelected) {
                                $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file, 'pchecked' => true);
                            } else {
                                $dirs[] = array('type' => 'dir', 'dir' => $dir, 'file' => $file);
                            }
                        }
                    }
                }
                $return = $dirs;
            }
        }
        wp_send_json($return);
    }
    
    /* add default settings option */
    public function add_settings_option() {
        if (!get_option('wpmf_gallery_image_size_value', false)) {
            add_option('wpmf_gallery_image_size_value', '["thumbnail","medium","large","full"]');
        }
        if (!get_option('wpmf_padding_masonry', false)) {
            add_option('wpmf_padding_masonry', 5);
        }

        if (!get_option('wpmf_padding_portfolio', false)) {
            add_option('wpmf_padding_portfolio', 10);
        }

        if (!get_option('wpmf_usegellery', false)) {
            add_option('wpmf_usegellery', 1);
        }

        if (!get_option('wpmf_useorder', false)) {
            add_option('wpmf_useorder', 1, '', 'yes');
        }

        if (!get_option('wpmf_create_folder', false)) {
            add_option('wpmf_create_folder', 'role', '', 'yes');
        }

        if (!get_option('wpmf_option_override', false)) {
            add_option('wpmf_option_override', 0, '', 'yes');
        }

        if (!get_option('wpmf_option_duplicate', false)) {
            add_option('wpmf_option_duplicate', 0, '', 'yes');
        }

        if (!get_option('wpmf_active_media', false)) {
            add_option('wpmf_active_media', 0, '', 'yes');
        }
        
        // insert term when user login and enable option 'Display only media by User/User'
        if (get_option('wpmf_active_media') == 1) {
            global $current_user;
            $user_roles = $current_user->roles;
            $role = array_shift($user_roles);
            $wpmf_create_folder = get_option('wpmf_create_folder');
            if ($role != 'administrator' && current_user_can('upload_files')) {
                if ($wpmf_create_folder == 'user') {
                    $slug = sanitize_title($current_user->data->user_login) . '-wpmf';
                    $inserted = wp_insert_term($current_user->data->user_login, WPMF_TAXO, array('parent' => 0, 'slug' => $slug));
                    if (!is_wp_error($inserted)) {
                        $updateted = wp_update_term($inserted['term_id'], WPMF_TAXO, array('term_group' => $current_user->data->ID));
                    }
                } elseif ($wpmf_create_folder == 'role') {
                    $slug = sanitize_title($role) . '-wpmf-role';
                    $inserted = wp_insert_term($role, WPMF_TAXO, array('parent' => 0, 'slug' => $slug));
                }
            }
        }

        if (!get_option('wpmf_folder_option2', false)) {
            add_option('wpmf_folder_option2', 1, '', 'yes');
        }

        if (!get_option('wpmf_option_searchall', false)) {
            add_option('wpmf_option_searchall', 0, '', 'yes');
        }

        if (!get_option('wpmf_usegellery_lightbox', false)) {
            add_option('wpmf_usegellery_lightbox', 1, '', 'yes');
        }

        if (!get_option('wpmf_media_rename', false)) {
            add_option('wpmf_media_rename', 0, '', 'yes');
        }

        if (!get_option('wpmf_patern_rename', false)) {
            add_option('wpmf_patern_rename', '{sitename} - {foldername} - #', '', 'yes');
        }

        if (!get_option('wpmf_rename_number', false)) {
            add_option('wpmf_rename_number', 0, '', 'yes');
        }

        if (!get_option('wpmf_option_media_remove', false)) {
            add_option('wpmf_option_media_remove', 0, '', 'yes');
        }

        $dimensions = array('400x300', '640x480', '800x600', '1024x768', '1600x1200');
        $dimensions_string = json_encode($dimensions);
        if (!get_option('wpmf_default_dimension', false)) {
            add_option('wpmf_default_dimension', $dimensions_string, '', 'yes');
        }

        if (!get_option('wpmf_selected_dimension', false)) {
            add_option('wpmf_selected_dimension', $dimensions_string, '', 'yes');
        }

        $weights = array(array('0-61440', 'kB'), array('61440-122880', 'kB'), array('122880-184320', 'kB'), array('184320-245760', 'kB'), array('245760-307200', 'kB'));
        $weight_string = json_encode($weights);
        if (!get_option('wpmf_weight_default', false)) {
            add_option('wpmf_weight_default', $weight_string, '', 'yes');
        }

        if (!get_option('wpmf_weight_selected', false)) {
            add_option('wpmf_weight_selected', $weight_string, '', 'yes');
        }

        $wpmf_color_singlefile = array('bgdownloadlink' => '#444444', 'hvdownloadlink' => '#888888', 'fontdownloadlink' => '#ffffff', 'hoverfontcolor' => '#ffffff');
        if (!get_option('wpmf_color_singlefile', false)) {
            add_option('wpmf_color_singlefile', json_encode($wpmf_color_singlefile), '', 'yes');
        }

        if (!get_option('wpmf_option_singlefile', false)) {
            add_option('wpmf_option_singlefile', 0, '', 'yes');
        }

        if (!get_option('wpmf_option_sync_media', false)) {
            add_option('wpmf_option_sync_media', 0, '', 'yes');
        }

        if (!get_option('wpmf_list_sync_media', false)) {
            add_option('wpmf_list_sync_media', array(), '', 'yes');
        }

        if (!get_option('wpmf_time_sync', false)) {
            add_option('wpmf_time_sync', $this->default_time_sync, '', 'yes');
        }

        if (!get_option('wpmf_lastRun_sync', false)) {
            add_option('wpmf_lastRun_sync', time(), '', 'yes');
        }

        if (!get_option('wpmf_slider_animation', false)) {
            add_option('wpmf_slider_animation', 'slide', '', 'yes');
        }
        
        if (!get_option('wpmf_option_mediafolder', false)) {
            add_option('wpmf_option_mediafolder', 0, '', 'yes');
        }
        
        if (!get_option('wpmf_option_countfiles', false)) {
            add_option('wpmf_option_countfiles', 0, '', 'yes');
        }
        
        if (!get_option('wpmf_option_lightboximage', false)) {
            add_option('wpmf_option_lightboximage', 0, '', 'yes');
        }
    }
    
    /* includes styles and some scripts */
    public function loadAdminScripts() {
        global $current_screen;
        if (!empty($current_screen->base) && $current_screen->base == 'settings_page_option-folder') {
            wp_enqueue_script('wpmf-script-option', plugins_url('/assets/js/script-option.js', dirname(__FILE__)), array(), WPMF_VERSION);
            wp_localize_script('wpmf-script-option', 'wpmflangoption', $this->wpmf_localize_script());
            wp_enqueue_script('wpmf-folder-tree-sync', plugins_url('/assets/js/sync_media/folder_tree_sync.js', dirname(__FILE__)), array(), WPMF_VERSION);
            wp_enqueue_script('wpmf-folder-tree-categories', plugins_url('/assets/js/sync_media/folder_tree_categories.js', dirname(__FILE__)), array(), WPMF_VERSION);
            wp_enqueue_script('wpmf-general-thumb', plugins_url('/assets/js/regenerate_thumbnails.js', dirname(__FILE__)), array(), WPMF_VERSION);
            wp_enqueue_style('wpmf-setting-style', plugins_url('/assets/css/setting_style.css', dirname(__FILE__)), array(), WPMF_VERSION);
            wp_enqueue_style('wpmf-material-design-iconic-font.min', plugins_url('/assets/css/material-design-iconic-font.min.css', dirname(__FILE__)), array(), WPMF_VERSION);
        }
    }
    
    /* includes a script heartbeat */
    function wpmf_heartbeat_enqueue($hook_suffix) {
        wp_enqueue_script('heartbeat');
        add_action('admin_print_footer_scripts', array($this, 'wpmf_heartbeat_footer_js'), 20);
    }

    // Inject our JS into the admin footer
    function wpmf_heartbeat_footer_js() {
        global $pagenow;
        ?>
        <script>
                    (function ($) {
                        wpmfajaxsyn = function (curent) {
                            $.ajax({
                                type: "POST",
                                url: ajaxurl,
                                dataType: 'json',
                                data: {
                                    action: "wpmf_syncmedia",
                                    curent: curent
                                },
                                success: function (response) {
                                    if (response.status == 'error_time') {
                                        wpmfajaxsyn(curent);
                                    }
                                }
                            });
                        }
                        // Hook into the heartbeat-send
                        $(document).on('heartbeat-send', function (e, data) {
                            data['wpmf_heartbeat'] = 'wpmf_queue_process';
                        });

                        $(document).on('heartbeat-tick', function (e, data) {
                            // Only proceed if our EDD data is present
                            if (!data['wpmf_limit'])
                                return;

                            $.each(data['wpmf_limit'], function (i, v) {
                                wpmfajaxsyn(v);
                            });


                        });
                    }(jQuery));
        </script>
        <?php
    }

    public function wpmf_get_term_insert($folder_name, $parent) {
        if ($folder_name == '')
            return 0;
        
        global $wpdb;
        $sql = $wpdb->prepare("SELECT $wpdb->terms.term_id FROM $wpdb->terms,$wpdb->term_taxonomy WHERE taxonomy=%s AND name=%s AND parent=$parent AND $wpdb->terms.term_id=$wpdb->term_taxonomy.term_id", array(WPMF_TAXO, $folder_name));
        $term_id = $wpdb->get_results($sql);
        if (empty($term_id)) {
            $inserted = wp_insert_term($folder_name, WPMF_TAXO, array('parent' => $parent, 'slug' => sanitize_title($folder_name) . WPMF_TAXO));
            $term_id_insert = $inserted['term_id'];
        } else {
            $term_id_insert = $term_id[0]->term_id;
        }

        return $term_id_insert;
    }
    
    /* ajax sync from FTP to media library */
    public function wpmf_syncmedia() {
        $sync = get_option('wpmf_option_sync_media');
        if (empty($sync))
            wp_send_json(array('status' => false));
        $lists = get_option('wpmf_list_sync_media');
        if (empty($lists))
            return;

        $k = $_POST['curent'][0];
        $v = $_POST['curent'][1];
        $root = $v['folder_ftp'];
        if (!@file_exists($root))
            return;
        $files = scandir($root);
        $term = get_term($k, WPMF_TAXO);
        $this->wpmf_ajax_add_sync_folder($root, @$term->name, @$term->parent);
    }
    
    /* ajax sync from FTP to media library */
    public function wpmf_ajax_add_sync_folder($dir, $folder_name, $parent) {
        $i = 0;
        $term_id_insert = $this->wpmf_get_term_insert($folder_name, $parent);
        $files = scandir($dir); // List files and directories inside $dir path
        $files = array_diff($files, array('..', '.'));
        if (count($files) > 0) {

            foreach ($files as $file) {
                if ($i >= 3) {
                    wp_send_json(array('status' => 'error_time'));
                } else {
                    if (!is_file($dir . '/' . $file)) { // is directory
                        $this->wpmf_ajax_add_sync_folder($dir . '/' . $file, str_replace('  ', ' ', $file), $term_id_insert);
                    } else {
                        // is file
                        $upload_dir = wp_upload_dir();
                        $info_file = wp_check_filetype($dir . '/' . $file);
                        if (!empty($info_file) && !empty($info_file['ext']) && in_array(strtolower($info_file['ext']), $this->type_import)) {
                            $content = @file_get_contents($dir . '/' . $file);
                            $file = sanitize_file_name($file);
                            // check file exist , if not exist then insert file
                            $check_exist = $this->wpmf_check_exist_post('/' . $file, $term_id_insert);
                            if ($check_exist == 0) {
                                $check = $this->wpmf_insert_attachment_metadata($upload_dir['path'], $upload_dir['url'], $file, $content, $info_file['type'], $info_file['ext'], $term_id_insert);
                                if($check) $i++;
                            }
                        }
                    }
                }
            }

            if ($i >= 3) {
                wp_send_json(array('status' => true));
            }
        }
    }

    // Modify the data that goes back with the heartbeat-tick
    public function wpmf_heartbeat_received($response, $data) {
        if (!current_user_can('install_plugins'))
            return $response;
        $sync = get_option('wpmf_option_sync_media');
        if (empty($sync))
            return $response;

        if (isset($data['wpmf_heartbeat']) && $data['wpmf_heartbeat'] == 'wpmf_queue_process') {
            $lists = get_option('wpmf_list_sync_media');
            $lastRun = get_option('wpmf_lastRun_sync');
            $time_sync = get_option('wpmf_time_sync');
            if (empty($lists))
                return $response;
            if ($time_sync == 0)
                return $response;
            if (time() - (int) $lastRun < (int) $time_sync * 60)
                return $response;

            update_option('wpmf_lastRun_sync', time());
            foreach ($lists as $k => $v) {
                if (!@file_exists($v['folder_ftp']))
                    return;
                $current = array($k, $v);
                if (!empty($k)) {
                    $term = get_term($k, WPMF_TAXO);
                    $response = $this->wpmf_heartbeat_add_sync_folder($v['folder_ftp'], $term->name, $term->parent, $response, $current);
                } else {
                    $response = $this->wpmf_heartbeat_add_sync_folder($v['folder_ftp'], '', 0, $response, $current);
                }
            }
        }
        return $response;
    }

    public function wpmf_heartbeat_add_sync_folder($dir, $folder_name, $parent, $response, &$current) {
        $i = 0;
        $term_id_insert = $this->wpmf_get_term_insert($folder_name, $parent);
        $files = scandir($dir); // List files and directories inside $dir path
        $files = array_diff($files, array('..', '.'));
        if (count($files) > 0) {
            $info = pathinfo($dir);
            foreach ($files as $file) {
                if ($i >= 3) {
                    $response['wpmf_limit'][$current[0]] = $current;
                    return $response;
                } else {
                    if (!is_file($dir . '/' . $file)) {
                        $response = $this->wpmf_heartbeat_add_sync_folder($dir . '/' . $file, str_replace('  ', ' ', $file), $term_id_insert, $response, $current);
                        return $response;
                    } else {
                        $upload_dir = wp_upload_dir();
                        $info_file = wp_check_filetype($dir . '/' . $file);
                        if (!empty($info_file) && !empty($info_file['ext']) && in_array(strtolower($info_file['ext']), $this->type_import)) {
                            $content = @file_get_contents($dir . '/' . $file);
                            $file = sanitize_file_name($file);
                            // check file exist , if not exist then insert file
                            $check_exist = $this->wpmf_check_exist_post('/' . $file, $term_id_insert);
                            if ($check_exist == 0) {
                                $check = $this->wpmf_insert_attachment_metadata($upload_dir['path'], $upload_dir['url'], $file, $content, $info_file['type'], $info_file['ext'], $term_id_insert);
                                if($check) $i++;
                            }
                        }
                    }
                }
            }
        }

        $response['wpmf_limit'][$current[0]] = $current;
        return $response;
    }
    
    /* check post exist */
    public function wpmf_check_exist_post($file, $term_id_insert) {
        global $wpdb;
        if (empty($term_id_insert)) {
            $sql = $wpdb->prepare(
                    "SELECT COUNT(*) FROM " . $wpdb->prefix . "posts"
                    . " WHERE guid LIKE %s "
                    . "AND ID NOT IN(SELECT object_id FROM " . $wpdb->prefix . "term_relationships) ", array("%$file%"));
            $check_exist = $wpdb->get_var($sql);
        } else {
            $sql = $wpdb->prepare(
                    "SELECT COUNT(*) FROM " . $wpdb->prefix . "posts," . $wpdb->prefix . "term_relationships"
                    . " WHERE guid LIKE %s "
                    . "AND ID = object_id "
                    . "AND term_taxonomy_id=%d", array("%$file%", $term_id_insert));
            $check_exist = $wpdb->get_var($sql);
        }

        return $check_exist;
    }

    public function wpmf_localize_script() {
        return array(
            'undimension' => __('Remove dimension', 'wpmf'),
            'editdimension' => __('Edit dimension', 'wpmf'),
            'unweight' => __('Remove weight', 'wpmf'),
            'editweight' => __('Edit weight', 'wpmf'),
            'error' => __('This value is already existing', 'wpmf'),
            'wpmf_root_site' => str_replace('/wp-content/themes', '', get_theme_root())
        );
    }
  
    public function wpmf_whow_notice() {
        if (current_user_can('manage_options')) {
            echo '<script type="text/javascript">' . PHP_EOL
            . 'function importWpmfgallery(doit,button){' . PHP_EOL
            . 'jQuery(button).closest("p").find(".spinner").show().css({"visibility":"visible"});' . PHP_EOL
            . 'jQuery.post(ajaxurl, {action: "import_gallery" , doit :doit}, function(response) {' . PHP_EOL
            . 'if(response == "error time"){' . PHP_EOL
            . 'jQuery("#wmpfImportgallery").click();' . PHP_EOL
            . '}else{' . PHP_EOL
            . 'jQuery(button).closest("div#wpmf_error").hide();' . PHP_EOL
            . 'if(doit===true){' . PHP_EOL
            . 'jQuery("#wpmf_error").after("<div class=\'updated\'> <p><strong>' . __('NextGEN galleries successfully imported in WP Media Folder', 'wpmf') . '</strong></p></div>");' . PHP_EOL
            . '}' . PHP_EOL
            . '}' . PHP_EOL
            . '});' . PHP_EOL
            . '}' . PHP_EOL
            . '</script>';
            echo '<div class="error" id="wpmf_error">'
            . '<p>'
            . __('You\'ve just installed WP Media Folder, to save your time we can import your nextgen gallery into WP Media Folder', 'wpmf')
            . '<a href="#" class="button button-primary" style="margin: 0 5px;" onclick="importWpmfgallery(true,this);" id="wmpfImportgallery">' . __('Sync/Import NextGEN galleries', 'wpmf') . '</a> or <a href="#" onclick="importWpmfgallery(false,this);" style="margin: 0 5px;" class="button">' . __('No thanks ', 'wpmf') . '</a><span class="spinner" style="display:none; margin:0; float:none"></span>'
            . '</p>'
            . '</div>';
        }
    }

    public function add_settings_menu() {
        add_options_page('Setting Folder Options', 'Media Folder', 'manage_options', 'option-folder', array($this, 'view_folder_options'));
    }
    
    /* View settings page and update option */
    public function view_folder_options() {
        if (isset($_POST['btn_wpmf_save'])) {
            if (isset($_POST['wpmf_color_singlefile'])) {
                update_option('wpmf_color_singlefile', json_encode($_POST['wpmf_color_singlefile']));

                $file = WP_MEDIA_FOLDER_PLUGIN_DIR . '/assets/css/wpmf_single_file.css';
                if (@file_exists($file)) {
                    // get custom settings single file
                    $wpmf_color_singlefile = json_decode(get_option('wpmf_color_singlefile'));
                    $image_download = '../images/download.png';
                    // custom css by settings
                    $custom_css = "
                            .wpmf-defile:hover{
                                background: " . $wpmf_color_singlefile->hvdownloadlink . " url(" . $image_download . ") no-repeat scroll 5px center !important;
                                box-shadow: 1px 1px 12px #ccc !important;
                                color: " . $wpmf_color_singlefile->hoverfontcolor . "
                            }

                            .wpmf-defile{
                                background: " . $wpmf_color_singlefile->bgdownloadlink . " url(" . $image_download . ") no-repeat scroll 5px center !important;
                                color: " . $wpmf_color_singlefile->fontdownloadlink . ";
                                border: none;
                                border-radius: 0px;
                                box-shadow: none;
                                text-shadow: none;
                                transition: all 0.2s ease 0s;
                                float: left;
                                margin: 7px;
                                padding: 10px 20px 10px 60px;
                                text-decoration: none;
                            }
                            ";
                    
                    // write custom css to file wpmf_single_file.css
                    file_put_contents(
                            $file, $custom_css
                    );
                }
            }
            
            // update selected dimension
            if (isset($_POST['dimension'])) {
                $selected_d = json_encode($_POST['dimension']);
                update_option('wpmf_selected_dimension', $selected_d);
            } else {
                update_option('wpmf_selected_dimension', '[]');
            }
            
            // update selected weight
            if (isset($_POST['weight'])) {
                $selected_w = array();
                foreach ($_POST['weight'] as $we) {
                    $s = explode(',', $we);
                    $selected_w[] = array($s[0], $s[1]);
                }

                $se_w = json_encode($selected_w);
                update_option('wpmf_weight_selected', $se_w);
            } else {
                update_option('wpmf_weight_selected', '[]');
            }
            
            // update padding gallery
            if (isset($_POST['padding_gallery'])) {
                $padding_themes = $_POST['padding_gallery'];
                foreach ($padding_themes as $key => $padding_theme) {
                    if (!is_numeric($padding_theme)) {
                        if ($key == 'wpmf_padding_masonry') {
                            $padding_theme = 5;
                        } else {
                            $padding_theme = 10;
                        }
                    }
                    $padding_theme = (int) $padding_theme;
                    if ($padding_theme > 30 || $padding_theme < 0) {
                        if ($key == 'wpmf_padding_masonry') {
                            $padding_theme = 5;
                        } else {
                            $padding_theme = 10;
                        }
                    }

                    $pad = get_option($key);
                    if (!isset($pad)) {
                        add_option($key, $padding_theme);
                    } else {
                        update_option($key, $padding_theme);
                    }
                }
            }
            
            // update list size
            if (isset($_POST['size_value'])) {
                $size_value = json_encode($_POST['size_value']);
                update_option('wpmf_gallery_image_size_value', $size_value);
            }

            if (isset($_POST['wpmf_patern'])) {
                $patern = trim(str_replace('#', '', $_POST['wpmf_patern']), ' ');
                update_option('wpmf_patern_rename', $patern);
            }

            if (isset($_POST['input_time_sync'])) {
                if ((int) $_POST['input_time_sync'] < 0) {
                    $time_sync = (int) $this->default_time_sync;
                } else {
                    $time_sync = (int) $_POST['input_time_sync'];
                }
                update_option('wpmf_time_sync', $time_sync);
            }
            
            // update checkbox options
            $options_name = array('wpmf_option_mediafolder','wpmf_create_folder','wpmf_option_override','wpmf_option_duplicate','wpmf_active_media','wpmf_usegellery','wpmf_useorder','wpmf_option_searchall','wpmf_option_media_remove','wpmf_usegellery_lightbox','wpmf_media_rename','wpmf_option_singlefile','wpmf_option_sync_media','wpmf_slider_animation','wpmf_option_countfiles','wpmf_option_lightboximage');
            foreach ($options_name as $option){
                $this->update_option_checkbox($option);
            }
            $this->get_success_message();
        }
        
        $option_mediafolder = get_option('wpmf_option_mediafolder');
        $wpmf_create_folder = get_option('wpmf_create_folder');
        $option_override = get_option('wpmf_option_override');
        $option_duplicate = get_option('wpmf_option_duplicate');
        $wpmf_active_media = get_option('wpmf_active_media');
        $btnoption = get_option('wpmf_use_taxonomy');
        $btn_import_categories = get_option('_wpmf_import_notice_flag');
        
        $padding_masonry = get_option('wpmf_padding_masonry');
        $padding_portfolio = get_option('wpmf_padding_portfolio');
        $size_selected = json_decode(get_option('wpmf_gallery_image_size_value'));
        $usegellery = get_option('wpmf_usegellery');
        $slider_animation = get_option('wpmf_slider_animation');
        $useorder = get_option('wpmf_useorder');
        $option_searchall = get_option('wpmf_option_searchall');
        $option_usegellery_lightbox = get_option('wpmf_usegellery_lightbox');
        $wpmf_media_rename = get_option('wpmf_media_rename');
        $wpmf_patern = get_option('wpmf_patern_rename');

        $option_media_remove = get_option('wpmf_option_media_remove');
        $s_dimensions = get_option('wpmf_default_dimension');
        $a_dimensions = json_decode($s_dimensions);
        $string_s_de = get_option('wpmf_selected_dimension');
        $array_s_de = json_decode($string_s_de);

        $s_weights = get_option('wpmf_weight_default');
        $a_weights = json_decode($s_weights);
        $string_s_we = get_option('wpmf_weight_selected');
        $array_s_we = json_decode($string_s_we);
        
        $option_countfiles = get_option('wpmf_option_countfiles');
        $option_lightboximage = get_option('wpmf_option_lightboximage');
        $option_singlefile = get_option('wpmf_option_singlefile');
        $wpmf_color_singlefile = json_decode(get_option('wpmf_color_singlefile'));
        $wpmf_list_sync_media = get_option('wpmf_list_sync_media');
        $option_sync_media = get_option('wpmf_option_sync_media');
        $time_sync = get_option('wpmf_time_sync');
        if (!empty($wpmf_list_sync_media)) {
            foreach ($wpmf_list_sync_media as $k => $v) {
                if (!empty($k)) {
                    $term = get_term($k, WPMF_TAXO);
                    if (!empty($term)) {
                        $this->get_category_dir($k, $term->parent, $term->name);
                    }
                } else {
                    $this->breadcrumb_category[0] = '/';
                }
            }
        }
        
        if (is_plugin_active('wp-media-folder-addon/wp-media-folder-addon.php')) {
            // google drive
            $googleconfig = get_option('_wpmfAddon_cloud_config');
            if(isset($_POST['googleClientId']) && isset($_POST['googleClientSecret'])){
                if(is_array($googleconfig) && !empty($googleconfig)){
                    $googleconfig['googleClientId'] = $_POST['googleClientId'];
                    $googleconfig['googleClientSecret'] = $_POST['googleClientSecret'];
                }else{
                    $googleconfig = array('googleClientId' => $_POST['googleClientId'] , 'googleClientSecret' => $_POST['googleClientSecret']);
                }
                update_option('_wpmfAddon_cloud_config', $googleconfig);
            }

            $googleDrive = new wpmfAddonGoogleDrive();
            $googleconfig = get_option('_wpmfAddon_cloud_config');
            if(!empty($googleconfig)){
                
            }else{
                $googleconfig = array('googleClientId' => '' , 'googleClientSecret' => '');
            }
            
            $html_tabgoogle = apply_filters('wpmfaddon_ggsettings',$googleDrive,$googleconfig);
            // dropbox
            $Dropbox = new wpmfAddonDropbox();
            $dropboxconfig = get_option('_wpmfAddon_dropbox_config');
            if(isset($_POST['dropboxKey']) && isset($_POST['dropboxSecret'])){
                if(is_array($dropboxconfig) && !empty($dropboxconfig)){
                    if(!empty($_POST['dropboxAuthor'])){
                        //convert code authorCOde to Token
                       $list = $Dropbox->convertAuthorizationCode($_POST['dropboxAuthor']);
                    }
                    if(!empty($list['accessToken'])){
                        //save accessToken to database
                        $dropboxconfig['dropboxToken'] = $list['accessToken'];
                    }
                    $dropboxconfig['dropboxKey'] = $_POST['dropboxKey'];
                    $dropboxconfig['dropboxSecret'] = $_POST['dropboxSecret'];
                }else{
                    $dropboxconfig = array('dropboxKey' => $_POST['dropboxKey'] , 'dropboxSecret' => $_POST['dropboxSecret']);
                }
                update_option('_wpmfAddon_dropbox_config', $dropboxconfig);
            }
            
            $Dropbox = new wpmfAddonDropbox();
            $wpmfAddon_dropbox_config = get_option('_wpmfAddon_dropbox_config');
            if(!empty($wpmfAddon_dropbox_config)){
                
            }else{
                $dropboxconfig = array('dropboxKey' => '' , 'dropboxSecret' => '');
            }
            
            $html_tabdropbox = apply_filters('wpmfaddon_dbxsettings',$Dropbox,$dropboxconfig);
        }
        
        require_once( WP_MEDIA_FOLDER_PLUGIN_DIR . 'class/pages/wp-folder-options.php' );
    }
    
    public function get_category_dir($id, $term_id, $string) {
        $this->breadcrumb_category[$id] = '/' . $string . '/';
        if (!empty($term_id)) {
            $term = get_term($term_id, WPMF_TAXO);
            $this->get_category_dir($id, $term->parent, $term->name . '/' . $string);
        }
    }
    
    /* Display info after save settings */
    public function get_success_message() {
        require_once( WP_MEDIA_FOLDER_PLUGIN_DIR . 'class/pages/saved_info.php' );
    }
    
    /* Update option checkbox */
    public function update_option_checkbox($option) {
        if (isset($_POST[$option])) {
            update_option($option, $_POST[$option]);
        }
    }

    /* Ajax import from next gallery to media library */
    public function import_gallery() {
        global $wpdb;
        $option_import = get_option('wpmf_import_nextgen_gallery');
        if ($_POST['doit'] === 'true') {
            update_option('wpmf_import_nextgen_gallery', 'yes');
        } else {
            update_option('wpmf_import_nextgen_gallery', 'no');
        }

        if ($_POST['doit'] == 'true') {
            $begin_time = time();
            $loop = 0;
            $limit = 3;
            $gallerys = $wpdb->get_results("SELECT path,title,gid FROM " . $wpdb->prefix . 'ngg_gallery', OBJECT);
            $site_url = get_site_url();
            $site_path = get_home_path();
            $upload_dir = wp_upload_dir();

            if (is_multisite()) {
                $checks = get_term_by('name', 'sites-' . get_current_blog_id(), WPMF_TAXO);
                if (empty($checks) || ((!empty($checks) && $checks->parent != 0))) {
                    $sites_inserted = wp_insert_term('sites-' . get_current_blog_id(), WPMF_TAXO, array('parent' => 0));
                    if (is_wp_error($sites_inserted)) {
                        $sites_parrent = $checks->term_id;
                    } else {
                        $sites_parrent = $sites_inserted['term_id'];
                    }
                } else {
                    $sites_parrent = $checks->term_id;
                }
            } else {
                $sites_parrent = 0;
            }

            if (count($gallerys) > 0) {
                foreach ($gallerys as $gallery) {
                    $gallery_path = $gallery->path;
                    $gallery_path = str_replace('\\', '/', $gallery_path);
                    // create folder from nextgen gallery
                    $wpmf_category = get_term_by('name', $gallery->title, WPMF_TAXO);
                    if (empty($wpmf_category) || ((!empty($wpmf_category) && $wpmf_category->parent != $sites_parrent))) {
                        $inserted = wp_insert_term($gallery->title, WPMF_TAXO, array('parent' => $sites_parrent));
                        if (is_wp_error($inserted)) {
                            $term_id_insert = $wpmf_category->term_id;
                        } else {
                            $term_id_insert = $inserted['term_id'];
                        }
                    } else {
                        $term_id_insert = $wpmf_category->term_id;
                    }

                    // =========================
                    $sql = $wpdb->prepare("SELECT pid,filename FROM  ".$wpdb->prefix."ngg_pictures WHERE galleryid = %d" ,array($gallery->gid));
                    $image_childs = $wpdb->get_results($sql, OBJECT);
                    if (count($image_childs) > 0) {
                        foreach ($image_childs as $image_child) {
                            if ($loop >= $limit) {
                                wp_send_json('error time'); // run again ajax
                            } else {
                                $sql = $wpdb->prepare("SELECT COUNT(*) FROM " . $wpdb->prefix . "posts WHERE post_content=%s", array("[wpmf-nextgen-image-$image_child->pid]"));
                                $check_import = $wpdb->get_var($sql);
                                // check imported
                                if ($check_import == 0) {
                                    $url_image = $site_path . DIRECTORY_SEPARATOR . $gallery_path . DIRECTORY_SEPARATOR . $image_child->filename;
                                    $file_headers = @get_headers($url_image);
                                    if ($file_headers[0] != 'HTTP/1.1 404 Not Found') {
                                        $content = @file_get_contents($url_image);
                                        $info = pathinfo($url_image);
                                        if (!empty($info) && !empty($info['extension'])) {
                                            $ext = '.' . $info['extension'];
                                            if (@file_exists($upload_dir['path'] . DIRECTORY_SEPARATOR . $image_child->filename)) {
                                                $filename = uniqid() . $ext;
                                            } else {
                                                $filename = $image_child->filename;
                                            }
                                            $upload = file_put_contents($upload_dir['path'] . '/' . $filename, $content);

                                            // upload images
                                            if ($upload) {
                                                $attachment = array(
                                                    'guid' => $upload_dir['url'] . '/' . $filename,
                                                    'post_mime_type' => ($ext == '.jpg') ? 'image/jpeg' : 'image/' . substr($ext, 1),
                                                    'post_title' => str_replace($ext, '', $filename),
                                                    'post_content' => '[wpmf-nextgen-image-' . $image_child->pid . ']',
                                                    'post_status' => 'inherit'
                                                );

                                                $image_path = $upload_dir['path'] . '/' . $filename;
                                                $attach_id = wp_insert_attachment($attachment, $image_path);

                                                $attach_data = wp_generate_attachment_metadata($attach_id, $image_path);
                                                wp_update_attachment_metadata($attach_id, $attach_data);

                                                // create image in folder
                                                wp_set_object_terms((int) $attach_id, (int) $term_id_insert, WPMF_TAXO, false);
                                            }
                                            $loop++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
    
    /* this function do import wordpress category default */
    public function wpmf_impo_taxo() {
        return Wp_Media_Folder::wpmf_import_categories();
    }

    public function add_dimension() {
        if (isset($_POST['width_dimension']) && isset($_POST['height_dimension'])) {
            $min = $_POST['width_dimension'];
            $max = $_POST['height_dimension'];
            $new_dimension = $min . 'x' . $max;
            $s_dimensions = get_option('wpmf_default_dimension');
            $a_dimensions = json_decode($s_dimensions);
            if (in_array($new_dimension, $a_dimensions) == false) {
                array_push($a_dimensions, $new_dimension);
                update_option('wpmf_default_dimension', json_encode($a_dimensions));
                wp_send_json($new_dimension);
            } else {
                wp_send_json(false);
            }
        }
    }
    
    /* ajax edit selected size and weight filter */
    public function edit_selected($option_name, $old_value, $new_value) {
        $s_selected = get_option($option_name);
        $a_selected = json_decode($s_selected);

        if (in_array($old_value, $a_selected) == true) {
            $key_selected = array_search($old_value, $a_selected);
            $a_selected[$key_selected] = $new_value;
            update_option($option_name, json_encode($a_selected));
        }
    }
    
    /* ajax remove selected size and weight filter */
    public function remove_selected($option_name, $value) {
        $s_selected = get_option($option_name);
        $a_selected = json_decode($s_selected);
        if (in_array($value, $a_selected) == true) {
            $key_selected = array_search($value, $a_selected);
            unset($a_selected[$key_selected]);
            $a_selected = array_slice($a_selected, 0, count($a_selected));
            update_option($option_name, json_encode($a_selected));
        }
    }
    
    /* ajax remove size and weight filter */
    public function remove_dimension() {
        if (isset($_POST['value']) && $_POST['value'] != '') {
            // remove dimension
            $s_dimensions = get_option('wpmf_default_dimension');
            $a_dimensions = json_decode($s_dimensions);
            if (in_array($_POST['value'], $a_dimensions) == true) {
                $key = array_search($_POST['value'], $a_dimensions);
                unset($a_dimensions[$key]);
                $a_dimensions = array_slice($a_dimensions, 0, count($a_dimensions));
                $update_demen = update_option('wpmf_default_dimension', json_encode($a_dimensions));
                if (is_wp_error($update_demen)) {
                    wp_send_json($update_demen->get_error_message());
                } else {
                    $this->remove_selected('wpmf_selected_dimension', $_POST['value']); // remove selected
                    wp_send_json(true);
                }
            } else {
                wp_send_json(false);
            }
        }
    }
    
    /* ajax edit size and weight filter */
    public function edit() {
        if (isset($_POST['old_value']) && $_POST['old_value'] != '' && isset($_POST['new_value']) && $_POST['new_value'] != '') {
            $label = $_POST['label'];
            if ($label == 'dimension') {
                $s_dimensions = get_option('wpmf_default_dimension');
                $a_dimensions = json_decode($s_dimensions);
                if ((in_array($_POST['old_value'], $a_dimensions) == true) && (in_array($_POST['new_value'], $a_dimensions) == false)) {
                    $key = array_search($_POST['old_value'], $a_dimensions);
                    $a_dimensions[$key] = $_POST['new_value'];
                    $update_demen = update_option('wpmf_default_dimension', json_encode($a_dimensions));
                    if (is_wp_error($update_demen)) {
                        wp_send_json($update_demen->get_error_message());
                    } else {
                        $this->edit_selected('wpmf_selected_dimension', $_POST['old_value'], $_POST['new_value']); // edit selected
                        wp_send_json(array('value' => $_POST['new_value']));
                    }
                } else {
                    wp_send_json(false);
                }
            } else {
                $s_weights = get_option('wpmf_weight_default');
                $a_weights = json_decode($s_weights);
                if (isset($_POST['unit'])) {
                    $old_values = explode(',', $_POST['old_value']);
                    $old = array($old_values[0], $old_values[1]);
                    $new_values = explode(',', $_POST['new_value']);
                    $new = array($new_values[0], $new_values[1]);

                    if ((in_array($old, $a_weights) == true) && (in_array($new, $a_weights) == false)) {
                        $key = array_search($old, $a_weights);
                        $a_weights[$key] = $new;
                        $new_labels = explode('-', $new_values[0]);
                        if ($new_values[1] == 'kB') {
                            $label = ($new_labels[0] / 1024) . ' ' . $new_values[1] . '-' . ($new_labels[1] / 1024) . ' ' . $new_values[1];
                        } else {
                            $label = ($new_labels[0] / (1024 * 1024)) . ' ' . $new_values[1] . '-' . ($new_labels[1] / (1024 * 1024)) . ' ' . $new_values[1];
                        }
                        $update_weight = update_option('wpmf_weight_default', json_encode($a_weights));
                        if (is_wp_error($update_weight)) {
                            wp_send_json($update_weight->get_error_message());
                        } else {
                            $this->edit_selected('wpmf_weight_selected', $old, $new); // edit selected
                            wp_send_json(array('value' => $new_values[0], 'label' => $label));
                        }
                    } else {
                        wp_send_json(false);
                    }
                }
            }
        }
    }
    
    /* ajax add size to size filter */
    public function add_weight() {
        if (isset($_POST['min_weight']) && isset($_POST['max_weight'])) {
            if (!$_POST['unit'] || $_POST['unit'] == 'kB') {
                $min = $_POST['min_weight'] * 1024;
                $max = $_POST['max_weight'] * 1024;
                $unit = 'kB';
            } else {
                $min = $_POST['min_weight'] * 1024 * 1024;
                $max = $_POST['max_weight'] * 1024 * 1024;
                $unit = 'MB';
            }
            $new_unit = $unit;
            $label = $_POST['min_weight'] . ' ' . $unit . '-' . $_POST['max_weight'] . ' ' . $unit;
            $new_weight = array($min . '-' . $max, $unit);

            $s_weights = get_option('wpmf_weight_default');
            $a_weights = json_decode($s_weights);
            if (in_array($new_weight, $a_weights) == false) {
                array_push($a_weights, $new_weight);
                update_option('wpmf_weight_default', json_encode($a_weights));
                wp_send_json(array('key' => $min . '-' . $max, 'unit' => $unit, 'label' => $label));
            } else {
                wp_send_json(false);
            }
        }
    }

    /* ajax remove size to size filter */
    public function remove_weight() {
        if (isset($_POST['value']) && $_POST['value'] != '') {
            $s_weights = get_option('wpmf_weight_default');
            $a_weights = (array) json_decode($s_weights);
            $unit = $_POST['unit'];
            $weight_remove = array($_POST['value'], $unit);
            if (in_array($weight_remove, $a_weights) == true) {
                $key = array_search($weight_remove, $a_weights);
                unset($a_weights[$key]);
                $a_weights = array_slice($a_weights, 0, count($a_weights));
                $update_weight = update_option('wpmf_weight_default', json_encode($a_weights));
                if (is_wp_error($update_weight)) {
                    wp_send_json($update_weight->get_error_message());
                } else {
                    $this->remove_selected('wpmf_weight_selected', $weight_remove);  // remove selected
                    wp_send_json(true);
                }
            } else {
                wp_send_json(false);
            }
        }
    }
    
    /* ajax generate thumbnail */
    public function wpmf_regeneratethumbnail() {
        remove_filter('wp_generate_attachment_metadata', array($GLOBALS['wp_media_folder'], 'wpmf_after_upload'));
        global $wpdb;
        $limit = 1;

        $ofset = ((int) $_POST['paged'] - 1) * $limit;
        $count_images = $wpdb->get_var("SELECT COUNT(ID) FROM " . $wpdb->prefix . "posts as posts
                       WHERE   posts.post_type = 'attachment' AND post_mime_type LIKE '%image%'");

        $present = (100 / $count_images) * $limit;
        $k = 0;
        $urls = array();
        $attachments = get_posts(array('post_type' => 'attachment', 'post_mime_type' => 'image', 'posts_per_page' => $limit, 'offset' => $ofset));
        if (empty($attachments))
            wp_send_json(array('status' => 'ok', 'paged' => 0, 'success' => $this->result_gennerate_thumb));
        foreach ($attachments as $image) {
            $fullsizepath = get_attached_file($image->ID);
            if (false === $fullsizepath || !@file_exists($fullsizepath)) {
                $message = sprintf(__('The originally uploaded image file cannot be found at %s', 'wpmf'), '<code>' . esc_html($fullsizepath) . '</code>');
                $this->result_gennerate_thumb .= sprintf(__('<p>&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s</p>', 'wpmf'), esc_html(get_the_title($image->ID)), $image->ID, $message);
                wp_send_json(array('status' => 'error_time', 'paged' => $_POST['paged'], 'success' => $this->result_gennerate_thumb));
            }

            $metadata = wp_generate_attachment_metadata($image->ID, $fullsizepath);
            $url_image = wp_get_attachment_url($image->ID);
            $urls[] = $url_image;
            if (is_wp_error($metadata)) {
                $message = $metadata->get_error_message();
                $this->result_gennerate_thumb .= sprintf(__('<p>&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s</p>', 'wpmf'), esc_html(get_the_title($image->ID)), $image->ID, $message);
                wp_send_json(array('status' => 'error_time', 'paged' => $_POST['paged'], 'success' => $this->result_gennerate_thumb));
            }

            if (empty($metadata)) {
                $message = __('Unknown failure reason.', 'wpmf');
                $this->result_gennerate_thumb .= sprintf(__('<p>&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s</p>', 'wpmf'), esc_html(get_the_title($image->ID)), $image->ID, $message);
                wp_send_json(array('status' => 'error_time', 'paged' => $_POST['paged'], 'success' => $this->result_gennerate_thumb));
            }

            wp_update_attachment_metadata($image->ID, $metadata);
            $this->result_gennerate_thumb .= sprintf(__('<p>&quot;%1$s&quot; (ID %2$s) was successfully resized in %3$s seconds.</p>', 'wpmf'), esc_html(get_the_title($image->ID)), $image->ID, timer_stop());
            $k++;
        }

        if ($k >= $limit) {
            wp_send_json(array('status' => 'error_time', 'paged' => $_POST['paged'], 'success' => $this->result_gennerate_thumb, 'precent' => $present, 'url' => $urls));
        }
    }

    public function die_json_error_msg($id, $message) {
        wp_send_json(array('error' => sprintf(__('&quot;%1$s&quot; (ID %2$s) failed to resize. The error message was: %3$s', 'regenerate-thumbnails'), esc_html(get_the_title($id)), $id, $message)));
    }

}