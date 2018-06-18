<?php

// error_reporting(E_ALL);
// ini_set('display_errors', 1);
/*
	Plugin Name: Aw Social Tabs
	Plugin URI:
	Description: This plugin add like, Rate and Review capabilities to posts.
	Version: 1.0.0
	Author: G0947
	Author URI:
	License:
*/
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/* Include external classes */
include('classes/config/awstConfig.php');
include('classes/awstAjax.php');
include('classes/awstComman.php');
include('classes/awstAdminPages.php');
include('classes/awstFrontPages.php');
include('awstMain.php');

/*  create plugin object. */
$AwSocialTabs = new AwSocialTabs;

/**
 * Always ensure the awSocialTabsPostOption always has settings we need to exist.
 *
 * @author Aubrey Portwood
 * @since  Thursday, 11 30, 2017
 */
function wds_aw_social_tabs_options() {

	// AwSocialTabs Options.
	$options = get_option( 'awSocialTabsPostOptions', array() );

	// The options we need to exist to get liking on comments and activity_updates in wd_s/redcolaborar.
	$wds_options = array( 'page_like', 'comment_like' );

	// The options with our options.
	$new_options = array_merge( $options, $wds_options );

	// Ensure the DB always has these.
	update_option( 'awSocialTabsPostOptions', $new_options );
}
add_action( 'init', 'wds_aw_social_tabs_options' );

