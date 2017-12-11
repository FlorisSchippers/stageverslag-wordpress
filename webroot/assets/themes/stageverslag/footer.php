<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the "site-content" div and all content after.
 *
 * @package WordPress
 * @subpackage Burst
 * @since Burst 1.0
 */
?>
			</main>
			<footer id="footer">
				<div class="container">
					<?php
						// Social links navigation menu.
						wp_nav_menu( array(
							'theme_location' => 'footer_nav',
							'depth'          => 0,
							'link_before'    => '<span class="screen-reader-text">',
							'link_after'     => '</span>',
						) );
					?>
				</div>
			</footer>
		</div><!--/ .main-wrapper -->
	</div><!--/ #inner-wrap -->
</div><!--/ #outer-wrap -->

		<?php wp_footer(); ?>

	</body>
</html>
