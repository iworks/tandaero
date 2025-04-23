<?php

abstract class iWorks_Theme_Base {

	/**
	 * Theme option name
	 *
	 * @since 1.0.0
	 */
	protected $option_name = 'iw';

	/**
	 * Theme root.
	 *
	 * @since 1.0.0
	 * @var string $option_name_icon Option name ICON.
	 */
	protected $root = '';

	/**
	 * Child theme url.
	 *
	 * @since 1.0.0
	 * @var string $option_name_icon Option name ICON.
	 */
	protected $url = '';

	/**
	 * Option name, used to save data on postmeta table.
	 *
	 * @since 1.0.0
	 * @var string $version Theme version.
	 */
	protected $version = 'THEME_VERSION';

	/**
	 * Debug
	 *
	 * @since 1.0.0
	 * @var boolean $debug
	 */
	protected $debug = 'false';

	/**
	 * media
	 *
	 */
	protected $option_name_media = '_iworks_media';

	/**
	 * Nounce value
	 */
	protected $nonce_value = '4PufQi59LMAEnB1yp3r4m6y9x49RbIUy';

	/**
	 * Array for settings
	 *
	 * @since 1.0.0
	 */
	private array $settings_fields;

	protected function __construct() {
		$child_version = wp_get_theme();
		$this->version = $child_version->Version;
		$this->url     = get_stylesheet_directory_uri();
		$this->debug   = apply_filters( 'iworks_debug_theme', defined( 'WP_DEBUG' ) && WP_DEBUG );
		/**
		 * Settings Fields Array
		 */
		$this->settings_fields = apply_filters(
			'iworks/tadanero/settings-fields/array',
			array(
				'general' => array(
					'default' => array (
						'section' => 'default',
						'fields' => array(
							'version' => array(
								'type' => 'string', // Valid values are 'string', 'boolean', 'integer', 'number', 'array', and 'object'.
								'default' => '0.0.0.0',
								'label' => esc_html__( 'Version', 'tadanero' ),
							),
							'foo' => array(
								'type' => 'text',
								'label' => esc_html__( 'Name', 'tadanero' ),
							),
						),
					),
				),
				'writing' => array(
					'default' => array(
						'fields'=>array(),
					),
					'post_via_email' => array(
						'fields'=>array(),
					),
				),
				'reading' => array(
					'default' => array(
						'fields'=>array(),
					),
				),
				'discussion' => array(
					'default' => array(
						'fields'=>array(),
					),
					'avatars' => array(
						'fields'=>array(),
					),
				),
				'media' => array(
					'default' => array(
						'fields'=>array(),
					),
					'embeds' => array(
						'fields'=>array(),
					),
					'uploads' => array(
						'fields'=>array(),
					),
				),
				'permalink' => array(
					'optional' => array(
						'fields'=>array(),
					),
				),
			)
		);
	}

	/**
	 * Get settings_fields because it is private
	 */
	protected function get_settings_fields() {
		return $this->settings_fields;
	}

	/**
	 * Check is login page
	 */
	protected function is_wp_login() {
		$ABSPATH_MY = str_replace( array( '\\', '/' ), DIRECTORY_SEPARATOR, ABSPATH );
		return (
			(
				in_array( $ABSPATH_MY . 'wp-login.php', get_included_files() )
				|| in_array( $ABSPATH_MY . 'wp-register.php', get_included_files() )
			) || (
				isset( $_GLOBALS['pagenow'] )
				&& $GLOBALS['pagenow'] === 'wp-login.php'
			)
				|| $_SERVER['PHP_SELF'] == '/wp-login.php'
		);
	}

	/**
	 * Get assets URL
	 *
	 * @since 1.0.0
	 *
	 * @param string $file File name.
	 * @param string $group Group, default "images".
	 * @param boolean $add_version Add theme version, default "false", but always true for images.
	 *
	 * @return string URL into asset.
	 */
	protected function get_asset_url( $file, $group = 'images', $add_version = false ) {
		$url = sprintf(
			'%s/assets/%s/%s',
			$this->url,
			$group,
			$file
		);
		/**
		 * add version
		 */
		if (
			$add_version
			|| 'images' === $group
		) {
			$url = add_query_arg(
				array(
					'ver' => $this->version,
				),
				$url
			);
		}
		return esc_url( $url );
	}

	/**
	 * Build nonce name
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Name
	 * @param string $prefix Prefix of a name
	 *
	 * @return string Nonce name.
	 */
	protected function get_nonce_name( $value, $prefix = '' ) {
		if ( empty( $prefix ) ) {
			return sprintf( 'iworks-theme-%s', $value );
		}
		return sprintf( 'iworks-theme-%s-%s', $prefix, $value );
	}

	/**
	 * Media files
	 *
	 * @since 1.0.0
	 */
	public function html_media( $post ) {
		wp_enqueue_media();
		wp_nonce_field( $this->nonce_value, '_iworks_media_nonce' );
		$rows_name = 'iworks-media-file-rows';
		$classes   = array(
			'iworks-media-container',
			'image-wrapper',
			empty( $src ) ? '' : ( 0 < $value ? ' has-file' : ' has-old-file' ),
		);
		printf(
			'<div data-rows="%s" class="%s">',
			esc_attr( $rows_name ),
			esc_attr( implode( ' ', $classes ) )
		);
		echo '<p>';
		printf(
			'<button type="button" class="button button-add-file">%s</button>',
			esc_html__( 'Add file', 'tadanero' )
		);
		echo '</p>';
		printf(
			'<div class="%s" aria-hidden="true">',
			esc_attr( $rows_name )
		);
		$value = get_post_meta( $post->ID, $this->option_name_media, true );
		if ( is_array( $value ) ) {
			foreach ( $value as $attachment_ID ) {
				$this->media_row( $this->get_attachment_data( $attachment_ID ) );
			}
		}
		echo '</div>';
		echo '</div>';
		echo '<script type="text/html" id="tmpl-iworks-media-file-row">';
		$this->media_row();
		echo '</script>';
	}

	/**
	 * Media row helper
	 *
	 * @since 1.0.0
	 */
	protected function media_row( $data = array() ) {
		$data = wp_parse_args(
			$data,
			array(
				'id'      => '{{{data.id}}}',
				'type'    => '{{{data.type}}}',
				'subtype' => '{{{data.subtype}}}',
				'url'     => '{{{data.url}}}',
				'icon'    => '{{{data.icon}}}',
				'caption' => '{{{data.caption}}}',
			)
		);
		?>
	<div class="iworks-media-file-row">
		<span class="dashicons dashicons-move"></span>
		<span class="icon iworks-media-<?php echo esc_attr( $data['type'] ); ?>-<?php echo esc_attr( $data['subtype'] ); ?>" style="background-image:url(<?php echo esc_attr( $data['icon'] ); ?>"></span>
		<span><a href="<?php echo esc_attr( $data['url'] ); ?>" target="_blank"><?php echo esc_html( $data['url'] ); ?></a><br /><small><?php echo esc_html( $data['caption'] ); ?></small></span>
		<button type="button" aria-label="<?php esc_attr_e( 'Remove file', 'tadanero' ); ?>"><span class="dashicons dashicons-trash"></span></button>
		<input type="hidden" name="<?php echo esc_attr( $this->option_name_media ); ?>[]" value="<?php echo esc_attr( $data['id'] ); ?>" />
	</div>
		<?php
	}

	/**
	 * Add post meta
	 */
	protected function update_meta( $post_id, $option_name, $option_value ) {
		if ( empty( $option_value ) ) {
			delete_post_meta( $post_id, $option_name );
			return;
		}
		$result = add_post_meta( $post_id, $option_name, $option_value, true );
		if ( ! $result ) {
			update_post_meta( $post_id, $option_name, $option_value );
		}
	}

	private function get_attachment_data( $attachment_ID ) {
		$content_type = explode( '/', get_post_mime_type( $attachment_ID ) );
		if ( ! is_array( $content_type ) ) {
			$content_type = array(
				'unknown',
				'unknown',
			);
		}
		return         array(
			'id'      => $attachment_ID,
			'caption' => wp_get_attachment_caption( $attachment_ID ),
			'url'     => wp_get_attachment_url( $attachment_ID ),
			'type'    => $content_type[0],
			'subtype' => isset( $content_type[1] ) ? $content_type[1] : '',
			'icon'    => wp_get_attachment_image_src( $attachment_ID, 'thumbnail', true )[0],
		);
	}

	/**
	 * get file content
	 *
	 * @since 2.0.0
	 *
	 * @param string $path Path to file.
	 */
	protected function get_file( $path ) {
		$path = realpath( $path );
		if ( ! $path || ! @is_file( $path ) ) {
			return '';
		}
		return @file_get_contents( $path );
	}

	protected function add_meta_box_meta_description( $post_type ) {
		add_meta_box(
			'opi-meta-description',
			__( 'HTML Meta Description', 'tadanero' ),
			array( $this, 'meta_bo_description_html' ),
			$post_type,
			'normal',
			'default'
		);
	}

	public function meta_bo_description_html( $post ) {
		wp_nonce_field( $this->option_name_meta_description . '_nonce', 'meta_description_nonce' );
		$value = get_post_meta( $post->ID, $this->option_name_meta_description, true );
		printf(
			'<textarea name="%s" rows="5" class="large-text code">%s</textarea>',
			esc_attr( $this->option_name_meta_description ),
			esc_html( $value )
		);
	}

	/**
	 * Save meta Description;
	 *
	 * @since 2.0.0
	 *
	 * @param integer $post_id Post ID.
	 */
	public function save_meta_description( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		$nonce = filter_input( INPUT_POST, 'meta_description_nonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, $this->option_name_meta_description . '_nonce' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$value = trim( preg_replace( '/\s+/', ' ', filter_input( INPUT_POST, $this->option_name_meta_description, FILTER_SANITIZE_STRING ) ) );
		if ( empty( $value ) ) {
			delete_post_meta( $post_id, $this->option_name_meta_description );
		} else {
			$result = update_post_meta( $post_id, $this->option_name_meta_description, $value );
			if ( ! $result ) {
				add_post_meta( $post_id, $this->option_name_meta_description, $value, true );
			}
		}
	}


	/**
	 * Check is REST API request handler
	 */
	protected function is_rest_request() {
		return defined( 'REST_REQUEST' ) && REST_REQUEST;
	}

	protected function get_post_meta_name( $name ) {
		return sprintf(
			'%s_%s',
			$this->post_meta_prefix,
			esc_attr( $name )
		);
	}

	private function input( $type, $name, $value, $label, $attributes = array() ) {
		$content = '<p>';
		if ( ! empty( $label ) ) {
			$content .= sprintf(
				'<label>%s<br />',
				$label
			);
		}
		$atts = '';
		foreach ( $attributes as $attribute_key => $attribute_value ) {
			$atts .= sprintf(
				' %s="%s"',
				esc_html( $attribute_key ),
				esc_attr( $attribute_value )
			);
		}
		$content .= sprintf(
			'<input type="%s" name="%s" value="%s" %s />',
			esc_attr( $type ),
			esc_attr( $name ),
			esc_attr( $value ),
			$atts
		);
		if ( ! empty( $label ) ) {
			$content .= '</label>';
		}
		return $content;
	}

	protected function input_date( $name, $value, $label = '' ) {
		return $this->input( 'date', $name, $value, $label );
	}

	protected function input_url( $name, $value, $label = '' ) {
		return $this->input( 'url', $name, $value, $label );
	}

	protected function input_number( $name, $value, $label = '' ) {
		$attributes = array(
			'min' => 0,
		);
		return $this->input( 'number', $name, $value, $label, $attributes );
	}

	protected function input_text( $name, $value, $label = '' ) {
		return $this->input( 'text', $name, $value, $label );
	}

	protected function get_settings_field_name( $group, $section, $field_name ) {
		return substr(
			sprintf(
				'%s_%s_%s',
				$this->option_name,
				substr( hash( 'crc32', $group.$section), 0, 4 ),
				$field_name),
			0,
			20
		);
	}

	public function add_fields_html( $args ) {
		$value = get_option( $args['field_name'], isset( $args['default'] )? $args['default']:false );
		printf(
			'<input type="%s" value="%s" name="%s" class="regular-text code" />',
			esc_attr( isset( $args['type'] )? $args['type']:'text' ),
			esc_attr( $value ),
			esc_attr( $args['field_name'] )
		);
		if ( isset( $args['description'] ) ) {
			echo '<p class="description">';
			echo $args['description'];
			echo '</p>';
		}
	}

	/**
	 * Add settings Fields
	 */
	public function add_fields() {
		foreach( $this->get_settings_fields() as $option_group => $option_group_data ) {
			foreach( $option_group_data as $section => $data ) {
				foreach( $data['fields'] as $field_name => $field_data  ) {
					$field_data['page'] = $option_group;
					$field_data['section'] = $section;
					$field_data['field_name'] = $this->get_settings_field_name( $option_group, $section, $field_name );
					register_setting(
						$option_group,
						$field_data['field_name'],
						$field_data
					);
					add_settings_field(
						$field_data['field_name'],
						isset( $field_data['label'] )? $field_data['label']:sprintf( __( 'Tadanero: %s', 'opi-science-portal' ), $field_name ),
						array( $this, 'add_fields_html' ),
						$option_group,
						$section,
						$field_data
					);
				}
			}
		}
	}
}

