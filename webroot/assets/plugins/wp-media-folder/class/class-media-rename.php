<?php

class Wpmf_Media_Rename {

    function __construct() {
        add_filter('wp_handle_upload_prefilter', array($this, 'wpmf_custom_upload_filter'));
        add_filter('wp_generate_attachment_metadata', array($this, 'wpmf_after_upload'), 10, 2);
    }
    
    /* rename attachment after upload by settings */
    function wpmf_custom_upload_filter($file) {
        $patern = get_option('wpmf_patern_rename');
        $upload_dir = wp_upload_dir();
        $info = pathinfo($file['name']);
        $wpmf_rename_number = get_option('wpmf_rename_number');
        if (isset($_SESSION['wpmf-current-folder']) && $_SESSION['wpmf-current-folder'] != 0) {
            $current_folder = get_term($_SESSION['wpmf-current-folder'], WPMF_TAXO);
            $foldername = $current_folder->name;
        } else {
            $foldername = 'uncategorized';
        }

        $sitename = get_bloginfo('name');
        $original_filename = $info['filename'];
        $date = trim($upload_dir['subdir'], '/');
        $ext = empty($info['extension']) ? '' : '.' . $info['extension'];
        $number = (int) $wpmf_rename_number + 1;

        $patern = str_replace('{sitename}', $sitename, $patern);
        $patern = str_replace('{foldername}', $foldername, $patern);
        $patern = str_replace('{date}', $date, $patern);
        if(strpos( $patern , '{original name}') == false){
            $patern .= ' #';
            $patern = str_replace('#', $number, $patern);
        }else{
            $patern = str_replace('{original name}', $original_filename, $patern);
            $patern = str_replace('#', $number, $patern);
        }

        $file['name'] = $patern . $ext;
        return $file;
    }
    
    /* update option wpmf_rename_number */
    public function wpmf_after_upload($metadata, $attachment_id) {
        $wpmf_rename_number = get_option('wpmf_rename_number');
        $number = (int) $wpmf_rename_number + 1;
        update_option('wpmf_rename_number', $number);
        return $metadata;
    }

}

?>