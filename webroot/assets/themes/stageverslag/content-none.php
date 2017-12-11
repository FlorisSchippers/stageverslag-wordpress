<?php
/**
 * The default template for displaying content
 *
 * Used for both single and index/archive/search.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */
global $theme_config;
?>
<div class="container">
    <h1><?php echo __('No results found', $theme_config['text-domain']); ?></h1>
    <?php get_search_form(); ?>
</div>