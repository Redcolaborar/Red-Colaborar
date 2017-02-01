<?php
/**
 * @package LearnDash Zapier
 * @version 2.0.6.3 1i
 */
/*
Plugin Name: LearnDash Zapier
Plugin URI: http://www.learndash.com
Description: LearnDash Zapier Integration Plugin.
Version: 1.0
Author: LearnDash
Author URI: http://www.learndash.com
*/
add_action( 'init', 'ld_zapier_init', 0 );
function ld_zapier_init()
{
    $post_args = array(
        'labels' => array(
            'name' => __('LearnDash Zapier','learndash_zapier'),
            'singular_name' => __('LearnDash Zapier', 'learndash_zapier'),
            'add_new' => __('Add Trigger', 'learndash_zapier'),
            'add_new_item' => __('Add Trigger', 'learndash_zapier'),
            'edit' => __('Edit Trigger', 'learndash_zapier'),
            'edit_item' => __('Edit Trigger', 'learndash_zapier'),
            'new_item' => __('Trigger', 'learndash_zapier'),
            'view' => __('View Trigger', 'learndash_zapier'),
            'view_item' => __('View Trigger', 'learndash_zapier'),
            'search_items' => __('Search Trigger', 'learndash_zapier'),
            'not_found' => __('No Trigger found', 'learndash_zapier'),
            'not_found_in_trash' => __('No trigger found in Trash', 'learndash_zapier')
           ),
        'public' => true,
        'menu_position' => 15,
        'supports' => array(
            'title',
            ),
        'menu_icon' => 'dashicons-admin-generic',
        'has_archive' => true);
    $post_args = apply_filters("learndash_post_args_zapier", $post_args);
    register_post_type('sfwd-zapier', $post_args);
}
add_action('admin_menu', 'ld_zapier_menu', 1000);
function ld_zapier_menu() {
    global $submenu;
    if(isset($submenu["edit.php?post_type=sfwd-zapier"]))
    remove_menu_page( "edit.php?post_type=sfwd-zapier" );
}
function learndash_zapier_admin_tabs($admin_tabs) {
        $admin_tabs["zapier"] = array(
                                                                        "link"  =>      'edit.php?post_type=sfwd-zapier',
                                                                        "name"  =>      __("Zapier","learndash_zapier"),
                                                                        "id"    =>      "edit-sfwd-zapier",
                                                                        "menu_link"     =>      "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
                                                                );
         $admin_tabs["zapier-edit"] = array(
                                                                        "link"  =>      'post-new.php?post_type=sfwd-zapier',
                                                                        "name"  =>      __("Zapier","learndash_zapier"),
                                                                        "id"    =>      "sfwd-zapier",
                                                                        "menu_link"     =>      "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses",
                                                                );
        return $admin_tabs;
}
add_filter("learndash_admin_tabs", "learndash_zapier_admin_tabs", 1, 1);

function learndash_zapier_learndash_admin_tabs_on_page($admin_tabs_on_page, $admin_tabs, $current_page_id) {
        if(empty($admin_tabs_on_page["edit-sfwd-zapier"]) || !count($admin_tabs_on_page["edit-sfwd-zapier"]))
                $admin_tabs_on_page["edit-sfwd-zapier"] = array();

        if(empty($admin_tabs_on_page["sfwd-zapier"]) || !count($admin_tabs_on_page["sfwd-zapier"]))
                $admin_tabs_on_page["sfwd-zapier"] = array();

        $admin_tabs_on_page["edit-sfwd-zapier"] = array_merge($admin_tabs_on_page["sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses"], (array) $admin_tabs_on_page["edit-sfwd-zapier"]);
        $admin_tabs_on_page["sfwd-zapier"] = array_merge($admin_tabs_on_page["sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses"], (array) $admin_tabs_on_page["sfwd-zapier"]);

        foreach ($admin_tabs as $key => $value) {
                if($value["id"] == $current_page_id && $value["menu_link"] == "edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses")
                {
                        $admin_tabs_on_page[$current_page_id][] = "zapier";
                        return $admin_tabs_on_page;
                }
        }
        //$admin_tabs_on_page["sfwd-zapier"] = $admin_tabs_on_page["edit-sfwd-zapier"];
        return $admin_tabs_on_page;
}
add_filter("learndash_admin_tabs_on_page", "learndash_zapier_learndash_admin_tabs_on_page", 3, 3);


add_action( 'plugins_loaded', 'ld_zapier' );
function ld_zapier() {
    load_plugin_textdomain( 'learndash_zapier', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );                
}

add_action( 'admin_init', 'ld_zapier_meta_box' );
function ld_zapier_meta_box() {
    add_meta_box( 'ld_zapier_meta_box',
        __('Trigger Settings','learndash_zapier'),
        'ld_zapier_meta_box_content',
        'sfwd-zapier', 'normal', 'high'
    );
}

function ld_zapier_meta_box_content( $zapier_data ) {
    $webhook_url = esc_html( get_post_meta( $zapier_data->ID, 'webhook', true ) );
    $zapier_trigger =  get_post_meta( $zapier_data->ID, 'zapier_trigger', true);
    ?>
    <table>
    <tr>
            <td style="width: 150px"><?php _e('Trigger Event','learndash_zapier'); ?></td>
            <td>
                <select name="zapier_trigger">
                <option value=""><?php _e('Select','learndash_zapier'); ?></option>
                <option value="enrolled_into_course" <?php echo selected( $zapier_trigger,"enrolled_into_course" ); ?> ><?php _e('Enrolled into course','learndash_zapier'); ?></option>
                <option value="lesson_completed" <?php echo selected( $zapier_trigger,"lesson_completed" ); ?> ><?php _e('Lesson completed','learndash_zapier'); ?></option>
                <option value="topic_completed" <?php echo selected( $zapier_trigger,"topic_completed" ); ?> ><?php _e('Topic completed','learndash_zapier'); ?></option>
                <option value="quiz_passed" <?php echo selected( $zapier_trigger,"quiz_passed" ); ?> ><?php _e('Quiz passed','learndash_zapier'); ?></option>
                <option value="quiz_failed" <?php echo selected( $zapier_trigger,"quiz_failed" ); ?> ><?php _e('Quiz failed','learndash_zapier'); ?></option>
                <option value="quiz_completed" <?php echo selected( $zapier_trigger,"quiz_completed" ); ?> ><?php _e('Quiz completed','learndash_zapier'); ?></option>
                <option value="course_completed" <?php echo selected( $zapier_trigger,"course_completed" ); ?> ><?php _e('Course completed','learndash_zapier'); ?></option>
                </select>
		<br>
		<small><?php _e('A trigger will be sent on the selected event to the URL configured below.','learndash_zapier'); ?></small>
		<br>
            </td>
        </tr>
        <tr>
            <td style="width: 150px"><?php _e('Webhook URL','learndash_zapier'); ?></td>
            <td><input type="text"  name="webhook" value="<?php echo $webhook_url; ?>" /><br>
		<small><?php _e('This is the url of your Zapier webhook, provided by Zapier when creating a new Zap.','learndash_zapier'); ?></small>
	    </td>
        </tr>        
    </table>
    <?php
}
add_action( 'save_post', 'ld_zapier_save_post', 10, 2 );
function ld_zapier_save_post( $post_id, $zapier_data ) {
    if ( $zapier_data->post_type == 'sfwd-zapier' ) {
        if ( isset( $_POST['webhook'] ) ) {
            update_post_meta( $post_id, 'webhook', $_POST['webhook'] );
        }
        if ( isset( $_POST['zapier_trigger'] ) ) {
            update_post_meta( $post_id, 'zapier_trigger', $_POST['zapier_trigger'] );
        }
    }
}

//Enrolled into course
//do_action("learndash_update_course_access", $user_id, $course_id, $access_list, $remove);
add_action("learndash_update_course_access", "ld_zapier_learndash_update_course_access", 10, 4);
function ld_zapier_learndash_update_course_access($user_id, $course_id, $access_list, $remove) {
    ld_zapier_debug("ld_zapier_learndash_update_course_access");

    if($remove || empty($user_id) || empty($course_id))
        return;
    $user = get_user_by("id", $user_id);
    if(empty($user->ID))
        return;
    $course = get_post($course_id);
    if(empty($course->ID))
        return;

    $data = array(
            "user" => $user->data,
            "course" =>  $course
        );
    ld_zapier_debug($data);

    ld_zapier_send_trigger("enrolled_into_course", $data);
}

//Lesson Completed
//do_action('learndash_lesson_completed', array( 'user' => $current_user, 'course' => get_post($course_id), 'lesson' => $post,  'progress' => $course_progress)); 
add_action("learndash_lesson_completed", "ld_zapier_learndash_lesson_completed", 10, 1);
function ld_zapier_learndash_lesson_completed($data) {
    if(!empty($data["user"]->ID) && !empty($data["lesson"]->ID) && !empty($data["course"]->ID))
    ld_zapier_send_trigger("lesson_completed", $data);
}

//do_action('learndash_course_completed', array( 'user' => $current_user, 'course' => get_post($course_id), 'progress' => $course_progress)); 
add_action("learndash_course_completed", "ld_zapier_learndash_course_completed", 10, 1);
function ld_zapier_learndash_course_completed($data) {
    if(!empty($data["user"]->ID) && !empty($data["course"]->ID))
    ld_zapier_send_trigger("course_completed", $data);
}


//do_action('learndash_topic_completed', array( 'user' => $current_user, 'course' => get_post($course_id), 'lesson' => get_post($lesson_id), 'topic' => $post,  'progress' => $course_progress));
add_action("learndash_topic_completed", "ld_zapier_learndash_topic_completed", 10, 1);
function ld_zapier_learndash_topic_completed($data) {
    if(!empty($data["user"]->ID) && !empty($data["topic"]->ID)  && !empty($data["lesson"]->ID) && !empty($data["course"]->ID))
    ld_zapier_send_trigger("topic_completed", $data);
}

//do_action("learndash_quiz_completed", $quizdata, $current_user); //Hook for completed quiz
//$quizdata = array( "quiz" => $quiz,  "course" => $course, "questions" => $questions, "score" => $score, "count" => $count, "pass" => $pass, "rank" => '-', "time" => time(), 'pro_quizid' => $quiz_id, 'points' => $points, 'total_points' => $total_points, 'percentage' => $result, 'timespent' => $timespent);
add_action("learndash_quiz_completed", "ld_zapier_learndash_quiz_passed", 10, 2);
function ld_zapier_learndash_quiz_passed($data, $user) {

    if(!empty($user->ID) && !empty($data["quiz"]->ID) && !empty($data["course"]->ID))  {
        $data["user"] = $user;
        unset($data["rank"]);
        unset($data["questions"]);
        if(!empty($data["pass"]))
        ld_zapier_send_trigger("quiz_passed", $data);
        else
        ld_zapier_send_trigger("quiz_failed", $data);

        ld_zapier_send_trigger("quiz_completed", $data);
    }
}

function ld_zapier_send_trigger($type, $data) {
    $opt = array(
            "post_type" => "sfwd-zapier",
            "meta_query" => array(array(
                        "key" => "zapier_trigger",
                        "value" => $type,
                ))
        );
    $data["trigger_type"] = $type;
    $triggers = get_posts($opt);
    ld_zapier_debug($opt);
    ld_zapier_debug($triggers);

    if(!empty($triggers)) {
        foreach ($triggers as $trigger) {

            $webhook_url = get_post_meta( $trigger->ID, 'webhook', true);
            ld_zapier_debug($webhook_url.":".$post_id);
            if(!empty($data["user"]->data))
                $data["user"] = $data["user"]->data;

            ld_zapier_post($webhook_url, $data);
        }
    }
}
function ld_zapier_post($url, $data) {
    if(empty($url) || !filter_var($url, FILTER_VALIDATE_URL))
        return;

    $args = array(
            "method" => "POST",
            "timeout"       => 20,
            "body"  => $data
            );
    ld_zapier_debug($data);
    return wp_remote_post($url, $args);
}

function ld_zapier_debug($msg) {
    return;

    ini_set('log_errors', true);
    ini_set('error_log', dirname(__FILE__).DIRECTORY_SEPARATOR.'errors.log');
    global $ld_lms_processing_id;
    error_log("[$ld_lms_processing_id] ".print_r($msg, true));
}
