<?php
/**
 * Scripts & Styles
 *
 * @since 2.1.0
 *
 * @package LearnDash\Scripts
 */



/**
 * Styles for front-end
 *
 * @since 2.1.0
 */
function learndash_load_resources() {
	global $learndash_assets_loaded;

 	wp_enqueue_style( 
		'learndash_style', 
		LEARNDASH_LMS_PLUGIN_URL . 'assets/css/style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
		array(), 
		LEARNDASH_VERSION 
	);
	$learndash_assets_loaded['styles']['learndash_style'] = __FUNCTION__;

	wp_enqueue_style( 
		'sfwd_front_css', 
		LEARNDASH_LMS_PLUGIN_URL . 'assets/css/front'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', 
		array(), 
		LEARNDASH_VERSION 
	);
	$learndash_assets_loaded['styles']['sfwd_front_css'] = __FUNCTION__;

	wp_enqueue_style( 
		'jquery-dropdown-css', 
		LEARNDASH_LMS_PLUGIN_URL . 'assets/css/jquery.dropdown.min.css', 
		array(), 
		LEARNDASH_VERSION 
	);
	$learndash_assets_loaded['styles']['jquery-dropdown-css'] = __FUNCTION__;

	$filepath = locate_template( 
		array( 
			'learndash/learndash_template_style.min.css', 
			'learndash/learndash_template_style.css', 
			'learndash_template_style.min.css', 
			'learndash_template_style.css' 
		) 
	);

	if ( !empty( $filepath ) ) {
		wp_enqueue_style( 'learndash_template_style_css', str_replace( ABSPATH, '/', $filepath ), array(), LEARNDASH_VERSION );
		$learndash_assets_loaded['styles']['learndash_template_style_css'] = __FUNCTION__;
	} else if ( file_exists( LEARNDASH_LMS_PLUGIN_DIR .'/templates/learndash_template_style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css' ) ) {
		wp_enqueue_style( 'learndash_template_style_css', LEARNDASH_LMS_PLUGIN_URL . 'templates/learndash_template_style'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.css', array(), LEARNDASH_VERSION );
		$learndash_assets_loaded['styles']['learndash_template_style_css'] = __FUNCTION__;
	}
	
	// First check if the theme has the file learndash/learndash_template_script.js or learndash_template_script.js file
	$filepath = locate_template( 
					array( 
						'learndash/learndash_template_script.min.js', 
						'learndash/learndash_template_script.js', 
						'learndash_template_script.min.js', 
						'learndash_template_script.js' 
					) 
				);

	if ( !empty( $filepath ) ) {
		wp_enqueue_script( 'learndash_template_script_js', str_replace( ABSPATH, '/', $filepath ), array( 'jquery' ), LEARNDASH_VERSION, true );
		$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;
	} else if ( file_exists( LEARNDASH_LMS_PLUGIN_DIR .'/templates/learndash_template_script'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js' ) ) {
		wp_enqueue_script( 'learndash_template_script_js', LEARNDASH_LMS_PLUGIN_URL . 'templates/learndash_template_script'. ( ( defined( 'LEARNDASH_SCRIPT_DEBUG' ) && ( LEARNDASH_SCRIPT_DEBUG === true ) ) ? '' : '.min') .'.js', array( 'jquery' ), LEARNDASH_VERSION, true );
		$learndash_assets_loaded['scripts']['learndash_template_script_js'] = __FUNCTION__;

		$data = array();
		$data['ajaxurl'] = admin_url('admin-ajax.php');
		$data = array( 'json' => json_encode( $data ) );
		wp_localize_script( 'learndash_template_script_js', 'sfwd_data', $data );
	}

	// This will be dequeued via the get_footer hook if the button was not used. 
	wp_enqueue_script( 'jquery-dropdown-js', LEARNDASH_LMS_PLUGIN_URL . 'assets/js/jquery.dropdown.min.js', array( 'jquery' ), LEARNDASH_VERSION, true );
	$learndash_assets_loaded['scripts']['jquery-dropdown-js'] = __FUNCTION__;
}

add_action( 'wp_enqueue_scripts', 'learndash_load_resources', apply_filters( 'learndash_load_resources_priority', '10' ) );


function learndash_unload_resources() {
	global $learndash_shortcode_used;
	global $learndash_assets_loaded;
	
	// If we are showing a known LD post type then leave it all. 
	global $learndash_post_types;
	if ( ( is_singular( $learndash_post_types ) ) || ( $learndash_shortcode_used == true ) ) {
		return;
	}

	if ( ( isset( $learndash_assets_loaded['scripts'] ) ) && ( !empty( $learndash_assets_loaded['scripts'] ) ) ) {
		foreach( $learndash_assets_loaded['scripts'] as $script_tag => $function_loaded ) {
			// We *should* check these scripts to ensure we dequeue only ones set to load in the footer. Oh well. 
			wp_dequeue_script( $script_tag );
		}
	}
}
add_action( 'wp_print_footer_scripts', 'learndash_unload_resources', 1 );
