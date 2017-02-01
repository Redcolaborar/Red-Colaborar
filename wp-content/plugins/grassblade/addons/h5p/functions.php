<?php
add_action( 'admin_init', 'grassblade_h5p_check_plugin_availability' );
function grassblade_h5p_check_plugin_availability() {
  if (is_plugin_active('h5p/h5p.php') && class_exists('H5PContentQuery')){
    define("GB_H5P_SUPPORT_ENABLED", true);
  }
}
/**
 * Check for the valid h5p content and embed the xapi js libraries.
 */
function grassblade_h5pmods_alter_scripts(&$scripts, $libraries, $embed_type) {
    global $wpdb;
    if (empty($_GET['id'])) return;
    $post_id = $_GET['id'];
    $h5p_content = $wpdb->get_row($wpdb->prepare("SELECT id
          FROM {$wpdb->prefix}h5p_contents
          WHERE id = '%d'", $post_id));
     // check for the valid h5p content    
    if (empty($h5p_content)) return;
     // check auth and endpoint values attached with the h5p content launch url
    if (empty($_GET['auth']) || empty($_GET['endpoint'])) return;    
        $scripts[] = (object)array('path' => plugins_url('StatementViewer/scripts/xapiwrapper.js', dirname(__FILE__)), 'version' => '?ver=1.0');
        $scripts[] = (object)array('path' => plugins_url('h5p/js/script.js', dirname(__FILE__)), 'version' => '?ver=1.0');
}
add_action('h5p_alter_library_scripts', 'grassblade_h5pmods_alter_scripts', 10, 3);

/**  h5p content filter **/
function grassblade_h5pmods_embed_access($access, $h5p_content_id, $post_id) {
  if (!empty($h5p_content_id)) {
    if(empty($post_id)) {
	global $wpdb;
	$post_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'h5p_content_id' AND meta_value = '%d'", $h5p_content_id));
	if(empty($post_ids))
		return false;

	foreach($post_ids as $post_id) {
		if(grassblade_h5pmods_embed_access($access, $h5p_content_id, $post_id))
			return true;
	}
	return false;
    }

    if(current_user_can('manage_options'))
      return true;

    $xapi_content = get_post_meta( $post_id, 'xapi_content', true); 

    if($xapi_content['guest'])
      return true; //if guest access is enabled, allow access. 
    
    $user_id = get_current_user_id();
    $posts_with_content = grassblade_get_post_with_content($post_id);
    if(empty($posts_with_content)) 
      return false;
    elseif (function_exists('sfwd_lms_has_access')) 
    {
      foreach ($posts_with_content as $p) {
        $course_id = learndash_get_course_id($p->ID);

        if($course_id) {
          $has_access = sfwd_lms_has_access($course_id, $user_id);
          if($has_access)
            return true; //allow access if has access to any LearnDash course with the content
        }
        else
          $is_outside_learndash = true;
      }
      if(empty($is_outside_learndash))
        return false; //if content is only on learndash pages, and user doesn't have access to the courses. He doesn't have access to content. 
    }
    
    if($user_id)
      return true; //if nothing, allow access if logged in.
    else
      return false;
  }
  return $access;
}
add_filter('h5p_embed_access', 'grassblade_h5pmods_embed_access', 10, 3);

function grassblade_h5pmods_grassblade_shortcode_return($return, $params, $shortcode_atts, $attr) {
  $id = @$shortcode_atts["id"];

  if(!empty($id))
  $h5p_content_id = get_post_meta( $id, 'h5p_content_id', true);

  if(empty($h5p_content_id))
	return $return;

  $access = apply_filters('h5p_embed_access', true, $h5p_content_id, $id);
  if(!$access) 
    return;
  
  return $return;
}

add_filter("grassblade_shortcode_return", "grassblade_h5pmods_grassblade_shortcode_return", 10, 4);
