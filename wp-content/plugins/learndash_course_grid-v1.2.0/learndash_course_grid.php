<?php
/**
 * @package LearnDash Course Grid
 * @version 1.2.0
 */
/*
Plugin Name: LearnDash Course Grid
Plugin URI: http://www.learndash.com
Description: LearnDash Course Grid
Version: 1.2.0
Author: LearnDash
Author URI: http://www.learndash.com
*/

add_action("plugins_loaded", "learndash_course_grid_localize");
function learndash_course_grid_localize() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'learndash_course_grid' );
	load_textdomain( 'learndash_course_grid', WP_LANG_DIR . '/learndash_course_grid/learndash_course_grid-' . $locale . '.mo' );
	load_plugin_textdomain( 'learndash_course_grid', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );                
}

//add_action( 'wp_enqueue_scripts', 'learndash_course_grid_css_head', 0);
//function learndash_course_grid_css_head() {
//	wp_enqueue_style( 'ld-cga-bootstrap', plugins_url( 'bootstrap.min.css', __FILE__ ) );
//}
add_action( 'admin_enqueue_scripts', 'learndash_course_grid_admin', 0);
function learndash_course_grid_admin() {
	global $pagenow, $post;

	if($pagenow == "post.php" && $post->post_type == "sfwd-courses" || $pagenow == "post-new.php" && @$_GET["post_type"] == "sfwd-courses")
	wp_enqueue_script( 'learndash_course_grid_js', plugins_url( 'script.js', __FILE__ ), array('jquery') );
}
add_filter("the_content", "learndash_course_grid_css");
function learndash_course_grid_css($content) {
	if(strpos($content, "[ld_course_list") === false)
		return $content;

	wp_enqueue_style( 'learndash_course_grid_css', plugins_url( 'style.css', __FILE__ ) );
	wp_enqueue_script( 'learndash_course_grid_js', plugins_url( 'script.js', __FILE__ ), array('jquery') );
	wp_enqueue_style( 'ld-cga-bootstrap', plugins_url( 'bootstrap.min.css', __FILE__ ) );

	return str_replace("[ld_course_list", "<div id='ld_course_list'>[ld_course_list", $content);
}

add_filter( 'learndash_template', 'learndash_course_grid_course_list', 99, 5);
function learndash_course_grid_course_list($filepath, $name, $args, $echo, $return_file_path) {
	if($name == "course_list_template") {
		return dirname(__FILE__)."/course_list_template.php";
	}
	return $filepath;
}

function learndash_course_grid_course_list_ending($output) {
	return $output."</div><br style='clear:both'>";
}
add_filter("ld_course_list", "learndash_course_grid_course_list_ending",1, 1);


add_filter("learndash_post_args", "learndash_course_grid_post_args", 10, 1);

function learndash_course_grid_post_args($post_args) {
	foreach($post_args as $key => $post_arg) {
		if($post_arg["post_type"] == "sfwd-courses") {
			$course_short_description = array(
                                              'name' => __('Course Short Description', 'learndash_course_grid'),
                                              'type' => 'textarea',
                                              'help_text' => __('A short description of the course to show on course list generated by course list shortcode.', 'learndash_course_grid'),
                                            );
			$post_args[$key]["fields"] = array("course_short_description" => $course_short_description) + $post_args[$key]["fields"];
		}
	}
	return $post_args;
}

