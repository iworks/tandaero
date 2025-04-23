<?php
class iWorks_Table_Of_Content {

	private $tag = 'h2';

	private $post_types = array(
		'post',
	);

	private $used = array();

	public function __construct() {
		add_filter( 'the_content', array( $this, 'filter_the_content_add' ), 1 );
	}

	private function get_anchor( $text ) {
		$mask = apply_filters(
			'iworks_table_of_content_anchor_mask',
			'%s'
		);
		$name = sanitize_file_name( strtolower( $text ) );
		if ( empty( $name ) ) {
			$name = crc32( time() * rand( 1, 13 ) );
		} elseif ( preg_match( '/^[\d\-\.]+$/', $name ) ) {
			$name = 'toc-' . $name;
		} else {
			$name = preg_replace( '/^[\d\-\.]+/', '', $name );
		}
		$base = $name = sprintf( $mask, $name );
		if ( isset( $this->used[ $name ] ) ) {
			$i = 1;
			while ( isset( $this->used[ $name ] ) ) {
				$name = sprintf( '%s-%d', $base, $i++ );
			}
		}
		$this->used[ $name ] = $name;
		return $name;
	}

	public function filter_the_content_add( $content ) {
		if ( ! is_singular() ) {
			return $content;
		}
		if ( ! in_array( get_post_type(), $this->post_types ) ) {
			return $content;
		}
		$re         = sprintf(
			'/<%1$s([^>]*)>(.+)<\/%1$s>/',
			$this->tag
		);
		$toc        = '';
		$this->used = array();
		if ( preg_match_all( $re, $content, $matches ) ) {
			$toc .= '<div class="iworks-toc">';
			$toc .= sprintf(
				'<p class="iworks-toc-title">%s</p>',
				esc_attr__( 'Table of content', 'tadanero' )
			);
			$toc .= '<ol class="iworks-toc-list">';
			for ( $i = 0; $i < count( $matches[0] ); $i++ ) {
				$name = $this->get_anchor( $matches[2][ $i ] );
				if ( preg_match( '/id=[\'"]([^"^\]+)[\'"]/', $matches[1][ $i ], $match_id ) ) {
					$name = $match_id[1];
				} else {
					$pattern     = sprintf( '/%s/', preg_replace( '/\//', '\\/', $matches[0][ $i ] ) );
					$replacement = preg_replace(
						'/<' . $this->tag . '/',
						sprintf(
							'<%s id="%s"',
							$this->tag,
							$name
						),
						$matches[0][ $i ]
					);
					$content     = preg_replace( $pattern, $replacement, $content );
				}
				$toc .= sprintf(
					'<li class="iworks-toc-list-item"><a href="#%s">%s</a></li>',
					$name,
					$matches[2][ $i ]
				);
			}
			$toc .= '</ol>';
			$toc .= '</div>';
		}
		return $toc . $content;
	}
}
