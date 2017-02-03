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

