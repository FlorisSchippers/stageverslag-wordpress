<?php
/**
 * The template for displaying the header
 *
 * Displays all of the head element and everything up until the "site-content" div.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */

/**
 * Logo (clickable on all pages but home...)
 */
$sLogoPNG	= get_template_directory_uri().'/images/logo/logo.png';
$sLogoSVG	= get_template_directory_uri().'/images/logo/logo.svg';
$sLogo		= '<img onerror="this.onerror=null; this.src=\''.$sLogoPNG.'\'" src="'.$sLogoSVG.'" alt="Your" id="logo" />';
if (false === is_home()) {
	$sLogo	= '<a href="/">'.$sLogo.'</a>';
}

?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<!--[if lt IE 9]>
	<script src="<?php echo esc_url( get_template_directory_uri() ); ?>/js/html5.js"></script>
	<![endif]-->
	<!-- Webpack Javascript -->
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<!-- Off-canvas-menu -->
<div id="outer-wrap">
	<div id="inner-wrap" class="item-slide">

	<header id="main-header" class="item-slide">
        <div class="container">
    		<?php echo $sLogo; ?>
    		<a class="nav-trigger only-mobile"href="#">Menu</a>
            <nav id="main-nav">
                <?php
                    // Social links navigation menu.
                    wp_nav_menu( array(
                        'theme_location' => 'main_nav',
                        'depth'          => 0,
                        'link_before'    => '<span class="screen-reader-text">',
                        'link_after'     => '</span>',
                    ) );
                ?>
            </nav>
            <!-- Seach -->
            <form method="get" action="/" id="search">
                <input type="text" name="q" placeholder="Zoeken..." />
                <button type="submit" class="button">Zoeken</button>
            </form>
        </div>
	</header>
	<div class="main-wrapper">
		<main id="main" class="site-main" role="main">
		<?php
		/*
		 * two Columns

		<div class="container">
			<section class="wrapper post-feed">
				<div id="content" class="main-content">
					posts
				</div>
			</section>
			<aside class="right-sidebar">
				<?php get_sidebar(); ?>
			</aside>
			<br class="break">
		</div>
		<?php
		/*
		 * One Column
		<div class="container">
			<section class="wrapper post-feed">
				<div id="content" class="main-content">
					posts
				</div>
			</section>
		</div>
	</main>


		 **/
		?>
