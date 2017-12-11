<?php

class Wpmf_replace_file {

    function __construct() {
        add_action('wp_enqueue_media', array($this, 'wpmf_enqueue_admin_scripts'));
        add_action('wp_ajax_wpmf_replace_file', array($this, 'wpmf_replace_file'));
    }

    /* Ajax replace attachment */
    public function wpmf_replace_file() {
        if (!empty($_FILES["wpmf_replace_file"])) {
            if (empty($_POST['post_selected'])) {
                _e('Post empty', 'wpmf');
                die();
            }
            $id = $_POST['post_selected'];
            $post = get_post($id);
            $metadata = wp_get_attachment_metadata($id);

            $filepath = get_attached_file($id);
            $infopath = pathinfo($filepath);
            $allowedTypes = array('gif', 'jpg', 'png', 'bmp');
            $guid = $post->guid;

            if (!in_array($infopath['extension'], $allowedTypes)) {
                $new_filetype = wp_check_filetype($_FILES["wpmf_replace_file"]["name"]);
                if ($new_filetype['ext'] != $infopath["extension"]) {
                    _e('To replace a media and keep the link to this media working, it must be in the same format, ie. jpg > jpg… Thanks!', 'wpmf');
                    die();
                }
            }

            if ($_FILES["wpmf_replace_file"]["error"] > 0) {
                echo "Error: " . $_FILES["wpmf_replace_file"]["error"] . "<br>";
            } else {
                $uploadpath = wp_upload_dir();
                @unlink($filepath);
                if (in_array($infopath['extension'], $allowedTypes)) {
                    if (isset($metadata['sizes']) && is_array($metadata['sizes'])) {
                        foreach ($metadata['sizes'] as $size => $sizeinfo) {
                            $intermediate_file = str_replace(basename($filepath), $sizeinfo['file'], $filepath);
                            /** This filter is documented in wp-includes/functions.php */
                            $intermediate_file = apply_filters('wp_delete_file', $intermediate_file);
                            @unlink(path_join($uploadpath['basedir'], $intermediate_file));
                        }
                    }
                }


                echo "File replaced! <br>";
                echo "Upload: " . $_FILES["wpmf_replace_file"]["name"] . "<br>";
                echo "Type: " . $_FILES["wpmf_replace_file"]["type"] . "<br>";
                echo "Size: " . ($_FILES["wpmf_replace_file"]["size"] / 1024) . " kB<br>";
                echo "Stored in: " . $_FILES["wpmf_replace_file"]["tmp_name"];

                @move_uploaded_file($_FILES["wpmf_replace_file"]["tmp_name"], $infopath['dirname'] . "/" . $infopath['basename']);
                update_post_meta($id, 'wpmf_size', filesize($infopath['dirname'] . "/" . $infopath['basename']));
                if (in_array($infopath['extension'], $allowedTypes)) {
                    $this->createThumbs($filepath, $infopath['dirname'], $infopath['extension'], $metadata['sizes']);
                }
            }
        } else {
            _e('No file selected', 'wpmf');
            die();
        }
    }
    
    /* Create thumbnail after replace */
    function createThumbs($filepath, $pathToImages, $extimage, $metadatasizes) {

        if (isset($metadatasizes) && is_array($metadatasizes)) {
            $uploadpath = wp_upload_dir();
            foreach ($metadatasizes as $size => $sizeinfo) {
                $intermediate_file = str_replace(basename($filepath), $sizeinfo['file'], $filepath);
                $intermediate_file = apply_filters('wp_delete_file', $intermediate_file);

                switch ($extimage) {
                    case 'jpeg':
                    case 'jpg':
                        $img = imagecreatefromjpeg($filepath);
                        break;
                        break;

                    case 'png':
                        $img = imagecreatefrompng($filepath);
                        break;
                        break;

                    case 'gif':
                        $img = imagecreatefromgif($filepath);
                        break;

                    case 'bmp':
                        $img = imagecreatefromwbmp($filepath);
                        break;
                        break;
                }

                // load image and get image size
                $width = imagesx($img);
                $height = imagesy($img);
                $new_width = $sizeinfo['width'];
                $new_height = floor($height * ( $sizeinfo['width'] / $width ));
                $tmp_img = imagecreatetruecolor($new_width, $new_height);
                imagecopyresized($tmp_img, $img, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                switch ($extimage) {
                    case 'jpeg':
                    case 'jpg':
                        imagejpeg($tmp_img, path_join($uploadpath['basedir'], $intermediate_file));
                        break;
                        break;

                    case 'png':
                        $background = imagecolorallocate($tmp_img , 0, 0, 0);
                        imagecolortransparent($tmp_img, $background);
                        imagepng($tmp_img, path_join($uploadpath['basedir'], $intermediate_file));
                        break;
                        break;

                    case 'gif':
                        imagegif($tmp_img, path_join($uploadpath['basedir'], $intermediate_file));
                        break;

                    case 'bmp':
                        imagewbmp($tmp_img, path_join($uploadpath['basedir'], $intermediate_file));
                        break;
                        break;
                }
            }
        }
    }
    
    /* Update metadata for attachment after replace */
    public function update_meta_attachment($attachment_id) {
        $upload_dir = wp_upload_dir();
        if (isset($_SESSION['wpmf_re']['caption']) && isset($_SESSION['wpmf_re']['description']) && isset($_SESSION['wpmf_re']['post_parent'])) {
            $my_post = array(
                'ID' => $attachment_id,
                'post_excerpt' => $_SESSION['wpmf_re']['caption'],
                'post_content' => $_SESSION['wpmf_re']['description'],
                'post_parent' => $_SESSION['wpmf_re']['post_parent'],
                'post_title' => $_SESSION['wpmf_re']['title'],
            );
            // Update the post into the database
            wp_update_post($my_post);
            if (isset($_SESSION['wpmf_re']['alt'])) {
                update_post_meta($attachment_id, '_wp_attachment_image_alt', $_SESSION['wpmf_re']['alt']);
            }

            unset($_SESSION['wpmf_re']);
        }
    }
    
    /* includes styles and some scripts */
    function wpmf_enqueue_admin_scripts() {
        if (!current_user_can('upload_files'))
            return;
        wp_enqueue_script('wpmf-jquery-form', plugins_url('assets/js/jquery.form.js', dirname(__FILE__)), array('jquery'), WPMF_VERSION);
        wp_register_script('replace-image', plugins_url('assets/js/replace-image.js', dirname(__FILE__)), array('jquery'), WPMF_VERSION, true);
        wp_enqueue_script('replace-image');
        wp_enqueue_style('replace-style', plugins_url('assets/css/style_replace_image.css', dirname(__FILE__)), array(), WPMF_VERSION);
    }
}
