<?php
/**
 * Activity Feed Categories.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

namespace WebDevStudios\RedColaborar;

/**
 * Activity Feed Categories.
 *
 * @since 1.0.0
 */
class Topics {

	/**
	 * Hook into WP.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hooks() {

		// Create the topic category.
		add_action( 'init', array( $this, 'register_activity_topic_taxonomy' ) );

		if ( function_exists( 'bp_core_admin_hook' ) ) {

			// Hack the category menu for the taxonomy we added.
			add_action( bp_core_admin_hook(), array( $this, 'move_activity_topics_to_activity_submenu' ), 999 );
			add_action( 'admin_enqueue_scripts', array( $this, 'hack_activity_category_menu' ) );
		}

		add_action( 'init', array( $this, 'migrate_activity_posts_from_groups_to_topics' ) );
	}

	/**
	 * Perform a single group conversion.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return void Early bail if we're not performing it.
	 */
	public function migrate_activity_posts_from_groups_to_topics() {
		global $wpdb;

		// @codingStandardsIgnoreLine: GET access okay here.
		if ( ! isset( $_GET['convert_bp_groups'] ) ) {
			return;
		}

		/*
		 * The conversion map (second version).
		 *
		 * @see https://basecamp.com/1756504/projects/14515582/todos/331794627#comment_576792038
		 */
		$convert = array(

			// Agroecología.
			'4' => array(
				'name'    => 'Agroecología',
				'hashtag' => '',
			),

			// Conflictos y Procesos de Paz.
			'18' => array(
				'name'    => 'Inclusión Social',
				'hashtag' => '#iniciativasdepaz',
			),

			// Gerencia de proyectos.
			'16' => array(
				'name'    => 'Desarrollo de Capacidades',
				'hashtag' => '#genenciadeproyetos',
			),

			// Programa FICE.
			'9' => array(
				'name'    => 'Desarrollo de Capacidades',
				'hashtag' => '#FICE, #comunicacion',
			),

			// Inclusión Social y Género.
			'3' => array(
				'name'    => 'Inclusión Social',
				'hashtag' => '',
			),

			// Juventud.
			'2' => array(
				'name'    => 'Inclusión Social',
				'hashtag' => '#juventud',
			),

			// A p i B i o 2016 – Cooperativa de Trabajo COOPSOL.
			'20' => array(
				'name'    => 'Agroecología',
				'hashtag' => '#apicultura',
			),

			// Cambio Climático, Gestión de Recursos Naturales y Manejo de Riesgos.
			'5' => array(
				'name'    => 'Otros',
				'hashtag' => '#recursosnaturales, #cambioclimatico',
			),

			// Desarrollo Económico.
			'19' => array(
				'name'    => 'Otros',
				'hashtag' => '#desarrolloeconomico',
			),

			// Participantes Foro A  W  I  D    —    Brazil 2016.
			'25' => array(
				'name'    => 'Desarrollo de Capacidades',
				'hashtag' => '#mujeres',
			),

			// Rede de Alianças Brasileiras — Fundação Interamericana (IAF).
			'24' => array(
				'name'    => 'Otros',
				'hashtag' => '#Brasil',
			),
		);

		$build_groups = array();
		$groups = $wpdb->get_results( "SELECT id, name, slug, description FROM {$wpdb->prefix}bp_groups" );
		foreach ( $groups as $group ) {
			$activity = $wpdb->get_results( $wpdb->prepare( "SELECT type, id, item_id FROM {$wpdb->prefix}bp_activity WHERE item_id = %d", $group->id ) );

			foreach ( $activity as $activity ) {

				// Add the activity to this group.
				$build_groups[ $group->id ]['activity'][] = $activity->id;
				$build_groups[ $group->id ]['group'] = $group;
			}
		}

		$remap = array();
		foreach ( $build_groups as $group_id => $group ) {

			// @codingStandardsIgnoreLine: Non-strict comparison intended here.
			if ( ! in_array( $group_id, array_keys( $convert ) ) ) {
				continue;
			}

			$remap[ $convert[ $group_id ]['name'] ][ $group_id ] = array_merge( $group, array(
				'hashtag' => $convert[ $group_id ]['hashtag'],
			) );
		}

		foreach ( $remap as $name => $groups ) {

			// A slug to use.
			$slug = sanitize_title_with_dashes( $name );

			// Make sure we have a term for it.
			$term = wp_insert_term( $name, 'bp-activity-topics', array(
				'slug' => $slug,
			) );

			if ( is_wp_error( $term ) ) {

				// We do, make sure we have term_id.
				$term = get_term_by( 'slug', $slug, 'bp-activity-topics', ARRAY_A );
			}

			// Each group that is condensed under this name.
			foreach ( $groups as $group ) {
				foreach ( $group['activity'] as $activity_id ) {

					// Put in the term.
					wp_set_object_terms( $activity_id, $term['term_id'], 'bp-activity-topics', true );

					// Put in the meta.
					bp_activity_update_meta( $activity_id, 'topic', $term['term_id'] );

					/**
					 * Action for updating topic/activity.
					 *
					 * This is done in class-hashtags.php for easy find-ability.
					 *
					 * @author Aubrey Portwood
					 * @since  1.0.0
					 *
					 * @param int $activity_id Activity ID.
					 * @param array $hashtag   The hashtags to update.
					 */
					do_action( 'wds_rc_update_activity_hashtag', $activity_id, $group['hashtag'] );
				}
			}
		}
	}

	/**
	 * Hack the activity category (topics) menu highlighting.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function hack_activity_category_menu() {
		global $app;

		$screen = get_current_screen();
		if ( 'edit-tags' === $screen->base && 'bp-activity-topics' === $screen->taxonomy ) {

			// Because the activity isn't *really* a normal CPT, we have to hack the admin menu using JS to hightlight when the activity topics are loaded.
			wp_enqueue_script( 'wds-redcolaborar-activity-sub-menu', "{$app->url}/assets/js/activity-sub-menu.js", array( 'jquery' ), time(), false );
		}
	}

	/**
	 * Move the "Topics" activity category from the Post's menu to the Activity menu.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function move_activity_topics_to_activity_submenu() {

		// Add a link to that tags page from activity menu.
		add_submenu_page( 'bp-activity', '', esc_html__( 'Topics', 'redcolaborar' ), 'edit_posts', 'edit-tags.php?taxonomy=bp-activity-topics&post_type=bp-activity' );
	}

	/**
	 * Register the activity topic taxonomy.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 */
	public function register_activity_topic_taxonomy() {
		register_taxonomy( 'bp-activity-topics', array( 'bp-activity' ), array(
			'labels'            => array(
				'name'                  => _x( 'Activity Topics', 'Taxonomy Topics', 'redcolaborar' ),
				'singular_name'         => _x( 'Topic', 'Taxonomy Topic', 'redcolaborar' ),
				'search_items'          => __( 'Search Topics', 'redcolaborar' ),
				'popular_items'         => __( 'Popular Topics', 'redcolaborar' ),
				'all_items'             => __( 'All Topics', 'redcolaborar' ),
				'parent_item'           => __( 'Parent Topic', 'redcolaborar' ),
				'parent_item_colon'     => __( 'Parent Topic', 'redcolaborar' ),
				'edit_item'             => __( 'Edit Topic', 'redcolaborar' ),
				'update_item'           => __( 'Update Topic', 'redcolaborar' ),
				'add_new_item'          => __( 'Add New Topic', 'redcolaborar' ),
				'new_item_name'         => __( 'New Topic Name', 'redcolaborar' ),
				'add_or_remove_items'   => __( 'Add or remove Topics', 'redcolaborar' ),
				'choose_from_most_used' => __( 'Choose from most used Topics', 'redcolaborar' ),
				'menu_name'             => __( 'Activity Topics', 'redcolaborar' ),
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
}
