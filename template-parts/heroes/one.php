<?php
$extra = '';
if ( has_post_thumbnail() ) {
    $post_thumbnail_id = get_post_thumbnail_id();
    $color = apply_filters( 'iworks_aggresive_lazy_load_get_dominant_color', false, $post_thumbnail_id );
    $thumb = apply_filters( 'iworks_aggresive_lazy_load_get_tiny_thumbnail', false, $post_thumbnail_id );
    $url = get_the_post_thumbnail_url( get_the_ID(), 'full' );
    if ( $color && $thumb ) {
        $extra = sprintf(
            'style="background-image:url(%s);background-color:%s"',
            $thumb,
            'transparent' === $color? $color:sprintf( '#%s', $color )
        );
        $extra .= sprintf( ' data-src="%s"', esc_attr( $url ) );
    } else {
        $extra = sprintf( 'style="background-image:url(\'%s\')"', $url);
    }
}
?>
<li class="<?php echo esc_attr( implode( ' ', get_post_class() ) ); ?>">
	<a href="<?php the_permalink(); ?>">
		<span class="iworks-heroes-thumbnail" <?php echo $extra; ?>></span>
		<?php the_title( '<h4 class="iworks-heroes-name">', '</h4>' ); ?>
	</a>
<?php if ( ! is_front_page() ) { ?>
	<div class="iworks-heroes-content"><?php the_content(); ?></div>
<?php } ?>
</li>
