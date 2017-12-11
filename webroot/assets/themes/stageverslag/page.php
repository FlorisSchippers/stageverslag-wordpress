<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */

get_header(); ?>
<div class="container">
<?php
	// Start the loop.
	while ( have_posts() ) : the_post();

		// Include the page content template.
		get_template_part( 'content', '' );
	// End the loop.
	endwhile;
?>
</div>
<?php get_footer(); ?>
