<?php
/**
 * The default template for displaying overview items
 *
 * Used for All archives, searches and related posts index/archive/search.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */
global $theme_config;

// thumbnails
$thumbnail_id   = get_post_thumbnail_id($post->ID);
$thumbnail_url  = wp_get_attachment_image_src( $thumbnail_id, 'medium' );

// set the right URL and excerpt and read more text
$block_url      = get_the_permalink();
$block_excerpt  = burst_excerpt(140);
?>
<article <?php post_class(array('overview-item', 'image')); ?>>
    <a href="<?php echo $block_url; ?>" title="<?php echo get_the_title(); ?>">
        <div class="featured-image">
            <?php
            if(false === empty($thumbnail_url)){
                echo '<img src="' . $thumbnail_url[0] .'" alt="' . get_the_title() . '" />';
            }
            ?>
        </div>
        <div class="post-content">
            <h2><?php echo get_the_title(); ?></h2>
            <p>
                <?php echo $block_excerpt; ?>
            </p>
            <span class="button"><?php echo __('Read more', $theme_config['text-domain']); ?></span>
        </div>
    </a>
</article>
