<?php
/**
 * Custom BuddyPress functions that act independently of the theme templates.
 *
 * @package Red Colaborar
 */

/**
 * Removes the Public Message button.
 *
 * @author Corey Collins
 */
function wds_redcolaborar_bp_remove_public_message_button() {
	remove_filter( 'bp_member_header_actions','bp_send_public_message_button', 20 );
}
add_action( 'bp_member_header_actions', 'wds_redcolaborar_bp_remove_public_message_button' );

/**
 * Override default BP private message button to work on Friends tab
 *
 * @author Eric Fuller
 * @param  array $btn The button options.
 *
 * @return array $btn The adjusted button options.
 */
function wds_redcolaborar_bp_private_msg_args( $btn ) {

	if ( ! is_user_logged_in() ) {
		return false;
	}

	// Use output buffering to capture the SVG icon markup.
	ob_start();
	wds_redcolaborar_display_svg( array(
		'title' => __( 'Private Message', 'redcolaborar' ),
		'icon'  => 'icon-message',
		'fill'  => '#95989e',
	) );

	$btn['link_text'] = ob_get_clean();
	$btn['link_href'] = wds_redcolaborar_filter_message_button_link();

	return $btn;
}

/**
 * Create the private message link used in the members directory.
 *
 * @author Eric Fuller
 * @param string $link The link to be changes.
 *
 * @return string $link The composed private message link.
 */
function wds_redcolaborar_filter_message_button_link( $link = '' ) {

	// Get the user id.
	$bp_user_id = ( bp_get_member_user_id() ? bp_get_member_user_id() : bp_displayed_user_id() );

	// Compose private message link.
	$link = wp_nonce_url( bp_loggedin_user_domain() . bp_get_messages_slug() . '/compose/?r=' . bp_core_get_username( $bp_user_id ) );

	return $link;
}

/**
 * Filter and display the private message button.
 *
 * @author Eric Fuller
 *
 * @return string The button markup.
 */
function wds_redcolaborar_bp_dir_send_private_message_button() {

	// Bail if the the member id equals the logged in user id.
	if ( bp_get_member_user_id() === bp_loggedin_user_id() ) {
		return false;
	}

	add_filter( 'bp_get_send_private_message_link', 'wds_redcolaborar_filter_message_button_link', 1, 1 );
	add_filter( 'bp_get_send_message_button_args', 'wds_redcolaborar_bp_private_msg_args' );

	bp_send_message_button();
}

/**
 * Displays the featured posts only when it's the default view and homepage.
 *
 * @author Aubrey Portwood
 * @since  Friday, 11 24, 2017
 */
function wds_redcolaborar_display_homepage_featured_posts_on_default_view_and_frontpage() {

	// The wds-redcolaborar plugin is not active and it's the front page.
	if ( ! wds_redcolaborar() && is_front_page() ) {
		wds_redcolaborar_display_homepage_featured_posts();
	} else {
		if ( wds_redcolaborar()->shared->is_activity_page() ) {
			if ( wds_redcolaborar()->sidebar->is_default_view() || empty( $_GET ) ) { // @codingStandardsIgnoreLine: GET access okay here.

				// Only show featured posts when it's the activity page and it's either the default view or there are no filters.
				wds_redcolaborar_display_homepage_featured_posts();
			}
		}
	}
}

/**
 * Display homepage recent activity when not logged in.
 *
 * @since   NEXT
 *
 * @author  Corey Collins
 * @author  Aubrey Portwood Removed duplicate comments on featured posts.
 *
 * @return string           Early bail when no featured posts.
 */
function wds_redcolaborar_display_homepage_featured_posts() {

	// Set a logged-out key.
	$logged_in_out_key = '_logged_in';
	if ( ! is_user_logged_in() ) {
		$logged_in_out_key = '_logged_out';
	}

	// Check for the featured post IDs.
	$featured_posts = get_theme_mod( 'wds_redcolaborar_featured_post_post_ids' . $logged_in_out_key );

	// If there are no featured posts, bail.
	if ( ! $featured_posts ) {
		return '';
	}

	// Setup our query args.
	$args = array(
		'display_comments' => true, // False for none, stream/threaded - show comments in the stream or threaded under items.
		'include'          => esc_html( $featured_posts ),
		'sort'             => 'DESC', // Sort DESC or ASC.
		'per_page'         => 3, // Number of items per page.
	);

	if ( bp_has_activities( $args ) ) :

		// Grab the section background color.
		$section_bg_color = get_theme_mod( 'wds_redcolaborar_featured_post_section_bg_color' . $logged_in_out_key );

		// Add a class if a background color is set.
		$bg_color_class = $section_bg_color ? ' has-bg-color' : '';

		// Grab the position of our CTA block.
		$cta_first = get_theme_mod( "wds_redcolaborar_featured_post_block_show_first{$logged_in_out_key}" );

		// Check to see if the CTA block is hidden.
		$cta_visible       = get_theme_mod( "wds_redcolaborar_featured_post_block_show_hide{$logged_in_out_key}" );
		$cta_visible_class = $cta_visible ? ' cta-hidden' : ' cta-visible';

		?>
		<section class="featured-posts grid-container grid-x<?php echo esc_attr( $bg_color_class . $cta_visible_class ); ?>">
			<?php if ( $section_bg_color ) : ?>
				<div class="featured-posts-bg-color" style="background-color: <?php echo esc_attr( $section_bg_color ); ?>"></div>
			<?php endif; ?>

			<style>

			.awst_like_user_list a {
				padding: 4px 8px;
			}

			.featured-activity .activity-inner {
				/* display: none; */
				max-height: 500px;
				overflow-y: hidden;
			}

			.featured-activity .activity .rc-featured-view-more-container {
				display: block;
				width: 100%;
				text-align: center;
				background-color: #fff;
				padding: 10px 5px;
				-webkit-box-shadow: 0 -30px 15px 5px rgba( 255, 255, 255, 0.5 );
				box-shadow: 0 -30px 15px 5px rgba( 255, 255, 255, 0.5 );
				position: relative;
				z-index: 10;
			}

			</style>

			<div class="featured-activity">
				<ul class="featured-activity-list item-list clearfix">
					<?php

					// If we checked the box to display the card first, display here.
					if ( $cta_first ) {
						wds_redcolaborar_featured_posts_cta_block( $logged_in_out_key );
					}

					while ( bp_activities() ) {

						set_query_var( 'rc_is_featured', true );

						// Don't show comments when we load the template.
						// add_filter( 'wds_redcolaborar_entry_php_show_comments', '__return_false', 10 );

						// Load the activity and the template up.
						bp_the_activity();
						bp_get_template_part( 'activity/entry' );

						// Once we load the template w/out comments, ensure it's loaded the next time around.
						// remove_filter( 'wds_redcolaborar_entry_php_show_comments', '__return_false', 10 );
					}

					// If we have NOT checked the box to display the card first, display here.
					if ( ! $cta_first ) {
						wds_redcolaborar_featured_posts_cta_block( $logged_in_out_key );
					}

					?>
				</ul>
			</div><!-- .activity -->
		</section><!-- .featured-posts -->
	<?php endif; wp_reset_postdata(); // @codingStandardsIgnoreLine: Style okay here.
}

/**
 * Build and output the CTA Block in Featured Posts
 * @param  array  $args Carries the proper keys/classes for logged-in/out scenarios.
 *
 * @author Corey Collins
 */
function wds_redcolaborar_featured_posts_cta_block( $user_status ) {

	// Check to see if we're hiding the CTA.
	$cta_hide = get_theme_mod( 'wds_redcolaborar_featured_post_block_show_hide' . $user_status );

	// If hiding, bail!
	if ( $cta_hide ) {
		return '';
	}

	// Grab our CTA block details.
	$cta_title    = get_theme_mod( 'wds_redcolaborar_featured_post_block_title' . $user_status );
	$cta_text     = get_theme_mod( 'wds_redcolaborar_featured_post_block_text' . $user_status );
	$cta_url      = get_theme_mod( 'wds_redcolaborar_featured_post_block_url' . $user_status );
	$cta_button   = get_theme_mod( 'wds_redcolaborar_featured_post_block_button_text' . $user_status );
	$bg_color     = get_theme_mod( 'wds_redcolaborar_featured_post_block_bg_color' . $user_status );

	// Setup our inline styles, if a color is set.
	$color_styles = $bg_color ? ' style=background-color:' . esc_html( $bg_color ) . ';' : '';

	// Add a class if this item should be first.
	$cta_first_class = $cta_position ? ' cta-first' : ''; ?>

	<li class="post featured-post post-card post-card-register<?php echo esc_attr( $cta_first_class ); ?>"<?php echo esc_attr( $color_styles ); ?>>
		<div class="card-content">
			<?php echo $cta_title ? '<h3>' . esc_html( $cta_title ) . '</h3>' : ''; ?>

			<?php echo $cta_text ? '<p>' . wp_kses_post( $cta_text ) . '</p>' : ''; ?>

			<?php if ( $cta_url && $cta_button ) : ?>
				<a href="<?php echo esc_url( $cta_url ); ?>" class="button button-sign-up"><?php echo esc_html( $cta_button ); ?></a>
			<?php endif;?>
		</div><!-- .card-content -->
	</li>

	<?php
}

/**
 * Display the member bio for member directory page.
 *
 * @author Eric Fuller
 * @param  int $id User id.
 *
 * @return bool If no id.
 */
function wds_redcolaborar_display_profile_bio( $id ) {

	// Bail if no id.
	if ( empty( $id ) ) {
		return false;
	}

	// Setup profile args.
	$args = array(
		'field'   => 'Bio',
		'user_id' => $id,
	);

	// Get the profile data.
	$bio = bp_get_profile_field_data( $args );

	// Display if there is a bio.
	if ( ! empty( $bio ) ) :
		$member_url = ' <a href="' . bp_core_get_user_domain( $id ) . 'profile" class="read-more">' . __( '[&hellip;]', 'redcolaborar' ) . '</a>'; ?>

		<p><?php echo wds_recolaborar_limit_text( $bio, 22, $member_url ); ?></p>

	<?php endif;
}

/**
 * Display profile bar.
 *
 * @author jomurgel
 */
function wds_redcolaborar_display_profile_bar() {
	?>

	<nav class="profile-bar-container">
		<?php if ( is_user_logged_in() ) {

			// Display Home Link.
			wds_redcolaborar_display_home_link();

			// Display bookmarks link.
			wds_redcolaborar_display_bookmark_link();

			// Display notifications.
			wds_redcolaborar_display_notifications();

			// Display profile icon + menu.
			wds_redcolaborar_display_profile_menu();

		} else {

			// Display login/signup links.
			wds_redcolaborar_display_logged_out_view();
		}
		?>
	</nav><!-- .profile-bar-container -->
	<?php
}

/**
 * Display home link for profile bar.
 *
 * @author jomurgel
 */
function wds_redcolaborar_display_home_link() {
	?>

	<div class="profile-link home">
		<a href="<?php echo esc_url( get_site_url() ); ?>">
			<?php wds_redcolaborar_display_svg(
				array(
					'icon'   => 'icon-home',
					'title'  => esc_html__( 'Home', 'redcolaborar' ),
					'height' => '26',
					'width'  => '31',
				)
			); ?>
		</a>
	</div><!-- .home -->

	<?php
}

/**
 * Display bookmark link for profile bar.
 *
 * @author jomurgel
 */
function wds_redcolaborar_display_bookmark_link() {
	?>

	<div class="profile-link bookmarks">
		<a href="<?php echo esc_url( bp_core_get_user_domain( bp_loggedin_user_id() ) . 'activity/favorites' ); ?>">
			<?php wds_redcolaborar_display_svg(
				array(
					'icon'   => 'icon-bookmark',
					'title'  => esc_html__( 'Bookmarks', 'redcolaborar' ),
					'height' => '24',
					'width'  => '18',
				)
			); ?>
		</a>
	</div><!-- .bookmarks -->

	<?php
}

/**
 * Display notifications for profile bar.
 *
 * @author jomurgel
 */
function wds_redcolaborar_display_notifications() {
	?>

	<div class="profile-link notifications">
		<a href="<?php echo esc_url( bp_core_get_user_domain( bp_loggedin_user_id() ) . 'notifications' ); ?>">
			<?php wds_redcolaborar_display_svg(
				array(
					'icon'   => 'icon-notifications',
					'title'  => esc_html__( 'Notifications', 'redcolaborar' ),
					'height' => '26',
					'width'  => '21',
				)
			); ?>
			<?php // Don't show unless there are notifications.
			if ( bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ) > 0 ) : ?>
				<div class="notification-alert">
					<?php echo bp_notifications_get_unread_notification_count( bp_loggedin_user_id() ); ?>
				</div><!-- .notificatin-alert -->
			<?php endif; ?>
		</a>
		<span class="link-title">
			<a href="<?php echo esc_url( get_site_url() ); ?>"><?php esc_html_e( 'Notifications', 'redcolaborar' ); ?></a>
		</span><!-- .link-title -->
	</div><!-- .notifications -->

	<?php
}

/**
 * Display profile menu for profile bar.
 *
 * @author jomurgel
 */
function wds_redcolaborar_display_profile_menu() {
	?>
	<div class="profile-link user-profile">
		<div class="avatar">
			<div class="avatar-image">
				<?php echo bp_core_fetch_avatar( array(
					'item_id' => bp_loggedin_user_id(),
				) ); ?>
			</div><!-- .avatar-image -->

			<label for="profile-menu-dropdown" class="button-dropdown">
			<?php esc_html_e( 'Toggle Menu', 'redcolaborar' ); ?>
			</label><!-- .button-dropdown -->
			<input class="dropdown-open" type="checkbox" id="profile-menu-dropdown" aria-hidden="true" hidden />

			<div class="dropdown-inner">
			<?php

				// Profile bar menu args.
				$profile_args = array(
					'theme_location'  => 'profile-bar',
					'container'       => false,
					'container_class' => '',
					'container_id'    => '',
					'menu_id'         => 'profile-bar-menu',
					'menu_class'      => 'profile-bar-menu',
					'fallback_cb'     => false,
				);

				// Display the mobile menu.
				wp_nav_menu( $profile_args );
			?>
				<ul class="user-profile-bar-menu">
					<?php bp_get_loggedin_user_nav(); ?>
					<li class="user-profile-logout">
						<a href="<?php echo esc_url( wp_logout_url() ); ?>"><?php esc_html_e( 'Log Out', 'redcolaborar' ); ?></a>
					</li><!-- .user-profile-logout -->
				</ul><!-- .user-profile-bar-menu -->
			</div><!-- .dropdown-inner -->
		</div><!-- .avatar -->
		<span class="link-title">
			<?php echo bp_core_get_userlink( bp_loggedin_user_id(), true ); ?>
		</span><!-- .link-title -->
	</div><!-- .user-profile -->
	<?php
}

/**
 * DIsplay logged out buttons for profile bar.
 */
function wds_redcolaborar_display_logged_out_view() {
	?>

	<div class="profile-link log-in">
		<a href="<?php echo wp_login_url( get_permalink() ); ?>" class="button small"><?php esc_html_e( 'Log In', 'redcolaborar' ); ?></a>
	</div>
	<div class="profile-link sign-up">
		<a href="<?php echo esc_url( bp_get_signup_page() ); ?>" class="button small"><?php esc_html_e( 'Sign Up', 'redcolaborar' ); ?></a>
	</div>

	<?php
}

/**
 * Filters the output of the activity meta to include  topic.
 *
 * @author Corey Collins
 */
function wds_redcolaborar_filter_activity_meta() {

	// Get the activity date.
	$activity_date = bp_get_activity_date_recorded();

	// See if we have a topic attached.
	$topic = '';

	// Only output if we have the tax.
	if ( taxonomy_exists( 'bp-activity-topics' ) ) {
		$topic = wp_get_object_terms( bp_get_activity_id(), 'bp-activity-topics' );
	}

	// Build our topic output.
	$topic_output   = '';
	$activity_topic = '';

	// If we have a topic, build the filter URL.
	if ( $topic ) {
		$filter_url   = '?search=&categories-enabled=on&topics[' . $topic[0]->term_id . ']=' . $topic[0]->term_id . '&action=filter-activity';
		$topic_output = '<a href="' . esc_url( bp_get_activity_directory_permalink() . $filter_url ) . '">' . esc_html( $topic[0]->name ) . '</a>';
	}

	// Get the author ID.
	$author_id = bp_get_activity_user_id();

	// Get the author userdata.
	$author_data = get_userdata( $author_id );

	// Get the author display name.
	$author_name = $author_data->display_name;

	// Output our author link.
	$author = sprintf(
		__( '<a href="%1$s">%2$s</a>', 'redcolaborar' ),
		bp_get_activity_user_link(),
		$author_name
	);

	// Output our date.
	$date = sprintf(
		__( '<a href="%1$s"> %2$s ago</a>', 'redcolaborar' ),
		bp_activity_get_permalink( bp_get_activity_id() ),
		human_time_diff( strtotime( $activity_date ), current_time( strtotime( get_option( 'time_format' ) ) ) )
	);

	// Output the topic, if one exists.
	if ( $topic_output ) {
		$activity_topic = sprintf(
			__( ' in %1$s', 'redcolaborar' ),
			$topic_output
		);
	}

	echo wpautop( $author . $date . $activity_topic );
}
add_filter( 'bp_insert_activity_meta', 'wds_redcolaborar_filter_activity_meta' );

/**
 * Displays a bar letting the user know filters are applied.
 *
 * @author Corey Collins, Aubrey Portwood
 *
 * @return void Early bail if we shouldn't show this element.
 */
function wds_redcolaborar_display_activity_filters_bar() {

	// Are we viewing a filtered view? @codingStandardsIgnoreLine: GET access ok here.
	$is_filtered = isset( $_GET['action'] ) && 'filter-activity' !== $_GET['action'];

	// Are we viewing a default view? @codingStandardsIgnoreLine: GET access ok here.
	$is_default_view = isset( $_GET['action'] ) && 'load-default-view' !== $_GET['action'];

	// If the query var isn't set, do nothing.
	if ( ! $is_filtered && ! $is_default_view ) {
		return;
	}

	// Adds a logged-class for better styling.
	$logged_in_out = is_user_logged_in() ? ' logged-in' : ' logged-out';

	$message = $is_default_view ? __( 'You are viewing the activity feed with search filters applied.', 'redcolaborar' ) : __( 'You are viewing your saved, default filtered view.', 'redcolaborar' );

	?>
	<div class="filters-bar<?php echo esc_attr( $logged_in_out ); ?>">
		<div class="filter-message"><?php echo esc_html( $message ); ?><br/><br/>
			<?php do_action( 'wds_redcolaborar_display_activity_filters_bar' ); ?>
		</div>

		<a href="<?php echo esc_url( bp_get_activity_directory_permalink() ); ?>?search=" class="button small"><?php _e( 'View All Activity', 'redcolaborar' ); ?></a>
	</div><!-- .filters-bar -->
	<?php
}
add_action( 'bp_before_activity_loop', 'wds_redcolaborar_display_activity_filters_bar' );

/**
 * Get user's first and last name, else just their first name, else their
 * display name. Defaults to the current user if $user_id is not provided.
 *
 * @param  mixed  $user_id The user ID or object. Default is current user.
 * @return string          The user's name.
 * @author jomurgel
 */
function wds_redcolaborar_get_users_name( $user_id = null ) {

	// Grab userdata.
	$user_info = $user_id ? get_userdata( $user_id ) : wp_get_current_user();

	// Get Extended profile's first name field.
	$ex_first = bp_get_profile_field_data(
		array(
			'field'   => 'Nombre',
			'user_id' => $user_id,
		)
	);

	// Get Extended profile's last name field.
	$ex_last = bp_get_profile_field_data(
		array(
			'field'   => 'Apellido',
			'user_id' => $user_id,
		)
	);

	// If we have both first and last names in extended profile
	if ( $ex_first && $ex_last ) {
		return esc_html( $ex_first ) . ' ' . esc_html( $ex_last );
	}

	// If we only have a first name and no last name in extended profile
	if ( $ex_first && ! $ex_last ) {
		return esc_html( $ex_first );
	}

	// If we have both first and last names in WP fields
	if ( ( ! $ex_first && $ex_last ) && $user_info->first_name && $user_info->last_name ) {
		return esc_html( $user_info->first_name ) . ' ' . esc_html( $user_info->last_name );
	}

	// If we only have a first name and no last name...
	if ( ( ! $ex_first && $ex_last ) && $user_info->first_name && ! $user_info->last_name ) {
		return esc_html( $user_info->first_name );
	}

	// Else, the display name.
	return esc_html( $user_info->display_name );
}

/**
 * Adds a link to hide comments once made visible.
 *
 * @return string The output of our show/hide link.
 * @author Corey Collins
 */
function wds_redcolaborar_display_show_hide_comments_link() {

	$show_text = __( 'Show all comments', 'redcolaborar' );
	$hide_text = __( 'Hide comments', 'redcolaborar' );
	?>

	<a class="show-hide-comments-trigger" data-comment-count="" data-show-text="<?php echo esc_attr( $show_text ); ?>" data-hide-text="<?php echo esc_attr( $hide_text ); ?>" role="button"><?php echo esc_html( $hide_text ); ?></a>

	<?php
}

/**
 * Filter the JS strings in BuddyPress so we can localize them.
 *
 * @param  array  $args The array of BP args.
 * @return array        Our updated array.
 * @author Corey Collins
 */
function wds_redcolaborar_replace_buddypress_js_strings( $translated_text ) {

	// Start our switch statement.
	switch ( $translated_text ) :
		// English.
	    case 'Favorite' :
			$translated_text = __( 'Bookmark', 'redcolaborar' );
			break;
		case 'Remove favorite' :
			$translated_text = __( 'Remove Bookmark', 'redcolaborar' );
			break;
		case 'My Favorites' :
			$translated_text = __( 'My Bookmarks', 'redcolaborar' );
			break;

		// Spanish.
		case 'Favorito' :
			$translated_text = __( 'Bookmark', 'redcolaborar' );
			break;
		case 'Eliminar favorito' :
			$translated_text = __( 'Remove Bookmark', 'redcolaborar' );
			break;
		case 'Mis Favoritos' :
			$translated_text = __( 'My Bookmarks', 'redcolaborar' );
			break;
	endswitch;

	return $translated_text;
}
add_filter( 'gettext', 'wds_redcolaborar_replace_buddypress_js_strings', 20 );

/**
 * Filter BP activity updates and link hashtags found within them.
 *
 * @author Corey Collins
 * @author Aubrey Portwood Added improved pattern from the plugin.
 * @since  1.0.0
 *
 * @param  string $content The content of the activity update.
 * @return string          The updated content.
 */
function wds_redcolaborar_filter_bp_activity_hashtags( $content ) {
	if ( ! wds_redcolaborar() ) {

		// We need the pattern from the plugin.
		return $content;
	}

	// Trust the plugin's pattern.
	$pattern = wds_redcolaborar()->hashtags->pattern;

	// Build our query arg URL ahead of time.
	$query_url = add_query_arg( array(
		'search' => '%23\2',
		'action' => 'filter-activity',
	), get_home_url() );

	// The HTML tag.
	$template = ' <a href="' . $query_url . '">#\2</a>';

	// Find hashtags in our post and link them.
	$content = preg_replace( $pattern, $template, html_entity_decode( $content ) );

	// Return the content.
	return $content;
}
add_filter( 'bp_get_activity_content_body', 'wds_redcolaborar_filter_bp_activity_hashtags' );
