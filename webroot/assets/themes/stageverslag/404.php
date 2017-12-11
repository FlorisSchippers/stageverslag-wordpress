<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */
 
get_header(); ?>

<section class="error-404 not-found">
    <header class="page-header">
        <h1 class="page-title"><?php _e( 'Oops! That page can&rsquo;t be found.', $theme_config['text-domain'] ); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <p><?php _e( 'It looks like nothing was found at this location. Maybe try a search?', $theme_config['text-domain'] ); ?></p>

        <?php get_search_form(); ?>
    </div><!-- .page-content -->
</section><!-- .error-404 -->


<?php get_footer(); ?>
