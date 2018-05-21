<?php

// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(!is_plugin_active('rc-mediapress/rc-mediapress.php'))
{
  return ;
}

define( 'MEDIAPRESS_CUSTOMIZER_VERSION', '1.0.0' );
define( 'MEDIAPRESS_CUSTOMIZER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR', plugin_dir_path( __FILE__ ) );

add_action('wp_enqueue_scripts', 'mpc_enqueue_scripts');
function mpc_enqueue_scripts() {
	wp_enqueue_style( 'selectize-default', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . '/css/selectize.default.css', false );
}

require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'class.front.mediapress_customizer.php' );
register_activation_hook( __FILE__, array('Mediapresscustomizerfront','mediapress_customizer_plugin_activate') );

require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'mediapress_customizer_helper_functions.php' );
if ( is_admin() ) {
	require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'class.admin.mediapress_customizer.php' );
}
