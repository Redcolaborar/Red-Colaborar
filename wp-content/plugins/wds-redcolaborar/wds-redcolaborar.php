<?php
/**
 * Plugin Name: RedColaborar Modifications
 * Description: All modifications made my WebDevStudios to work with the recolaborar theme.
 * Version: 1.0.0
 * Author: WebDevStudios
 * Author URI: http://webdevstudios.com
 * Text Domain: redcolaborar
 * Network: False
 *
 * @since    1.0.0
 * @package  WebDevStudios\RedColaborar
 */

// Our namespace.
namespace WebDevStudios\RedColaborar;

// Require the App class.
require_once 'includes/class-app.php';

// Create a global variable for the app.
$app = null;

/**
 * Create/Get the App.
 *
 * @author Aubrey Portwood
 * @since  1.0.0
 *
 * @return App The App.
 */
function app() {
	global $app;

	if ( null === $app ) {

		// Create the app and go!
		$app = new App( __FILE__ );
		$app->attach();
		$app->hooks();
	}

	// Load language files.
	load_plugin_textdomain( 'redcolaborar', false, basename( dirname( __FILE__ ) ) . '/languages' );

	return $app;
}
add_action( 'plugins_loaded', 'WebDevStudios\RedColaborar\app' );

// When we deactivate this plugin...
register_deactivation_hook( __FILE__, array( app(), 'deactivate_plugin' ) );
