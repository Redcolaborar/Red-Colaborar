<?php

/**
 * Plugin Name: %PLUGIN_NAME%
 */

class Swift_Performance_Loader {

	public static function load(){
		wp_cookie_constants();
		$plugins = get_option('active_plugins');
		$plugin_file = '%PLUGIN_DIR%performance.php';
		if (in_array('%PLUGIN_SLUG%', $plugins) && file_exists($plugin_file)){
			include_once $plugin_file;
		}
	}
}
Swift_Performance_Loader::load();
?>
