<?php
/*
Plugin Name: Burst Custom Post Types
Plugin URI: http://burst-digitl.com/
Description: Create custom post types for this site
Version: n/a
Author: Burst
Author URI: http://burst-digitl.com/
License: GPL2/Creative Commons
Text Domain: burst-cpt
Domain Path: /languages/
*/
/*
 * Configuratie:
 */
$burst_cpt_config = array();
$burst_cpt_config['hide-default-posts'] = false;
/*
 How this works:
  - Create Arrays per post type like the sponsoren one
  - Give good names to the arrays and the post types in English
  - Set all the other properties right (the slug should be translatable)
  - In CPT-permalinks plugin code extend the array. Use the same ID in the get_option function after rewrite.
  - Translate the names of the post type options to Dutch in the nl_NL.po file
  - 
*/
function burst_custom_post_types() {
  $ontwikkelen = array(
		'label'               => __( 'Ontwikkelen Competenties', 'burst-cpt' ),
		'description'         => __( 'Ontwikkelen Competenties', 'burst-cpt' ),
		'labels'              => array(
      'name'                => __( 'Ontwikkelen Competenties', 'Post Type General Name', 'burst-cpt' ),
      'singular_name'       => __( 'Ontwikkelen Competentie', 'Post Type Singular Name', 'burst-cpt' ),
      'menu_name'           => __( 'Ontwikkelen Competenties', 'burst-cpt' ),
      'parent_item_colon'   => __( 'Parent Ontwikkelen Competenties', 'burst-cpt' ),
      'all_items'           => __( 'All Ontwikkelen Competenties', 'burst-cpt' ),
      'view_item'           => __( 'Show Ontwikkelen Competentie', 'burst-cpt' ),
      'add_new_item'        => __( 'Add a Ontwikkelen Competentie', 'burst-cpt' ),
      'add_new'             => __( 'Add', 'burst-cpt' ),
      'edit_item'           => __( 'Edit Ontwikkelen Competentie', 'burst-cpt' ),
      'update_item'         => __( 'Update Ontwikkelen Competentie', 'burst-cpt' ),
      'search_items'        => __( 'Find a Ontwikkelen Competentie', 'burst-cpt' ),
      'not_found'           => __( 'Ontwikkelen Competentie not found', 'burst-cpt' ),
      'not_found_in_trash'  => __( 'Ontwikkelen Competenties not found in bin', 'burst-cpt' ),
    ),
	'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
    'hierarchical'        => true,
    'public'              => true,
    'show_ui'             => true,
    'show_in_menu'        => true,
    'show_in_nav_menus'   => true,
    'show_in_admin_bar'   => true,
    'menu_position'       => 5,
    'can_export'          => true,
    'has_archive'         => true,
    'exclude_from_search' => false,
    'publicly_queryable'  => true,
    'show_in_rest'        => true,
    'capability_type'     => 'page',
    'rewrite'             => array( 'slug' => get_option( 'burst_cpt_base_ontwikkelen' ) ), // This option has the id that is set in the cpt-permalinks plugin
    'menu_icon'			  => 'dashicons-awards',
	);

	$onderzoeken = array(
		'label'               => __( 'Onderzoeken Competenties', 'burst-cpt' ),
		'description'         => __( 'Onderzoeken Competenties', 'burst-cpt' ),
		'labels'              => array(
			'name'                => __( 'Onderzoeken Competenties', 'Post Type General Name', 'burst-cpt' ),
			'singular_name'       => __( 'Onderzoeken Competentie', 'Post Type Singular Name', 'burst-cpt' ),
			'menu_name'           => __( 'Onderzoeken Competenties', 'burst-cpt' ),
			'parent_item_colon'   => __( 'Parent Onderzoeken Competenties', 'burst-cpt' ),
			'all_items'           => __( 'All Onderzoeken Competenties', 'burst-cpt' ),
			'view_item'           => __( 'Show Onderzoeken Competentie', 'burst-cpt' ),
			'add_new_item'        => __( 'Add a Onderzoeken Competentie', 'burst-cpt' ),
			'add_new'             => __( 'Add', 'burst-cpt' ),
			'edit_item'           => __( 'Edit Onderzoeken Competentie', 'burst-cpt' ),
			'update_item'         => __( 'Update Onderzoeken Competentie', 'burst-cpt' ),
			'search_items'        => __( 'Find a Onderzoeken Competentie', 'burst-cpt' ),
			'not_found'           => __( 'Onderzoeken Competentie not found', 'burst-cpt' ),
			'not_found_in_trash'  => __( 'Onderzoeken Competenties not found in bin', 'burst-cpt' ),
		),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => get_option( 'burst_cpt_base_onderzoeken' ) ), // This option has the id that is set in the cpt-permalinks plugin
		'menu_icon'			  => 'dashicons-awards',
	);

	$ontwerpen = array(
		'label'               => __( 'Ontwerpen Competenties', 'burst-cpt' ),
		'description'         => __( 'Ontwerpen Competenties', 'burst-cpt' ),
		'labels'              => array(
			'name'                => __( 'Ontwerpen Competenties', 'Post Type General Name', 'burst-cpt' ),
			'singular_name'       => __( 'Ontwerpen Competentie', 'Post Type Singular Name', 'burst-cpt' ),
			'menu_name'           => __( 'Ontwerpen Competenties', 'burst-cpt' ),
			'parent_item_colon'   => __( 'Parent Ontwerpen Competenties', 'burst-cpt' ),
			'all_items'           => __( 'All Ontwerpen Competenties', 'burst-cpt' ),
			'view_item'           => __( 'Show Ontwerpen Competentie', 'burst-cpt' ),
			'add_new_item'        => __( 'Add a Ontwerpen Competentie', 'burst-cpt' ),
			'add_new'             => __( 'Add', 'burst-cpt' ),
			'edit_item'           => __( 'Edit Ontwerpen Competentie', 'burst-cpt' ),
			'update_item'         => __( 'Update Ontwerpen Competentie', 'burst-cpt' ),
			'search_items'        => __( 'Find a Ontwerpen Competentie', 'burst-cpt' ),
			'not_found'           => __( 'Ontwerpen Competentie not found', 'burst-cpt' ),
			'not_found_in_trash'  => __( 'Ontwerpen Competenties not found in bin', 'burst-cpt' ),
		),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => get_option( 'burst_cpt_base_ontwerpen' ) ), // This option has the id that is set in the cpt-permalinks plugin
		'menu_icon'			  => 'dashicons-awards',
	);

	$ondernemen = array(
		'label'               => __( 'Ondernemen Competenties', 'burst-cpt' ),
		'description'         => __( 'Ondernemen Competenties', 'burst-cpt' ),
		'labels'              => array(
			'name'                => __( 'Ondernemen Competenties', 'Post Type General Name', 'burst-cpt' ),
			'singular_name'       => __( 'Ondernemen Competentie', 'Post Type Singular Name', 'burst-cpt' ),
			'menu_name'           => __( 'Ondernemen Competenties', 'burst-cpt' ),
			'parent_item_colon'   => __( 'Parent Ondernemen Competenties', 'burst-cpt' ),
			'all_items'           => __( 'All Ondernemen Competenties', 'burst-cpt' ),
			'view_item'           => __( 'Show Ondernemen Competentie', 'burst-cpt' ),
			'add_new_item'        => __( 'Add a Ondernemen Competentie', 'burst-cpt' ),
			'add_new'             => __( 'Add', 'burst-cpt' ),
			'edit_item'           => __( 'Edit Ondernemen Competentie', 'burst-cpt' ),
			'update_item'         => __( 'Update Ondernemen Competentie', 'burst-cpt' ),
			'search_items'        => __( 'Find a Ondernemen Competentie', 'burst-cpt' ),
			'not_found'           => __( 'Ondernemen Competentie not found', 'burst-cpt' ),
			'not_found_in_trash'  => __( 'Ondernemen Competenties not found in bin', 'burst-cpt' ),
		),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => get_option( 'burst_cpt_base_ondernemen' ) ), // This option has the id that is set in the cpt-permalinks plugin
		'menu_icon'			  => 'dashicons-awards',
	);

	$betrokken = array(
		'label'               => __( 'Betrokken Competenties', 'burst-cpt' ),
		'description'         => __( 'Betrokken Competenties', 'burst-cpt' ),
		'labels'              => array(
			'name'                => __( 'Betrokken Competenties', 'Post Type General Name', 'burst-cpt' ),
			'singular_name'       => __( 'Betrokken Competentie', 'Post Type Singular Name', 'burst-cpt' ),
			'menu_name'           => __( 'Betrokken Competenties', 'burst-cpt' ),
			'parent_item_colon'   => __( 'Parent Betrokken Competenties', 'burst-cpt' ),
			'all_items'           => __( 'All Betrokken Competenties', 'burst-cpt' ),
			'view_item'           => __( 'Show Betrokken Competentie', 'burst-cpt' ),
			'add_new_item'        => __( 'Add a Betrokken Competentie', 'burst-cpt' ),
			'add_new'             => __( 'Add', 'burst-cpt' ),
			'edit_item'           => __( 'Edit Betrokken Competentie', 'burst-cpt' ),
			'update_item'         => __( 'Update Betrokken Competentie', 'burst-cpt' ),
			'search_items'        => __( 'Find a Betrokken Competentie', 'burst-cpt' ),
			'not_found'           => __( 'Betrokken Competentie not found', 'burst-cpt' ),
			'not_found_in_trash'  => __( 'Betrokken Competenties not found in bin', 'burst-cpt' ),
		),
		'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'revisions', 'page-attributes', ),
		'hierarchical'        => true,
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_in_rest'        => true,
		'capability_type'     => 'page',
		'rewrite'             => array( 'slug' => get_option( 'burst_cpt_base_betrokken' ) ), // This option has the id that is set in the cpt-permalinks plugin
		'menu_icon'			  => 'dashicons-awards',
	);

  // Registering Custom Post Type
	register_post_type( 'ontwikkelen', $ontwikkelen );
	register_post_type( 'onderzoeken', $onderzoeken );
	register_post_type( 'ontwerpen', $ontwerpen );
	register_post_type( 'ondernemen', $ondernemen );
	register_post_type( 'betrokken', $betrokken );
}
// add the action to create the custom post yypes.
// Third argument is the priority.. needs to be 9 or lower.
add_action( 'init', 'burst_custom_post_types', 0 );

/*
 * For some themes we don't want to see the default
 * posts post type so we hide it.
 * Set $burst_cpt_config['hide-default-posts'] to true if you like to hide it.
 */
function remove_menus() {
    remove_menu_page( 'edit.php' );
}
if($burst_cpt_config['hide-default-posts']){
  add_action( 'admin_menu', 'remove_menus' );
}