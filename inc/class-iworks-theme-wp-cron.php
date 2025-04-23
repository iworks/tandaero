<?php

require_once 'class-iworks-theme-base.php';

class iWorks_Theme_WP_Cron extends iWorks_Theme_Base {

	private $hook = 'tadanero';

	public function __construct() {
		add_action( 'shutdown', array( $this, 'action_shutdown_maybe_wp_schedule_event' ) );
	}

	public function action_shutdown_maybe_wp_schedule_event() {
		$when = wp_next_scheduled( $this->hook );
		if ( false === $when ) {
			wp_schedule_event( time(), 'twicedaily', $this->hook );
			return;
		}
	}
}

