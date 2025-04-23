<?php
/**
 * WP Cron
 *
 * use __return_true to turn it on
 */
add_filter( 'iworks/theme/load-wp-cron', '__return_false' );

/**
 * cookie
 */
add_filter( 'iworks/theme/load-cookies', '__return_false' );

/**
 * Table of Content
 */
add_filter( 'iworks/theme/load-toc', '__return_false' );

/**
 * Cache Support
 */
add_filter( 'iworks/theme/load-cache', '__return_false' );

/**
 * Load theme class
 */
require_once get_template_directory() . '/inc/class-iworks-theme.php';
new iWorks_Theme;

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

