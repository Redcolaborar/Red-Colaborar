<?php
/**
 * Activity feed sidebar.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

namespace WebDevStudios\RedColaborar;

/**
 * Activity feed sidebar.
 *
 * @since 1.0.0
 */
class Sidebar {

	/**
	 * Hooks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {

		// Make sure our $_GET is set with any sent filters.
		add_action( 'init', array( $this, 'set_ajax_filters_get' ) );

		// Don't allow empty filters.
		add_action( 'template_redirect', array( $this, 'dont_allow_empty_filters' ) );

		// When the sidebar loads, let's load the template.
		add_action( 'redcolaborar_sidebar', array( $this, 'template' ) );

		// When we update/post an activity post...
		add_action( 'bp_activity_posted_update', array( $this, 'update_activity_topic' ), 10, 3 );

		// Modify the activity loop filters.
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_for_topics' ), 10 );
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_date' ), 10 );
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_search' ), 10 );
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_mentions' ), 10 );
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_bookmarks' ), 10 );
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_order' ), 10 );

		// Set the activity filter types.
		add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'set_activity_filter_types' ), 9999 );

		// Comments this out until we re-add the author filtering.
		// add_filter( 'wds_redcolaborar_activity_filters', array( $this, 'adjust_activity_filter_by_author' ), 10 );

		// Hide some template elements.
		add_action( 'wds_redcolaborar_item_list_tabs_classes', array( $this, 'remove_activity_ajax_navigation' ) );

		// Save the user's default view.
		add_action( 'template_redirect', array( $this, 'save_default_view' ), 10 );
		add_action( 'template_redirect', array( $this, 'load_default_view' ), 20 );

		// Make sure we save the category we added when it's saved.
		add_action( 'wp_ajax_buddypress-edit-activity-save', array( $this, 'save_edit_topics_category' ) );

		// Filter the activity feed.
		add_action( 'bp_ajax_querystring', array( $this, 'filter_activity_feed' ), 99, 2 );

		// Hide load new auto-refresh when filters are present.
		add_action( 'body_class', array( $this, 'hide_autorefresh_button' ) );

		// Add content to the activity filters bar.
		add_action( 'wds_redcolaborar_display_activity_filters_bar', array( $this, 'default_view' ) );
	}

	/**
	 * Don't allow the submission of empty filters.
	 *
	 * Because the filter sidebar could submit a GET query that really
	 * has no values set, here we look for that, and if it happens,
	 * goes to the homepage with no GET filters set.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function dont_allow_empty_filters() {

		// Copy $_GET.
		$filters = $_GET; // @codingStandardsIgnoreLine: GET access okay here.

		// If we're filtering our filters...
		if ( isset( $filters['action'] ) && 'filter-activity' === $filters['action'] ) {

			// Remove the action setting.
			unset( $filters['action'] );

			// Clear our any empty values.
			foreach ( $filters as $key => $value ) {
				if ( empty( $value ) ) {
					unset( $filters[ $key ] );
				}
			}

			// If, at the end, we're empty, no filters were actually set.
			if ( empty( $filters ) ) {

				// No filters must be present, clear them!
				wp_redirect( home_url() );
				exit;
			}
		}
	}

	/**
	 * Convert Load More AJAX filters to $_GET.
	 *
	 * This takes an AJAX request (which should submit wdsFilters) and turns
	 * that wdsFilters value into values in $_GET where the rest
	 * of this script is expecting the data to be.
	 *
	 * Since all of our filters are stored in the GET URL values, e.g.
	 * ?topics=..., we need to send that to the backend when using an AJAX request,
	 * but then re-set $_GET to have those values.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 *
	 * @return void Early bail when not on the activity page/AJAX request.
	 */
	public function set_ajax_filters_get() {
		if ( ! app()->shared->is_activity_page() ) {
			return;
		}

		// This is where the AJAX request should be setting the data. @codingStandardsIgnoreLine: REQUEST access okay here.
		$wds_filters = isset( $_REQUEST['wdsFilters'] ) ? $_REQUEST['wdsFilters'] : array();

		if ( ! empty( $wds_filters ) ) {

			// This should convert ?topics=....&search=....&date-after=... to an array we can re-assign to $_GET.
			$wds_filters = wp_parse_args( $wds_filters, array() );

			if ( is_array( $wds_filters ) ) {
				foreach ( $wds_filters as $filter => $value ) {
					$_GET[ $filter ] = $value;
				}
			}
		}
	}

	/**
	 * Set the filter types presented on the activity feed.
	 *
	 * - MediaPress Uploads
	 * - Activity Updates
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Filters.
	 * @return array           Filters.
	 */
	public function set_activity_filter_types( $filters ) {
		/*
		Filters by the `type` column in the database, which is a string
		categorizing the activity item (eg, 'new_blog_post', 'created_group').
		Accepts a comma-delimited string or an array of types. Default: false.
		 */
		return array_merge( $filters, array( 'action' => array( 'activity_update', 'mpp_media_upload' ) ) );
	}

	/**
	 * Show options to save and clear default view.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail if user is not logged in.
	 */
	public function default_view() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		?>

		<?php if ( ! $this->is_default_view() ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'default-enabled', 'on', $_SERVER['REQUEST_URI'] ) ); ?>"><?php esc_html_e( 'Save as my Default View', 'redcolaborar' ); ?></a>
		<?php else : ?>
			<a href="<?php echo esc_url( add_query_arg( 'action', 'clear-default-view', $_SERVER['REQUEST_URI'] ) ); ?>"><?php esc_html_e( 'Clear my Default View', 'redcolaborar' ); ?></a>
		<?php endif; ?>

		<?php
	}

	/**
	 * Hide auto refresh button.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param array $body_classes The current body classes.
	 * @return array              The body classes with filtering added when filtering on the activity page.
	 */
	public function hide_autorefresh_button( $body_classes = array() ) {

		// @codingStandardsIgnoreLine: GET access here.
		if ( app()->shared->is_activity_page() && ! empty( $_GET ) ) {
			return array_merge( $body_classes, array( 'filtering' ) );
		}

		return $body_classes;
	}

	/**
	 * Filter the activity feed.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $query_string The current BP query string.
	 * @param  string $object       The object, e.g. "activity".
	 * @return string               The current BP query string with our filters combined.
	 */
	public function filter_activity_feed( $query_string, $object ) {
		if ( 'activity' !== $object || false === app()->shared->is_activity_page() ) {
			return $query_string;
		}

		/**
		 * Filter the filters used to filter the activities (whew).
		 *
		 * This is where the sidebar filter values get plugged into the
		 * activity feed.
		 *
		 * @author Aubrey Portwood
		 * @since  NEXT
		 *
		 * @param string $filters The filters passed to bp_ajax_querystring().
		 */
		$filters = build_query( apply_filters( 'wds_redcolaborar_activity_filters', array() ) );

		// Combine our filters with BP's own.
		if ( ! empty( $filters ) ) {

			// We have modified filters, add them.
			$query_string = "{$query_string}&{$filters}";
		}

		return $query_string;
	}

	/**
	 * Filter activity by orderby parameter.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Filters.
	 * @return array           Filters.
	 */
	public function adjust_activity_filter_by_order( $filters ) {
		if ( ! app()->shared->is_activity_page() ) {
			return $filters;
		}

		if ( ! $this->enabled( 'orderby' ) ) {
			return $filters;
		}

		/*
		 @todo: We need to actually add filters here, but
		        it's pending discussions with the client
		        ATM.
		 */

		return $filters;
	}

	/**
	 * Filter the activity feed by username/author.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Filters.
	 * @return array           Filters with user_id set.
	 */
	public function adjust_activity_filter_by_author( $filters ) {
		if ( ! app()->shared->is_activity_page() ) {
			return $filters;
		}

		if ( ! $this->enabled( 'username' ) ) {
			return $filters;
		}

		$user_id = $this->get_filtered_username( 'id' );
		if ( empty( $user_id ) ) {
			return $filters;
		}

		return array_merge( $filters, array(
			'user_id' => $user_id,
		) );
	}

	/**
	 * Get the filtered username.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 *
	 * @param string $format Format, id for user id, otherwise the username string.
	 * @param string $force  Whether or not to force return of the username value, even if it doesn't exist.
	 * @return string|int    The username or user id.
	 */
	public function get_filtered_username( $format = '', $force = false ) {

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( ! isset( $_GET['username'] ) ) {
			return 'int' === $format ? 0 : '';
		}

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( ! username_exists( $_GET['username'] ) ) {
			if ( 'id' === $format ) {

				// You asked for an ID, so you always get an int, so if the user doesn't exist, you get 0.
				return 0;
			}

			// The username doesn't exist, you can have the value if you want it, but otherwise we'll give you nothing when they don't exist.
			return $force ? sanitize_text_field( $_GET['username'] ) : ''; // @codingStandardsIgnoreLine: GET access okay here.
		}

		if ( 'id' === $format ) {

			// @codingStandardsIgnoreLine: GET access okay here.
			$user = get_user_by( 'login', $_GET['username'] );

			// Not instanceof is not working here, used is_a instead.
			if ( is_a( $user, 'WP_User' ) ) {

				// Found a user, use this.
				return $user->ID;
			}

			// No user, use this.
			return 0;
		}

		// They want a string, pass back the username. @codingStandardsIgnoreLine: GET access okay here.
		return sanitize_text_field( isset( $_GET['username'] ) ? $_GET['username'] : '' );
	}

	/**
	 * Save the category from the edit activity AJAX modal.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function save_edit_topics_category() {
		check_ajax_referer( 'buddypress-edit-activity', 'buddypress_edit_activity_nonce' );

		// What activity?
		$activity_id = isset( $_REQUEST['activity_id'] ) ? $_REQUEST['activity_id'] : 0;

		// What category did they choose?
		$activity_category_id = isset( $_REQUEST['category'] ) ? absint( $_REQUEST['category'] ) : 0;

		// Set the term for the activity.
		$this->wp_set_object_terms_and_activity_meta( $activity_id, $activity_category_id );
	}

	/**
	 * Get the user's default view.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return mixed The value of get_user_meta.
	 */
	public function get_default_view() {
		return get_user_meta( get_current_user_id(), 'bp_activity_default_url', true );
	}

	/**
	 * Load the user's default view on activity.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail when not on the activity page.
	 */
	public function load_default_view() {
		if ( isset( $_GET['action'] ) && 'clear-default-view' === $_GET['action'] ) {

			// The user wants to clear their meta.
			delete_user_meta( get_current_user_id(), 'bp_activity_default_url' );

			// Don't do it again.
			wp_redirect( add_query_arg( 'action', 'filter-activity', remove_query_arg( 'action', $_SERVER['REQUEST_URI'] ) ) );
			exit;
		}

		if ( ! app()->shared->is_activity_page() ) {

			// Only re-direct on the activity page.
			return;
		}

		// If this is not the current default view, and we aren't trying to filter...
		if ( ! $this->is_default_view() && empty( $_GET ) ) { // @codingStandardsIgnoreLine: GET access okay.

			// Get the user's default view from meta.
			$default_view = $this->get_default_view();

			// If we have a default view and it's not already loaded.
			if ( ! empty( $default_view ) ) {

				// And as long as it's something, try to go to it.
				wp_redirect( add_query_arg( 'action', 'load-default-view', home_url( $default_view ) ) );
				exit;
			}
		}
	}

	/**
	 * Save the user's default view.
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 *
	 * @return void Early bail if it's not supposed to save.
	 */
	public function save_default_view() {
		if ( ! app()->shared->is_activity_page() ) {

			// Only re-direct on the activity page.
			return;
		}

		if ( ! $this->enabled( 'default' ) ) {

			// Don't update or re-direct on any other pages.
			return;
		}

		$request = ( isset( $_SERVER['REQUEST_URI'] ) && is_string( $_SERVER['REQUEST_URI'] ) )
			? strip_tags( $_SERVER['REQUEST_URI'] ) :
			'';

		$request = $this->prepare_request_for_save( $request );

		// Save to the user's meta.
		update_user_meta( get_current_user_id(), 'bp_activity_default_url', $request );

		// Go to that view.
		wp_redirect( add_query_arg( 'action', 'filter-activity', $request ) );
		exit;
	}

	/**
	 * Strip the saved view request so we don't keep re-saving the default view again and again.
	 *
	 * The reason we do this is because if we save the view to the DB and it
	 * still has ?default=enabled=on it will, upon loading that view, re-save
	 * itself as the default view.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $request The request, usually $_SERVER['REQUEST_URI'].
	 * @return stting          The request, with some query vars stripped.
	 */
	public function prepare_request_for_save( $request ) {

		// Never save the default url with these keys set.
		return remove_query_arg( array( 'default-enabled', 'action' ), $request );
	}

	/**
	 * Hide the activity ajax filters, we have our own.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function remove_activity_ajax_navigation() {
		echo esc_attr( ' hidden' );
	}

	/**
	 * Filter my bookmarks.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Activity feed filters.
	 * @return array           Activity feed filters, with scope for favorites set if we ask for them.
	 */
	public function adjust_activity_filter_by_bookmarks( $filters ) {
		if ( ! $this->enabled( 'bookmarks' ) ) {
			return $filters;
		}

		return array_merge( $filters, array(
			'scope' => 'favorites',
		) );
	}

	/**
	 * Filter by mediapress content type,
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Activity feed filters.
	 * @return array           Activity feed filters.
	 */
	public function adjust_activity_filter_by_content_type( $filters ) {
		if ( ! $this->enabled( 'media' ) ) {
			return $filters;
		}

		// Keep current meta queries.
		$current_query = isset( $filters['meta_query'] ) ? $filters['meta_query'] : array();
		return array_merge( $filters, array(

			// Always append search terms so we don't loose any.
			'meta_query' => array_merge( $current_query, array(

				// Still any of the meta queries.
				'relation' => 'AND',

				// Get anything that has these meta values.
				array(
					'relation' => 'OR',

					array(
						'key'   => '_mpp_activity_type',
						'value' => 'media_upload',
					),
					array(
						'key'   => '_mpp_activity_type',
						'value' => 'media_comment',
					),
					array(
						'key'   => '_mpp_activity_type',
						'value' => 'media_publish',
					),
					array(
						'key'   => '_mpp_activity_type',
						'value' => 'gallery_comment',
					),
					array(
						'key'   => '_mpp_activity_type',
						'value' => 'add_media',
					),
				),
			) ),
		) );
	}

	/**
	 * Filter my mentions.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  Activity feed filters.
	 * @return array           Activity feed filters, with scope for mentions set if we ask for them.
	 */
	public function adjust_activity_filter_by_mentions( $filters ) {
		if ( ! $this->enabled( 'mentions' ) ) {
			return $filters;
		}

		return array_merge( $filters, array(
			'scope' => 'mentions',
		) );
	}

	/**
	 * Is the default view for the user loaded?
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return boolean True if it is, false if not.
	 */
	public function is_default_view() {
		return $this->get_default_view() === $this->prepare_request_for_save( $_SERVER['REQUEST_URI'] );
	}

	/**
	 * Is a filter component enabled, e.g. date, categories.
	 *
	 * Note search is treated differently.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string $component_checkbox The component, e.g. date, categories.
	 * @return boolean                    True if it is enabled, false if not.
	 */
	public function enabled( $component_checkbox ) {
		if ( 'search' === $component_checkbox && ! empty( $this->get_search_filter() ) ) {

			// The search component does not have an enabler, but it's enabled if we're searching.
			return true;
		}

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( 'content-types' === $component_checkbox && ( isset( $_GET['content-types'] ) && ! empty( $_GET['content-types'] ) ) ) {
			return true;
		}

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( isset( $_GET["{$component_checkbox}-enabled"] ) ) {

			// These are checkbox components.
			return true;
		}

		return false;
	}

	/**
	 * Get the search filter value.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return string The value in ?search=
	 */
	public function get_search_filter() {

		// @codingStandardsIgnoreLine: GET access okay here.
		return isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : '';
	}

	/**
	 * Filter activity feed by search filter.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  The filters being used.
	 * @return array           The filters, but with search_terms if we're searching.
	 */
	public function adjust_activity_filter_by_search( $filters ) {
		if ( ! app()->shared->is_activity_page() ) {
			return $filters;
		}

		if ( ! $this->enabled( 'search' ) ) {
			return $filters;
		}

		$search = $this->get_search_filter();

		if ( empty( $search ) ) {
			return $filters;
		}

		return array_merge( $filters, array(

			// Always append search terms so we don't loose any.
			'search_terms' => $this->csv_append_to( $search, $filters['search_terms'] ),
		) );
	}

	/**
	 * Append something to a CSV list.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param string $value    The item to add.
	 * @param string $to       The current list.
	 * @return string          A CSV of the list, with that added.
	 */
	public function csv_append_to( $value, $to ) {
		if ( empty( $to ) ) {

			// Nothing to add, first value.
			return $value;
		}

		return "{$to},{$value}";
	}

	/**
	 * Get the filtered topics.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return array A list of ID's of topics.
	 */
	public function get_filtered_topics() {

		// @codingStandardsIgnoreLine: GET access okay here, we sanitize before, and we don't update in any way this value.
		return isset( $_GET['topics'] ) ? array_map( 'absint', $_GET['topics'] ) : array();
	}

	/**
	 * Filter activity by date or date ranges.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  The filters being used on the activity template.
	 * @return array           The filters with date ranges.
	 */
	public function adjust_activity_filter_by_date( $filters ) {
		if ( ! app()->shared->is_activity_page() ) {
			return $filters;
		}

		if ( ! $this->enabled( 'date' ) ) {
			return $filters;
		}

		$date_before = $this->get_filtered_date( 'array', 'before' );
		$date_after = $this->get_filtered_date( 'array', 'after' );

		if ( ! empty( $date_before ) && ! empty( $date_after ) ) {
			return array_merge( $filters, array(
				'date_query' => array(
					'after' => $date_after,
					'before' => $date_before,

					// Include items on the after or before date.
					'inclusive' => true,
				),
			) );
		}

		// We have a date before, but no after.
		if ( ! empty( $date_before ) && empty( $date_after ) ) {
			return array_merge( $filters, array(
				'date_query' => array(
					'before' => $date_before,

					// Include items on before.
					'inclusive' => true,
				),
			) );
		}

		// We have a date after, but no before.
		if ( empty( $date_before ) && ! empty( $date_after ) ) {
			return array_merge( $filters, array(
				'date_query' => array(
					'after' => $date_after,

					// Include items on after.
					'inclusive' => true,
				),
			) );
		}

		return $filters;
	}

	/**
	 * Change what displays on the activity loop.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  array $filters  The filters their using.
	 * @return array           The filters we want to use too.
	 */
	public function adjust_activity_filter_for_topics( $filters ) {
		if ( ! app()->shared->is_activity_page() ) {
			return $filters;
		}

		if ( ! $this->enabled( 'categories' ) ) {
			return $filters;
		}

		$required_topic_categories = $this->get_filtered_topics();
		if ( empty( $required_topic_categories ) ) {

			// No topics are required, just use default.
			return $filters;
		}

		// Compile a list of topic meta key/value pairs the activity must have (at least one).
		$topic_meta = array();
		foreach ( $required_topic_categories as $topic_id ) {
			$topic_meta[] = array(

				/*
				 We assign the topic id to meta when we also add it to the term,
				 this way we can use the meta_query feature of BP (because it doesn't have
				 a taxonomy query feature).
				 */
				'key' => 'topic',
				'value' => $topic_id,
			);
		}

		// Keep any current queries.
		$current_query = isset( $filters['meta_query'] ) ? $filters['meta_query'] : array();
		return array_merge( $filters, array(
			'meta_query' => array_merge( $current_query, array(
				'relation' => 'AND',

				array_merge( array(
					'relation' => 'OR',
				), $topic_meta ),
			) ),
		) );
	}

	/**
	 * Set activity topics on new activity posts.
	 *
	 * @param  string $content     The content.
	 * @param  int    $user_id     The user ID.
	 * @param  int    $activity_id The activity Post ID.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function update_activity_topic( $content, $user_id, $activity_id ) {

		// Get the category (topic activity) they want to post it in. @codingStandardsIgnoreLine: Direct access okay here, because by the time this hook runs, nonce checks have already been done.
		$activity_category_id = isset( $_POST['category'] ) ? absint( $_POST['category'] ) : 0;

		// Update the term.
		$this->wp_set_object_terms_and_activity_meta( $activity_id, $activity_category_id );
	}

	/**
	 * Add an activity item to a topic, and also update meta so we can query it later.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  int $activity_id             The Activity Post ID.
	 * @param  int $activity_category_id    The category/topic to add it to.
	 */
	public function wp_set_object_terms_and_activity_meta( $activity_id, $activity_category_id ) {

		// Add this activity post to that term/category.
		wp_set_object_terms( $activity_id, $activity_category_id, 'bp-activity-topics', false );

		// Also store what topic they chose as meta for filtering later.
		bp_activity_update_meta( $activity_id, 'topic', $activity_category_id );
	}

	/**
	 * Get the date they are filtering by.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param string $format       What format you want, e.g. array, string, defaults to array.
	 * @param string $before_after Whether to get the before or after date.
	 * @return array|string        An array of year, month, date, or YYYY-MM-DD if you set $string to true.
	 */
	public function get_filtered_date( $format = '', $before_after = 'after' ) {
		$default = array();

		// @codingStandardsIgnoreLine: Direct access okay here, we sanitize below.
		$date = isset( $_GET["date-{$before_after}"] ) ? $_GET["date-{$before_after}"] : '';

		if ( empty( $date ) ) {
			return 'string' === $format ? '' : array();
		}

		// Separate into an array.
		$date = explode( '-', $date );
		if ( count( $date ) !== 3 ) {

			// No date.
			return 'string' === $format ? '' : $default;
		}

		// Validate/sanitize the data.
		$date_array['year'] = strlen( $date[0] ) >= 4 ? absint( $date[0] ) : false;
		$date_array['month'] = $date[1] <= 12 ? absint( $date[1] ) : false;
		$date_array['day'] = $date[2] <= 31 ? absint( $date[2] ) : false;

		// When asking for a string, make sure you get 01/01/2017 format with 0 prefix for HTML date.
		if ( 'string' === $format ) {
			foreach ( array( 'month', 'day' ) as $key ) {
				if ( $date_array[ $key ] < 10 ) {
					$date_array[ $key ] = "0{$date_array[ $key ]}";
				}
			}
		}

		if ( in_array( false, $date_array, true ) ) {

			// Something didn't check out, no date.
			return 'string' === $format ? '' : $default;
		}

		if ( 'string' === $format ) {
			return "{$date_array['year']}-{$date_array['month']}-{$date_array['day']}";
		}

		return $date_array;
	}

	/**
	 * The filter template.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail if not the activity page.
	 */
	public function template() {
		if ( ! app()->shared->is_activity_page() ) {
			return;
		}

		$topics = $this->get_filtered_topics();
		$date_before = $this->get_filtered_date( 'string', 'before' );
		$date_after = $this->get_filtered_date( 'string', 'after' );
		$search = isset( $_GET['search'] ) ? sanitize_text_field( $_GET['search'] ) : ''; // @codingStandardsIgnoreLine: $_GET access okay here.
		$username = $this->get_filtered_username( 'username', true );

		?>
		<aside id="wds-recolaborar-sidebar-filters">
			<form class="filters" method="get">

				<div class="form-element search-form-element search-form">
					<label for="search-field">
						<input
							class="search-field"
							id="search-field"
							type="text"
							name="search"
							value="<?php echo esc_attr( $search ); ?>"
							aria-required="false"
							autocomplete="off"
							placeholder="<?php esc_attr_e( 'Search for posts or #hashtag', 'redcolaborar' ); ?>" />
					</label>
				</div>

				<h5 class="form-element-title"><?php esc_html_e( 'Filter Search', 'redcolaborar' ); ?></h5>

				<div class="form-element">
					<label for="categories-enabled">
						<input
							<?php if ( $this->enabled( 'categories' ) ) : ?>
								checked="checked"
							<?php endif; ?>

							type="checkbox"
							name="categories-enabled"
							id="categories-enabled"
							class="toggle" />

						<?php esc_html_e( 'Categories', 'redcolaborar' ); ?>

						<span class="toggle-target" aria-hidden="true">
							<?php wds_redcolaborar_bp_activity_topics_checklist( $topics ); ?>
						</span>
					</label>
				</div>

				<div class="form-element">
					<input
						<?php if ( $this->enabled( 'date' ) ) : ?>
							checked="checked"
						<?php endif; ?>

							type="checkbox"
							name="date-enabled"
							id="date-enabled"
							class="toggle" />

					<label for="date-enabled">
						<?php esc_html_e( 'Date', 'redcolaborar' ); ?>
					</label>

					<span class="toggle-target" aria-hidden="true">
						<label for="date-after"><?php esc_html_e( 'After', 'redcolaborar' ); ?></label>
						<input type="text" readonly id="date-after" name="date-after" value="<?php echo esc_attr( $date_after ); ?>" /><br />
						<br />
						<label for="date-after"><?php esc_html_e( 'Before', 'redcolaborar' ); ?></label>
						<input type="text" readonly id="date-before" name="date-before" value="<?php echo esc_attr( $date_before ); ?>" />
					</span>
				</div>

				<div class="form-element">
					<label for="mentions-enabled"><input
						<?php if ( $this->enabled( 'mentions' ) ) : ?>
							checked="checked"
						<?php endif; ?>
						type="checkbox"
						id="mentions-enabled"
						name="mentions-enabled" />

						<?php esc_html_e( 'My Mentions', 'redcolaborar' ); ?>
					</label>
				</div>

				<div class="form-element">
					<label for="bookmarks-enabled"><input
						<?php if ( $this->enabled( 'bookmarks' ) ) : ?>
							checked="checked"
						<?php endif; ?>
						type="checkbox"
						id="bookmarks-enabled"
						name="bookmarks-enabled" />

						<?php esc_html_e( 'My Bookmarks', 'redcolaborar' ); ?>
					</label>
				</div>

				<div class="button-group">
					<button type="submit"><?php esc_html_e( 'Apply', 'redcolaborar' ); ?></button>
					<a href="<?php echo esc_url( bp_get_activity_directory_permalink() ); ?>?search=" class="button outline"><?php _e( 'Clear', 'redcolaborar' ); ?></a>
				</div>

				<input type="hidden" name="action" value="filter-activity" />
			</form>
		</aside><!-- #wds-recolaborar-sidebar-filters -->
		<?php
	}

	/**
	 * Discover if filters are being ordered by a specific value.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @param  string  $orderby The value.
	 * @return boolean          True if it is, false if not.
	 */
	public function is_ordered_by( $orderby ) {

		// What was requested?
		$orderby_enabled = isset( $_GET['orderby-enabled'] ) ? $_GET['orderby-enabled'] : '';

		if ( '' === $orderby && '' === $orderby_enabled ) {
			return true;
		}

		if ( 'most-comments' === $orderby && 'most-comments' === $orderby_enabled ) {
			return true;
		}

		if ( 'most-liked' === $orderby && 'most-liked' === $orderby_enabled ) {
			return true;
		}

		return false;
	}

	/**
	 * The markup for the Author filter checkbox.
	 * Moved this to its own function so it can be removed from the sidebar output, but to retain the markup overall.
	 * This should live between Date and My Mentions.
	 *
	 * @author Corey Collins
	 */
	private function author_filter_field() {
		?>
		<div class="form-element">
			<input
				<?php if ( $this->enabled( 'username' ) ) : ?>
					checked="checked"
				<?php endif; ?>

					type="checkbox"
					id="username-enabled"
					name="username-enabled"
					class="toggle" />

			<label for="username-enabled">
				<?php esc_html_e( 'Author', 'redcolaborar' ); ?>
			</label>

			<span class="toggle-target" aria-hidden="true">
				<input type="username" name="username" placeholder="<?php esc_html_e( 'Search by username', 'redcolaborar' ); ?>" value="<?php echo esc_attr( $username ); ?>" /><br />
			</span>
		</div>
		<?php
	}
}
