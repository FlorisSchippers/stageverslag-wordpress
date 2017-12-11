<?php
/**
 * Template Name: Homepage
 **/
global $theme_config;
get_header(); ?>
<?php
    // First block: Content from Wordpress
?>
<?php if ( have_posts() ) : ?>

			<?php
			// Start the Loop.
			while ( have_posts() ) : the_post();

				/*
				 * Include the Post-Format-specific template for the content.
				 * If you want to override this in a child theme, then include a file
				 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
				 */
				get_template_part( 'content', get_post_format() );

			// End the loop.
			endwhile;

			// Previous/next page navigation.
			the_posts_pagination( array(
				'prev_text'          => __( 'Previous page', $theme_config['text-domain'] ),
				'next_text'          => __( 'Next page', $theme_config['text-domain'] ),
				'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', $theme_config['text-domain'] ) . ' </span>',
			) );

		// If no content, include the "No posts found" template.
		else :
			get_template_part( 'content', 'none' );

		endif;
		?>
<?php get_footer(); ?>