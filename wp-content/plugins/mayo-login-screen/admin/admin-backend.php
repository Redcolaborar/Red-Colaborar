<?php
/*
==============================
Table of content
==============================

- Admin init
- Add admin page
- Enqueue admin script

*/

//-- Add admin menu ------------------------------
add_action( 'admin_init', 'mayo_login_screen_admin_init' );
function mayo_login_screen_admin_init(){
	wp_register_style( 'mayo_login_screen_admin',MAYO_LOGIN_SCREEN_URL.'admin/admin-backend.css',false,'1');
	wp_register_script( 'mayo_login_screen_admin',MAYO_LOGIN_SCREEN_URL.'admin/admin-backend.js',array('jquery','wp-color-picker'),'1');
	wp_register_script( 'jquery_xcolor',MAYO_LOGIN_SCREEN_URL.'includes/jquery.xcolor.min.js',array('jquery'),'1');
	wp_register_script( 'tinycolor',MAYO_LOGIN_SCREEN_URL.'includes/tinycolor.js',false,'1');
}

//-- Add admin menu ------------------------------
add_action('admin_menu','mayo_login_screen_admin_menu');
function mayo_login_screen_admin_menu() {
	$mayo_admin_login_screen_page = add_theme_page('Login Screen', 'Login Screen', 'edit_theme_options', 'login-screen', 'mayo_login_screen_admin_page' );
	
	add_action('admin_print_scripts-'.$mayo_admin_login_screen_page,'mayo_admin_login_screen_page_enqueue');
}

//-- Add admin page ------------------------------
function mayo_login_screen_admin_page() {
	if ( !current_user_can( 'edit_theme_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	else{
		include(MAYO_LOGIN_SCREEN_DIR.'admin/admin-backend-login-screen.php');
	}
}

//-- Enqueue admin script ------------------------------
function mayo_admin_login_screen_page_enqueue(){
	wp_enqueue_style( 'mayo_login_screen_admin');
	wp_enqueue_script( 'mayo_login_screen_admin');
	
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_media();
	
	//wp_enqueue_script( 'jquery_xcolor');
	wp_enqueue_script( 'tinycolor');
}



add_action( 'plugins_loaded', 'mayo_login_screen_export_file' );
function mayo_login_screen_export_file(){
	if( isset( $_POST['mayo_login_screen_export'] ) && check_admin_referer( 'mayo_login_screen_setting' ) ){
		header("Content-type: application/x-msdownload");
		header("Content-Disposition: attachment; filename=mayo-login-screen.json");
		header("Pragma: no-cache");
		header("Expires: 0");
		
		global $mayo_login_screen_option;
		echo json_encode($mayo_login_screen_option);
		exit();
	}
}