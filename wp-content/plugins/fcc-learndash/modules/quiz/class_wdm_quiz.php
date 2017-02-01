<?php

class Wdm_Quiz
{
    public function __construct()
    {
        include_once trailingslashit(dirname(dirname(__FILE__))).'includes/class-wdm-wusp-get-data.php';

        global $wdm_plugin_data;
        $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

        if ($get_data_from_db == 'available') {
            add_shortcode('wdm_quiz_creation', array($this, 'wdm_quiz_creation'));
            add_action('init', array($this, 'wdm_quiz_save'));
            add_shortcode('wdm_quiz_list', array($this, 'wdm_quiz_list'));
            add_action('before_delete_post', array($this, 'wdm_quiz_delete'));
            //add_filter('learndash_quiz_email_admin',array($this,'wdm_quiz_email'),10,2);
        }
    }

    public function wdm_quiz_creation()
    {
        //echo plugins_url( 'sfwd-lms' ); 
        //session_start();
        global $current_user;
        //echo "<pre>";print_R($current_user);echo "</pre>";
        ob_start();
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-course-style', plugins_url('css/style.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-quiz-script', plugins_url('js/wdm_quiz.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-quiz-script',
                    'wdm_quiz_script_object',
                    array(
                        'lesson_or_topic_string' => __('-- Select a Lesson or Topic --', 'fcc')
                    )
                );
                wp_enqueue_script('wdm-question-script', plugins_url('js/wdm_question.js', dirname(dirname(__FILE__))), array('jquery'));

                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_quiz_creation.php';
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))), array('jquery'));
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))), array('jquery'));
                wp_enqueue_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))), array('jquery'));
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
                wp_enqueue_style('wdm-course-style', plugins_url('css/style.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-select2-style', plugins_url('css/wdm_select2.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_script('wdm-quiz-script', plugins_url('js/wdm_quiz.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-quiz-script',
                    'wdm_quiz_script_object',
                    array(
                        'lesson_or_topic_string' => __('-- Select a Lesson or Topic --', 'fcc')
                    )
                );
                wp_enqueue_script('wdm-question-script', plugins_url('js/wdm_question.js', dirname(dirname(__FILE__))), array('jquery'));

                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include_once dirname(__FILE__).'/wdm_quiz_creation.php';
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))), array('jquery'));
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))), array('jquery'));
                wp_enqueue_script('wdm-validate-js', plugins_url('js/wdm_validate.js', dirname(dirname(__FILE__))), array('jquery'));
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

    public function wdm_quiz_save()
    {

        //echo "hhelo";exit;
        $wdm_flag = 0;
        $wdm_error = '';
        // echo "<pre>";
        // print_r($_POST);
        // echo "</pre>";
        // die();
        if (isset($_POST[ 'wdm_quiz_action' ])) {
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

        if (isset($_POST['order_number']) && !empty($_POST['order_number'])) {
            $order_number = $_POST['order_number'];
        } else {
            $order_number = 0;
        }
            //session_start();
            global $wpdb;
            //echo "<pre>";print_R($_POST);echo "</pre>";exit;	
            $term_relationship = $wpdb->prefix.'term_relationships';
            $wdm_title = $_POST[ 'title' ];
            $wdm_content = $_POST[ 'wdm_content' ];
            $post_status = get_option('wdm_fcc_post_status', 'draft');

            if (isset($_POST[ 'quizid' ])) {
                //echo $wdm_content;exit;

                $quiz_id = $_POST[ 'quizid' ];
                $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $quiz_id AND post_type like 'sfwd-quiz'";
                $author_id = $wpdb->get_var($sql);
                if ($author_id != get_current_user_id()) {
                    wp_die("cheating hu'h?");
                    exit;
                }
                $quiz_post = array(
                    'ID' => $quiz_id,
                    'post_title' => $wdm_title,
                    'post_content' => $wdm_content,
                    'post_status' => $post_status,
                    'post_author' => get_current_user_id(),
                    'menu_order'  => $order_number,
                );
                // Update the post into the database
                wp_update_post($quiz_post);

                update_post_meta($quiz_id, '_timeLimitCookie', $_POST['timeLimitCookie']);
                
            } else {
                $post_sql = "SELECT post_name FROM {$wpdb->prefix}posts WHERE post_name LIKE '".sanitize_title($wdm_title)."'";
                $post_name = $wpdb->get_var($post_sql);
                if ($post_name == '') {
                    $post_name = sanitize_title($wdm_title);
                } else {
                    $post_name .= '-'.time();
                }
                $quiz = array(
                    'post_title' => $wdm_title,
                    'post_status' => $post_status,
                    'post_type' => 'sfwd-quiz',
                    'post_content' => $wdm_content,
                    'post_author' => get_current_user_id(),
                    'post_name' => $post_name,
                    'menu_order'  => $order_number,
                );

//$is_visible = ($course->visible == 1 ? 'visible' : 'private');
//$sync_log .= "<br />Course Created: ".$course->fullname."<br /> <br />";
//generate a random unique sku for courses imported.
//$sku = "course_".mt_rand();
                $quiz_id = wp_insert_post($quiz);
                update_post_meta($quiz_id, '_timeLimitCookie', $_POST['timeLimitCookie']);
            }

            $sql = "DELETE FROM $term_relationship WHERE object_id = $quiz_id";
            $wpdb->query($sql);
//			if ( isset( $_POST[ 'category' ] ) && (count( $_POST[ 'category' ] ) > 0) ) {
//				foreach ( $_POST[ 'category' ] as $k => $v ) {
//					$category_data = array(
//						'object_id'			 => $course_id,
//						'term_taxonomy_id'	 => $v,
//					);
//					$wpdb->insert( $term_relationship, $category_data );
//				}
//			}
            if (isset($_POST[ 'tag' ]) && (count($_POST[ 'tag' ]) > 0)) {
                foreach ($_POST[ 'tag' ] as $k => $v) {
                    $category_data = array(
                        'object_id' => $quiz_id,
                        'term_taxonomy_id' => $v,
                    );
                    $wpdb->insert($term_relationship, $category_data);
                }
            }
            $data = array();
            if (isset($_POST[ 'sfwd-quiz_course' ])) {
                $data[ 'sfwd-quiz_course' ] = $_POST[ 'sfwd-quiz_course' ];
                update_post_meta($quiz_id, 'course_id', $_POST[ 'sfwd-quiz_course' ]);
            }
            if (isset($_POST[ 'sfwd-quiz_repeats' ])) {
                $data[ 'sfwd-quiz_repeats' ] = $_POST[ 'sfwd-quiz_repeats' ];
            }
            if (isset($_POST[ 'sfwd-quiz_threshold' ])) {
                $data[ 'sfwd-quiz_threshold' ] = $_POST[ 'sfwd-quiz_threshold' ];
            }
            if (isset($_POST[ 'sfwd-quiz_passingpercentage' ])) {
                $data[ 'sfwd-quiz_passingpercentage' ] = $_POST[ 'sfwd-quiz_passingpercentage' ];
            }
            if (isset($_POST[ 'sfwd-quiz_lesson' ])) {
                $data[ 'sfwd-quiz_lesson' ] = $_POST[ 'sfwd-quiz_lesson' ];
                update_post_meta($quiz_id, 'lesson_id', $_POST[ 'sfwd-quiz_lesson' ]);
            }
            if (isset($_POST[ 'sfwd-quiz_certificate' ])) {
                $data[ 'sfwd-quiz_certificate' ] = $_POST[ 'sfwd-quiz_certificate' ];
            }

            $toplist_data = array(
                'toplistDataAddPermissions' => (isset($_POST[ 'toplistDataAddPermissions' ]) ? $_POST[ 'toplistDataAddPermissions' ] : ''),
                'toplistDataSort' => (isset($_POST[ 'toplistDataSort' ]) ? $_POST[ 'toplistDataSort' ] : ''),
                'toplistDataAddMultiple' => (isset($_POST[ 'toplistDataAddMultiple' ]) ? $_POST[ 'toplistDataAddMultiple' ] : ''),
                'toplistDataAddBlock' => (isset($_POST[ 'toplistDataAddBlock' ]) ? $_POST[ 'toplistDataAddBlock' ] : ''),
                'toplistDataShowLimit' => (isset($_POST[ 'toplistDataShowLimit' ]) ? $_POST[ 'toplistDataShowLimit' ] : ''),
                'toplistDataShowIn' => (isset($_POST[ 'toplistDataShowIn' ]) ? $_POST[ 'toplistDataShowIn' ] : ''),
                'toplistDataCaptcha' => (isset($_POST[ 'toplistDataCaptcha' ]) ? $_POST[ 'toplistDataCaptcha' ] : ''),
                'toplistDataAddAutomatic' => (isset($_POST[ 'toplistDataAddAutomatic' ]) ? $_POST[ 'toplistDataAddAutomatic' ] : ''),
            );
            $toplist_data = serialize($toplist_data);
            //echo "<pre>";print_r($toplist_data);echo "</pre>";
            if (isset($_POST['resultGradeEnabled'])) {
                $resultText_temp = array();
                $result_data = $_POST['resultTextGrade'];
                foreach ($result_data['activ'] as $k => $v) {
                    if ($v == 1) {
                        $resultText_temp['text'][] = $result_data['text'][$k];
                        $resultText_temp['prozent'][] = $result_data['prozent'][$k];
                    }
                }
                $resultText = serialize($resultText_temp);
            } else {
                $resultText = (isset($_POST[ 'resultText' ]) ? $_POST[ 'resultText' ] : '');
            }

            $quiz_master = array(
                'name' => $wdm_title,
                'text' => (isset($_POST[ 'text' ]) ? $_POST[ 'text' ] : ''),
                'result_text' => $resultText,
                'result_grade_enabled' => (isset($_POST[ 'resultGradeEnabled' ]) ? $_POST[ 'resultGradeEnabled' ] : ''),
                'title_hidden' => (isset($_POST[ 'titleHidden' ]) ? $_POST[ 'titleHidden' ] : 0),
                'btn_restart_quiz_hidden' => (isset($_POST[ 'btnRestartQuizHidden' ]) ? $_POST[ 'btnRestartQuizHidden' ] : 0),
                'btn_view_question_hidden' => (isset($_POST[ 'btnViewQuestionHidden' ]) ? $_POST[ 'btnViewQuestionHidden' ] : 0),
                'question_random' => (isset($_POST[ 'questionRandom' ]) ? $_POST[ 'questionRandom' ] : 0),
                'answer_random' => (isset($_POST[ 'answerRandom' ]) ? $_POST[ 'answerRandom' ] : 0),
                'sort_categories' => (isset($_POST[ 'sortCategories' ]) ? $_POST[ 'sortCategories' ] : 0),
                'time_limit' => (isset($_POST[ 'timeLimit' ]) ? $_POST[ 'timeLimit' ] : 0),
                'statistics_on' => (isset($_POST[ 'statisticsOn' ]) ? $_POST[ 'statisticsOn' ] : 0),
                'statistics_ip_lock' => (isset($_POST[ 'statisticsIpLock' ]) ? $_POST[ 'statisticsIpLock' ] : 0),
                'quiz_run_once' => (isset($_POST[ 'quizRunOnce' ]) ? $_POST[ 'quizRunOnce' ] : 0),
                'quiz_run_once_type' => (isset($_POST[ 'quizRunOnceType' ]) ? $_POST[ 'quizRunOnceType' ] : 0),
                'quiz_run_once_cookie' => (isset($_POST[ 'quizRunOnceCookie' ]) ? $_POST[ 'quizRunOnceCookie' ] : 0),
                'numbered_answer' => (isset($_POST[ 'numberedAnswer' ]) ? $_POST[ 'numberedAnswer' ] : 0),
                'hide_answer_message_box' => (isset($_POST[ 'hideAnswerMessageBox' ]) ? $_POST[ 'hideAnswerMessageBox' ] : 0),
                'disabled_answer_mark' => (isset($_POST[ 'disabledAnswerMark' ]) ? $_POST[ 'disabledAnswerMark' ] : 0),
                'show_max_question' => (isset($_POST[ 'showMaxQuestion' ]) ? $_POST[ 'showMaxQuestion' ] : 0),
                'show_max_question_value' => (isset($_POST[ 'showMaxQuestionValue' ]) ? $_POST[ 'showMaxQuestionValue' ] : 0),
                'show_max_question_percent' => (isset($_POST[ 'showMaxQuestionPercent' ]) ? $_POST[ 'showMaxQuestionPercent' ] : 0),
                'toplist_activated' => (isset($_POST[ 'toplistActivated' ]) ? $_POST[ 'toplistActivated' ] : 0),
                'toplist_data' => $toplist_data,
                'show_average_result' => (isset($_POST[ 'showAverageResult' ]) ? $_POST[ 'showAverageResult' ] : 0),
                'prerequisite' => (isset($_POST[ 'prerequisite' ]) ? $_POST[ 'prerequisite' ] : 0),
                'quiz_modus' => (isset($_POST[ 'quizModus' ]) ? $_POST[ 'quizModus' ] : 0),
                'show_review_question' => (isset($_POST[ 'showReviewQuestion' ]) ? $_POST[ 'showReviewQuestion' ] : 0),
                'quiz_summary_hide' => (isset($_POST[ 'quizSummaryHide' ]) ? $_POST[ 'quizSummaryHide' ] : 0),
                'skip_question_disabled' => (isset($_POST[ 'skipQuestionDisabled' ]) ? $_POST[ 'skipQuestionDisabled' ] : 0),
                'email_notification' => (isset($_POST[ 'emailNotification' ]) ? $_POST[ 'emailNotification' ] : 0),
                'user_email_notification' => (isset($_POST[ 'userEmailNotification' ]) ? $_POST[ 'userEmailNotification' ] : 0),
                'show_category_score' => (isset($_POST[ 'showCategoryScore' ]) ? $_POST[ 'showCategoryScore' ] : 0),
                'hide_result_correct_question' => (isset($_POST[ 'hideResultCorrectQuestion' ]) ? $_POST[ 'hideResultCorrectQuestion' ] : 0),
                'hide_result_quiz_time' => (isset($_POST[ 'hideResultQuizTime' ]) ? $_POST[ 'hideResultQuizTime' ] : 0),
                'hide_result_points' => (isset($_POST[ 'hideResultPoints' ]) ? $_POST[ 'hideResultPoints' ] : 0),
                'autostart' => (isset($_POST[ 'autostart' ]) ? $_POST[ 'autostart' ] : 0),
                'forcing_question_solve' => (isset($_POST[ 'forcingQuestionSolve' ]) ? $_POST[ 'forcingQuestionSolve' ] : 0),
                'hide_question_position_overview' => (isset($_POST[ 'hideQuestionPositionOverview' ]) ? $_POST[ 'hideQuestionPositionOverview' ] : 0),
                'hide_question_numbering' => (isset($_POST[ 'hideQuestionNumbering' ]) ? $_POST[ 'hideQuestionNumbering' ] : 0),
                'form_activated' => (isset($_POST[ 'formActivated' ]) ? $_POST[ 'formActivated' ] : 0),
                'form_show_position' => (isset($_POST[ 'formShowPosition' ]) ? $_POST[ 'formShowPosition' ] : 0),
                'start_only_registered_user' => (isset($_POST[ 'startOnlyRegisteredUser' ]) ? $_POST[ 'startOnlyRegisteredUser' ] : 0),
                'questions_per_page' => (isset($_POST[ 'questionsPerPage' ]) ? $_POST[ 'questionsPerPage' ] : 0),
                'show_category' => (isset($_POST[ 'showCategory' ]) ? $_POST[ 'showCategory' ] : 0),
            );

            if (isset($_POST[ 'sfwd-quiz_quiz_pro' ])) {
                $data[ 'sfwd-quiz_quiz_pro' ] = $_POST[ 'sfwd-quiz_quiz_pro' ];

                $wpdb->update($wpdb->prefix.'wp_pro_quiz_master', $quiz_master, array('id' => $_POST[ 'sfwd-quiz_quiz_pro' ]));

                $quiz_master_id = $_POST[ 'sfwd-quiz_quiz_pro' ];
            } else {
                $wpdb->insert($wpdb->prefix.'wp_pro_quiz_master', $quiz_master);
                $data[ 'sfwd-quiz_quiz_pro' ] = $wpdb->insert_id;
                $quiz_master_id = $wpdb->insert_id;
            }
            $wdm_custom_field_data = array();
            $i = 0;
            //echo "<pre>";print_R($_POST['form']);echo "</pre>";
            $sql = "DELETE FROM {$wpdb->prefix}wp_pro_quiz_form WHERE quiz_id = ".$quiz_master_id;
            $results = $wpdb->query($sql);
            if (isset($_POST['formActivated'])) {
                if (isset($_POST[ 'form' ])) {
                    foreach ($_POST[ 'form' ] as $k => $v) {
                        if ($k > 4) {
                            foreach ($v as $key => $value) {
                                if ($key == 'form_id' && $value != 0) {
                                    $wdm_custom_field_data[ $key ] = $value;
                                } elseif ($key == 'form_delete') {
                                    $wdm_custom_field_data[ 'quiz_id' ] = $quiz_master_id;
                                //echo "<pre>";print_r($wdm_custom_field_data);echo "</pre>";exit;
                                $wdm_custom_field_data[ 'sort' ] = $i;
                                    ++$i;
                                    $wpdb->insert($wpdb->prefix.'wp_pro_quiz_form', $wdm_custom_field_data);
                                    $wdm_custom_field_data = array();
                                } elseif ($key == 'data') {
                                    if ($value != '') {
                                        $items = explode("\n", $value);
                                    //echo "<pre>";print_R($items);echo "</pre>";exit;
                                    $f[ 'data' ] = array();

                                        foreach ($items as $item) {
                                            $item = trim($item);

                                            if (!empty($item)) {
                                                $f[ 'data' ][] = $item;
                                            }
                                        }

                                        $form_data_new = '["'.implode('","', $f[ 'data' ]).'"]';
                                    //update_option('wdm_temp',$form_data_new);
                                    //echo $form_data_new;
                                    $wdm_custom_field_data[ 'data' ] = $form_data_new;
                                    } else {
                                        $form_data_new = null;
                                    }
                                } elseif ($key != 'form_id') {
                                    $wdm_custom_field_data[ $key ] = $value;
                                }
                            }
                        }
                    }
                }
            }
            if (isset($_POST[ 'prerequisite' ])) {
                if (isset($_POST[ 'prerequisiteList' ])) {
                    if (count($_POST[ 'prerequisiteList' ]) > 0) {
                        $sql = "DELETE FROM {$wpdb->prefix}wp_pro_quiz_prerequisite WHERE prerequisite_quiz_id = ".$quiz_master_id;
                        $results = $wpdb->query($sql);
                        $prerequisite_data = array();
                        foreach ($_POST[ 'prerequisiteList' ] as $k => $v) {
                            $prerequisite_data[ 'prerequisite_quiz_id' ] = $quiz_master_id;
                            $quizMeta = get_post_meta($v, '_sfwd-quiz', true);
                            // $prerequisite_data[ 'quiz_id' ]				 = $v;
                            $prerequisite_data[ 'quiz_id' ] = $quizMeta['sfwd-quiz_quiz_pro'];
                            $wpdb->insert($wpdb->prefix.'wp_pro_quiz_prerequisite', $prerequisite_data);
                            $prerequisite_data = array();
                        }
                    }
                }
            }
            update_post_meta($quiz_id, '_sfwd-quiz', $data);
            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_quiz_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?quizid='.$quiz_id;
            if (!isset($_POST[ 'quizid' ])) {
                $_SESSION['update'] = 1;
            } else {
                $_SESSION['update'] = 2;
            }

            wp_redirect($link);
            exit;
        }
    }

    public function wdm_quiz_list()
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
                    )
                )
            );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))),  array('jquery'));

                include_once dirname(__FILE__).'/wdm_quiz_list.php';
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

                include_once dirname(__FILE__).'/wdm_quiz_list.php';
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_quiz_email($email, $quiz)
    {
        $pro_quiz_id = $quiz->getid();
    //$quiz_id = get_ld_quiz_id($pro_quiz_id);
    $ld_pro = new LD_QuizPro();
        $quiz_id = $ld_pro->get_ld_quiz_id($pro_quiz_id);
    //echo $quiz_id;
    $quiz_post = get_post($quiz_id);
        $author_id = $quiz_post->post_author;
        $user = get_user_by('id', $author_id);
        $author_email = $user->user_email;
    //echo "<pre>";print_R($author_email);echo "</pre>";exit;
    //exit;
    if ($email['email'] != '') {
        $email['email'] .= ','.$author_email;
    } else {
        $email['email'] = $author_email;
    }
//	$email['email'] = 'jignashu.solanki@wisdmlabs.com';
    return $email;
    }
    public function wdm_quiz_delete($postid)
    {
        //echo $postid;exit;
        // We check if the global post type isn't ours and just return
        global $post_type;
        global $wpdb;
        if ($post_type != 'sfwd-quiz') {
            return;
        }

        $sql = 'DELETE FROM '.$wpdb->prefix."usermeta WHERE meta_key like 'wdm_question_id%' AND meta_value like '$postid'";
        $wpdb->query($sql);

        // My custom stuff for deleting my custom post type here
    }
}

new Wdm_Quiz();
