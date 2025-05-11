<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Tadanero
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">
	<?php tadanero_post_thumbnail(); ?>
	<header class="entry-header">
		<?php
			the_title( '<h2 class="entry-title">', '</h2>' );
		?>
	</header><!-- .entry-header -->
	<div class="entry-content"><?php the_excerpt(); ?></div><!-- .entry-content -->
	</a>
	<footer class="entry-footer">
<?php
		tadanero_posted_on();
		tadanero_posted_by();
?>
	</footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
