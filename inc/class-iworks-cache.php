<?php

class iWorks_Cache {

	/**
	 * option name
	 */
	protected $option_name = '';

	/**
	 * Class version used with assets enqueue functions.
	 *
	 * @since 1.0.0
	 * @var string $version Class version.
	 */
	protected $version = 'THEME_VERSION';

	/**
	 * Keys to clear for `wp_cache_delete` function.
	 *
	 * @since 1.0.0
	 */
	protected $cache_keys = array();

	/**
	 * Debug
	 *
	 * @since 1.0.0
	 */
	protected $debug = false;


	/**
	 * Current language
	 *
	 * @since 1.0.0
	 */
	protected $current_language;

	public function __construct() {
		$this->option_name      = crc32( get_bloginfo( 'url' ) );
		$this->current_language = apply_filters( 'wpml_current_language', null ); // Store current language
		/**
		 * Allow to set plugin debug independently to WP_DEBUG.
		 *
		 * @since 1.0.0
		 *
		 * @param boolean debug?
		 */
		// $this->debug = apply_filters( 'iworks_debug', defined( 'WP_DEBUG' ) && WP_DEBUG );
		/**
		 * WordPress Hooks
		 */
		add_action( 'save_post', array( $this, 'clear_cache' ) );
		/**
		 * iWorks memcache Hooks
		 */
		add_filter( 'iworks_cache_get', array( $this, 'filter_get_value' ), 10, 2 );
		add_action( 'iworks_cache_set', array( $this, 'action_set_cache' ), 10, 2 );
		add_action( 'iworks_cache_keys', array( $this, 'action_add_keys' ), 10, 1 );
	}

	public function action_set_cache( $name, $value ) {
		$key = $this->get_cache_key( $name );
		if ( empty( $value ) ) {
			$this->cache_clear( $key );
		} else {
			$this->cache_set( $key, $value );
		}
	}

	public function filter_get_value( $content, $name ) {
		$key = $this->get_cache_key( $name );
		$c   = $this->cache_get( $key );
		if ( empty( $c ) ) {
			return $content;
		}
		return $c;
	}

	/**
	 * Clear cache helper.
	 * It is used during save_post action.
	 */
	public function clear_cache( $post_id ) {
		if ( ! function_exists( 'wp_cache_delete' ) ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}
		$this->raw_clear_cache( $post_id );
	}

	/**
	 * Clear cache without questoions!
	 *
	 * Helper for public `clear_cache()` function, does not check is version,
	 * post_type or sth, just clear cache for post and all parents.
	 *
	 * @since 1.0.0
	 */
	private function raw_clear_cache( $post_id ) {
		if ( ! function_exists( 'wp_cache_delete' ) ) {
			return;
		}
		wp_cache_delete( $this->get_cache_key( $post_id ) );
		$parent_id = intval( wp_get_post_parent_id( $post_id ) );
		if ( 0 < $parent_id ) {
			$this->raw_clear_cache( $parent_id );
		}
	}

	/**
	 * Get cached shortcode content
	 *
	 * @since 1.0.0
	 */
	protected function cache_get( $key ) {
		if ( $this->debug ) {
			return null;
		}
		if ( ! function_exists( 'wp_cache_get' ) ) {
			return null;
		}
		return wp_cache_get( $key );
	}

	/**
	 * set cached shortcode content
	 *
	 * @since 1.0.0
	 */
	protected function cache_set( $key, $content ) {
		if ( ! function_exists( 'wp_cache_set' ) ) {
			return;
		}
		wp_cache_set( $key, $content );
	}

	/**
	 * clear group cache helper
	 *
	 * @since 1.0.0
	 */
	protected function cache_clear( $keys ) {
		if ( ! function_exists( 'wp_cache_delete' ) ) {
			return;
		}
		if ( ! is_array( $keys ) ) {
			$keys = array( $keys );
		}
		foreach ( $keys as $key ) {
			wp_cache_delete( $key );
		}
	}

	/**
	 * get cache key
	 *
	 * @since 1.0.0
	 *
	 * @param string $key base kasy
	 */
	protected function get_cache_key( $key, $lang = null ) {
		if ( empty( $lang ) ) {
			$lang = $this->current_language;
		}
		$key = sprintf( 'i%s%s', $this->option_name, $key );
		if ( ! empty( $lang ) ) {
			$key .= $lang;
		}
		if ( ! in_array( $key, $this->cache_keys ) ) {
			$this->cache_keys[] = $key;
		}
		return $key;
	}

	/**
	 * add cache key to list of cache keys
	 *
	 * @since 1.0.0
	 *
	 */
	public function action_add_keys( $name ) {
		$key = $this->get_cache_key( $name );
	}

}
