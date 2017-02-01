<?php
/* Plugin Name: Frontend Course Creation
 * Plugin URI: https://wisdmlabs.com/front-end-course-creation-for-learndash/
 * Description: The plugin creates a user role 'Course Author' with privileges to add a course, lesson, topic or quiz from the front-end.
 * Version: 2.0.3
 * Author: Wisdmlabs
 * Author URI: http://wisdmlabs.com/
 * Text Domain: fcc
 * Domain Path: /languages
 * */

if (session_id() == '') {
    session_start();
}

//
load_plugin_textdomain('fcc', false, dirname(plugin_basename(__FILE__)).'/languages');

add_action('admin_init', 'wdm_fcc_admin_activation');

function wdm_fcc_admin_activation()
{
    if (!is_plugin_active('sfwd-lms/sfwd_lms.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        unset($_GET[ 'activate' ]);
        add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
    }

    if (is_multisite()) {
        // if (!is_plugin_active('buddypress/bp-loader.php')) {
        //     deactivate_plugins(plugin_basename(__FILE__));
        //     unset($_GET[ 'activate' ]);
        //     add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
        // }
        if (!function_exists('is_plugin_active_for_network')) {
            include_once ABSPATH.'/wp-admin/includes/plugin.php';
        }
        if (is_plugin_active_for_network('fcc-learndash/fcc.php')) {
            add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
        }
    } else {
        if (!is_plugin_active('buddypress/bp-loader.php')) {
            deactivate_plugins(plugin_basename(__FILE__));
            unset($_GET[ 'activate' ]);
            add_action('admin_notices', 'wdm_fcc_my_plugin_admin_notices');
        }
    }
}

function wdm_fcc_my_plugin_admin_notices()
{
    if (!is_plugin_active('sfwd-lms/sfwd_lms.php')) {
        ?>
		<div class='error'><p>
				<?php echo __("LearnDash LMS plugin is not active. In order to make the 'Frontend Course Creation' plugin work, you need to install and activate LearnDash LMS first.", 'fcc');
        ?>
			</p></div>

		<?php

    }
    if (!is_plugin_active('buddypress/bp-loader.php')) {
        if (!is_plugin_active_for_network('fcc-learndash/fcc.php')) {
            ?>
		<div class='error'><p>
				<?php echo __('Please Activate BuddyPress Plugin for Activating Frontend Course Creation plugin', 'fcc');
            ?>
			</p></div>

		<?php

        } else {
            ?>
        <div class='error'><p>
                <?php echo __('Please Activate BuddyPress Plugin to Run Front End Course Creation', 'fcc');
            ?>
            </p></div>

        <?php

        }
    }
}

function wdm_fcc_admin_activation_network()
{
    if (!function_exists('is_plugin_active_for_network')) {
        include_once ABSPATH.'/wp-admin/includes/plugin.php';
    }

    if (!is_plugin_active_for_network('sfwd-lms/sfwd_lms.php')) {
        ?><div class='error'><p><?php
        echo __("LearnDash LMS plugin is not active. In order to make the 'Frontend Course Creation' plugin work, you need to install and activate LearnDash LMS first.", 'fcc');
        ?></p></div>

		<?php
        deactivate_plugins(plugin_basename(__FILE__));
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }
}

add_action('network_admin_notices', 'wdm_fcc_admin_activation_network');

$wdm_plugin_data = array(
    'plugin_short_name' => 'Frontend Course Creation', //Plugins short name appears on the License Menu Page
    'plugin_slug' => 'fcc', //this slug is used to store the data in db. License is checked using two options viz edd_<slug>_license_key and edd_<slug>_license_status
    'plugin_version' => '2.0.3', //Current Version of the plugin. This should be similar to Version tag mentioned in Plugin headers
    'plugin_name' => 'Frontend Course Creation', //Under this Name product should be created on WisdmLabs Site
     'store_url' => 'https://wisdmlabs.com', //Url where program pings to check if update is available and license validity
    'author_name' => 'WisdmLabs', //Author Name
);

include_once 'modules/includes/class-wdm-wusp-add-data-in-db.php';
new WdmWuspAddDataFCC\WdmWuspAddDataInDB($wdm_plugin_data);

/*
 * This code checks if new version is available
 */
if (!class_exists('WdmWuspPluginUpdater')) {
    include 'modules/includes/class-wdm-wusp-plugin-updater.php';
}

//get current license key
$l_key = trim(get_option('edd_'.$wdm_plugin_data[ 'plugin_slug' ].'_license_key'));

// setup the updater
new WuspPluginUpdaterFCC\WdmWuspPluginUpdater($wdm_plugin_data[ 'store_url' ], __FILE__, array(
    'version' => $wdm_plugin_data[ 'plugin_version' ], // current version number
    'license' => $l_key, // license key (used get_option above to retrieve from DB)
    'item_name' => $wdm_plugin_data[ 'plugin_name' ], // name of this plugin
    'author' => $wdm_plugin_data[ 'author_name' ], //author of the plugin
)
);

$l_key = null;

// register_activation_hook( __FILE__, 'plugin_activation' );

add_action('init', 'wdm_fcc_plugin_activation');

function wdm_fcc_plugin_activation()
{
    include_once 'modules/includes/class-wdm-wusp-get-data.php';

    global $wdm_plugin_data;
    //echo '<pre>';print_R($wdm_plugin_data);echo '</pre>';exit;
    $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

    // echo $get_data_from_db;
    // exit;
//echo $get_data_from_db;exit;
    if ($get_data_from_db == 'available') { //If License

        // remove_role( 'wdm_course_author' );

        add_role(
        'wdm_course_author', __('Course Author', 'fcc'), array(
            'read' => true,
            'upload_files' => true,
        ));
        // echo "asdasdasd";exit;
        // 	exit;
        global $wpdb;
        $wdm_course_create_page = get_option('wdm_course_create_page');
        // echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";
        // exit;
        if ($wdm_course_create_page == '') {
            $course_create_page = array(
                'post_title' => __('Create Course', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_course_creation]',
                'post_author' => get_current_user_id(),
            );

            $course_page_id = wp_insert_post($course_create_page);
            update_option('wdm_course_create_page', $course_page_id);
        }
        $wdm_course_list_page = get_option('wdm_course_list_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_course_list_page == '') {
            $course_list_page = array(
                'post_title' => __('Course List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_course_list]',
                'post_author' => get_current_user_id(),
            );

            $course_page_id = wp_insert_post($course_list_page);
            update_option('wdm_course_list_page', $course_page_id);
        }
        $wdm_lesson_create_page = get_option('wdm_lesson_create_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_lesson_create_page == '') {
            $lesson_create_page = array(
                'post_title' => __('Create Lesson', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_lesson_creation]',
                'post_author' => get_current_user_id(),
            );

            $lesson_page_id = wp_insert_post($lesson_create_page);
            update_option('wdm_lesson_create_page', $lesson_page_id);
        }
        $wdm_lesson_list_page = get_option('wdm_lesson_list_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_lesson_list_page == '') {
            $lesson_list_page = array(
                'post_title' => __('Lesson List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_lesson_list]',
                'post_author' => get_current_user_id(),
            );

            $lesson_page_id = wp_insert_post($lesson_list_page);
            update_option('wdm_lesson_list_page', $lesson_page_id);
        }

        $wdm_topic_create_page = get_option('wdm_topic_create_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_topic_create_page == '') {
            $topic_create_page = array(
                'post_title' => __('Create Topic', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_topic_creation]',
                'post_author' => get_current_user_id(),
            );

            $topic_page_id = wp_insert_post($topic_create_page);
            update_option('wdm_topic_create_page', $topic_page_id);
        }
        $wdm_topic_list_page = get_option('wdm_topic_list_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_topic_list_page == '') {
            $topic_list_page = array(
                'post_title' => __('Topic List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_topic_list]',
                'post_author' => get_current_user_id(),
            );

            $topic_page_id = wp_insert_post($topic_list_page);
            update_option('wdm_topic_list_page', $topic_page_id);
        }
        $wdm_quiz_create_page = get_option('wdm_quiz_create_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_quiz_create_page == '') {
            $quiz_create_page = array(
                'post_title' => __('Create Quiz', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_quiz_creation]',
                'post_author' => get_current_user_id(),
            );

            $quiz_page_id = wp_insert_post($quiz_create_page);
            update_option('wdm_quiz_create_page', $quiz_page_id);
        }
        $wdm_quiz_list_page = get_option('wdm_quiz_list_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_quiz_list_page == '') {
            $quiz_list_page = array(
                'post_title' => __('Quiz List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_quiz_list]',
                'post_author' => get_current_user_id(),
            );

            $quiz_list_page = wp_insert_post($quiz_list_page);
            update_option('wdm_quiz_list_page', $quiz_list_page);
        }
        $wdm_question_create_page = get_option('wdm_question_create_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_question_create_page == '') {
            $question_create_page = array(
                'post_title' => __('Create Question', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_question_creation]',
                'post_author' => get_current_user_id(),
            );

            $question_page_id = wp_insert_post($question_create_page);
            update_option('wdm_question_create_page', $question_page_id);
        }
        $wdm_question_list_page = get_option('wdm_question_list_page');
        //echo "<pre>";print_R($wdm_course_create_page);echo "</pre>";exit;
        if ($wdm_question_list_page == '') {
            $question_list_page = array(
                'post_title' => __('Question List', 'fcc'),
                'post_status' => 'publish',
                'post_type' => 'page',
                'post_content' => '[wdm_question_list]',
                'post_author' => get_current_user_id(),
            );

            $question_page_id = wp_insert_post($question_list_page);
            update_option('wdm_question_list_page', $question_page_id);
        }
    }// End-If License
}

include_once dirname(__FILE__).'/modules/course/class_wdm_course.php';
include_once dirname(__FILE__).'/modules/lesson/class_wdm_lesson.php';
include_once dirname(__FILE__).'/modules/topic/class_wdm_topic.php';
include_once dirname(__FILE__).'/modules/quiz/class_wdm_quiz.php';
include_once dirname(__FILE__).'/modules/question/class_wdm_question.php';
include_once dirname(__FILE__).'/modules/buddypress/class_ld_buddypress.php';
include_once dirname(__FILE__).'/modules/settings/wdm_course_setting.php';
include_once dirname(__FILE__).'/modules/commission/commission.php';

function wdm_insert_attachment($imgurl, $post_id)
{
    include_once 'modules/includes/class-wdm-wusp-get-data.php';

    global $wdm_plugin_data;
    $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

    if ($get_data_from_db == 'available') { //If License
        // $filename should be the path to a file in the upload directory.
        $upload_dir = wp_upload_dir();
        $image_data = file_get_contents($imgurl);
        //echo $image_data;exit;
        $filename = basename($imgurl);

        // The ID of the post this attachment is for.
        $parent_post_id = $post_id;

        $file = $upload_dir[ 'path' ].'/'.$filename;
        file_put_contents($file, $image_data);

        $wp_filetype = wp_check_filetype($filename, null);
        $attachment = array(
            'post_mime_type' => $wp_filetype[ 'type' ],
            'post_title' => sanitize_file_name($filename),
            'post_content' => '',
            'post_status' => 'inherit',
        );
        $attach_id = wp_insert_attachment($attachment, $file, $parent_post_id);
        require_once ABSPATH.'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attach_id, $file);
        wp_update_attachment_metadata($attach_id, $attach_data);

        update_post_meta($parent_post_id, '_thumbnail_id', $attach_id);
    } //End If License
}

// add_shortcode( 'wdm_quiz', 'wdm_quiz' );
// function wdm_quiz() {
// 	include_once( dirname( __FILE__ ) . '/wdm_quiz.php' );
// }
//replace last occurrence of string
function str_lreplace($search, $replace, $subject)
{
    $pos = strrpos($subject, $search);

    if ($pos !== false) {
        $subject = substr_replace($subject, $replace, $pos, strlen($search));
    }

    return $subject;
}

add_action('wp_ajax_wdm_tag_add', 'wdm_fcc_add_tag');

function wdm_fcc_add_tag()
{
    global $wpdb;
    //ob_start();

    include_once 'modules/includes/class-wdm-wusp-get-data.php';

    global $wdm_plugin_data;
    $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);
    if ($get_data_from_db == 'available') {
        $term_table = $wpdb->prefix.'terms';
        $taxonomy = $wpdb->prefix.'term_taxonomy';
        $tag = $_POST[ 'tag' ];
        $sql = "SELECT term_id FROM $term_table WHERE name like '$tag'";
        $result = $wpdb->get_var($sql);
        if ($result != '') {
            echo json_encode(array('error' => __('Tag already exist', 'fcc')));
        } else {
            $string = str_replace(' ', '-', $tag); // Replaces all spaces with hyphens.
            $tag_slug = preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
            $data = array(
                'name' => $tag,
                'slug' => $tag_slug,
            );
            $wpdb->insert($term_table, $data);
            $insert_id = $wpdb->insert_id;
            $data = array(
                'term_id' => $insert_id,
                'taxonomy' => 'post_tag',
            );
            $wpdb->insert($taxonomy, $data);
            $insert_id = $wpdb->insert_id;
            $message = $insert_id.'$'.$tag;
            echo json_encode(array('success' => $message));
            //echo $insert_id;
        }
        die();
    }
}

add_filter('ajax_query_attachments_args', 'wdm_fcc_show_users_own_attachments', 1, 1);

function wdm_fcc_show_users_own_attachments($query)
{
    include_once 'modules/includes/class-wdm-wusp-get-data.php';

    global $wdm_plugin_data;
    $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

    if ($get_data_from_db == 'available') {
        $id = get_current_user_id();
        if (!current_user_can('manage_options')) {
            $query[ 'author' ] = $id;
        }
    }

    return $query;
}

add_action('admin_menu', 'wdm_fcc_reset_author_metabox');

/**
 *  To remove default author meta box and add custom author meta box, to list users having role "authors" or "Instructor" in LD custom post types.
 */
function wdm_fcc_reset_author_metabox()
{

    // Determine if user is a network (super) admin. Will also check if user is admin if network mode is disabled.
    if (is_super_admin()) {
        $wdm_ar_post_types = array(
            'sfwd-courses',
            'sfwd-lessons',
            'sfwd-quiz',
            'sfwd-topic', );

        foreach ($wdm_ar_post_types as $value) {
            remove_meta_box('authordiv', $value, 'normal');
            add_meta_box('authordiv', __('Author', 'fcc'), 'wdm_fcc_post_author_meta_box', $value);
        }
    }
}

/**
 * Custom Author meta box to display on a edit post page.
 */
function wdm_fcc_post_author_meta_box($post)
{
    global $user_ID;
    ?>
	<label class="screen-reader-text" for="post_author_override"><?php _e('Author', 'fcc');
    ?></label>
	<?php
    $wdm_args = array(
        'name' => 'post_author_override',
        'selected' => empty($post->ID) ? $user_ID : $post->post_author,
        'include_selected' => true,
    );
    $args = apply_filters('wdm_author_args', $wdm_args);
    wdm_fcc_wp_dropdown_users($args);
}

/**
 * To create HTML dropdown element of the users for given argument.
 */
function wdm_fcc_wp_dropdown_users($args = '')
{
    $defaults = array(
        'show_option_all' => '',
        'show_option_none' => '',
        'hide_if_only_one_author' => '',
        'orderby' => 'display_name',
        'order' => 'ASC',
        'include' => '',
        'exclude' => '',
        'multi' => 0,
        'show' => 'display_name',
        'echo' => 1,
        'selected' => 0,
        'name' => 'user',
        'class' => '',
        'id' => '',
        'include_selected' => false,
        'option_none_value' => -1,
    );

    $defaults[ 'selected' ] = is_author() ? get_query_var('author') : 0;

    $r = wp_parse_args($args, $defaults);
    $show = $r[ 'show' ];
    $show_option_all = $r[ 'show_option_all' ];
    $show_option_none = $r[ 'show_option_none' ];
    $option_none_value = $r[ 'option_none_value' ];

    $query_args = wp_array_slice_assoc($r, array('blog_id', 'include', 'exclude', 'orderby', 'order'));
    $query_args[ 'fields' ] = array('ID', 'user_login', $show);

    $users = array_merge(get_users(array('role' => 'administrator')), get_users(array('role' => 'wdm_course_author')), get_users(array('role' => 'author')));

    //echo '<pre>'; print_r( $users ); echo '</pre>';

    if (!empty($users) && (count($users) > 1)) {
        $name = esc_attr($r[ 'name' ]);
        if ($r[ 'multi' ] && !$r[ 'id' ]) {
            $id = '';
        } else {
            $id = $r[ 'id' ] ? " id='".esc_attr($r[ 'id' ])."'" : " id='$name'";
        }
        $output = "<select name='{$name}'{$id} class='".$r[ 'class' ]."'>\n";

        if ($show_option_all) {
            $output .= "\t<option value='0'>$show_option_all</option>\n";
        }

        if ($show_option_none) {
            $_selected = selected($option_none_value, $r[ 'selected' ], false);
            $output .= "\t<option value='".esc_attr($option_none_value)."'$_selected>$show_option_none</option>\n";
        }

        $found_selected = false;
        foreach ((array) $users as $user) {
            $user->ID = (int) $user->ID;
            $_selected = selected($user->ID, $r[ 'selected' ], false);
            if ($_selected) {
                $found_selected = true;
            }
            $display = !empty($user->$show) ? $user->$show : '('.$user->user_login.')';
            $output .= "\t<option value='$user->ID'$_selected>".esc_html($display)."</option>\n";
        }

        if ($r[ 'include_selected' ] && !$found_selected && ($r[ 'selected' ] > 0)) {
            $user = get_userdata($r[ 'selected' ]);
            $_selected = selected($user->ID, $r[ 'selected' ], false);
            $display = !empty($user->$show) ? $user->$show : '('.$user->user_login.')';
            $output .= "\t<option value='$user->ID'$_selected>".esc_html($display)."</option>\n";
        }

        $output .= '</select>';
    }
    if ($r[ 'echo' ]) {
        echo $output;
    }

    return $output;
}

function wdm_is_course_author($user_id)
{
    if (!is_user_logged_in()) {
        return false;
    }

    $current_user = get_user_by('id', $user_id);
    if (in_array('wdm_course_author', $current_user->roles)) {
        return true;
    } else {
        return false;
    }
}

// add_filter('show_admin_bar', 'wdm_fcc_hide_admin_bar', 10, 1);

// function wdm_fcc_hide_admin_bar($status)
// {
//     if (!is_user_logged_in()) {
//         return $status;
//     }
//     if (wdm_is_course_author(get_current_user_id())) {
//         return false;
//     } else {
//         return $status;
//     }
// }

add_action('init', 'wdm_fcc_role_problem');

function wdm_fcc_role_problem()
{
    if (!is_user_logged_in()) {
        return;
    }

    $user_obj = wp_get_current_user();

    if (count($user_obj->roles) < 2) {
        if (wdm_is_course_author(get_current_user_id())) {
            if (is_admin() && !defined('DOING_AJAX')) {
                $allowDashboardAccess = apply_filters('wdm_course_author_accessing_dashboard', false);
                if (!$allowDashboardAccess) {
                    wp_redirect(site_url());
                    die();
                }
            }
        }
    }

//	if (!current_user_can('edit_post')) {
    if (wdm_is_course_author(get_current_user_id())) {
        $user = new WP_User(get_current_user_id());
        $user->add_cap('edit_post');
        $user->add_cap('edit_others_pages');
        $user->add_cap('edit_published_pages');
//}
    }
}

add_action('admin_menu', 'wdm_fcc_remove_menu', 1000);
// removing posts menu from backend
function wdm_fcc_remove_menu()
{
    //echo get_permalink(37);
    if (wdm_is_course_author(get_current_user_id())) {
        remove_menu_page('edit.php');
    }
}

add_filter('pre_get_posts', 'wdm_fcc_show_public_preview');

function wdm_fcc_show_public_preview($query)
{
    if (!is_user_logged_in()) {
        return $query;
    }
    $user_id = get_current_user_id();
    if (!wdm_is_course_author($user_id)) {
        return $query;
    }
    //echo '<pre>';print_R($query);echo '</pre>';exit;
    if (
    $query->is_singular() && (isset($_GET[ 'wdm_preview' ]) || isset($_GET['p']))
    ) {
        //	echo "inside";exit;
        add_filter('posts_results', 'wdm_fcc_set_post_to_publish', 10, 1);
    }

    return $query;
}

//add_filter( 'posts_results', 'wdm_fcc_set_post_to_publish' , 10, 1);
//changing post status to publish for course author
function wdm_fcc_set_post_to_publish($posts)
{
    if (empty($posts)) {
        return;
    }
    if (!is_user_logged_in()) {
        return $posts;
    }
    $user_id = get_current_user_id();
    if (!wdm_is_course_author($user_id)) {
        return $posts;
    }
    //echo '<pre>';print_R($posts);echo '</pre>';exit;
    if (isset($posts[ 0 ]->post_status) && isset($posts[ 0 ]->post_author) && $posts[ 0 ]->post_author == $user_id) {
        $post_id = $posts[ 0 ]->ID;
        if ($posts[ 0 ]->post_status == 'draft') {
            $posts[ 0 ]->post_status = 'publish';

            // Disable comments and pings for this post
            add_filter('comments_open', '__return_false');
            add_filter('pings_open', '__return_false');
        }
        //echo '<pre>';print_R($posts);echo '</pre>';exit;
        return $posts;
    } else {
        return $posts;
    }
}

add_filter('sfwd_lms_has_access', 'wdm_fcc_sfwd_lms_has_access', 100, 3);
/*
 * Giving access to course author
 */

function wdm_fcc_sfwd_lms_has_access($status, $post_id, $user_id)
{
    if (!is_user_logged_in()) {
        return $status;
    }
    if ($user_id == '') {
        $user_id = get_current_user_id();
    }
    if (!wdm_is_course_author($user_id)) {
        return $status;
    }
    $post_data = get_post($post_id);
    //echo '<pre>';print_R($post_data);echo '</pre>';
    //echo '<pre>';print_R($_SESSION);echo '</pre>';exit;
    if (isset($post_data->post_author) && $post_data->post_author == $user_id) {
        return true;
    } else {
        return $status;
    }
}

/**
 * Function to hide some of the admin bar options.
 */
function hideAdminBarOptions()
{
    $user_obj = wp_get_current_user();

    if (count($user_obj->roles) < 2) {
        if (wdm_is_course_author(get_current_user_id())) {
            global $wp_admin_bar;
            $wp_admin_bar->remove_menu('site-name');
            $wp_admin_bar->remove_menu('new-content');
            $wp_admin_bar->remove_menu('my-sites');
            $wp_admin_bar->remove_menu('comments');
            $wp_admin_bar->remove_node( 'edit' );
        }
    }
}
add_action('wp_before_admin_bar_render', 'hideAdminBarOptions');
