<?php
/**
 * Plugin Name: BuddyPress Edit Activity Mediapress Integration
 * Description: Edit BuddyPress media posts from the front-end
 * Author:      RZ IT Solutions
 * Author URI:  http://rz.eng.br
 * Version:     1.0.0
 */

// Exit if accessed directly
if (!defined('ABSPATH'))
  exit;

// Directory
if (!defined( 'BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_DIR' ) ) {
  define( 'BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
}

// Url
if (!defined( 'BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_URL' ) ) {
  $plugin_url = plugin_dir_url( __FILE__ );

  // If we're using https, update the protocol. Workaround for WP13941, WP15928, WP19037.
  if ( is_ssl() )
    $plugin_url = str_replace( 'http://', 'https://', $plugin_url );

  define( 'BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_URL', $plugin_url );
}

if( !is_dir( WP_PLUGIN_DIR . '/buddypress-edit-activity' ) ) {
  /* Load BP Edit Activity First */
  require_once( BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_DIR . '/buddypress-edit-activity/buddypress-edit-activity.php');
}

/**
 * Main
 *
 * @return void
 */
function bp_mp_edit_activity_init()
{
  global $bp, $BUDDYBOSS_EDIT_MP_ACTIVITY;

  //Check BuddyPress is install and active
  if ( ! function_exists( 'bp_is_active' ) ) {
    if(function_exists('buddyboss_edit_activity_install_buddypress_notice')) {
      add_action( 'admin_notices', 'buddyboss_edit_activity_install_buddypress_notice' );
    }

    return;
  }

  $main_include  = BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_DIR  . 'includes/bp_mediapress_edit.php';

  try
  {
    if ( file_exists( $main_include ) )
    {
      require( $main_include );
    }
    else{
      $msg = sprintf( __( "Couldn't load main class at:<br/>%s", 'buddypress-edit-activity' ), $main_include );
      throw new Exception( $msg, 404 );
    }
  }
  catch( Exception $e )
  {
    $msg = sprintf( __( "<h1>Fatal error:</h1><hr/><pre>%s</pre>", 'buddypress-edit-activity' ), $e->getMessage() );
    echo $msg;
  }

  $BUDDYBOSS_EDIT_MP_ACTIVITY = BP_Edit_Mediapress::instance();

}
add_action( 'plugins_loaded', 'bp_mp_edit_activity_init' );
