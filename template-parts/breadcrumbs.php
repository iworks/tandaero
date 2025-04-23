<?php
if ( function_exists( 'bcn_display' ) ) {
	echo '<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">';
	bcn_display();
	echo '</div>';
}
?>
