<?php
require_once 'class-iworks-theme-base.php';

class iWorks_Cookie_Notice extends iWorks_Theme_Base {
	/**
	 * Module option name.
	 *
	 * @var string
	 */
	protected $option_name = '_iworks_cookie_notice';

	/**
	 * Cookie name string.
	 *
	 * @var string
	 */
	private $cookie_name = __CLASS__;

	/**
	 * User meta name.
	 *
	 * @var string
	 */
	private $user_meta_name = __CLASS__;


	private $data = array();

	/**
	 * Branda_Cookie_Notice constructor.
	 */
	public function __construct() {
		if ( is_admin() ) {
			return;
		}
		if ( $this->is_rest_request() ) {
			return;
		}
		parent::__construct();
		add_action( 'wp_footer', array( $this, 'add_cookie_notice' ), PHP_INT_MAX );
		add_action( 'wp_ajax_iworks_cookie_notice', array( $this, 'save_user_meta' ) );
		add_action( 'wp_ajax_nopriv_iworks_dismiss_visitor_notice', array( $this, 'dismiss_visitor_notice' ) );
	}

	private function set_data() {
		if ( ! function_exists( 'get_privacy_policy_url' ) ) {
			return;
		}
		$id         = 'iworks-cookie-notice';
		$this->data = apply_filters(
			'iworks/theme/cookie/data',
			array(
				'name'    => $id,
				'cookie'  => array(
					'domain'   => defined( 'COOKIE_DOMAIN' ) && COOKIE_DOMAIN ? COOKIE_DOMAIN : '',
					'name'     => $this->cookie_name,
					'path'     => defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/',
					'secure'   => is_ssl() ? 'on' : 'off',
					'timezone' => HOUR_IN_SECONDS * get_option( 'gmt_offset' ),
					'value'    => YEAR_IN_SECONDS,
				),
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'logged'  => is_user_logged_in() ? 'yes' : 'no',
				'user_id' => get_current_user_id(),
				'nonce'   => wp_create_nonce( __CLASS__ ),
				'text'    => sprintf(
					__( 'We use cookies and similar technologies to provide services and to gather information for statistical and other purposes. You can change the way you want the cookies to be stored or accessed on your device in the settings of your browser. If you do not agree, change the settings of your browser. For more information, refer to our %s.', 'tadanero' ),
					sprintf(
						'<a href="%s">%s</a>',
						get_privacy_policy_url(),
						_x( 'Privacy Policy', 'in cookie message, mayby propoer form', 'tadanero' )
					)
				),
			)
		);
	}

	/**
	 * Cookie notice output.
	 *
	 * @since 1.0.0
	 */
	public function add_cookie_notice() {
		$show = $this->show_cookie_notice();
		if ( ! $show ) {
			return;
		}
		$this->set_data();
		$content  = sprintf(
			'<div id="%s" role="banner">',
			$this->data['name']
		);
		$content .= sprintf(
			'<div class="cookie-notice-container"><span id="iworks-cn-notice-text" class="text">%s</span>',
			$this->data['text']
		);
		// Data.
		$content .= sprintf(
			'<span><a href="#" class="button iworks-cn-set-cookie" aria-label="%s">%s</a></span>',
			esc_attr__( 'Close cookie information.', 'tadanero' ),
			esc_html__( 'Close cookie information', 'tadanero' )
		);
		$content .= '</div>';
		$content .= '</div>';
		$content .= PHP_EOL;
		echo apply_filters( 'iworks_cookie_notice_output', $content, $this->data );
		/**
		 * cookie js data
		 */
		echo '<script id="iworks-cookie-notice-js">';
		printf( 'window.iworks_cookie = %s;', wp_json_encode( $this->data ) );
		echo '</script>';
		echo PHP_EOL;
	}

	/**
	 * Get current time.
	 *
	 * @return int|string
	 */
	private function get_now() {
		return current_time( 'timestamp' ) - HOUR_IN_SECONDS * get_option( 'gmt_offset' );
	}

	/**
	 * Show cookie notice?
	 *
	 * @since 1.0.0
	 */
	private function show_cookie_notice() {
		$time = filter_input( INPUT_COOKIE, $this->cookie_name, FILTER_SANITIZE_NUMBER_INT );
		if ( ! empty( $time ) ) {
			$now = $this->get_now();
			if ( $time > $now ) {
				return false;
			}
		}
		// Check settings for logged user.
		if ( is_user_logged_in() ) {
			$user_time = 0;
			$time      = get_user_meta( get_current_user_id(), $this->user_meta_name, true );
			$key       = $this->get_meta_key_name();
			if ( isset( $time[ $key ] ) ) {
				$user_time = intval( $time[ $key ] );
			}
			if ( 0 < $user_time ) {
				$now = $this->get_now();
				if ( $user_time > $now ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Get user meta key name.
	 *
	 * @param null|int $blog_id Blog ID.
	 *
	 * @return string
	 */
	private function get_meta_key_name( $blog_id = null ) {
		if ( empty( $blog_id ) ) {
			$blog_id = get_current_blog_id();
		}
		$key = sprintf(
			'blog_%d_version_%s',
			$blog_id,
			sanitize_title( $this->version )
		);
		return $key;
	}

	/**
	 * Save user meta info about cookie.
	 *
	 * @since 1.0.0
	 */
	public function save_user_meta() {
		if ( ! isset( $_POST['nonce'] ) ) {
			wp_send_json_error( 'missing nonce' );
		}
		if ( ! wp_verify_nonce( $_POST['nonce'], __CLASS__ ) ) {
			wp_send_json_error( 'wrong nonce' );
		}
		if ( ! isset( $_POST['user_id'] ) ) {
			wp_send_json_error( 'missing user ID' );
		}
		$value   = current_time( 'timestamp' ) + intval( $this->data['cookie']['value'] );
		$user_id = filter_input( INPUT_POST, 'user_id', FILTER_SANITIZE_NUMBER_INT );
		if ( 0 < $user_id ) {
			$time = get_user_meta( $user_id, $this->user_meta_name, true );
			if ( ! is_array( $time ) ) {
				$time = array();
			}
			$key          = $this->get_meta_key_name();
			$time[ $key ] = $value;
			update_user_meta( $_POST['user_id'], $this->user_meta_name, $time );
			// Clear caches.
			$this->clear_cache();
		}
		wp_send_json_success();
	}

	/**
	 * Dismiss the cookie notice for visitor.
	 *
	 * To dismiss cookie notice, we need to clear caches
	 * if HB is active.
	 *
	 * @since 1.0.0
	 */
	public function dismiss_visitor_notice() {
		// Verify nonce first.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], __CLASS__ ) ) {
			wp_send_json_error( 'invalid nonce' );
		}
		// Clear caches.
		$this->clear_cache();
		// Send a success notice.
		wp_send_json_success();
	}

	/**
	 * Clear cache to hide cookie notice.
	 *
	 * We should clear the page cache when cookie notice is
	 * dismissed by a visitor. Otherwise it will keep on showing
	 * the notice even after dismissal.
	 *
	 * @since 1.0.0
	 */
	private function clear_cache() {
		// Clear HB cache.
		do_action( 'wphb_clear_page_cache' );
	}

	/**
	 * Check Privacy policy page
	 *
	 * @since 1.0.0
	 */
	private function check_privacy_policy_page() {
		$policy_page_id = (int) get_option( 'wp_page_for_privacy_policy' );
		if ( ! empty( $policy_page_id ) && 'publish' === get_post_status( $policy_page_id ) ) {
			return true;
		}
		return false;
	}
}
