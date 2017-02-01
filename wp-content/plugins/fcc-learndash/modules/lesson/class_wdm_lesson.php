<?php

class Wdm_Lesson
{
    public function __construct()
    {
        include_once trailingslashit(dirname(dirname(__FILE__))).'includes/class-wdm-wusp-get-data.php';

        global $wdm_plugin_data;
        $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

        if ($get_data_from_db == 'available') { //If License

            //add_filter( 'pep_add_custom_field_in_form', array( $this, 'add_custom_fields' ) );
            add_shortcode('wdm_lesson_creation', array($this, 'wdm_lesson_creation'));
            add_action('init', array($this, 'wdm_lesson_save'));
            add_shortcode('wdm_lesson_list', array($this, 'wdm_lesson_list'));
        }
    }

    public function wdm_lesson_creation()
    {
        //echo plugins_url( 'sfwd-lms' ); 
        //session_start();
        global $current_user;
        //echo "<pre>";print_R($current_user);echo "</pre>";
        ob_start();
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-course-script', plugins_url('js/wdm_lesson.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_lesson_creation.php';
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-validate-js', 'wdm_validate_object',
                    array(
                        'wdm_enter_title'   => __('Please Enter Title', 'fcc')
                    )
                );
                $data = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'wdm_empty_tag' => __('Please Enter Tag','fcc'),
                    'wdm_tag_added' => __('Tag added successfully', 'fcc')
                    );
                wp_localize_script('wdm-custom-js', 'wdm_data', $data);
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
                wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-course-script', plugins_url('js/wdm_lesson.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_lesson_creation.php';
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-validate-js', 'wdm_validate_object',
                    array(
                        'wdm_enter_title'   => __('Please Enter Title', 'fcc')
                    )
                );
                $data = array(
                    'ajax_url' => admin_url('admin-ajax.php'),
                    'wdm_empty_tag' => __('Please Enter Tag','fcc'),
                    'wdm_tag_added' => __('Tag added successfully', 'fcc')
                    );
                wp_localize_script('wdm-custom-js', 'wdm_data', $data);
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_lesson_save()
    {
        global $wpdb;
        $wdm_flag = 0;
        $wdm_error = '';
        if (isset($_POST[ 'wdm_lesson_action' ])) {
            if ($_POST['title'] == '') {
                $wdm_error .= __('ERROR: Title is Required', 'fcc').'<br>';
                $wdm_flag = 1;
            }

        // if($_POST['wdm_content'] == ''){
        // 	$wdm_error .= __('ERROR: Description is Required','fcc').'<br>';
        // 	$wdm_flag = 1;
        // }

        if ($wdm_flag == 1) {
            define('WDM_ERROR', $wdm_error);

            return;
        }
            $term_relationship = $wpdb->prefix.'term_relationships';
            $wdm_path_data = wp_upload_dir();
            $wdm_path = $wdm_path_data[ 'path' ];
            $wdm_url = $wdm_path_data[ 'url' ];
//		echo "<pre>";
//		print_r( wp_upload_dir() );
//		echo "</pre>";
//		echo "<pre>";
//		print_r( $_POST );
//		echo "</pre>";exit;
//		echo "<pre>";
//		print_r( $_FILES );
//		echo "</pre>";
//        
        if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
            $order_number = $_POST['order_number'];
        } else {
            $order_number = 0;
        }
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $post_status = get_option('wdm_fcc_post_status', 'draft');
            //session_start();
            if (isset($_POST[ 'lessonid' ])) {
                //echo $wdm_content;exit;
                $lesson_id = $_POST[ 'lessonid' ];
                $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $lesson_id AND post_type like 'sfwd-lessons'";
                $author_id = $wpdb->get_var($sql);
                if ($author_id != get_current_user_id()) {
                    wp_die("cheating hu'h?");
                    exit;
                }
                $lesson_post = array(
                    'ID' => $lesson_id,
                    'post_title' => $wdm_title,
                    'post_content' => $wdm_content,
                    'post_status' => $post_status,
                    'post_author' => get_current_user_id(),
                    'menu_order'    => $order_number,
                );

                // Update the post into the database
                wp_update_post($lesson_post);
            } else {
                $post_sql = "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name LIKE '".sanitize_title($wdm_title)."'";
                $post_name = $wpdb->get_var($post_sql);
                if ($post_name == '') {
                    $post_name = sanitize_title($wdm_title);
                } else {
                    $post_name .= '-'.time();
                }
                $lesson = array(
                    'post_title' => $wdm_title,
                    'post_status' => $post_status,
                    'post_type' => 'sfwd-lessons',
                    'post_content' => $wdm_content,
                    'post_author' => get_current_user_id(),
                    'post_name' => $post_name,
                    'menu_order'    => $order_number,
                );

//$is_visible = ($course->visible == 1 ? 'visible' : 'private');
//$sync_log .= "<br />Course Created: ".$course->fullname."<br /> <br />";
//generate a random unique sku for courses imported.
//$sku = "course_".mt_rand();
                $lesson_id = wp_insert_post($lesson);
            }
            $sql = "DELETE FROM $term_relationship WHERE object_id = $lesson_id";
            $wpdb->query($sql);
            if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
                foreach ($_POST[ 'category' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $lesson_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                foreach ($_POST[ 'tag' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $lesson_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }

            if (isset($_FILES[ 'featured_image' ]) && $_FILES[ 'featured_image' ][ 'name' ] != '') {
                $extension = explode('.', $_FILES[ 'featured_image' ][ 'name' ]);
                $ext = $extension[ count($extension) - 1 ];
                $target_file = $wdm_path.'/'.$lesson_id.'.'.$ext;
                $target_file_url = $wdm_url.'/'.$lesson_id.'.'.$ext;
                move_uploaded_file($_FILES[ 'featured_image' ][ 'tmp_name' ], $target_file);
                wdm_insert_attachment($target_file_url, $lesson_id);
            }
            $data = array();
            if (isset($_POST[ 'sfwd-lessons_course' ])) {
                $data[ 'sfwd-lessons_course' ] = $_POST[ 'sfwd-lessons_course' ];
                update_post_meta($lesson_id, 'course_id', $_POST[ 'sfwd-lessons_course' ]);
            }
            if (isset($_POST[ 'sfwd-lessons_forced_lesson_time' ])) {
                $data[ 'sfwd-lessons_forced_lesson_time' ] = $_POST[ 'sfwd-lessons_forced_lesson_time' ];
            }
            if (isset($_POST[ 'sfwd-lessons_lesson_assignment_upload' ])) {
                $data[ 'sfwd-lessons_lesson_assignment_upload' ] = $_POST[ 'sfwd-lessons_lesson_assignment_upload' ];
            }
            if (isset($_POST[ 'sfwd-lessons_auto_approve_assignment' ])) {
                $data[ 'sfwd-lessons_auto_approve_assignment' ] = $_POST[ 'sfwd-lessons_auto_approve_assignment' ];
            }

            if (isset($_POST['sfwd-lessons_lesson_assignment_points_enabled'])) {
                $data['sfwd-lessons_lesson_assignment_points_enabled'] = $_POST['sfwd-lessons_lesson_assignment_points_enabled'];
            }

            if (isset($_POST[ 'sfwd-lessons_lesson_assignment_points_amount' ])) {
                $data[ 'sfwd-lessons_lesson_assignment_points_amount' ] = $_POST[ 'sfwd-lessons_lesson_assignment_points_amount' ];
            }

            if (isset($_POST[ 'sfwd-lessons_sample_lesson' ])) {
                $data[ 'sfwd-lessons_sample_lesson' ] = $_POST[ 'sfwd-lessons_sample_lesson' ];
            }
            if (isset($_POST[ 'sfwd-lessons_visible_after' ])) {
                $data[ 'sfwd-lessons_visible_after' ] = $_POST[ 'sfwd-lessons_visible_after' ];
            }
            if (isset($_POST[ 'sfwd-lessons_visible_after_specific_date' ])) {
                $data[ 'sfwd-lessons_visible_after_specific_date' ] = $_POST[ 'sfwd-lessons_visible_after_specific_date' ];
            }

            //$wdm_course_data = serialize($data);
            update_post_meta($lesson_id, '_sfwd-lessons', $data);
            //echo "12321";
            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_lesson_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?lessonid='.$lesson_id;
            if (!isset($_POST[ 'lessonid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            wp_redirect($link);
            exit;
        }
    }

    public function wdm_lesson_list()
    {
        ob_start();
        global $wpdb;
        global $current_user;
        $table = $wpdb->prefix.'posts';
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-datatable-script', 'wdm_datatable_object', 
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn'  => __('Previous', 'fcc'),
                'wdm_next_btn'  => __('Next', 'fcc'),
                'wdm_search_bar'    => __('Search','fcc'),
                'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table'   => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching'   => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf( __('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))),  array('jquery'));

                include_once dirname(__FILE__).'/wdm_lesson_list.php';
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
                wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-datatable-script', 'wdm_datatable_object', 
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn'  => __('Previous', 'fcc'),
                'wdm_next_btn'  => __('Next', 'fcc'),
                'wdm_search_bar'    => __('Search','fcc'),
                'wdm_info_empty'    => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table'   => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching'   => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf( __('(filtered from %s total entries)', 'fcc'), '_MAX_')
            )
        );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))),  array('jquery'));

                include_once dirname(__FILE__).'/wdm_lesson_list.php';
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();

        return ob_get_clean();
    }
}

new Wdm_Lesson();
