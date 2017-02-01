<?php

class Wdm_Course
{
    public function __construct()
    {
        include_once trailingslashit(dirname(dirname(__FILE__))).'includes/class-wdm-wusp-get-data.php';

        global $wdm_plugin_data;
        $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

        if ($get_data_from_db == 'available') { //If License
            add_shortcode('wdm_course_creation', array($this, 'wdm_course_creation'));
            add_action('init', array($this, 'wdm_course_save'));
            add_shortcode('wdm_course_list', array($this, 'wdm_course_list'));
            // add_action('wp_ajax_wdm_tag_add', array($this, 'wdm_tag_add'));
        }
    }
    
    public function wdm_course_creation()
    {

        //echo plugins_url( 'sfwd-lms' );
        //session_start();
        global $current_user, $post;
        //echo "<pre>";print_R($post);echo "</pre>";
        //echo "<pre>";print_R($current_user);echo "</pre>";
        //global $post_id;
        //echo $post_id;exit;
        ob_start();
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-course-script', plugins_url('js/wdm_course.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_course_creation.php';
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
                wp_enqueue_script('wdm-course-script', plugins_url('js/wdm_course.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_course_creation.php';
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

    public function wdm_course_save()
    {
        global $current_user;
        $wdm_error = '';
        //echo "<pre>";print_R($_POST);echo "</pre>";exit;
        if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
            $order_number = $_POST['order_number'];
        } else {
            $order_number = 0;
        }

        if (is_user_logged_in() && !is_super_admin(get_current_user_id())) {
            if (isset($current_user->roles)) {
                if (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles)) {
                    //$_REQUEST['post_id'] = 0;
                }
            }
        }

        $wdm_flag = 0;

        if (isset($_POST[ 'wdm_course_action' ])) {
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
            global $wpdb;
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
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $post_status = get_option('wdm_fcc_post_status', 'draft');
            if (isset($_POST[ 'courseid' ])) {
                //echo $wdm_content;exit;
                $course_id = $_POST[ 'courseid' ];
                $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $course_id AND post_type like 'sfwd-courses'";
                $author_id = $wpdb->get_var($sql);
                if ($author_id != get_current_user_id()) {
                    wp_die("cheating hu'h?");
                    exit;
                }
                $course_post = array(
                    'ID' => $course_id,
                    'post_title' => $wdm_title,
                    'post_content' => $wdm_content,
                    'post_status' => $post_status,
                    'post_author' => get_current_user_id(),
                    'menu_order' => $order_number
                );

                // Update the post into the database
                wp_update_post($course_post);
            } else {
                $post_sql = "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name LIKE '".sanitize_title($wdm_title)."'";
                $post_name = $wpdb->get_var($post_sql);
                if ($post_name == '') {
                    $post_name = sanitize_title($wdm_title);
                } else {
                    $post_name .= '-'.time();
                }
                $course = array(
                    'post_title' => $wdm_title,
                    'post_status' => $post_status,
                    'post_type' => 'sfwd-courses',
                    'post_content' => $wdm_content,
                    'post_author' => get_current_user_id(),
                    'post_name' => $post_name,
                    'menu_order' => $order_number
                );

//$is_visible = ($course->visible == 1 ? 'visible' : 'private');
//$sync_log .= "<br />Course Created: ".$course->fullname."<br /> <br />";
//generate a random unique sku for courses imported.
//$sku = "course_".mt_rand();
                $course_id = wp_insert_post($course);
            }
            $sql = "DELETE FROM $term_relationship WHERE object_id = $course_id";
            $wpdb->query($sql);
            if (isset($_POST[ 'category' ]) && (count($_POST[ 'category' ]) > 0)) {
                foreach ($_POST[ 'category' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $course_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                foreach ($_POST[ 'tag' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $course_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }

            if (isset($_FILES[ 'featured_image' ]) && $_FILES[ 'featured_image' ][ 'name' ] != '') {
                if ($_FILES['featured_image']['type'] == 'image/jpeg' || $_FILES['featured_image']['type'] == 'image/png') {
                    $extension = explode('.', $_FILES[ 'featured_image' ][ 'name' ]);
                    $ext = $extension[ count($extension) - 1 ];
                    $target_file = $wdm_path.'/'.$course_id.'.'.$ext;
                    $target_file_url = $wdm_url.'/'.$course_id.'.'.$ext;
                    move_uploaded_file($_FILES[ 'featured_image' ][ 'tmp_name' ], $target_file);
                    wdm_insert_attachment($target_file_url, $course_id);
                } else {
                    $wdm_error .= __('ERROR: For featured image only .png and .jpg extensions are allowed', 'fcc').'<br>';
                    $wdm_flag = 1;
                }
            }
            $data = array();
            if (isset($_POST[ 'sfwd-courses_course_materials' ])) {
                $data[ 'sfwd-courses_course_materials' ] = $_POST[ 'sfwd-courses_course_materials' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_price_type' ])) {
                $data[ 'sfwd-courses_course_price_type' ] = $_POST[ 'sfwd-courses_course_price_type' ];
            }
            if (isset($_POST[ 'sfwd-courses_custom_button_url' ])) {
                $data[ 'sfwd-courses_custom_button_url' ] = $_POST[ 'sfwd-courses_custom_button_url' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_price' ])) {
                $data[ 'sfwd-courses_course_price' ] = $_POST[ 'sfwd-courses_course_price' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_access_list' ])) {
                $data[ 'sfwd-courses_course_access_list' ] = $_POST[ 'sfwd-courses_course_access_list' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_lesson_orderby' ])) {
                $data[ 'sfwd-courses_course_lesson_orderby' ] = $_POST[ 'sfwd-courses_course_lesson_orderby' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_lesson_order' ])) {
                $data[ 'sfwd-courses_course_lesson_order' ] = $_POST[ 'sfwd-courses_course_lesson_order' ];
            }
            if (isset($_POST[ 'sfwd-courses_course_prerequisite' ])) {
                $data[ 'sfwd-courses_course_prerequisite' ] = $_POST[ 'sfwd-courses_course_prerequisite' ];
            }

            if (isset($_POST[ 'sfwd-courses_course_disable_lesson_progression' ])) {
                $data[ 'sfwd-courses_course_disable_lesson_progression' ] = $_POST[ 'sfwd-courses_course_disable_lesson_progression' ];
            }
            if (isset($_POST[ 'sfwd-courses_expire_access' ])) {
                $data[ 'sfwd-courses_expire_access' ] = $_POST[ 'sfwd-courses_expire_access' ];
            }
            if (isset($_POST[ 'sfwd-courses_expire_access_days' ])) {
                $data[ 'sfwd-courses_expire_access_days' ] = $_POST[ 'sfwd-courses_expire_access_days' ];
            }
            if (isset($_POST[ 'sfwd-courses_expire_access_delete_progress' ])) {
                $data[ 'sfwd-courses_expire_access_delete_progress' ] = $_POST[ 'sfwd-courses_expire_access_delete_progress' ];
            }
            if (isset($_POST[ 'sfwd-courses_certificate' ])) {
                $data[ 'sfwd-courses_certificate' ] = $_POST[ 'sfwd-courses_certificate' ];
            }

            //$wdm_course_data = serialize($data);
            update_post_meta($course_id, '_sfwd-courses', $data);
            update_post_meta($course_id, 'course_price_billing_p3', $_POST[ 'course_price_billing_p3' ]);
            update_post_meta($course_id, 'course_price_billing_t3', $_POST[ 'course_price_billing_t3' ]);
            //echo "12321";
            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_course_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?courseid='.$course_id;
            if (!isset($_POST[ 'courseid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            if ($wdm_flag == 1) {
                $_SESSION['wdm_error'] = $wdm_error;
        //	return;
            }
            wp_redirect($link);
            exit;
        }
    }

    public function wdm_course_list()
    {
        ob_start();
        global $wpdb;
        global $current_user;
        //echo "<pre>";print_r($current_user);echo "</pre>";
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

                include_once dirname(__FILE__).'/wdm_course_list.php';
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

                include_once dirname(__FILE__).'/wdm_course_list.php';
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }
}

new Wdm_Course();
