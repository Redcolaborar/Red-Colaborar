<?php
/**
* Plugin Name: MAYO - Login Screen
* Plugin URI: http://PassionInDesign.com
* Description: Let you customize the default Wordpress login screen without coding.
* Version: 1.3
* Author: Passion In Design
* Author URI: http://PassionInDesign.com
**/



/*
==============================
Table of content
==============================

- Define constant
- Include admin files

*/



//-- Define constant ------------------------------
define('MAYO_LOGIN_SCREEN_DIR',__DIR__ .'/');
define('MAYO_LOGIN_SCREEN_URL',plugins_url('',__FILE__ ).'/');



global $mayo_login_screen_option;
$mayo_login_screen_option = get_option('mayo_login_screen_option');



//-- Include admin files ------------------------------
if(is_admin()) include(MAYO_LOGIN_SCREEN_DIR.'admin/admin-backend.php');



//-- Login screen ------------------------------
add_action( 'login_enqueue_scripts', 'mayo_login_screen' );
function mayo_login_screen(){
	global $mayo_login_screen_option;
	echo '<style>';
	echo stripslashes($mayo_login_screen_option['login_screen_css']);
	echo '</style>';
}

add_filter('login_headerurl','mayo_login_screen_url');
function mayo_login_screen_url( $url ) {
	global $mayo_login_screen_option;
	if( isset($mayo_login_screen_option['login_screen_logo_link']) && $mayo_login_screen_option['login_screen_logo_link'] !=='' ){

		switch( $mayo_login_screen_option['login_screen_logo_link'] ){
			case '@front':
			case '@home':
				$url = get_home_url();
			break;
			case '@posts':
			case '@post':
				$url = get_permalink( get_option( 'page_for_posts' ) );
			break;
			case '':
				$url = '';
			break;
			default:
				$url = $mayo_login_screen_option['login_screen_logo_link'];
			break;
		}
	}
	else{
		$url = '';
	}
	return $url;
}

add_filter('login_headertitle', 'mayo_login_screen_title_on_logo');
function mayo_login_screen_title_on_logo() {
	return get_bloginfo('name');
}