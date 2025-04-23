<?php

require_once 'class-iworks-theme-base.php';

class iWorks_User extends iWorks_Theme_Base {

	/**
	 * mam fields
	 */
	private $fields = array();

	public function __construct() {
		parent::__construct();
		/**
		 * hooks
		 */
		add_action( 'edit_user_profile', array( $this, 'action_edit_user_profile' ) );
		add_action( 'edit_user_profile_update', array( $this, 'action_edit_user_profile_update' ) );
		/**
		 * own
		 */
		add_filter( 'iworks_user_social_media', array( $this, 'filter_iworks_user_social_media' ), 10, 2 );
	}

	private function get_user_field_name( $name ) {
		return sprintf(
			'iworks_user_%s',
			$name
		);
	}

	/**
	 * fields
	 */
	private function set_fields() {
		$this->fields = apply_filters(
			'iworks_theme_user_fields',
			array(
				'facebook'  => __( 'Facebook', 'tadanero' ),
				'instagram' => __( 'Instagram', 'tadanero' ),
				'twitter'   => __( 'Twitter', 'tadanero' ),
				'linkedin'  => __( 'LinkedIn', 'tadanero' ),
			)
		);
	}

	public function action_edit_user_profile( $user ) {
		$this->set_fields();
		printf( '<h3 class="heading">%s</h3>', esc_html__( 'Social Media', 'tadanero' ) );
		echo '<table class="form-table">';
		foreach ( $this->fields as $name => $label ) {
			$name = $this->get_user_field_name( $name );
			echo '<tr>';
			printf(
				'<th><label for="%s">%s</label></th>',
				esc_attr( $name ),
				esc_html( $label )
			);
			echo '<td>';
			printf(
				'<input type="url" class="regular-text code" name="%1$s" id="%1$s" value="%2$s" />',
				esc_attr( $name ),
				get_user_meta( $user->ID, $name, true )
			);
			echo '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}

	public function action_edit_user_profile_update( $user_id ) {
		$this->set_fields();
		foreach ( $this->fields as $name => $label ) {
			$name  = $this->get_user_field_name( $name );
			$value = filter_input( INPUT_POST, $name, FILTER_SANITIZE_URL );
			update_user_meta( $user_id, $name, $value );
		}
	}

	public function get_output( $user_id ) {
		$user  = get_userdata( $user_id );
		$items = '';
		$this->set_fields();
		foreach ( $this->fields as $key => $label ) {
			$name  = $this->get_user_field_name( $key );
			$value = get_user_meta( $user->ID, $name, true );
			if ( empty( $value ) ) {
				continue;
			}
			if ( ! wp_http_validate_url( $value ) ) {
				continue;
			}
			$classes = array(
				'social-media-item',
				'social-media-' . $key,
			);
			$items  .= sprintf(
				'<li class="%s"><a href="%s" target="_blank"><span class="sr-only">%s</span></a></li>',
				esc_attr( implode( ' ', $classes ) ),
				esc_url( $value ),
				sprintf(
					esc_html__( '%1$s on %2$s', 'tadanero' ),
					$user->display_name,
					$label
				)
			);
		}
		if ( empty( $items ) ) {
			return '';
		}
		$content  = '<div class="social-media">';
		$content .= sprintf(
			'<div class="social-media-title">%s</div>',
			esc_html__( 'Find me on:', 'tadanero' )
		);
		$content .= '<ul class="social-media-items">';
		$content .= $items;
		$content .= '</div>';
		return $content;
	}

	public function filter_iworks_user_social_media( $content, $user_id ) {
		return $this->get_output( $user_id );

	}

}

