<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Tadanero
 */

?>

	<footer id="colophon" class="site-footer">
		<div class="site-footer-container">
			<div class="site-info">
<?php
wp_nav_menu(
	array(
		'theme_location'  => 'footer',
		'menu_id'         => 'footer-menu',
		'container_class' => 'footer-navigation-container',
		'depth'           => 1,
	)
);
?>
			</div>
			<div class="site-info">
<?php
wp_nav_menu(
	array(
		'theme_location'  => 'social',
		'menu_id'         => 'social-menu',
		'container_class' => 'social-navigation-container',
		'link_before'     => '<span class="sr-only">',
		'link_after'      => '</span>',
		'depth'           => 1,
	)
);
?>
			</div>
		</div>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
