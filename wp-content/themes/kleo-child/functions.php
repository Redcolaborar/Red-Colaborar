<?php
/**
 * @package WordPress
 * @subpackage Kleo
 * @author SeventhQueen <themesupport@seventhqueen.com>
 * @since Kleo 1.0
 */

/**
 * Kleo Child Theme Functions
 * Add custom code below
*/ 



/* Top menu */
function object_to_array($object) {
 if (is_object($object)) {
  return array_map(__FUNCTION__, get_object_vars($object));
 } else if (is_array($object)) {
  return array_map(__FUNCTION__, $object);
 } else {
  return $object;
 }
}

add_action( 'admin_bar_menu', 'remove_wp_logo', 999 );

function remove_wp_logo( $wp_admin_bar ) {
    //if (get_current_user_id() == 1) {
        $top_secondary = object_to_array($wp_admin_bar->get_node( 'top-secondary' ));

        $my_account = object_to_array($wp_admin_bar->get_node( 'my-account' ));
        $user_actions = object_to_array($wp_admin_bar->get_node( 'user-actions' ));
        $user_info = object_to_array($wp_admin_bar->get_node( 'user-info' ));
        $edit_profile = object_to_array($wp_admin_bar->get_node( 'edit-profile' ));
        $logout = object_to_array($wp_admin_bar->get_node( 'logout' ));

        $my_account_activity = object_to_array($wp_admin_bar->get_node( 'my-account-activity' ));

        $my_account_messages = object_to_array($wp_admin_bar->get_node( 'my-account-messages' ));
        $my_account_messages_inbox = object_to_array($wp_admin_bar->get_node( 'my-account-messages-inbox' ));
        $my_account_messages_starred = object_to_array($wp_admin_bar->get_node( 'my-account-messages-starred' ));
        $my_account_messages_sentbox = object_to_array($wp_admin_bar->get_node( 'my-account-messages-sentbox' ));
        $my_account_messages_compose = object_to_array($wp_admin_bar->get_node( 'my-account-messages-compose' ));

        $my_account_groups = object_to_array($wp_admin_bar->get_node( 'my-account-groups' ));
        $my_account_groups_memberships = object_to_array($wp_admin_bar->get_node( 'my-account-groups-memberships' ));
        $my_account_groups_invites = object_to_array($wp_admin_bar->get_node( 'my-account-groups-invites' ));

        $my_account_cursos = object_to_array($wp_admin_bar->get_node( 'my-account-cursos' ));
        $my_account_my_cursos = object_to_array($wp_admin_bar->get_node( 'my-account-my-cursos' ));
        $my_account_crear_cursos = $my_account_my_cursos;

        $recursos = object_to_array($wp_admin_bar->get_node( 'my-account-mediapress' ));
        $recursos_carpetas = object_to_array($wp_admin_bar->get_node( 'my-account-mediapress-my-galleries' ));
        $recursos_create = object_to_array($wp_admin_bar->get_node( 'my-account-mediapress-create' ));
        $recursos_articles = object_to_array($wp_admin_bar->get_node( 'my-account-mediapress-articles' ));

        $articulos = object_to_array($wp_admin_bar->get_node( 'my-account-social-articles' ));
        $articulos_nuevo = object_to_array($wp_admin_bar->get_node( 'nuevo-articulo' ));

        $settings = object_to_array($wp_admin_bar->get_node( 'my-account-settings' ));
        $settings_general = object_to_array($wp_admin_bar->get_node( 'my-account-settings-general' ));
        $settings_notifications = object_to_array($wp_admin_bar->get_node( 'my-account-settings-notifications' ));
        $settings_profile = object_to_array($wp_admin_bar->get_node( 'my-account-settings-profile' ));


        /* Delete all current menu options */
        $all_nodes = $wp_admin_bar->get_nodes();
     
        foreach ($all_nodes as $node) {
            $wp_admin_bar->remove_node( $node->id );
        }

        // Menu derecha
        $wp_admin_bar->add_node( $top_secondary );

        // Hello
        $my_account['parent'] = 'top-secondary';
        $wp_admin_bar->add_node( $my_account );
        $wp_admin_bar->add_node( $user_actions );
        $wp_admin_bar->add_node( $user_info );
        $wp_admin_bar->add_node( $edit_profile );
        $wp_admin_bar->add_node( $logout );

        // Actividad
        $my_account_activity['parent'] = 'my-account';
        $wp_admin_bar->add_node( $my_account_activity );

        // Mensajes
        $my_account_messages['parent'] = 'my-account';
        $wp_admin_bar->add_node( $my_account_messages );
        $wp_admin_bar->add_node( $my_account_messages_inbox );
        $wp_admin_bar->add_node( $my_account_messages_starred );
        $wp_admin_bar->add_node( $my_account_messages_sentbox );
        $wp_admin_bar->add_node( $my_account_messages_compose );

        // Grupos
        $my_account_groups['parent'] = 'my-account';
        $wp_admin_bar->add_node( $my_account_groups );
        $wp_admin_bar->add_node( $my_account_groups_memberships );
        $wp_admin_bar->add_node( $my_account_groups_invites );

        // Cursos
        $my_account_cursos['parent'] = 'my-account';
        $wp_admin_bar->add_node( $my_account_cursos );
        $my_account_my_cursos['title'] = 'Mis Cursos';
        $my_account_my_cursos['href'] = bp_core_get_user_domain( get_current_user_id() ).'listing/';
        $wp_admin_bar->add_node( $my_account_my_cursos );

        $my_account_crear_cursos['id'] = 'my-account-crear-curso';
        $my_account_crear_cursos['title'] = 'Crear Curso';
        $my_account_crear_cursos['href'] = 'http://redcolaborar.org/create-course/';
        $wp_admin_bar->add_node( $my_account_crear_cursos );

        // Recursos
        $recursos['parent'] = 'my-account';
        $recursos['title'] = 'Mis Recursos';
        $wp_admin_bar->add_node( $recursos );
        //$wp_admin_bar->add_node( $recursos_carpetas );
        $recursos_create['title'] = 'Crear carpeta';
        $wp_admin_bar->add_node( $recursos_create );
        //$wp_admin_bar->add_node( $recursos_articles );

        // Articulos
        $articulos['parent'] = 'my-account';
        $articulos['title'] = 'Mis Noticias';
        $wp_admin_bar->add_node( $articulos );
        $articulos_nuevo['title'] = 'Escribir noticia';
        $wp_admin_bar->add_node( $articulos_nuevo );

        // Preguntas y respuestas
        $my_account_preguntas = $my_account_crear_cursos;
        $my_account_preguntas['parent'] = 'my-account';
        $my_account_preguntas['id'] = 'my-account-mis-preguntas';
        $my_account_preguntas['title'] = 'Mis Preguntas';
        $my_account_preguntas['href'] = bp_core_get_user_domain( get_current_user_id() ).'questions/';
        $wp_admin_bar->add_node( $my_account_preguntas );

        $my_account_preguntas_2 = $my_account_crear_cursos;
        $my_account_preguntas_2['parent'] = 'my-account-mis-preguntas';
        $my_account_preguntas_2['id'] = 'my-account-mis-preguntas-2';
        $my_account_preguntas_2['title'] = 'Hacer Pregunta';
        $my_account_preguntas_2['href'] = 'http://redcolaborar.org/preguntas/hacer-pregunta/';
        $wp_admin_bar->add_node( $my_account_preguntas_2 );

        $my_account_preguntas_3 = $my_account_crear_cursos;
        $my_account_preguntas_3['parent'] = 'my-account-mis-preguntas';
        $my_account_preguntas_3['id'] = 'my-account-mis-respuestas';
        $my_account_preguntas_3['title'] = 'Mis Respuestas';
        $my_account_preguntas_3['href'] = bp_core_get_user_domain( get_current_user_id() ).'answers/';
        $wp_admin_bar->add_node( $my_account_preguntas_3 );

        // Configuracion
        $settings['parent'] = 'my-account';
        $wp_admin_bar->add_node( $settings );
        $wp_admin_bar->add_node( $settings_general );
        $wp_admin_bar->add_node( $settings_notifications );
        $wp_admin_bar->add_node( $settings_profile );

        // Reportar problema
        $my_account_rep_prob = $my_account_crear_cursos;
        $my_account_rep_prob['parent'] = 'my-account';
        $my_account_rep_prob['id'] = 'my-account-reportar-problemas';
        $my_account_rep_prob['title'] = 'Reportar Problema';
        $my_account_rep_prob['href'] = 'http://redcolaborar.org/foros/foro/problemas-y-sugerencias/';
        $wp_admin_bar->add_node( $my_account_rep_prob );
    //}
}


/* pulling Parent Theme styles */

function my_theme_enqueue_styles() {
    $parent_style = 'parent-style'; // This is 'divi-style' for the Divi theme.
    wp_enqueue_style ( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style ( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( $parent_style ), wp_get_theme()->get('Version') );

    
    wp_enqueue_style ( 'mentions-style', get_stylesheet_directory_uri() . '/mentions/jquery.mentionsInput.css' );
    wp_enqueue_script( 'mentions-elastic-script', get_stylesheet_directory_uri() . '/mentions/lib/jquery.elastic.js' );
    wp_enqueue_script( 'mentions-events-input-script', get_stylesheet_directory_uri() . '/mentions/lib/jquery.events.input.js' );
    wp_enqueue_script( 'mentions-script', get_stylesheet_directory_uri() . '/mentions/jquery.mentionsInput.js' );

    wp_enqueue_script( 'child-script', get_stylesheet_directory_uri() . '/custom.js' );
}


add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );



// login links in main nav

add_filter( 'wp_nav_menu_items', 'nav_sign_links');
function nav_sign_links($menu) {
    if (is_user_logged_in()){
         $outlink = '<li><a href="http://redcolaborar.org/wp-login.php?action=logout"></a></li>';
         $menu = $menu . $outlink;
         return $menu;
    } else {

         $signlinks = '<li><a href="http://redcolaborar.org/wp-login.php?">Ingresar</a></li><li><a href="http://redcolaborar.org/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=Facebook"><img alt="Facebook" title="Ingresar con Facebook" src="http://redcolaborar.org/wp-content/plugins/wordpress-social-login/assets/img/32x32/wpzoom/facebook.png"></a></li><li><a href="http://redcolaborar.org/wp-login.php?action=wordpress_social_authenticate&mode=login&provider=LinkedIn"><img alt="LinkedIn" title="Ingresar con LinkedIn" src="http://redcolaborar.org/wp-content/plugins/wordpress-social-login/assets/img/32x32/wpzoom/linkedin.png"></a></li>';
         $menu = $menu . $signlinks;
         return $menu;
    }
}


// Hacking meta boxes configuration - set default for all users

/*
source: http://wordpress.stackexchange.com/questions/15376/how-to-set-default-screen-options
*/



add_action('user_register', 'set_user_metaboxes');


//add_action('admin_init', 'set_user_metaboxes');



function set_user_metaboxes($user_id=NULL) {
    
$post_types= array( 'post', 'page', 'link', 'attachment', 'propuesta_proyecto', 'sfwd-courses', 'sfwd-lessons', 'sfwd-topic', 'sfwd-quiz', 'sfwd-certificates', 'sfwd-assignment' );
    
// add any custom post types here:
    
foreach ($post_types as $post_type) {

       
// These are the metakeys we will need to update
       
$meta_key= array(
'order' => "meta-box-order_$post_type",
'hidden' => "metaboxhidden_$post_type",
);

       
// The rest is the same as drebabels's code,
       
// with '*_user_meta()' changed to '*_user_option()'

       
// So this can be used without hooking into user_register
       
if ( ! $user_id)
           
$user_id = get_current_user_id();

       
// Set the default order if it has not been set yet
       
if ( ! get_user_meta( $user_id, $meta_key['order'], true) ) {
           
$meta_value = array(
'side' => 'submitdiv,categorydiv,postimagediv,tagsdiv-post_tag',
 'normal' => '',
 'advanced' => '',
);
           
update_user_meta( $user_id, $meta_key['order'], $meta_value );
       
}

       
// Set the default hiddens if it has not been set yet
       
if ( ! get_user_meta( $user_id, $meta_key['hidden'], true) ) {
           
$meta_value = array('postcustom','trackbacksdiv','commentstatusdiv','commentsdiv','slugdiv','authordiv','revisionsdiv','formatdiv','layout_sectionid','postexcerpt','slider_sectionid');
           update_user_meta( $user_id, $meta_key['hidden'], $meta_value );
       
}
   
}

}



//Manage Your Media Only - users see only their uploads


add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );


function show_current_user_attachments( $query = array() ) {
    
$user_id = get_current_user_id();
    
if( $user_id ) {
        
$query['author'] = $user_id;
    
}
    
return $query;

}



//Allow HTML in sitewide notices


function bp_disable_kses_notices() {
	
if( current_user_can('manage_options') ) {
		
remove_filter( 'bp_get_message_notice_text', 'wp_filter_kses', 1 );
		
remove_filter( 'messages_notice_message_before_save', 'wp_filter_kses', 1 );
	
}

}




//Enable visual editor in bbpress


add_action('init','bp_disable_kses_notices');


function bbp_enable_visual_editor( $args = array() ) {
    
$args['tinymce'] = true;
    $args['quicktags'] = false;
    
return $args;

}

add_filter( 'bbp_after_get_the_content_parse_args', 'bbp_enable_visual_editor' );



//Disable html tags when pasting on bbpress


function bbp_tinymce_paste_plain_text( $plugins = array() ) {
    
$plugins[] = 'paste';
    return $plugins;
}
add_filter( 'bbp_get_tiny_mce_plugins', 'bbp_tinymce_paste_plain_text' );



//Changing default bbpress topic title from 80 to 160 characters


add_filter ('bbp_get_title_max_length','change_title');


function change_title ($default) {

$default=160 ;

return $default ;

}





?>

