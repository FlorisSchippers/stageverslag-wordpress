<?php
/**
 * Custom template tags for Burst
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */

if ( ! function_exists( 'burst_comment_nav' ) ) :
/**
 * Display navigation to next/previous comments when applicable.
 *
 * @since Burst 1.0
 */
function burst_comment_nav() {
  global $theme_config;
	// Are there comments to navigate through?
	if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
	?>
	<nav class="navigation comment-navigation" role="navigation">
		<h2 class="screen-reader-text"><?php _e( 'Comment navigation', $theme_config['text-domain'] ); ?></h2>
		<div class="nav-links">
			<?php
				if ( $prev_link = get_previous_comments_link( __( 'Older Comments', $theme_config['text-domain'] ) ) ) :
					printf( '<div class="nav-previous">%s</div>', $prev_link );
				endif;

				if ( $next_link = get_next_comments_link( __( 'Newer Comments', $theme_config['text-domain'] ) ) ) :
					printf( '<div class="nav-next">%s</div>', $next_link );
				endif;
			?>
		</div><!-- .nav-links -->
	</nav><!-- .comment-navigation -->
	<?php
	endif;
}
endif;


if ( ! function_exists( 'burst_post_thumbnail' ) ) :
/**
 * Display an optional post thumbnail.
 *
 * Wraps the post thumbnail in an anchor element on index views, or a div
 * element when on single views.
 *
 * @since Burst 1.0
 */
function burst_post_thumbnail() {
  global $theme_config;
	if ( post_password_required() || is_attachment() || ! has_post_thumbnail() ) {
		return;
	}

	if ( is_singular() ) :
	?>

	<div class="post-thumbnail">
		<?php the_post_thumbnail(); ?>
	</div><!-- .post-thumbnail -->

	<?php else : ?>

	<a class="post-thumbnail" href="<?php the_permalink(); ?>" aria-hidden="true">
		<?php
			the_post_thumbnail( 'post-thumbnail', array( 'alt' => get_the_title() ) );
		?>
	</a>

	<?php endif; // End is_singular()
}
endif;

if ( ! function_exists( 'burst_get_link_url' ) ) :
/**
 * Return the post URL.
 *
 * Falls back to the post permalink if no URL is found in the post.
 *
 * @since Burst 1.0
 *
 * @see get_url_in_content()
 *
 * @return string The Link format URL.
 */
function burst_get_link_url() {
  global $theme_config;
	$has_url = get_url_in_content( get_the_content() );

	return $has_url ? $has_url : apply_filters( 'the_permalink', get_permalink() );
}
endif;

if ( ! function_exists( 'burst_excerpt_more' ) && ! is_admin() ) :
/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
 *
 * @since Burst 1.0
 *
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function burst_excerpt_more( $more ) {
  global $theme_config;
	$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading %s', $theme_config['text-domain'] ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'burst_excerpt_more' );
endif;

/**
 * burst_excerpt
 * - Limit the length of excerpt to any given length
 *
 **/
function burst_excerpt($limit) {
    global $post;
    $excerpt = get_the_excerpt($post);
    $text = strip_tags($excerpt);

    if('' === $text){
        $text = apply_filters( 'the_content', get_the_content() );
    }

    $excerpt = explode('...',$text);
    $excerpt = $excerpt[0];

    if (strlen($text) >= $limit) {
        $excerpt = substr($excerpt, 0, $limit) . '...';
    }
    $excerpt = preg_replace('`[[^]]*]`','',$excerpt);

    return $excerpt;
}

/*
 * Share bar
 */
 if( !function_exists('burst_get_share_bar')){
    function burst_get_share_bar (){
        global $theme_config, $post;
        $html = '<div class="content">
                    <div class="container">
                        <ul>
                            <li>' . __('Dit artikel delen:', $theme_config['text-domain']) . '</li>
                            <li><a href="https://twitter.com/share?url='. urlencode(get_the_permalink($post->ID)) .'&text=' . urlencode(get_the_title()) . '" target="_blank" class="icon-item">'. burst_get_svg_icons('twitter', __('Share on Twitter', $theme_config['text-domain'])) . '</a></li>
                            <li><a href="http://www.facebook.com/sharer.php?u=' .  urlencode(get_the_permalink()) .'&t='. urlencode(get_the_title()) .'" target="_blank" class="icon-item">'. burst_get_svg_icons('facebook', __('Share on Facebook', $theme_config['text-domain'])) . '</a></li>
                            <li><a href="mailto:example@example.com?body=' .  urlencode(get_the_permalink()) .'&t='. urlencode(get_the_title()) .'" target="_blank" class="icon-item">' . burst_get_svg_icons('mail', __('Deel via e-mail', $theme_config['text-domain'])) . '</a></li>
                            <li><a href="whatsapp://send?text=' . urlencode(get_the_permalink()) . '" class="icon-item only-mobile" target="_blank">'. burst_get_svg_icons('whatsapp', __('Share on Whatsapp', $theme_config['text-domain'])) . '</a></li>
                        </ul>
                    </div>
                </div>';
        return $html;
    }
}

/*
* Get SVG icons
*/
if(!function_exists('burst_get_svg_icons')){
    function burst_get_svg_icons($name, $title){
      return '<svg role="img" class="icon '. $name .'" title="' . $title . '">
            <use xlink:href="#'.$name .'"/>
          </svg>';
    }
}
