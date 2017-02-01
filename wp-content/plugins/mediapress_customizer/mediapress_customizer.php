<?php
/**
 * @package mediapress-customizer
 */
/*
Plugin Name: Mediapress Customizer
Plugin URI: http://redcolaborar.org/
Description: This plugin is for customizing the functionality of mediapress and buddypress.
Version: 1.0.0
Author: Anylinuxwork
Author URI: http://anylinuxwork.slack.com/
License: GPLv2 or later
Text Domain: mediapress-customizer
*/
// Make sure we don't expose any info if called directly
if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

include_once( ABSPATH . 'wp-admin/includes/plugin.php' ); 
if(!is_plugin_active('mediapress/mediapress.php'))
{
  return ;
}


define( 'MEDIAPRESS_CUSTOMIZER_VERSION', '1.0.0' );
define( 'MEDIAPRESS_CUSTOMIZER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR', plugin_dir_path( __FILE__ ) );

require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'class.front.mediapress_customizer.php' );
register_activation_hook( __FILE__, array('Mediapresscustomizerfront','mediapress_customizer_plugin_activate') );

require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'mediapress_customizer_helper_functions.php' );
if ( is_admin() ) {
	require_once( MEDIAPRESS_CUSTOMIZER_PLUGIN_URL_DIR . 'class.admin.mediapress_customizer.php' );
}