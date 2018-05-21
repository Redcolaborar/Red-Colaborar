<?php
/**
 * Hashtags.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

namespace WebDevStudios\RedColaborar;

/**
 * Hashtags.
 *
 * @since 1.0.0
 */
class Hashtags {

	/**
	 * The pattern used to match hashtags.
	 *
	 * @see https://regex101.com/r/eH8cS3/1                 Where I generated the pattern.
	 * @see https://studyspanish.com/typing-spanish-accents Where I got the Spanish accents.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @var string
	 */
	public $pattern = '/(^|\A|\s?)#([^ :<>&\*!\^\$?.\/\[\]\)\(%\-\+{}\|~=;]+)/';

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {
		add_action( 'template_redirect', array( $this, 'fix_hashtag_links' ) );
		add_action( 'wds_rc_update_activity_hashtag', array( $this, 'update_hashatag' ), 10, 4 );
		add_action( 'init', array( $this, 'register_activity_hashtag_taxonomy' ) );

		if ( function_exists( 'bp_core_admin_hook' ) ) {

			// Hack the hashtags menu for the taxonomy we added.
			add_action( bp_core_admin_hook(), array( $this, 'move_activity_hashtags_to_activity_submenu' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'hack_activity_hashtag_menu' ) );
		}

		// Add autocomplete.
		add_action( 'bulk_actions-edit-bp-activity-hashtags', array( $this, 'hashtag_bulk_actions' ) );
		add_action( 'manage_edit-bp-activity-hashtags_columns', array( $this, 'hashtag_columns' ) );
		add_action( 'manage_bp-activity-hashtags_custom_column', array( $this, 'hashtag_column_values' ), 10, 3 );

		// Save autocomplete.
		add_action( 'admin_init', array( $this, 'bulkedit_hashtag_assign_autocomplete' ) );

		// Autocomplete JS.
		add_action( 'wp_enqueue_scripts', array( $this, 'jquery_textcomplete' ) );

		// Make sure we associate the post with hashtags terms.
		add_action( 'bp_activity_posted_update', array( $this, 'update_hashtag_terms' ), 10, 3 );
	}

	/**
	 * Update hashtag terms.
	 *
	 * @param  string $content     The content.
	 * @param  int    $user_id     The user ID.
	 * @param  int    $activity_id The activity Post ID.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function update_hashtag_terms( $content, $user_id, $activity_id ) {
		$matches = array();

		// Get all the hashtags.
		preg_match_all( $this->pattern, $content, $matches );

		if ( isset( $matches[0] ) ) {

			// Sanitize matches.
			foreach ( $matches[0] as &$match ) {
				if ( ! is_string( $match ) ) {
					unset( $match );
				}
			}

			// Associate all the hashtags.
			wp_set_object_terms( $activity_id, $matches[0], 'bp-activity-hashtags', true );
		}
	}

	/**
	 * Load jQuery Textcomplete.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function jquery_textcomplete() {
		global $app;

		if ( $app->shared->is_activity_page() && is_user_logged_in() ) {
			wp_enqueue_script( 'jquery-textcomplete', "{$app->url}/assets/js/textcomplete.min.js", array( 'jquery' ), time(), false );
			wp_enqueue_script( 'wds-redcolaborar-autocomplete', "{$app->url}/assets/js/autocomplete.js", array( 'jquery', 'jquery-textcomplete' ), time(), false );

			// Get our autocomplete hashtags.
			$hashtags = get_terms( array(
				'taxonomy'   => 'bp-activity-hashtags',
				'hide-empty' => false,
				'fields'     => 'names',

				// Only get one's that have this key.
				'meta_key'   => 'autocomplete',
			) );

			// Make sure we have hashtags, even if they're none, could be a WP_Error.
			if ( ! is_array( $hashtags ) ) {
				$hashtags = array();
			}

			foreach ( $hashtags as &$hashtag ) {
				$hashtag = str_replace( '#', '', $hashtag );
			}

			// Make sure the hashtags are available to JS.
			wp_localize_script( 'wds-redcolaborar-autocomplete', 'wdsRedColaborarHashtags', $hashtags );
		}
	}

	/**
	 * Hashtag column values.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $content     The default content.
	 * @param  string $column_name The column name.
	 * @param  int    $term_id     The term ID.
	 * @return string              The column value.
	 */
	public function hashtag_column_values( $content, $column_name, $term_id ) {
		if ( 'autocomplete' === $column_name ) {
			return get_term_meta( $term_id, 'autocomplete', true ) ? esc_html__( 'Yes', 'redcolaborar' ) : esc_html__( 'No', 'redcolaborar' );
		}
	}

	/**
	 * Add columns to hashtag terms edit.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param array $columns Columns.
	 * @return array         Columns with new one's added.
	 */
	public function hashtag_columns( $columns ) {
		return array_merge( $columns, array(
			'autocomplete' => esc_html__( 'Autocomplete', 'redcolaborar' ),
		) );
	}

	/**
	 * Apply bulk edit for autocomplete to hashtags.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function bulkedit_hashtag_assign_autocomplete() {

		// The tags to apply it to. @codingStandardsIgnoreLine: REQUEST test okay here, we check later.
		$tags = isset( $_REQUEST['delete_tags'] ) ? $_REQUEST['delete_tags'] : array();

		// We must be trying to assign autocomplete. @codingStandardsIgnoreLine: REQUEST test okay here, we check later.
		if ( isset( $_REQUEST['action'] ) && 'add-autocomplete' === $_REQUEST['action'] ) {

			check_admin_referer( 'bulk-tags' );
			foreach ( $tags as $tag_id ) {

				// Set this tag as autocomplete.
				update_term_meta( $tag_id, 'autocomplete', true );
			}
		}

		// We must be trying to remove autocomplete. @codingStandardsIgnoreLine: REQUEST test okay here, we check later.
		if ( isset( $_REQUEST['action'] ) && 'remove-autocomplete' === $_REQUEST['action'] ) {

			check_admin_referer( 'bulk-tags' );
			foreach ( $tags as $tag_id ) {

				// This should no longer be autocomplete.
				delete_term_meta( $tag_id, 'autocomplete' );
			}
		}
	}

	/**
	 * Add menu items to hashtags bulk items menu.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $actions  The actions.
	 * @return array           The actions with autocomplete enabled added.
	 */
	public function hashtag_bulk_actions( $actions ) {
		return array_merge( $actions, array(
			'add-autocomplete'    => esc_html__( 'Enable Autocomplete', 'redcolaborar' ),
			'remove-autocomplete' => esc_html__( 'Disable Autocomplete', 'redcolaborar' ),
		) );
	}

	/**
	 * Hack the submenu to show our hashtags menu.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hack_activity_hashtag_menu() {
		global $app;

		$screen = get_current_screen();
		if ( 'edit-tags' === $screen->base && 'bp-activity-hashtags' === $screen->taxonomy ) {

			// Because the activity isn't *really* a normal CPT, we have to hack the admin menu using JS to hightlight when the activity topics are loaded.
			wp_enqueue_script( 'wds-redcolaborar-activity-sub-menu', "{$app->url}/assets/js/activity-sub-menu.js", array( 'jquery' ), time(), false );
		}
	}

	/**
	 * Move the "Hashtags" activity category from the Post's menu to the Activity menu.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function move_activity_hashtags_to_activity_submenu() {

		// Add a link to that tags page from activity menu.
		add_submenu_page( 'bp-activity', '', esc_html__( 'Hashtags', 'redcolaborar' ), 'edit_posts', 'edit-tags.php?taxonomy=bp-activity-hashtags&post_type=bp-activity' );
	}

	/**
	 * Register the activity Hashtag taxonomy.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function register_activity_hashtag_taxonomy() {
		register_taxonomy( 'bp-activity-hashtags', array( 'bp-activity' ), array(
			'labels'            => array(
				'name'                  => _x( 'Hashtags', 'Taxonomy Hashtags', 'redcolaborar' ),
				'singular_name'         => _x( 'Hashtag', 'Taxonomy Hashtag', 'redcolaborar' ),
				'search_items'          => __( 'Search Hashtags', 'redcolaborar' ),
				'popular_items'         => __( 'Popular Hashtags', 'redcolaborar' ),
				'all_items'             => __( 'All Hashtags', 'redcolaborar' ),
				'parent_item'           => __( 'Parent Hashtag', 'redcolaborar' ),
				'parent_item_colon'     => __( 'Parent Hashtag', 'redcolaborar' ),
				'edit_item'             => __( 'Edit Hashtag', 'redcolaborar' ),
				'update_item'           => __( 'Update Hashtag', 'redcolaborar' ),
				'add_new_item'          => __( 'Add New Hashtag', 'redcolaborar' ),
				'new_item_name'         => __( 'New Hashtag Name', 'redcolaborar' ),
				'add_or_remove_items'   => __( 'Add or remove Hashtags', 'redcolaborar' ),
				'choose_from_most_used' => __( 'Choose from most used Hashtags', 'redcolaborar' ),
				'menu_name'             => __( 'Hashtags', 'redcolaborar' ),
			),
			'public'            => false,
			'show_in_nav_menus' => false,
			'show_admin_column' => false,
			'hierarchical'      => false,
			'show_tagcloud'     => false,
			'show_ui'           => true,
			'query_var'         => true,
			'rewrite'           => false,
			'capabilities'      => array(),
		) );
	}

	/**
	 * Update hashtags.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  int    $activity_id On the given activity.
	 * @param  string $hashtag     Hashtag or list of hashtags.
	 * @return void                Early bail when this isn't necessary.
	 */
	public function update_hashatag( $activity_id, $hashtag ) {
		if ( empty( $hashtag ) ) {

			// No hashtag to add.
			return;
		}

		// If we have a csv list, let's make sure it's an array.
		if ( stristr( $hashtag, ',' ) ) {
			$hashtags = explode( ',', $hashtag );
		} else {
			$hashtags = array( $hashtag );
		}

		global $wpdb;
		$activity_content = $wpdb->get_var( $wpdb->prepare( "SELECT content FROM {$wpdb->prefix}bp_activity WHERE id = %d", $activity_id ) );
		if ( empty( trim( $activity_content ) ) ) {

			// No content to add tag to.
			return;
		}

		foreach ( $hashtags as $hashtag ) {
			$hashtag = trim( $hashtag );

			// If the hashtag isn't already in the text.
			if ( ! stristr( $activity_content, $hashtag ) ) {

				// Sanitize.
				$hashtag = esc_html( $hashtag );

				// It hasn't been tagged, go for it.
				$activity_content = "{$activity_content} {$hashtag}";

				// Make sure the hashtag term gets updated too.
				$this->update_hashtag_terms( $hashtag, 0, $activity_id );
			}
		}

		// Update the content with the new hashtags.
		$update = $wpdb->update( "{$wpdb->prefix}bp_activity", array( 'content' => $activity_content ), array( 'ID' => $activity_id ), array( '%s' ), array( '%d' ) );
	}

	/**
	 * Fix hashtag link.
	 *
	 * All we do here is listen for them to be searching for a
	 * hashtag, if they are, use our own filter queries.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early exit if they aren't even searching for a hashtag.
	 */
	public function fix_hashtag_links() {

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( ! isset( $_GET['s'] ) ) {
			return;
		}

		// @codingStandardsIgnoreLine: GET access okay here.
		$is_hashtag = preg_match( $this->pattern, $_GET['s'] );

		if ( $is_hashtag ) {
			$hashtag = urlencode( $_GET['s'] ); // @codingStandardsIgnoreLine: GET access okay here.
			wp_redirect( add_query_arg( array(
				'search' => $hashtag,
				'action' => 'filter-activity',
			), home_url() ) );
			exit;
		}
	}
}
