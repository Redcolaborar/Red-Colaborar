<?php
define('GBL_LEARNDASH_PLUGIN_FILE', 'sfwd-lms/sfwd_lms.php');
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if(is_plugin_active(GBL_LEARNDASH_PLUGIN_FILE))
{
	add_action('admin_menu', 'grassblade_learndash_menu', 1100);


	if ( is_admin() && defined( 'DOING_AJAX' ) && DOING_AJAX )
	{
		// Load Quiz Tracking module only when Ajax is Running
		include_once( dirname(__FILE__).'/quiz/index.php' );
		new grassblade_learndash_quiz();
	}

}

function grassblade_learndash_menu() {
	add_submenu_page("edit.php?post_type=sfwd-courses", __("TinCan Settings", "grassblade"), __("TinCan Settings", "grassblade"),'manage_options','admin.php?page=grassblade-lrs-settings', 'grassblade_menu_page');
	add_submenu_page("edit.php?post_type=sfwd-courses", __("PageViews Settings", "grassblade"),  __("PageViews Settings", "grassblade"),'manage_options','admin.php?page=pageviews-settings', 'grassblade_pageviews_menupage');
}

function grassblade_learndash_admin_tabs_on_page($admin_tabs_on_page, $admin_tabs, $current_page_id) {
	if(empty($admin_tabs_on_page["toplevel_page_grassblade-lrs-settings"]) || !count($admin_tabs_on_page["toplevel_page_grassblade-lrs-settings"]))
		$admin_tabs_on_page["toplevel_page_grassblade-lrs-settings"] = array();
	
	$admin_tabs_on_page["toplevel_page_grassblade-lrs-settings"] = array_merge($admin_tabs_on_page["sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses"], (array) $admin_tabs_on_page["toplevel_page_grassblade-lrs-settings"]);

	foreach ($admin_tabs as $key => $value) {
		if($value["id"] == $current_page_id && $value["menu_link"] == "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses")
		{
			$admin_tabs_on_page[$current_page_id][] = "grassblade-lrs-settings";
			return $admin_tabs_on_page;
		}
	}

	return $admin_tabs_on_page;
}
add_filter("learndash_admin_tabs_on_page", "grassblade_learndash_admin_tabs_on_page", 3, 3);

function grassblade_learndash_submenu($add_submenu) {
	$add_submenu["grassblade"] = array(
									"name" 	=>	__('One Click Upload', "grassblade"),
									"cap"	=>	"manage_options",
									"link"	=> 'edit.php?post_type=gb_xapi_content'
									);
	return $add_submenu;
}
add_filter("learndash_submenu", "grassblade_learndash_submenu", 1, 1 );
function grassblade_learndash_admin_tabs($admin_tabs) {
	$admin_tabs["grassblade"] = array(
									"link"	=>	'edit.php?post_type=gb_xapi_content',
									"name"	=>	__('One Click Upload', "grassblade"),
									"id"	=>	"edit-gb_xapi_content",
									"menu_link"	=> 	"edit.php?post_type=gb_xapi_content",
								);
	$admin_tabs["grassblade-lrs-settings"] = array(
									"link"	=>	'admin.php?page=grassblade-lrs-settings',
									"name"	=>	__("TinCan Settings","grassblade"),
									"id"	=>	"toplevel_page_grassblade-lrs-settings",
									"menu_link"	=> 	"edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
								);
	return $admin_tabs;
}
add_filter("learndash_admin_tabs", "grassblade_learndash_admin_tabs", 1, 1);
function grassblade_learndash_mark_lesson_complete_if_children_complete($lesson_id, $user_id) {
	$lesson = get_post($lesson_id);
	//$user_course_progress = get_user_meta($user_id, '_sfwd-course_progress', true);
	$quizzes = learndash_get_lesson_quiz_list($lesson, $user_id);
	if(!empty($quizzes) && count($quizzes))
	{
		foreach ($quizzes as $quiz) {
			if($quiz["status"] != "completed")
				return false;
		}

	}
	else
	{
		$topics = learndash_topic_dots($lesson_id,false,"array",$user_id);
		if(!empty($topics) && count($topics))
		{
			foreach ($topics as $topic) {
				if(empty($topic->completed))
					return false;
			}
		}
	}
	$ret = learndash_process_mark_complete($user_id, $lesson_id);
	$course_id = learndash_get_course_id($lesson_id);
	learndash_course_status($course_id, $user_id);				
	return $ret;
}
function grassblade_learndash_lesson_completed($data) {

	grassblade_debug('grassblade_learndash_lesson_completed');
	//grassblade_debug($data);
	$grassblade_settings = grassblade_settings();

    $grassblade_tincan_endpoint = $grassblade_settings["endpoint"];
    $grassblade_tincan_user = $grassblade_settings["user"];
    $grassblade_tincan_password = $grassblade_settings["password"];
	$grassblade_tincan_track_guest = $grassblade_settings["track_guest"];

	$xapi = new NSS_XAPI($grassblade_tincan_endpoint, $grassblade_tincan_user, $grassblade_tincan_password);
	$actor = grassblade_getactor($grassblade_tincan_track_guest, "1.0", $data["user"]);

	if(empty($actor))
	{
		grassblade_debug("No Actor. Shutting Down.");
		return;
	}
	$course = $data['course'];
	$lesson = $data['lesson'];
	$progress = $data['progress'];
	
	$course_title = $course->post_title;
	$course_url = grassblade_post_activityid($course->ID);
	$lesson_title = $lesson->post_title;
	$lesson_url = grassblade_post_activityid($lesson->ID);
	
	if(!empty($course->ID) &&!empty($data['progress'][$course->ID]['completed']) && $data['progress'][$course->ID]['completed'] == 1) {
		//Course Attempted
		$xapi->set_verb('attempted');
		$xapi->set_actor_by_object($actor);	
		$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_object($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$statement = $xapi->build_statement();
		//grassblade_debug($statement);
		$xapi->new_statement();
			
	}
	
	//Lesson Attempted
	$xapi->set_verb('attempted');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($lesson_url, $lesson_title, $lesson_title, 'http://adlnet.gov/expapi/activities/lesson','Activity');
	$statement = $xapi->build_statement();
	//grassblade_debug($statement);
	$xapi->new_statement();
	
	//Lesson Completed
	$xapi->set_verb('completed');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($lesson_url, $lesson_title, $lesson_title, 'http://adlnet.gov/expapi/activities/lesson','Activity');
	$result = array(
				'completion' => true
				);	
	$xapi->set_result_by_object($result);

	$statement = $xapi->build_statement();
	//grassblade_debug($statement);
	$xapi->new_statement();
	
	foreach($xapi->statements as $statement)
	{
		$ret = $xapi->SendStatements(array($statement));
	}	
}
function grassblade_learndash_topic_completed($data) {

	grassblade_debug('grassblade_learndash_topic_completed');
	//grassblade_debug($data);
	$grassblade_settings = grassblade_settings();

    $grassblade_tincan_endpoint = $grassblade_settings["endpoint"];
    $grassblade_tincan_user = $grassblade_settings["user"];
    $grassblade_tincan_password = $grassblade_settings["password"];
	$grassblade_tincan_track_guest = $grassblade_settings["track_guest"];

	$xapi = new NSS_XAPI($grassblade_tincan_endpoint, $grassblade_tincan_user, $grassblade_tincan_password);
	$actor = grassblade_getactor($grassblade_tincan_track_guest, "1.0", $data["user"]);

	if(empty($actor))
	{
		grassblade_debug("No Actor. Shutting Down.");
		return;
	}
	$course = $data['course'];
	$topic = $data['topic'];
	$progress = $data['progress'];
	
	$lesson_id = learndash_get_setting($topic, "lesson");
	if(!empty($lesson_id))
	{
		$lesson = get_post($lesson_id);
	}

	$course_title = $course->post_title;
	$course_url = grassblade_post_activityid($course->ID);
	$topic_title = $topic->post_title;
	$topic_url = grassblade_post_activityid($topic->ID);
	
	if(!empty($lesson->ID))
	{
		$parent_title 	= $lesson->post_title;
		$parent_url 	= grassblade_post_activityid($lesson->ID);
		$parent_type	= 'lesson';
	}
	else
	{
		$parent_title	= $course_title;
		$parent_url		= $course_url;
		$parent_type	= 'course';
	}

	if(!empty($course->ID) &&!empty($data['progress'][$course->ID]['completed']) && $data['progress'][$course->ID]['completed'] == 1) {
		//Course Attempted
		$xapi->set_verb('attempted');
		$xapi->set_actor_by_object($actor);	
		$xapi->set_parent($parent_url, $parent_title, $parent_title, 'http://adlnet.gov/expapi/activities/'.$parent_type,'Activity');
		$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_object($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$statement = $xapi->build_statement();
		//grassblade_debug($statement);
		$xapi->new_statement();
			
	}
	
	//topic Attempted
	$xapi->set_verb('attempted');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($parent_url, $parent_title, $parent_title, 'http://adlnet.gov/expapi/activities/'.$parent_type,'Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($topic_url, $topic_title, $topic_title, 'http://adlnet.gov/expapi/activities/topic','Activity');
	$statement = $xapi->build_statement();
	//grassblade_debug($statement);
	$xapi->new_statement();
	
	//topic Completed
	$xapi->set_verb('completed');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($parent_url, $parent_title, $parent_title, 'http://adlnet.gov/expapi/activities/'.$parent_type,'Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($topic_url, $topic_title, $topic_title, 'http://adlnet.gov/expapi/activities/topic','Activity');
	$result = array(
				'completion' => true
				);	
	$xapi->set_result_by_object($result);

	$statement = $xapi->build_statement();
	//grassblade_debug($statement);
	$xapi->new_statement();
	
	foreach($xapi->statements as $statement)
	{
		$ret = $xapi->SendStatements(array($statement));
	}	
}
function grassblade_learndash_course_completed($data) {
	grassblade_debug('grassblade_learndash_course_completed');
	//grassblade_debug($data);
	
	$grassblade_settings = grassblade_settings();

    $grassblade_tincan_endpoint = $grassblade_settings["endpoint"];
    $grassblade_tincan_user = $grassblade_settings["user"];
    $grassblade_tincan_password = $grassblade_settings["password"];
	$grassblade_tincan_track_guest = $grassblade_settings["track_guest"];

	$xapi = new NSS_XAPI($grassblade_tincan_endpoint, $grassblade_tincan_user, $grassblade_tincan_password);
	$actor = grassblade_getactor($grassblade_tincan_track_guest, "1.0", $data["user"]);

	if(empty($actor))
	{
		grassblade_debug("No Actor. Shutting Down.");
		return;
	}
	$course = $data['course'];
	$progress = $data['progress'];
	$course_title = $course->post_title;
	$course_url = grassblade_post_activityid($course->ID);	
	//Lesson Completed
	$xapi->set_verb('completed');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$result = array(
				'completion' => true
				);	
	$xapi->set_result_by_object($result);	
	$statement = $xapi->build_statement();
	grassblade_debug($statement);
	$xapi->new_statement();	
	foreach($xapi->statements as $statement)
	{
		$ret = $xapi->SendStatements(array($statement));
	}		
}
function grassblade_learndash_quiz_completed($data, $user = null) {
	//define('GB_DEBUG', true);
	grassblade_debug('grassblade_learndash_quiz_completed');
	grassblade_debug($data);

	if(!empty($data["statement_id"]))
		return;

	$grassblade_settings = grassblade_settings();

    $grassblade_tincan_endpoint = $grassblade_settings["endpoint"];
    $grassblade_tincan_user = $grassblade_settings["user"];
    $grassblade_tincan_password = $grassblade_settings["password"];
	$grassblade_tincan_track_guest = $grassblade_settings["track_guest"];
	
	$xapi = new NSS_XAPI($grassblade_tincan_endpoint, $grassblade_tincan_user, $grassblade_tincan_password);
	$actor = grassblade_getactor($grassblade_tincan_track_guest, "1.0", $user);

	if(empty($actor))
	{
		grassblade_debug("No Actor. Shutting Down.");
		return;
	}
	$course = $data['course'];
	$quiz = $data['quiz'];
	$pass = !empty($data['pass'])? true:false;
	$score = $data['score']*1;
	if(isset($data["percentage"]))
	$score_scaled = number_format( $data["percentage"]/100, 2);
	if(isset($data["timespent"]))
	$duration = "PT".$data["timespent"]."S";

	$course_title = $course->post_title;
	$course_url = grassblade_post_activityid($course->ID);
	$quiz_title = $quiz->post_title;
	$quiz_url = grassblade_post_activityid($quiz->ID);
	

	$has_xapi_content = get_post_meta($quiz->ID, "show_xapi_content", true);
	if(!empty($has_xapi_content)) {
		//Quiz Attempted
		$xapi->set_verb('attempted');
		$xapi->set_actor_by_object($actor);	
		$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_object($quiz_url, $quiz_title, $quiz_title, 'http://adlnet.gov/expapi/activities/assessment','Activity');
		$statement = $xapi->build_statement();
		grassblade_debug($statement);
		$xapi->new_statement();
	}
	else
	{
		//LearnDash Quiz Passed/Failed
		if($pass)
		$xapi->set_verb('passed');
		else
		$xapi->set_verb('failed');
		$xapi->set_actor_by_object($actor);	
		$xapi->set_parent($quiz_url, $quiz_title, $quiz_title, 'http://adlnet.gov/expapi/activities/assessment','Activity');
		$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
		$xapi->set_object($quiz_url, $quiz_title, $quiz_title, 'http://adlnet.gov/expapi/activities/assessment','Activity');
		$result = array(
					'completion' => true,
					'success' => $pass,
					'score' => array('min' => 0, 'raw' => $score)
					);
		if(isset($data["points"]))
			$result["score"]["raw"] = $data["points"];
		if(isset($data["total_points"]))
			$result["score"]["max"] = $data["total_points"];

		if(isset($score_scaled))
			$result["score"]["scaled"] = $score_scaled;
		
		if(isset($duration))
			$result["duration"] = $duration;

		$xapi->set_result_by_object($result);

		$statement = $xapi->build_statement();
		grassblade_debug($statement);
		$xapi->new_statement();
	}
	//Quiz Completed
	$xapi->set_verb('completed');
	$xapi->set_actor_by_object($actor);	
	$xapi->set_parent($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_grouping($course_url, $course_title, $course_title, 'http://adlnet.gov/expapi/activities/course','Activity');
	$xapi->set_object($quiz_url, $quiz_title, $quiz_title, 'http://adlnet.gov/expapi/activities/assessment','Activity');
	$result = array(
				'completion' => true,
				'success' => $pass,
				'score' => array('raw' => $score)
				);	
	if(isset($data["points"]))
		$result["score"]["raw"] = $data["points"];
	
	if(isset($data["total_points"]))
		$result["score"]["max"] = $data["total_points"];
		
	if(isset($score_scaled))
		$result["score"]["scaled"] = $score_scaled;
	
	if(isset($duration))
		$result["duration"] = $duration;

	$xapi->set_result_by_object($result);

	$statement = $xapi->build_statement();
	grassblade_debug($statement);
	$xapi->new_statement();
	
	foreach($xapi->statements as $statement)
	{
		$ret = $xapi->SendStatements(array($statement));
		grassblade_debug($ret);
	}	
}
add_action('learndash_lesson_completed', 'grassblade_learndash_lesson_completed', 1, 1);
add_action('learndash_topic_completed', 'grassblade_learndash_topic_completed', 1, 1);
add_action('learndash_course_completed', 'grassblade_learndash_course_completed', 1, 1);
add_action('learndash_quiz_completed', 'grassblade_learndash_quiz_completed', 1, 2);

function grassblade_learndash_process_mark_complete($return, $post, $user) {
	if(empty($post->ID) || empty($user->ID))
		return false;
	$user_id = $user->ID;
	$content_id = get_post_meta($post->ID, "show_xapi_content", true);

	if(empty($content_id))
		return true;

	$xapi_content = get_post_meta($content_id, "xapi_content", true);

	if(empty($xapi_content["completion_tracking"]))
		return true;

	$completed = get_user_meta($user->ID, "completed_".$content_id);

	if(empty($completed))
		return false;
	
	return true;
}
add_filter("learndash_process_mark_complete", "grassblade_learndash_process_mark_complete", 1, 3);

function grassblade_learndash_content_completed($statement, $content_id, $user) {
	$user_id = $user->ID;
	$xapi_content = get_post_meta($content_id, "xapi_content", true);

	if(empty($xapi_content["completion_tracking"])) {
		echo "\nCompletion tracking not enabled. ";
		return true;
	}
	
	//$content_id = 189;
	global $wpdb;
	$post_ids = $wpdb->get_col( $wpdb->prepare("select post_id from $wpdb->postmeta where meta_key = 'show_xapi_content' AND meta_value = '%d'", $content_id) );
	echo "<pre>";
	print_r($post_ids);

	foreach ($post_ids as $post_id) {
		$post = get_post($post_id);
		if($post->post_type == "sfwd-quiz") 
			grassblade_learndash_quiz_completion($post, $user, $statement);

		if($post->post_type == "sfwd-lessons" || $post->post_type == "sfwd-topic") {
			$has_topic_or_quiz = $wpdb->get_var( $wpdb->prepare("select post_id from $wpdb->postmeta where meta_key = 'lesson_id' AND meta_value = '%d' LIMIT 1", $post_id) );

			if(!empty($has_topic_or_quiz)) {
				$msg = "\nNot marking lesson/topic [".$post_id."] complete for user [".$user_id."/".$user->user_login."] because lesson/topic [".$post_id."] has quiz: ".$lesson_has_quiz;
				grassblade_admin_notice($msg);
				echo $msg;
				continue;
			}
		
		}
		echo "\nlearndash_process_mark_complete: user_id = ".$user->ID. " post_id = ".$post->ID;
		echo " : ".learndash_process_mark_complete($user->ID, $post->ID);
		
		$lesson_id = learndash_get_setting($post, "lesson");
		if(!empty($lesson_id) && defined("LEARNDASH_VERSION") && version_compare(LEARNDASH_VERSION, "2.0.5.3.", ">="))
		echo " Mark Lesson ".$lesson_id." Complete: ".grassblade_learndash_mark_lesson_complete_if_children_complete($lesson_id, $user->ID);
	}
}
//add_action("init", "grassblade_learndash_content_completed");
add_action("grassblade_completed", "grassblade_learndash_content_completed", 10, 3);

function grassblade_learndash_quiz_completion($post, $user, $statement) {
		$user_id = $user->ID;
		$post_id = $post->ID;
		if($post->post_type == "sfwd-quiz") {
		//if(!learndash_is_quiz_notcomplete(null, array($post->ID => 1 )))
		//	return false;
		echo "\ngrassblade_learndash_quiz_completion: user_id = ".$user->ID. " post_id = ".$post->ID;
		$statement = json_decode($statement);
		$result = @$statement->result;
		
		$usermeta = get_user_meta( $user->ID, '_sfwd-quizzes', true );
		$usermeta = maybe_unserialize( $usermeta );
		if ( !is_array( $usermeta ) ) $usermeta = Array();
		
		foreach($usermeta as $quiz_data) {
			if(!empty($quiz_data["statement_id"]) && $quiz_data["statement_id"] == @$statement->id)
				continue;
		}

		$score = !empty($statement->result->score->raw)? $statement->result->score->raw:(!empty($statement->result->score->scaled)? $statement->result->score->scaled*100:0);
		$percentage = !empty($statement->result->score->scaled)? $statement->result->score->scaled*100:((!empty($statement->result->score->max) && isset($statement->result->score->raw))? $statement->result->score->raw*100/($statement->result->score->max - @$statement->result->score->min):100);
		$percentage = round($percentage, 2);

		$quiz_id = $post->ID;
		$timespent = isset($statement->result->duration)? grassblade_duration_to_seconds($statement->result->duration):null;
		$count = 1;
		
		$quiz = get_post_meta($quiz_id, '_sfwd-quiz', true);
		$passingpercentage = intVal($quiz['sfwd-quiz_passingpercentage']);
		$pass = ($percentage >= $passingpercentage)? 1:0;
		$quiz = get_post($quiz_id);
		$quizdata = array( "statement_id" => @$statement->id, "quiz" => $quiz_id, "quiz_title" => $quiz->post_title, "score" => $score, "total" => $score, "count" => $count, "pass" => $pass, "rank" => '-', "time" => strtotime(@$statement->stored), 'percentage' => $percentage, 'timespent' => $timespent);
		$usermeta[] = $quizdata;

		$quizdata['quiz'] = $quiz;
		$courseid = learndash_get_course_id($quiz_id);
		$quizdata['course'] = get_post($courseid);		

		update_user_meta( $user_id, '_sfwd-quizzes', $usermeta );
		
		do_action("learndash_quiz_completed", $quizdata, $user); //Hook for completed quiz

		return true;
	}
}
function grassblade_learndash_slickquiz_loadresources($return, $post) {
	$content_id = get_post_meta($post->ID, "show_xapi_content", true);

	if(empty($content_id))
		return $return;
	else
		return false;
}

add_filter("leandash_slickquiz_loadresources", "grassblade_learndash_slickquiz_loadresources", 1, 2);
function grassblade_learndash_disable_advance_quiz($return, $post) {
	$content_id = get_post_meta($post->ID, "show_xapi_content", true);

	if(empty($content_id))
		return $return;
	else
		return true;
}

add_filter("learndash_disable_advance_quiz", "grassblade_learndash_disable_advance_quiz", 1, 2);

function grassblade_learndash_quiz_content_access($return, $post) {
	if(!is_null($return) || $post->post_type != "sfwd-quiz")
		return $return;
	$lesson_id = learndash_get_setting($post, "lesson");
	if(!empty($lesson_id)) {
		$lesson_content_id = get_post_meta($lesson_id, "show_xapi_content", true);
		if(!empty($lesson_content_id)) {
			$user_id = get_current_user_id();
			$lesson_completed = get_user_meta($user_id, "completed_".$lesson_content_id);
			if(empty($lesson_completed))
				return __("You do not have access to this quiz. Please go back and complete the content on the previous page.", "grassblade");
		}
	}


	return $return;
}
add_filter("learndash_content_access", "grassblade_learndash_quiz_content_access", 2, 9);

function grassblade_learndash_add_certificate_link($content) {
	global $post;
	$content_id = get_post_meta($post->ID, "show_xapi_content", true);
	if(!empty($content_id))
	{
		if(!learndash_is_quiz_notcomplete(null, array($post->ID => 1 ))) 
		$content .= "<br>" . apply_filters("grassblade_learndash_quiz_certificate_link", learndash_get_certificate_link($post->ID), $post);
	}
	return $content;
}
if(is_plugin_active(GBL_LEARNDASH_PLUGIN_FILE))
add_filter("the_content", "grassblade_learndash_add_certificate_link", 1, 10);

	function grassblade_duration_to_seconds($timeval) {
		if(empty($timeval)) return 0;
		
		$timeval = str_replace("PT", "", $timeval);
		$timeval = str_replace("H", "h ", $timeval);
		$timeval = str_replace("M", "m ", $timeval);
		$timeval = str_replace("S", "s ", $timeval);

		$time_sections = explode(" ", $timeval);
		$h = $m = $s = 0;
		foreach($time_sections as $k => $v) {
			$value = trim($v);
			
			if(strpos($value, "h"))
			$h = intVal($value);
			else if(strpos($value, "m"))
			$m = intVal($value);
			else if(strpos($value, "s"))
			$s = intVal($value);
		}
		$time = $h * 60 * 60 + $m * 60 + $s;
		
		if($time == 0)
		$time = (int) $timeval;
		
		return $time;
	}


add_filter("grassblade_group_leaders", "grassblade_get_learndash_group_leaders", 10, 2);
function grassblade_get_learndash_group_leaders($leaders, $group) {
	if(@$group->post_type != "groups")
		return $leaders;

	return $leaders +  grassblade_learndash_group_leaders($group);
}


add_filter("grassblade_group_users", "grassblade_get_learndash_group_users" , 10, 2);
function grassblade_get_learndash_group_users($users, $group) {
	if(@$group->post_type != "groups")
		return $users;

	return $users +  grassblade_learndash_group_users($group);
}

function grassblade_learndash_group_leaders($group) {
	if(!function_exists("learndash_get_groups_administrator_ids"))
		return array();

	$leader_ids = learndash_get_groups_administrator_ids($group->ID);
	$leaders = array();
	if(!empty($leader_ids))
	foreach ($leader_ids as $leader_id) {
		$leader = get_user_by("id", $leader_id);
		$leaders[$leader_id] = $leader->user_email;
	}

	return $leaders;
}
function grassblade_learndash_group_users($group) {
	if(!function_exists("learndash_get_groups_user_ids"))
		return array();

	$user_ids = learndash_get_groups_user_ids($group->ID);
	if(!empty($user_ids))
	foreach ($user_ids as $user_id) {
		$users[$user_id] = grassblade_user_email($user_id);
	}

	return $users;
}

add_filter("grassblade_groups", "grassblade_get_learndash_groups", 10, 2);
function grassblade_get_learndash_groups($return, $params) {

    $params["post_type"] = "groups";
    if(empty($params["posts_per_page"]))
    	$params["posts_per_page"] = -1;

    $leaders_list = @$params["leaders_list"];
    $users_list = @$params["users_list"];
    unset($params["leaders_list"]);
    unset($params["users_list"]);

    if(!empty($params["id"]))
    	$groups = array(get_post($params["id"]));
	else
    	$groups = get_posts($params);    

    foreach ($groups as $k => $group) {
		$g = array();
		$g["ID"] = $group->ID;
		$g["name"] = $group->post_title;
		if(!empty($leaders_list))
		$g["group_leaders"] = grassblade_learndash_group_leaders($group);

		if(!empty($users_list))
		$g["group_users"] = grassblade_learndash_group_users($group);
        $return[$g["ID"]] = $g;
    }
    if(!empty($params["id"]))
    {	
    	if(!empty($return[$params["id"]]))
    	return $return[$params["id"]];
    }
    return $return;
}

add_filter("gb_leaderboard_ids", 'grassblade_learndash_leaderboard_ids', 10, 3); 
function grassblade_learndash_leaderboard_ids($ids, $post, $shortcode_atts) {
	if($post->post_type == "sfwd-courses")
	{
		global $wpdb;
		$post_ids = $wpdb->get_col($wpdb->prepare("SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = 'show_xapi_content' AND  meta_value > 0 AND post_id IN (SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'course_id' AND meta_value = '%d')", $post->ID));
		if(!empty($post_ids))
		$ids = array_merge($ids, $post_ids);

		if(!empty($post_ids))
		return $ids;
	}
	return $ids;
}
add_action('wp_head','grassblade_learndash_hide_mark_complete_button');
function grassblade_learndash_hide_mark_complete_button(){
	global $post;
	if(empty($post->ID)) return;
	$selected_id = get_post_meta($post->ID, "show_xapi_content", true);
	if(!empty($selected_id)) {
		$completion_tracking = grassblade_xapi_content::is_completion_tracking_enabled($selected_id);
		if($completion_tracking) {
			?>
		<style>
			#sfwd-mark-complete{ 
			 	display:none;
			}
		</style>
		<?php
		}
	}
}

add_filter("grassblade_add_to_content_post", "grassblade_learndash_grassblade_add_to_content_post", 2, 10);
function grassblade_learndash_grassblade_add_to_content_post($selected_id, $post, $content) {
	if(!empty($post) && $post->post_type == "sfwd-quiz")
		return false;
	else
		return $selected_id;
}
add_filter("learndash_quiz_content", "grassblade_learndash_quiz_content", 2, 10);
function grassblade_learndash_quiz_content($quiz_content, $post) {
	$selected_id = get_post_meta($post->ID, "show_xapi_content", true);
	if(!empty($selected_id)) {
		if(strpos($quiz_content, "[grassblade]") === false)
		$quiz_content = do_shortcode('[grassblade id='.$selected_id."]");
		else
		$quiz_content = str_replace("[grassblade]", do_shortcode('[grassblade id='.$selected_id."]"),	$quiz_content);

		$completion_tracking = grassblade_xapi_content::is_completion_tracking_enabled($selected_id);
		$gb_completion_tracking_alert = apply_filters("gb_completion_tracking_alert", __("Click OK to confirm that you have completed the content above?", "grassblade"), $post->ID, $selected_id);
		if($completion_tracking && !empty($gb_completion_tracking_alert)) {
			$quiz_content .= ' <script> jQuery(function() { jQuery("form#sfwd-mark-complete").submit(function(e) { var completed_course=confirm("'.$gb_completion_tracking_alert.'"); if(completed_course == false) e.preventDefault();}); });</script> ';
		}

		if(learndash_is_quiz_notcomplete(null, array($post->ID => 1 ))) 
		$quiz_content .= "<br>" . learndash_mark_complete($post); 
	}

	return $quiz_content;
}
add_filter("grassblade_add_to_content_box_onchange", "grassblade_learndash_grassblade_add_to_content_box_onchange", 2, 10);
function grassblade_learndash_grassblade_add_to_content_box_onchange($action, $post) {
	if(!empty($post->post_type) && $post->post_type == "sfwd-quiz")
		return '';
	else
		return $action;
}
