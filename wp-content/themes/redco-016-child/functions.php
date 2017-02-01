<?php

add_filter( 'wp_nav_menu_items', 'my_nav_menu_loginout_link');
function my_nav_menu_loginout_link($menu) {
    if (is_user_logged_in()){

      $logoutlink = '<li><a href="'. wp_logout_url() .'">Salir</a></li>';
      $menu = $menu . $logoutlink;
      return $menu;

    } else {
      $signlinks = '<li><a href="'. site_url('wp-login.php') .'">Ingresar</a></li><li><a href="'. site_url('wp-login.php?action=register') .'">Registrate</a></li>';
      $menu = $menu . $signlinks;
      return $menu;
    }
}

function theme_enqueue_styles() {

    $parent_style = 'parent-style';

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style )
    );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

# /wp-content/themes/theme_name/functions.php
# Create Project Proposal Post Type for Members

/*-------------------------------------------------------------------------------------------*/
/* propuesta_proyecto Post Type */
/*-------------------------------------------------------------------------------------------*/
/*class propuesta_proyecto {

	function propuesta_proyecto() {
		add_action('init',array($this,'create_post_type'));
	}

	function create_post_type() {
		$labels = array(
		    'name' => 'Propuestas de Proyecto',
		    'singular_name' => 'Propuesta de Proyecto',
		    'add_new' => 'Add New',
		    'all_items' => 'Todas Propuestas de Proyectos',
		    'add_new_item' => 'Add New Propuesta de Proyecto',
		    'edit_item' => 'Edit Propuesta de Proyecto',
		    'new_item' => 'New Propuesta de Proyecto',
		    'view_item' => 'View Propuesta de Proyecto',
		    'search_items' => 'Search Propuesta de Proyecto',
		    'not_found' =>  'No Propuestas de Proyecto found',
		    'not_found_in_trash' => 'No Propuestas de Proyecto found in trash',
		    'parent_item_colon' => 'Parent Post:',
		    'menu_name' => 'Propuestas de Proyecto'
		);
		$args = array(
			'labels' => $labels,
			'description' => "Entradas para compartir y discutir propuestas de proyectos",
			'public' => true,
			'exclude_from_search' => false,
			'publicly_queryable' => true,
			'show_ui' => true,
			'show_in_nav_menus' => true,
			'show_in_menu' => true,
			'show_in_admin_bar' => true,
			'menu_position' => 20,
			'menu_icon' => 'building',
			'taxonomies' => array ( 'category', 'post_tag'),
			'capability_type' => array("propuesta_proyecto", "propuestas_proyecto"),
			'hierarchical' => true,
			'supports' => array('title','editor','custom-fields','comments','revisions','thumbnail'),
			'has_archive' => true,
			'rewrite' => array('slug' => 'your-slug', 'with_front' => 'before-your-slug'),
			'query_var' => true,
			'can_export' => true
		);
		register_post_type('propuesta_proyecto',$args);
	}
}

$propuesta_proyecto = new propuesta_proyecto();*/
											


# /wp-content/themes/theme_name/functions.php
# Give Administrators and Editors All Project Proposals Capabilities

/*
function add_pp_caps_to_admin() {
  $caps = array(
    'read',
    'read_propuesta_proyecto',
    'read_private_propuestas_proyecto',
    'edit_propuestas_proyecto',
    'edit_private_propuestas_proyecto',
    'edit_published_propuestas_proyecto',
    'edit_others_propuestas_proyecto',
    'publish_propuestas_proyecto',
    'delete_propuestas_proyecto',
    'delete_private_propuestas_proyecto',
    'delete_published_propuestas_proyecto',
    'delete_others_propuestas_proyecto',
  );
  $roles = array(
    get_role( 'administrator' ),
    get_role( 'editor' ),
  );
  foreach ($roles as $role) {
    foreach ($caps as $cap) {
      $role->add_cap( $cap );
    }
  }
}
add_action( 'after_setup_theme', 'add_pp_caps_to_admin' ); */


?>
