<?php
/**
 * Login
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

namespace WebDevStudios\RedColaborar;

/**
 * Login
 *
 * @author Aubrey Portwood
 * @since 1.0.0
 */
class Login {

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'wp_logout', array( $this, 'phone_home' ) );
		add_action( 'wp_login', array( $this, 'phone_home' ) );
	}

	/**
	 * Go home.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function phone_home() {
		wp_redirect( home_url() );
		exit;
	}
}
