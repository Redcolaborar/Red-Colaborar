<?php

/**
 * Plugin Name: %PLUGIN_NAME%
 */

class Swift_Performance_Loader {

	public static function load(){
		wp_cookie_constants();
		$plugins = get_option('active_plugins');
		if (in_array('swift-performance/performance.php', $plugins)){
			include_once trailingslashit(str_replace('mu-plugins', 'plugins', __DIR__)) . 'swift-performance/performance.php';
		}
	}
}
Swift_Performance_Loader::load();
?>
