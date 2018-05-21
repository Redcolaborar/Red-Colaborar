<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Red Colaborar
 */

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function wds_redcolaborar_categorized_blog() {

	// Get the categories.
	$all_the_cool_cats = get_transient( 'wds_redcolaborar_categories' );
	if ( false === $all_the_cool_cats ) {
		// Create an array of all the categories that are attached to posts.
		$all_the_cool_cats = get_categories( array(
			'fields'     => 'ids',
			'hide_empty' => 1,
			// We only need to know if there is more than one category.
			'number'     => 2,
		) );

		// Count the number of categories that are attached to the posts.
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'wds_redcolaborar_categories', $all_the_cool_cats );
	}

	if ( $all_the_cool_cats > 1 ) {
		// This blog has more than 1 category so wds_redcolaborar_categorized_blog should return true.
		return true;
	} else {
		// This blog has only 1 category so wds_redcolaborar_categorized_blog should return false.
		return false;
	}
}

/**
 * Get an attachment ID from it's URL.
 *
 * @param string $attachment_url The URL of the attachment.
 * @return int The attachment ID.
 */
function wds_redcolaborar_get_attachment_id_from_url( $attachment_url = '' ) {

	global $wpdb;

	$attachment_id = false;

	// If there is no url, return.
	if ( '' === $attachment_url ) {
		return false;
	}

	// Get the upload directory paths.
	$upload_dir_paths = wp_upload_dir();

	// Make sure the upload path base directory exists in the attachment URL, to verify that we're working with a media library image.
	if ( false !== strpos( $attachment_url, $upload_dir_paths['baseurl'] ) ) {

		// If this is the URL of an auto-generated thumbnail, get the URL of the original image.
		$attachment_url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $attachment_url );

		// Remove the upload path base directory from the attachment URL.
		$attachment_url = str_replace( $upload_dir_paths['baseurl'] . '/', '', $attachment_url );

		// Do something with $result.
		$attachment_id = $wpdb->get_var( $wpdb->prepare( "SELECT wposts.ID FROM $wpdb->posts wposts, $wpdb->postmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND wpostmeta.meta_value = '%s' AND wposts.post_type = 'attachment'", $attachment_url ) ); // WPCS: db call ok , cache ok.
	}

	return $attachment_id;
}

/**
 * Returns an <img> that can be used anywhere a placeholder image is needed
 * in a theme. The image is a simple colored block with the image dimensions
 * displayed in the middle.
 *
 * @author Ben Lobaugh
 * @throws Exception Details of missing parameters.
 * @param array $args {.
 * @type int $width
 * @type int $height
 * @type string $background_color
 * @type string $text_color
 * }
 * @return string
 */
function wds_redcolaborar_get_placeholder_image( $args = array() ) {
	$default_args = array(
		'width'            => '',
		'height'           => '',
		'background_color' => 'dddddd',
		'text_color'       => '000000',
	);

	$args = wp_parse_args( $args, $default_args );

	// Extract the vars we want to work with.
	$width = $args['width'];
	$height = $args['height'];
	$background_color = $args['background_color'];
	$text_color = $args['text_color'];

	// Perform some quick data validation.
	if ( ! is_numeric( $width ) ) {
		throw new Exception( esc_html__( 'Width must be an integer', 'redcolaborar' ) );
	}

	if ( ! is_numeric( $height ) ) {
		throw new Exception( esc_html__( 'Height must be an integer', 'redcolaborar' ) );
	}

	if ( ! ctype_xdigit( $background_color ) ) {
		throw new Exception( esc_html__( 'Please provide a valid hex color value for background_color', 'redcolaborar' ) );
	}

	if ( ! ctype_xdigit( $text_color ) ) {
		throw new Exception( esc_html__( 'Please provide a valid hex color value for text_color', 'redcolaborar' ) );
	}

	// Set up the url to the image.
	$url = "http://placeholder.wdslab.com/i/{$width}x$height/$background_color/$text_color";

	// Text that will be utilized by screen readers.
	$alt = apply_filters( 'wds_redcolaborar_placeholder_image_alt', esc_html__( 'WebDevStudios Placeholder Image', 'redcolaborar' ) );

	return "<img src='$url' width='$width' height='$height' alt='$alt' />";
}

/**
 * Returns an photo from Unsplash.com wrapped in an <img> that can be used
 * in a theme. There are limited category and search capabilities to attempt
 * matching the site subject.
 *
 * @author Ben Lobaugh
 * @throws Exception Details of missing parameters.
 * @param array $args {.
 * @type int $width
 * @type int $height
 * @type string $category Optional. Maybe be one of: buildings, food, nature, people, technology, objects
 * @type string $keywords Optional. Comma seperated list of keywords, such as: sailboat, water
 * }
 * @return string
 */
function wds_redcolaborar_get_placeholder_unsplash( $args = array() ) {
	$default_args = array(
		'width'    => '',
		'height'   => '',
		'category' => '',
		'keywords' => '',
	);

	$args = wp_parse_args( $args, $default_args );

	$valid_categories = array(
		'buildings',
		'food',
		'nature',
		'people',
		'technology',
		'objects',
	);

	// If there is an invalid category lets erase it.
	if ( ! empty( $args['category'] ) && ! in_array( $args['category'], $valid_categories, true ) ) {
		$args['category'] = '';
	}

	// Perform some quick data validation.
	if ( ! is_numeric( $args['width'] ) ) {
		throw new Exception( esc_html__( 'Width must be an integer', 'redcolaborar' ) );
	}

	if ( ! is_numeric( $args['height'] ) ) {
		throw new Exception( esc_html__( 'Height must be an integer', 'redcolaborar' ) );
	}

	// Set up the url to the image.
	$url = 'https://source.unsplash.com/';

	// Apply a category if desired.
	if ( ! empty( $args['category'] ) ) {
		$category = rawurlencode( $args['category'] );
		$url .= "category/$category/";
	}

	// Dimensions go after category but before search keywords.
	$url .= "{$args['width']}x{$args['height']}";

	if ( ! empty( $args['keywords'] ) ) {
		$keywords = rawurlencode( $args['keywords'] );
		$url .= "?$keywords";
	}

	// Text that will be utilized by screen readers.
	$alt = apply_filters( 'wds_redcolaborar_placeholder_image_alt', esc_html__( 'WebDevStudios Placeholder Image', 'redcolaborar' ) );

	return "<img src='$url' width='{$args['width']}' height='{$args['height']}' alt='$alt' />";
}

/**
 * Display the customizer header scripts.
 *
 * @author Greg Rickaby
 */
function wds_redcolaborar_display_customizer_header_scripts() {

	// Check for header scripts.
	$scripts = get_theme_mod( 'wds_redcolaborar_header_scripts' );

	// None? Bail...
	if ( ! $scripts ) {
		return false;
	}

	// Otherwise, echo the scripts!
	echo force_balance_tags( $scripts ); // WPCS XSS OK.
}

/**
 * Display the customizer footer scripts.
 *
 * @author Greg Rickaby
 */
function wds_redcolaborar_display_customizer_footer_scripts() {

	// Check for footer scripts.
	$scripts = get_theme_mod( 'wds_redcolaborar_footer_scripts' );

	// None? Bail...
	if ( ! $scripts ) {
		return false;
	}

	// Otherwise, echo the scripts!
	echo force_balance_tags( $scripts ); // WPCS XSS OK.
}

/**
 * Access the plugin app.
 *
 * @author Aubrey Portwood
 * @since  the.date;
 *
 * @return WebDevStudios\RedColaborar\App Redcolaborar application class object.
 */
function wds_redcolaborar() {
	$wds_redcolaborar = is_plugin_active( 'wds-redcolaborar/wds-redcolaborar.php' );

	if ( $wds_redcolaborar ) {
		return WebDevStudios\RedColaborar\app();
	}

	return false;
}

/**
 * Show homepage to everyone. Show the other pages to logged in users
 *
 * @author Aubrey Portwood
 * @since  Tuesday, 11 28, 2017
 */
function rc_only_show_homepage() {

	$is_logged_in  = is_user_logged_in();
	$slug_no_user  = 'bienvenida';
	$slug_register = 'register';

	$page_no_user = get_page_by_path( $slug_no_user, OBJECT );

	if ( $is_logged_in && is_page( $slug_no_user ) ) {
		wp_redirect( home_url() );
		exit;
	}

	if ( ! $is_logged_in && ! is_page( $slug_no_user ) && ! is_page( $slug_register ) && ! is_admin() ) {
		if ( ! empty( $page_no_user ) ) {
			wp_redirect( get_permalink( $page_no_user->ID ) );
		} else {
			auth_redirect();
		}

		exit;
	}
}
add_action( 'template_redirect', 'rc_only_show_homepage', 1 );
