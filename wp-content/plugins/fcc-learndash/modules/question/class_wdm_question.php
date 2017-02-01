<?php

class Wdm_Question
{
    public function __construct()
    {
        include_once trailingslashit(dirname(dirname(__FILE__))).'includes/class-wdm-wusp-get-data.php';

        global $wdm_plugin_data;
        $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

        if ($get_data_from_db == 'available') { //If License

            add_shortcode('wdm_question_creation', array($this, 'wdm_question_creation'));
            add_action('init', array($this, 'wdm_question_save'));
            add_shortcode('wdm_question_list', array($this, 'wdm_question_list'));
            add_action('wp_ajax_wdm_question_category', array($this, 'wdm_question_category'));
        }
    }

    public function wdm_question_category()
    {
        $func = isset($_POST[ 'func' ]) ? $_POST[ 'func' ] : '';
        $data = isset($_POST[ 'data' ]) ? $_POST[ 'data' ] : null;

        $categoryMapper = new WpProQuiz_Model_CategoryMapper();
        //echo "<pre>";print_R($data);echo "</pre>";
        $category = new WpProQuiz_Model_Category($data);

        $categoryMapper->save($category);
        $user_category = get_user_meta(get_current_user_id(), 'wdm_question_category_ids', true);
        if ($user_category == '') {
            $temp = array();
        } else {
            $temp = explode(',', $user_category);
        }
        $temp[] = $category->getCategoryId();
        $user_category = implode(',', $temp);
        update_user_meta(get_current_user_id(), 'wdm_question_category_ids', $user_category);
        echo json_encode(array('categoryId' => $category->getCategoryId(),
            'categoryName' => $category->getCategoryName(),
        ));
        die();
    }

    public function wdm_question_creation()
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
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                    //wp_enqueue_script( 'wdm-quiz-script', plugins_url( 'js/wdm_quiz.js', dirname( dirname( __FILE__ ) ) ) );
                    wp_enqueue_script('wdm-question-script', plugins_url('js/wdm_question.js', dirname(dirname(__FILE__))),  array('jquery'));
                $data = array(
                        'admin_url' => admin_url('admin-ajax.php'),
                    );
                wp_localize_script('wdm-quiz-script', 'wdm_topic_data', $data);
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include dirname(__FILE__).'/wdm_question_creation.php';
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))),  array('jquery'));
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
                wp_enqueue_style('wdm-course-style', plugins_url('css/wdm_course.css', dirname(dirname(__FILE__))));
                wp_enqueue_style('wdm-course-style', plugins_url('css/style.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-accordion-script', plugins_url('js/jquery-ui.js', dirname(dirname(__FILE__))),  array('jquery'));
                    //wp_enqueue_script( 'wdm-quiz-script', plugins_url( 'js/wdm_quiz.js', dirname( dirname( __FILE__ ) ) ) );
                    wp_enqueue_script('wdm-question-script', plugins_url('js/wdm_question.js', dirname(dirname(__FILE__))),  array('jquery'));
                $data = array(
                        'admin_url' => admin_url('admin-ajax.php'),
                    );
                wp_localize_script('wdm-quiz-script', 'wdm_topic_data', $data);
                wp_enqueue_script('wdm-select2-js', plugins_url('js/wdm_select2.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_enqueue_style('wdm-accordion-style', plugins_url('css/jquery-ui.css', dirname(dirname(__FILE__))));
                include dirname(__FILE__).'/wdm_question_creation.php';
                wp_enqueue_script('wdm-custom-js', plugins_url('js/wdm_custom.js', dirname(dirname(__FILE__))),  array('jquery'));
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }

    public function wdm_question_save($param)
    {
        //echo "<pre>";print_R($_POST);echo "</pre>";exit;
        global $wpdb;
        if (isset($_POST[ 'wdm_question_action' ]) && isset($_POST[ 'quiz_id' ])) {
            //session_start();
            // echo '<pre>';
            // print_r($_POST);
            // echo '</pre>';
            // die();
            $quiz_id = $_POST[ 'quiz_id' ];
            $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $quiz_id AND post_type like 'sfwd-quiz'";
            $author_id = $wpdb->get_var($sql);
            if ($author_id != get_current_user_id()) {
                wp_die("cheating hu'h?");
                exit;
            }
            $pro_data = maybe_unserialize(get_post_meta($quiz_id, '_sfwd-quiz', true));
            $pro_quiz_id = $pro_data[ 'sfwd-quiz_quiz_pro' ];

            $sql = "SELECT post_author FROM {$wpdb->prefix}posts WHERE ID = $quiz_id";
            $author_id = $wpdb->get_var($sql);
            if ($author_id != get_current_user_id()) {
                wp_die("cheating hu'h?");
                exit;
            }
            $title = (isset($_POST[ 'title' ]) ? $_POST[ 'title' ] : '');
            $answerPointsActivated = (isset($_POST[ 'answerPointsActivated' ]) ? $_POST[ 'answerPointsActivated' ] : '');
            $showPointsInBox = (isset($_POST[ 'showPointsInBox' ]) ? $_POST[ 'showPointsInBox' ] : '');
            $question = stripslashes((isset($_POST[ 'question' ]) ? $_POST[ 'question' ] : ''));
            $correctMsg = stripslashes((isset($_POST[ 'correctMsg' ]) ? $_POST[ 'correctMsg' ] : ''));
            $incorrectMsg = stripslashes((isset($_POST[ 'incorrectMsg' ]) ? $_POST[ 'incorrectMsg' ] : ''));
            $tipEnabled = (isset($_POST[ 'tipEnabled' ]) ? $_POST[ 'tipEnabled' ] : '');
            $tipMsg = stripslashes((isset($_POST[ 'tipMsg' ]) ? $_POST[ 'tipMsg' ] : ''));
            $correctSameText = (isset($_POST[ 'correctSameText' ]) ? $_POST[ 'correctSameText' ] : '');

            $matrixSortAnswerCriteriaWidth = (isset($_POST[ 'matrixSortAnswerCriteriaWidth' ]) ? $_POST[ 'matrixSortAnswerCriteriaWidth' ] : '');
            $answerPointsDiffModusActivated = (isset($_POST[ 'answerPointsDiffModusActivated' ]) ? $_POST[ 'answerPointsDiffModusActivated' ] : '');
            $disableCorrect = (isset($_POST[ 'disableCorrect' ]) ? $_POST[ 'disableCorrect' ] : '');

            $answerType = (isset($_POST[ 'answerType' ]) ? $_POST[ 'answerType' ] : '');
            $answerData = $_POST[ 'answerData' ];
            $points = 0;
            $maxPoints = (isset($_POST[ 'points' ]) ? $_POST[ 'points' ] : 0);
            if ($answerType == 'cloze_answer' && isset($answerData[ 'cloze' ])) {
                preg_match_all('#\{(.*?)(?:\|(\d+))?(?:[\s]+)?\}#im', $answerData[ 'cloze' ][ 'answer' ], $matches);

                $points = 0;
                $maxPoints = 0;

                foreach ($matches[ 2 ] as $match) {
                    if (empty($match)) {
                        $match = 1;
                    }

                    $points += $match;
                    $maxPoints = max($maxPoints, $match);
                }
                $answerData[ 'cloze' ][ 'answer' ] = stripslashes($answerData[ 'cloze' ][ 'answer' ]);
                $answerData = array(new WpProQuiz_Model_AnswerTypes($answerData[ 'cloze' ]));
            } elseif ($answerType == 'assessment_answer' && isset($answerData[ 'assessment' ])) {
                preg_match_all('#\{(.*?)\}#im', $answerData[ 'assessment' ][ 'answer' ], $matches);

                $points = 0;
                $maxPoints = 0;

                foreach ($matches[ 1 ] as $match) {
                    preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);

                    $points += count($ms[ 1 ]);
                    $maxPoints = max($maxPoints, count($ms[ 1 ]));
                }
                $answerData[ 'assessment' ][ 'answer' ] = stripslashes($answerData[ 'assessment' ][ 'answer' ]);
                $answerData = array(new WpProQuiz_Model_AnswerTypes($answerData[ 'assessment' ]));
            } elseif ($answerType == 'assessment_answer' && isset($answerData[ 'assessment' ])) {
                preg_match_all('#\{(.*?)\}#im', $answerData[ 'assessment' ][ 'answer' ], $matches);

                $points = 0;
                $maxPoints = 0;

                foreach ($matches[ 1 ] as $match) {
                    preg_match_all('#\[([^\|\]]+)(?:\|(\d+))?\]#im', $match, $ms);

                    $points += count($ms[ 1 ]);
                    $maxPoints = max($maxPoints, count($ms[ 1 ]));
                }

                $answerData = array(new WpProQuiz_Model_AnswerTypes($answerData[ 'assessment' ]));
            } elseif ($answerType == 'essay' && isset($answerData[ 'essay' ])) {
                $answerDataTemp = new WpProQuiz_Model_AnswerTypes($answerData['essay']);
                $answerDataTemp->setPoints( $_POST['points'] );
                $answerDataTemp->setGraded( true );
                $answerDataTemp->setGradedType( $_POST['answerData']['essay']['type'] );
                $answerDataTemp->setGradingProgression( $_POST['answerData']['essay']['progression'] );
                $points = $_POST['points'];
                $answerData = array($answerDataTemp);
            }

            else {
                unset($answerData[ 'cloze' ]);
                unset($answerData[ 'assessment' ]);

                if (isset($answerData[ 'none' ])) {
                    unset($answerData[ 'none' ]);
                }

                $answerData_temp = array();

                foreach ($answerData as $k => $v) {
                    if (trim($v[ 'answer' ]) == '') {
                        if ($answerType != 'matrix_sort_answer') {
                            continue;
                        } else {
                            if (trim($v[ 'sort_string' ]) == '') {
                                continue;
                            }
                        }
                    }
                    $v[ 'answer' ] = stripslashes($v[ 'answer' ]);
                    $answerType_temp = new WpProQuiz_Model_AnswerTypes($v);
                    $points += $answerType_temp->getPoints();

                    $maxPoints = max($maxPoints, $answerType_temp->getPoints());

                    $answerData_temp[] = $answerType_temp;
                }
                $answerData = $answerData_temp;
            }

            if ($answerType === 'assessment_answer') {
                $answerPointsActivated = 1;
            }
            if (isset($_POST[ 'answerPointsActivated' ])) {
                if (isset($_POST[ 'answerPointsDiffModusActivated' ])) {
                    $points = $maxPoints;
                } else {
                    $points = $points;
                }
            } else {
                $points = $maxPoints;
            }
            // echo "<pre>";
            // print_r($answerData);
            // echo "</pre>";
            // die();
            //echo serialize($answerData);
            $data = array(
                'quiz_id' => $pro_quiz_id,
                'online' => 1,
                'sort' => 1,
                'title' => $title,
                'points' => $points,
                'question' => $question,
                'correct_msg' => $correctMsg,
                'incorrect_msg' => $incorrectMsg,
                'correct_same_text' => $correctSameText,
                'tip_enabled' => $tipEnabled,
                'tip_msg' => $tipMsg,
                'answer_type' => $answerType,
                'show_points_in_box' => $showPointsInBox,
                'answer_points_activated' => $answerPointsActivated,
                'answer_data' => serialize($answerData),
                'category_id' => ((isset($_POST[ 'category' ]) && $_POST[ 'category' ] != -1 && $_POST[ 'category' ] != 0) ? $_POST[ 'category' ] : 0),
                'answer_points_diff_modus_activated' => $answerPointsDiffModusActivated,
                'disable_correct' => $disableCorrect,
                'matrix_sort_answer_criteria_width' => $matrixSortAnswerCriteriaWidth,
            );
            if (isset($_POST[ 'questionid' ])) {
                $question_id = $_POST[ 'questionid' ];
                $results = $wpdb->update($wpdb->prefix.'wp_pro_quiz_question', $data, array('id' => $question_id));
            } else {
                $results = $wpdb->insert($wpdb->prefix.'wp_pro_quiz_question', $data);
                $question_id = $wpdb->insert_id;
            }
            update_user_meta(get_current_user_id(), 'wdm_question_id_'.$question_id, $quiz_id);
            $table = $wpdb->prefix.'posts';
            $sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_question_creation]%' AND post_status like 'publish'";
            $course_result = $wpdb->get_var($sql);
            $link = get_permalink($course_result);
            $link .= '?questionid='.$question_id;
            if (!isset($_POST[ 'questionid' ])) {
                $_SESSION[ 'update' ] = 1;
            } else {
                $_SESSION[ 'update' ] = 2;
            }

            wp_redirect($link);
            exit;
        }
        //$temp = new WpProQuiz_Model_AnswerTypes($_POST['answerData'][0]);
        //$temp->setAnswer = 'asdasd';
        //$temp->setHtml = "sadasd";
        //echo "<pre>";print_R($temp);echo "</pre>";
    }

    public function wdm_question_list()
    {
        ob_start();
        global $wpdb;
        global $current_user;
        $table = $wpdb->prefix.'usermeta';
        if (is_user_logged_in()) {
            if (is_super_admin(get_current_user_id())) {
                wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-datatable-script', 'wdm_datatable_object',
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn' => __('Previous', 'fcc'),
                'wdm_next_btn' => __('Next', 'fcc'),
                'wdm_search_bar' => __('Search', 'fcc'),
                'wdm_info_empty' => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table' => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching' => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf(__('(filtered from %s total entries)', 'fcc'), '_MAX_'),
            )
        );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))),  array('jquery'));

                include_once dirname(__FILE__).'/wdm_question_list.php';
            } elseif (isset($current_user->roles) && (in_array('administrator', $current_user->roles) || in_array('wdm_course_author', $current_user->roles))) {
                wp_enqueue_style('wdm-datatable-style', plugins_url('css/datatable.css', dirname(dirname(__FILE__))));
                wp_enqueue_script('wdm-datatable-script', plugins_url('js/datatable.js', dirname(dirname(__FILE__))),  array('jquery'));
                wp_localize_script('wdm-datatable-script', 'wdm_datatable_object',
            array(
                'wdm_no_data_string' => __('No data available in table', 'fcc'),
                'wdm_previous_btn' => __('Previous', 'fcc'),
                'wdm_next_btn' => __('Next', 'fcc'),
                'wdm_search_bar' => __('Search', 'fcc'),
                'wdm_info_empty' => __('Showing 0 to 0 of 0 entries', 'fcc'),
                'showing__start__to__end__of__total__entries' => sprintf(
                   __('Showing %s to %s of %s entries', 'fcc'),
                   '_START_',
                   ' _END_',
                   '_TOTAL_'
               ),
                'showing_length_of_table' => sprintf(
                    __('Show %s entries', 'fcc'),
                    '_MENU_'
                ),
                'wdm_no_matching' => __('No matching records found', 'fcc'),
                'wdm_filtered_from' => sprintf(__('(filtered from %s total entries)', 'fcc'), '_MAX_'),
            )
        );
                wp_enqueue_script('wdm-datatable-column-script', plugins_url('js/datatable-column.js', dirname(dirname(__FILE__))),  array('jquery'));

                include_once dirname(__FILE__).'/wdm_question_list.php';
            } else {
                echo '<h3>'.__('You do not have sufficient permissions to view this page.', 'fcc').'</h3>';
            }
        } else {
            echo '<h3>'.__('Please Login to view this page.', 'fcc').'</h3>';
        }

        return ob_get_clean();
    }
}

new Wdm_Question();
