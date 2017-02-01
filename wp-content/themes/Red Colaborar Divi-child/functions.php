
<?php

/* pulling Divi styles */

function my_theme_enqueue_styles() {

    $parent_style = 'parent-style'; // This is 'divi-style' for the Divi theme.

    wp_enqueue_style( $parent_style, get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}
add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );

/* footer */

if ( ! function_exists( 'et_get_original_footer_credits' ) ) :
function et_get_original_footer_credits() {
	return sprintf( __( 'Designed by %1$s | Powered by %2$s', 'Divi' ), '<a href="http://www.lingos.org" title="LINGOs">LINGOs</a>', '<a href="http://www.iaf.gov" title="Inter American Foundation">IAF</a>' );
}
endif;

/*Date: 26/08/2016
Desc: Updating the user roles based on buddypress user role selection.*/


/*
add_action( 'bp_core_activated_user', 'user_bp_core_activated_user', 10, 3 );
function user_bp_core_activated_user(  $user_id, $key, $user ) {
	$user_role = $user['meta']['field_156'];
     switch($user_role) {
        case "Perfil Organizacional - Donatario FIA":
            $new_role = 'wdm_course_author';
            break;
        case "Perfil Organizacional":
            $new_role = 'wdm_course_author';
            break;
		case "Perfil Individual - Donatario FIA":
            $new_role = 'subscriber';
            break;
		case "Perfil Individual":
            $new_role = 'subscriber';
            break;
    }
    wp_update_user(array(
        'ID' => $user_id,
        'role' => $new_role
    ));

}
*/

/* End updating user role */

/**
*
* Here below customization and hooks for Red Colaborar by M. Bricola
*
*/



add_filter( 'wp_nav_menu_items', 'nav_sign_links');
function nav_sign_links($menu) {
    if (is_user_logged_in()){
         $outlink = '<li><a href="http://redcolaborar.org/wp-login.php?action=logout"></a></li>';
         $menu = $menu . $outlink;
         return $menu;
    } else {

         $signlinks = '<li><a href="http://redcolaborar.org/wp-login.php?">Ingresar</a></li><li><a href="http://redcolaborar.org/register">Registrarse</a></li>';
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

/*
Hack for showing featured post image when missing. Source: http://justintadlock.com/archives/2012/07/05/how-to-define-a-default-post-thumbnail
*/


/* 
add_filter( 'post_thumbnail_html', 'my_post_thumbnail_html' );

function my_post_thumbnail_html( $html ) {

	if ( empty( $html ) )
		$html = '<img src="http://redcolaborar.org/wp-content/uploads/animated_slide2_hands-pc2.png" alt="" />';

	return $html;
}
*/

//Manage Your Media Only - users see only their uploads

add_filter( 'ajax_query_attachments_args', 'show_current_user_attachments', 10, 1 );

function show_current_user_attachments( $query = array() ) {
    $user_id = get_current_user_id();
    if( $user_id ) {
        $query['author'] = $user_id;
    }
    return $query;
}


//Hide Divi builder and pagination options, BadgeOS button and Grassblade options in add new post form

add_action('admin_head', 'my_admin_css');

function my_admin_css(){
if (get_post_type() == 'post') {
echo '<style>
.et_pb_toggle_builder_wrapper,#et_settings_meta_box,#insert_badgeos_shortcodes,#grassblade_add_to_content_box {
display: none !important;
}
</style>';
}
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

add_filter ('bbp_get_title_max_length','change_title') ;

Function change_title ($default) {
$default=160 ;
Return $default ;
}

?>
