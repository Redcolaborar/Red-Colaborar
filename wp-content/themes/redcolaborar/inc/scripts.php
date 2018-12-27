<?php
/**
 * Custom scripts and styles.
 *
 * @package Red Colaborar
 */

/**
 * Register Google font.
 *
 * @link http://themeshaper.com/2014/08/13/how-to-add-google-fonts-to-wordpress-themes/
 */
function wds_redcolaborar_font_url() {

	$fonts_url = '';

	/**
	 * Translators: If there are characters in your language that are not
	 * supported by the following, translate this to 'off'. Do not translate
	 * into your own language.
	 */
	$cabin = esc_html_x( 'on', 'Cabin font: on or off', 'redcolaborar' );
	$open_sans = esc_html_x( 'on', 'Open Sans font: on or off', 'redcolaborar' );

	if ( 'off' !== $cabin || 'off' !== $open_sans ) {
		$font_families = array();

		if ( 'off' !== $cabin ) {
			$font_families[] = 'Cabin:400,400i,500,700';
		}

		if ( 'off' !== $open_sans ) {
			$font_families[] = 'Open Sans:400,700';
		}

		$query_args = array(
			'family' => urlencode( implode( '|', $font_families ) ),
		);

		$fonts_url = add_query_arg( $query_args, '//fonts.googleapis.com/css' );
	}

	return $fonts_url;
}

/**
 * Enqueue scripts and styles.
 */
function wds_redcolaborar_scripts() {
	/**
	 * If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
	 */
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) || ( isset( $_GET['script_debug'] ) ) ? true : false;

	/**
	 * If we are debugging the site, use a unique version every page load so as to ensure no cache issues.
	 */
	$version = '1.1';

	/**
	 * Should we load minified files?
	 */
	$suffix = ( true === $debug ) ? '' : '.min';

	/**
	 * Global variable for IE.
	 */
	global $is_IE;

	// Register styles & scripts.
	wp_register_style( 'redcolaborar-google-font', wds_redcolaborar_font_url(), array(), null );
	wp_register_style( 'slick-carousel', get_template_directory_uri() . '/assets/bower_components/slick-carousel/slick/slick.css', null, '1.6.0' );
	wp_register_script( 'slick-carousel', get_template_directory_uri() . '/assets/bower_components/slick-carousel/slick/slick' . $suffix . '.js', array( 'jquery' ), '1.6.0', true );

	// Enqueue styles.
	wp_enqueue_style( 'redcolaborar-google-font' );
	wp_enqueue_style( 'redcolaborar-style', get_stylesheet_directory_uri() . '/style' . $suffix . '.css', array(), $version );

	// Enqueue scripts.
	if ( $is_IE ) {
		wp_enqueue_script( 'redcolaborar-babel-polyfill', get_template_directory_uri() . '/assets/scripts/babel-polyfill.min.js', array(), $version, true );
	}

	// If is the activity directory.
	if ( function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory() ) {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_register_style( 'jquery-ui', get_template_directory_uri() . '/assets/bower_components/jquery-ui/themes/smoothness/jquery-ui.css' );
		wp_enqueue_style( 'jquery-ui' );
		wp_enqueue_script( 'sticky-sidebar', get_template_directory_uri() . '/assets/scripts/sticky-sidebar' . $suffix . '.js', array( 'jquery' ), $version, true );
	}

	wp_register_script( 'redcolaborar-scripts', get_template_directory_uri() . '/assets/scripts/project' . $suffix . '.js', array( 'jquery' ), $version, true );

	wp_enqueue_script( 'redcolaborar-scripts' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Enqueue the scaffolding Library script.
	if ( is_page_template( 'template-scaffolding.php' ) ) {
		wp_enqueue_script( 'redcolaborar-scaffolding', get_template_directory_uri() . '/assets/scripts/scaffolding' . $suffix . '.js', array( 'jquery' ), $version, true );
	}
}
add_action( 'wp_enqueue_scripts', 'wds_redcolaborar_scripts' );

/**
 * Enqueue scripts for the customizer.
 *
 * @author Corey Collins
 */
function wds_redcolaborar_customizer_scripts() {

	/**
	 * If WP is in script debug, or we pass ?script_debug in a URL - set debug to true.
	 */
	$debug = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG == true ) || ( isset( $_GET['script_debug'] ) ) ? true : false;

	/**
	 * If we are debugging the site, use a unique version every page load so as to ensure no cache issues.
	 */
	$version = '1.0.0';

	/**
	 * Should we load minified files?
	 */
	$suffix = ( true === $debug ) ? '' : '.min';

	wp_enqueue_script( 'wds_redcolaborar_customizer', get_template_directory_uri() . '/assets/scripts/customizer' . $suffix . '.js', array( 'jquery' ), $version, true );
}
add_action( 'customize_controls_enqueue_scripts', 'wds_redcolaborar_customizer_scripts' );

/**
 * Add SVG definitions to footer.
 */
function wds_redcolaborar_include_svg_icons() {

	// Define SVG sprite file.
	$svg_icons = get_template_directory() . '/assets/images/svg-icons.svg';

	// If it exists, include it.
	if ( file_exists( $svg_icons ) ) {
		require_once( $svg_icons );
	}
}
add_action( 'wp_footer', 'wds_redcolaborar_include_svg_icons', 9999 );
