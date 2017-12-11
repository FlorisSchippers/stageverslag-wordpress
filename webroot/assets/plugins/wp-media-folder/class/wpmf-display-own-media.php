<?php

require_once( WP_MEDIA_FOLDER_PLUGIN_DIR . '/class/class-media-folder.php' );

class Wpmf_Display_Own_Media {

    function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'wpmf_admin_page_table_script'));
        add_action('wp_enqueue_media', array($this, 'wpmf_load_custom_wp_admin_script'));
        add_action('wp_ajax_display_media', array($this, 'wpmf_display_media'));
    }
    
    /* includes some scripts */
    public function wpmf_admin_page_table_script() {
        global $pagenow;
        if ($pagenow == 'upload.php') {
            $this->wpmf_load_custom_wp_admin_script();
        }
    }
    
    /* includes some scripts */
    public function wpmf_load_custom_wp_admin_script() {
        wp_enqueue_script('wpmf-filter-display-media');
    }

    function wpmf_display_media() {
        if (isset($_POST['wpmf_display_media'])) {
            $_SESSION['wpmf_display_media'] = $_POST['wpmf_display_media'];
        }
    }

}

?>