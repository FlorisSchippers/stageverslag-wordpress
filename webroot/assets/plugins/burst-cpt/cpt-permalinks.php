<?php
/*
Plugin Name: Custom Post type custom slugs
Plugin URI: http://burst-digital.com/
Description: Dynamicly change the permalinks for your posttypes
Version: n/a
Author: Burst
Author URI: http://burst-digital.com/
License: GPL2/Creative Commons
Text Domain: burst-cpt
Domain Path: /languages/
*/
/*
 * Expand this array with the names of your custom posttypes
 */
$custom_post_types = array(
    array(
        'name' => __('CPT Base Ontwikkelen Competenties'),
        'id'   => __('burst_cpt_base_ontwikkelen')
    ),
	array(
		'name' => __('CPT Base Onderzoeken Competenties'),
		'id'   => __('burst_cpt_base_onderzoeken')
	),
	array(
		'name' => __('CPT Base Ontwerpen Competenties'),
		'id'   => __('burst_cpt_base_ontwerpen')
	),
	array(
		'name' => __('CPT Base Ondernemen Competenties'),
		'id'   => __('burst_cpt_base_ondernemen')
	),
	array(
		'name' => __('CPT Base Betrokken Competenties'),
		'id'   => __('burst_cpt_base_betrokken')
	),
);
add_action( 'load-options-permalink.php', 'burst_load_permalinks' );
function burst_load_permalinks()
{
  global $custom_post_types;
  foreach ($custom_post_types as $post_type){
    $post_type_id = $post_type['id'];
    
    if( isset( $_POST[$post_type_id] ) ){
      update_option( $post_type_id,  $_POST[$post_type_id] );
    }
    
// Add a settings field to the permalink page
    add_settings_field( $post_type_id,  $post_type['name'], 'cpt_callback', 'permalink', 'optional', $post_type);
  }
}

function cpt_callback($args){
  $id = $args['id'];  
  $value = get_option( $id );	
  echo '<input type="text" value="' . esc_attr( $value ) . '" name="'.$id.'" id="'.$id.'" class="regular-text" />';
}