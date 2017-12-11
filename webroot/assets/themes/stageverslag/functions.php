<?php
/**
 * Burst functions and definitions
 *
 * Set up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 * When using a child theme you can override certain functions (those wrapped
 * in a function_exists() call) by defining them first in your child theme's
 * functions.php file. The child theme's functions.php file is included before
 * the parent theme's file, so the child theme functions would be used.
 *
 * @link https://codex.wordpress.org/Theme_Development
 * @link https://codex.wordpress.org/Child_Themes
 *
 * Functions that are not pluggable (not wrapped in function_exists()) are
 * instead attached to a filter or action hook.
 *
 * For more information on hooks, actions, and filters,
 * {@link https://codex.wordpress.org/Plugin_API}
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */

/*
 * Define Theme basic Configuration
 */
global $theme_config;
$theme_config = array();
$theme_config['theme-name'] = 'burst';
$theme_config['text-domain'] = 'burst';
/**
 * Set the content width based on the theme's design and stylesheet.
 *
 * @since Burst 1.0
 */
if ( ! isset( $content_width ) ) {
	$content_width = 660;
}



if ( ! function_exists( 'burst_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 *
 * @since Burst 1.0
 */
function burst_setup() {
  global $theme_config;
   /*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on burst, use a find and replace
	 * to change $theme_config['theme-name'] to the name of your theme in all the template files
	 */
	load_theme_textdomain( $theme_config['theme-name'], get_template_directory() . '/languages' );

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * See: https://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
	 */
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 825, 510, true );

	// This theme uses wp_nav_menu() in two locations.
	register_nav_menus( array(
		'main_nav' => __( 'Main Navigation', $theme_config['text-domain'] ),
		'footer_nav'  => __( 'Footer Menu', $theme_config['text-domain'] ),
	) );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support( 'html5', array(
		'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
	) );

	/*
	 * Enable support for Post Formats.
	 *
	 * See: https://codex.wordpress.org/Post_Formats
	 */
	add_theme_support( 'post-formats', array(
		'aside', 'image', 'video', 'quote', 'link', 'gallery', 'status', 'audio', 'chat'
	) );
}
endif; // burst_setup
add_action( 'after_setup_theme', 'burst_setup' );

/**
 * Register widget area.
 *
 * @since Burst 1.0
 *
 * @link https://codex.wordpress.org/Function_Reference/register_sidebar
 */
function burst_widgets_init() {
  global $theme_config;
	register_sidebar( array(
		'name'          => __( 'Momenten', $theme_config['text-domain'] ),
		'id'            => 'moments-sidebar',
		'description'   => __( 'This sidebar shows the moments.', $theme_config['text-domain'] ),
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'burst_widgets_init' );

/**
 * Enqueue scripts and styles.
 *
 * @since Burst 1.0
 */
function burst_scripts() {
  global $theme_config;
	// Add custom fonts, used in the main stylesheet.
	wp_enqueue_style( 'font-style', '//fonts.googleapis.com/css?family=Droid+Sans:400,700', array(), null );
	// wp_enqueue_style( 'std-css', '//p.stdcss.com/0.0.7/std.min.css', array(), null ); ZIT IN DE SASS AHGI

	// Load our main stylesheet.
    if(env('WP_ENV') !== 'development'){
        wp_enqueue_style( 'main-style', get_stylesheet_uri() );
    }

	wp_enqueue_script( 'burst-script', get_template_directory_uri() . '/js/site.js', array( 'jquery' ), '20150330', true );

}
add_action( 'wp_enqueue_scripts', 'burst_scripts' );


// REMOVE WP EMOJI
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );

/**
 * Custom template tags for this theme.
 *
 * @since Burst 1.0
 */
require get_template_directory() . '/inc/template-tags.php';
