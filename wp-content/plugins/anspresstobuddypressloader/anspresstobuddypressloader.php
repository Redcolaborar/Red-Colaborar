<?php
/**
 * @package AnsPress to BuddyPress Loader.
 */
/*
Plugin Name: AnsPress to BuddyPress Loader
Plugin URI: http://mywebsolutions.us/
Description: A plugin to Load AnsPress to BuddyPress that matches the category
Version: 1.0.1
Author: Cristopher Perando
Author URI: http://mywebsolutions.us/
License: GPLv2 or later
Text Domain: A plugin to Load AnsPress to BuddyPress that matches the category
*/

if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'APTOBPLOADER_VERSION', '1.0.1');
define( 'APTOBPLOADER_DB_VERSION', '1.0.0');
define( 'APTOBPLOADER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APTOBPLOADER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APTOBPLOADER_ICON', APTOBPLOADER_PLUGIN_URL.'assets/images/icon-aptobploader.jpg');


//initialize dependences
require_once(APTOBPLOADER_PLUGIN_DIR . 'Helper/Config.php' );

//prepare plugins
$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;
register_activation_hook(__FILE__, array($LibAPtoBPLoader, 'install'));
register_deactivation_hook(__FILE__, array($LibAPtoBPLoader, 'uninstall') );

//Initialize Class
class AnsPressToBuddyPressLoader_Plugin extends \AnsPressToBuddyPressLoader\Helper\Config\Config
{
	public function __construct()
	{
		parent::__construct();
	}	
}

//preloadclass
/* Only load code that needs BuddyPress to run once BP is loaded and initialized. */
$AnsPressToBuddyPressLoader_Plugin = new AnsPressToBuddyPressLoader_Plugin;