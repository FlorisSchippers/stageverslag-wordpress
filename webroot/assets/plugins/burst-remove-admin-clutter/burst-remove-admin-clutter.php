<?php
/*
Plugin Name: Burst Remove Admin Clutter
Plugin URI: http://burst-digital.com/
Description: Removes pages from Wordpress admin
Version: 0.1
Author: Burst
Author URI: http://burst-digital.com/
License: GPL2/Creative Commons
Text Domain: hero-sea
*/

add_action( 'admin_menu', 'custom_menu_page_removing' );

/**
 * Removes unnecessary menu items
 */
function custom_menu_page_removing() {

//	remove_menu_page( 'edit.php' );                   //Posts
//	remove_menu_page( 'post-new.php' );               //Posts
//	remove_menu_page( 'edit-comments.php' );          //Comments
//	remove_menu_page( 'upload.php' );                 //Media
//	remove_menu_page( 'edit.php?post_type=page' );    //Pages
}
