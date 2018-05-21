<?php
/**
 * Action hooks and filters.
 *
 * A place to put hooks and filters that aren't necessarily template tags.
 *
 * @package Red Colaborar
 */

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function wds_redcolaborar_body_classes( $classes ) {

	// @codingStandardsIgnoreStart
	// Allows for incorrect snake case like is_IE to be used without throwing errors.
	global $is_IE;

	// If it's IE, add a class.
	if ( $is_IE ) {
		$classes[] = 'ie';
	}
	// @codingStandardsIgnoreEnd

	// Give all pages a unique class.
	if ( is_page() ) {
		$classes[] = 'page-' . basename( get_permalink() );
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Are we on mobile?
	// PHP CS wants us to use jetpack_is_mobile instead, but what if we don't have Jetpack installed?
	// Allows for using wp_is_mobile without throwing an error.
	// @codingStandardsIgnoreStart
	if ( wp_is_mobile() ) {
		$classes[] = 'mobile';
	}
	// @codingStandardsIgnoreEnd

	// Adds "no-js" class. If JS is enabled, this will be replaced (by javascript) to "js".
	$classes[] = 'no-js';

	// Add a cleaner class for the scaffolding page template.
	if ( is_page_template( 'template-scaffolding.php' ) ) {
		$classes[] = 'template-scaffolding';
	}

	if ( is_user_logged_in() ) {
		$classes[] = 'has-profile-bar';
	}

	// Add a `has-sidebar` class if we're using the sidebar template.
	if ( is_page_template( 'template-sidebar-right.php' ) || is_singular( 'post' ) || ( is_buddypress() && ! bp_is_user() && ! bp_is_register_page() ) ) {
		$classes[] = 'has-sidebar';
	}

	// Adds a class to BP Profiles.
	if ( function_exists( 'bp_is_user' ) && bp_is_user() && ! bp_is_single_activity() ) {
		$classes[] = 'buddypress-profile';
	}

	return $classes;
}
add_filter( 'body_class', 'wds_redcolaborar_body_classes' );

/**
 * Flush out the transients used in wds_redcolaborar_categorized_blog.
 */
function wds_redcolaborar_category_transient_flusher() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}
	// Like, beat it. Dig?
	delete_transient( 'wds_redcolaborar_categories' );
}
add_action( 'delete_category', 'wds_redcolaborar_category_transient_flusher' );
add_action( 'save_post',     'wds_redcolaborar_category_transient_flusher' );

/**
 * Customize "Read More" string on <!-- more --> with the_content();
 */
function wds_redcolaborar_content_more_link() {
	return ' <a class="more-link" href="' . get_permalink() . '">' . esc_html__( 'Read More', 'redcolaborar' ) . '...</a>';
}
add_filter( 'the_content_more_link', 'wds_redcolaborar_content_more_link' );

/**
 * Customize the [...] on the_excerpt();
 *
 * @param string $more The current $more string.
 * @return string Replace with "Read More..."
 */
function wds_redcolaborar_excerpt_more( $more ) {
	return sprintf( ' <a class="more-link" href="%1$s">%2$s</a>', get_permalink( get_the_ID() ), esc_html__( 'Read more...', 'redcolaborar' ) );
}
add_filter( 'excerpt_more', 'wds_redcolaborar_excerpt_more' );

/**
 * Enable custom mime types.
 *
 * @param array $mimes Current allowed mime types.
 * @return array Updated allowed mime types.
 */
function wds_redcolaborar_custom_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	$mimes['svgz'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'wds_redcolaborar_custom_mime_types' );

/**
 * Disable the "Cancel reply" link. It doesn't seem to work anyway, and it only makes the "Leave Reply" heading confusing.
 */
add_filter( 'cancel_comment_reply_link', '__return_false' );

// Additional scripts from Customizer.
add_action( 'wp_head', 'wds_redcolaborar_display_customizer_header_scripts', 999 );
add_action( 'wp_footer', 'wds_redcolaborar_display_customizer_footer_scripts', 999 );

// Create shortcode for SVG.
// Usage [svg icon="facebook-square" title="facebook" desc="like us on facebook" fill="#000000" height="20px" width="20px"].
add_shortcode( 'svg', 'wds_redcolaborar_display_svg' );

/**
 * Move the edit button on BP activity cards.
 *
 * Two actions were needed here because one class inherits the other.
 *
 * @author Eric Fuller
 */
function wds_redcolaborar_move_activity_edit_button() {

	// Bail if if classes aren't present
	if ( ! class_exists( 'BuddyBoss_Edit_Activity' ) || ! class_exists( 'BP_Edit_Mediapress' ) ) {
		return;
	}

	$buddy_boss      = BuddyBoss_Edit_Activity::instance();
	$edit_mediapress = BP_Edit_Mediapress::instance();

	// Bail if $buddy_boss is not an instance of BuddyBoss_Edit_Activity.
	if ( ! $buddy_boss instanceof BuddyBoss_Edit_Activity ) {
		return;
	}

	// Bail if $edit_mediapress is not an instance of BP_Edit_Mediapress.
	if ( ! $edit_mediapress instanceof BP_Edit_Mediapress ) {
		return;
	}

	remove_action( 'bp_activity_entry_meta', array( $buddy_boss, 'btn_edit_activity' ), 3, 10 );
	remove_action( 'bp_activity_entry_meta', array( $edit_mediapress, 'btn_edit_activity' ), 3, 10 );
}
add_action( 'bp_init', 'wds_redcolaborar_move_activity_edit_button' );

/**
 * Hide the search from that displays on top of the members directory.
 *
 * @author Eric Fuller
 *
 * @return string The empty markup string.
 */
function wds_redcolaborar_bp_remove_members_search() {
	return '';
}
add_filter( 'bp_directory_members_search_form', 'wds_redcolaborar_bp_remove_members_search' );

/**
 * Enable Shortcodes for Activity Stream
 *
 * @author jomurgel
 */
function wds_redcolaborar_add_shortcodes_to_activity_stream() {
	add_filter( 'bp_get_activity_content_body', 'do_shortcode', 1 );
}
add_action( 'bp_init', 'wds_redcolaborar_add_shortcodes_to_activity_stream' );

/**
 * Add media uploads to editable content types.
 *
 * @author Eric Fuller
 * @param  array $allowed The editable content types.
 *
 * @return array $allowed The allowed editable activity types.
 */

function wds_redcolaborar_add_new_activity_type( $allowed ) {

	$allowed[] = 'mpp_media_upload';
	return $allowed;

}
add_filter( 'b_e_a_plugin_option_editable_types', 'wds_redcolaborar_add_new_activity_type' );


/**
* Add editor-styles.css so we can show the .lead styling in the editor
*
* @author Corey Collins
*/
function wds_redcolaborar_add_editor_styles() {
	add_editor_style( 'editor-style.css' );
}
add_action( 'init', 'wds_redcolaborar_add_editor_styles' );

/**
 * Removes the wp admin bar if not admin.
 *
 * @author jomurgel
 */
function wds_redcolaborar_remove_admin_bar() {

	if ( ! current_user_can( 'administrator' ) && ! is_admin() ) {
		show_admin_bar( false );
	}
}
add_action( 'after_setup_theme', 'wds_redcolaborar_remove_admin_bar' );
