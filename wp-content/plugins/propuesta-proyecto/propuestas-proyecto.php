<?php
/*
Plugin Name: Propuestas de Proyecto
Description: Custom Post Types Propuesta de Proyecto for Red Colaborar prototype. It enables users to share project proposals and comment on them
Author: Maurizio Bricola
Author URI: http://www.creators.toys
*/

# Create Project Proposal Post Type for Members

/*-------------------------------------------------------------------------------------------*/
/* propuesta_proyecto Post Type */
/*-------------------------------------------------------------------------------------------*/

add_action('init', 'create_post_type');

function create_post_type() {
	// Set up the arguments for the post type.
	$args = array(
		// A short description of what your post type is. As far as I know, this isn't used anywhere
		// in core WordPress.  However, themes may choose to display this on post type archives.
		'description'         => __( 'Custom Post Types Propuesta de Proyecto for Red Colaborar prototype. It enables users to share project proposals and comment on them.', '' ), // string
		// Whether the post type should be used publicly via the admin or by front-end users.  This
		// argument is sort of a catchall for many of the following arguments.  I would focus more
		// on adjusting them to your liking than this argument.
		'public'              => true, // bool (default is FALSE)
		// Whether queries can be performed on the front end as part of parse_request().
		'publicly_queryable'  => true, // bool (defaults to 'public').
		// Whether to exclude posts with this post type from front end search results.
		'exclude_from_search' => false, // bool (defaults to the opposite of 'public' argument)
		// Whether individual post type items are available for selection in navigation menus.
		'show_in_nav_menus'   => true, // bool (defaults to 'public')
		// Whether to generate a default UI for managing this post type in the admin. You'll have
		// more control over what's shown in the admin with the other arguments.  To build your
		// own UI, set this to FALSE.
		'show_ui'             => true, // bool (defaults to 'public')
		// Whether to show post type in the admin menu. 'show_ui' must be true for this to work.
		// Can also set this to a string of a top-level menu (e.g., 'tools.php'), which will make
		// the post type screen be a sub-menu.
		'show_in_menu'        => true, // bool (defaults to 'show_ui')
		// Whether to make this post type available in the WordPress admin bar. The admin bar adds
		// a link to add a new post type item.
		'show_in_admin_bar'   => true, // bool (defaults to 'show_in_menu')
		// The position in the menu order the post type should appear. 'show_in_menu' must be true
		'menu_position'       => null, // int (defaults to 25 - below comments)
		// The URI to the icon to use for the admin menu item or a dashicon class. See:
		// https://developer.wordpress.org/resource/dashicons/
		'menu_icon'           => 'dashicons-lightbulb', // string (defaults to use the post icon)
		// Whether the posts of this post type can be exported via the WordPress import/export plugin
		// or a similar plugin.
		'can_export'          => true, // bool (defaults to TRUE)
		// Whether to delete posts of this type when deleting a user who has written posts.
		'delete_with_user'    => true, // bool (defaults to TRUE if the post type supports 'author')
		// Whether this post type should allow hierarchical (parent/child/grandchild/etc.) posts.
		'hierarchical'        => true, // bool (defaults to FALSE)
		// Whether the post type has an index/archive/root page like the "page for posts" for regular
		// posts. If set to TRUE, the post type name will be used for the archive slug.  You can also
		// set this to a string to control the exact name of the archive slug.
		'has_archive'         => true, // bool|string (defaults to FALSE)
		// Sets the query_var key for this post type. If set to TRUE, the post type name will be used.
		// You can also set this to a custom string to control the exact key.
		'query_var'           => true, // bool|string (defaults to TRUE - post type name)
		// A string used to build the edit, delete, and read capabilities for posts of this type. You
		// can use a string or an array (for singular and plural forms).  The array is useful if the
		// plural form can't be made by simply adding an 's' to the end of the word.  For example,
		// array( 'box', 'boxes' ).
		'taxonomies' => array ( 'category', 'post_tag'),// add categories and taxonomy support to custom post type
		'capability_type'     => 'propuesta-proyecto', // string|array (defaults to 'post')
		// Whether WordPress should map the meta capabilities (edit_post, read_post, delete_post) for
		// you.  If set to FALSE, you'll need to roll your own handling of this by filtering the
		// 'map_meta_cap' hook.
		'map_meta_cap'        => true, // bool (defaults to FALSE)
		// Provides more precise control over the capabilities than the defaults.  By default, WordPress
		// will use the 'capability_type' argument to build these capabilities.  More often than not,
		// this results in many extra capabilities that you probably don't need.  The following is how
		// I set up capabilities for many post types, which only uses three basic capabilities you need
		// to assign to roles: 'manage_propuestas_proyecto', 'edit_propuestas_proyecto', 'create_propuestas_proyecto'.  Each post type
		// is unique though, so you'll want to adjust it to fit your needs.
		'capabilities' => array(
			// meta caps (don't assign these to roles)
			'edit_post'              => 'edit_propuesta_proyecto',
			'read_post'              => 'read_propuesta_proyecto',
			'delete_post'            => 'delete_propuesta_proyecto',
			// primitive/meta caps
			'create_posts'           => 'create_propuestas_proyectos',
			// primitive caps used outside of map_meta_cap()
			'edit_posts'             => 'edit_propuestas_proyecto',
			'edit_others_posts'      => 'edit_others_propuestas_proyecto',
			'publish_posts'          => 'publish_propuestas_proyecto',
			'read_private_posts'     => 'read_private_propuestas_proyecto',
			// primitive caps used inside of map_meta_cap()
			'read'                   => 'read',
			'delete_posts'           => 'delete_propuestas_proyecto',
			'delete_private_posts'   => 'delete_private_propuestas_proyecto',
			'delete_published_posts' => 'delete_published_propuestas_proyecto',
			'delete_others_posts'    => 'delete_others_propuestas_proyecto',
			'edit_private_posts'     => 'edit_propuestas_proyecto',
			'edit_published_posts'   => 'edit_propuestas_proyecto',
			'unfiltered_upload'			 => 'unfiltered_upload',
			'upload_files'					 => 'upload_files'
		),
		// How the URL structure should be handled with this post type.  You can set this to an
		// array of specific arguments or true|false.  If set to FALSE, it will prevent rewrite
		// rules from being created.
		'rewrite' => array(
			// The slug to use for individual posts of this type.
			'slug'       => 'propuesta-proyecto', // string (defaults to the post type name)
			// Whether to show the $wp_rewrite->front slug in the permalink.
			'with_front' => true, // bool (defaults to TRUE)
			// Whether to allow single post pagination via the <!--nextpage--> quicktag.
			'pages'      => true, // bool (defaults to TRUE)
			// Whether to create pretty permalinks for feeds.
			'feeds'      => true, // bool (defaults to the 'has_archive' argument)
			// Assign an endpoint mask to this permalink.
			'ep_mask'    => EP_PERMALINK, // const (defaults to EP_PERMALINK)
		),
		// What WordPress features the post type supports.  Many arguments are strictly useful on
		// the edit post screen in the admin.  However, this will help other themes and plugins
		// decide what to do in certain situations.  You can pass an array of specific features or
		// set it to FALSE to prevent any features from being added.  You can use
		// add_post_type_support() to add features or remove_post_type_support() to remove features
		// later.  The default features are 'title' and 'editor'.
		'supports' => array(
			// Post titles ($post->post_title).
			'title',
			// Post content ($post->post_content).
			'editor',
			// Post author ($post->post_author).
			'author',
			// Featured images (the user's theme must support 'post-thumbnails').
			'thumbnail',
			// Displays comments meta box.  If set, comments (any type) are allowed for the post.
			'comments',
			// Displays the Revisions meta box. If set, stores post revisions in the database.
			'revisions',
		),
		// Labels used when displaying the posts in the admin and sometimes on the front end.  These
		// labels do not cover post updated, error, and related messages.  You'll need to filter the
		// 'post_updated_messages' hook to customize those.
		'labels' => array(
			'name'                  => 'Propuestas de Proyecto',
			'singular_name'         => 'Propuesta Proyecto',
			'menu_name'             => 'Propuesta de Proyecto',
			'name_admin_bar'        => 'Propuestas de Proyecto',
			'add_new'               => 'Agregar Nueva',
			'add_new_item'          => 'Agregar Nueva Propuesta de Proyecto',
			'edit_item'             => 'Editar Propuesta de Proyecto',
			'new_item'              => 'Nueva Propuesta de Proyecto',
			'view_item'             => 'Ver Propuesta de Proyecto',
			'search_items'          => 'Buscar Propuestas de Proyecto',
			'not_found'             => 'No se han encontrado Propuestas de Proyecto',
			'not_found_in_trash'    => 'No se han encontrado Propuestas de Proyecto en la papelera',
			'all_items'             => 'Todas las Propuestas de Proyecto',
			'featured_image'        => 'Imagen Destacada',
			'set_featured_image'    => 'Set featured image',
			'remove_featured_image' => 'Remove featured image',
			'use_featured_image'    => 'Use as featred image',
			'insert_into_item'      => 'Insertar en la Propuesta de Proyecto',
			'uploaded_to_this_item' => 'Uploaded a la Propuesta de Proyecto',
			'views'                 => 'Filtrar el listado de Propuestas de Proyecto',
			'pagination'            => 'Navigación del listado de Propuestas',
			'list'                  => 'Listado de Propuestas',
			// Labels for hierarchical post types only.
			'parent_item'        => 'Parent Post',
			'parent_item_colon'  => 'Parent Post:',
		)
	);
	// Register the post type.
	register_post_type(
		'propuesta_proyecto', // Post type name. Max of 20 characters. Uppercase and spaces not allowed.
		$args      // Arguments for post type.
	);
}

?>
