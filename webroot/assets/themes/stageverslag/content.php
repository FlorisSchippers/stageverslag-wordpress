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
<article <?php post_class(); ?>>
	<?php
		// Post thumbnail.
		burst_post_thumbnail();

        echo '<h1>'.get_the_title().'</h1>';
    ?>
	<div class="post-content">
		<?php
			/* translators: %s: Name of current post */
            echo apply_filters('the_content', get_the_content());
		?>
	</div>
</article>
