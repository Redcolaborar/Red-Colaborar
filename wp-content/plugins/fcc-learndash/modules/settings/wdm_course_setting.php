<?php
    // echo "hello..........";

if (!class_exists('Wdm_Course_Setting')) {
    class Wdm_Course_Setting
    {
        public function __construct()
        {
            include_once trailingslashit(dirname(dirname(__FILE__))).'includes/class-wdm-wusp-get-data.php';

            global $wdm_plugin_data;
            $get_data_from_db = WuspGetDataFCC\WdmWuspGetData::getDataFromDb($wdm_plugin_data);

            if ($get_data_from_db == 'available') {
                //Create custom tab on learndash settings page
                add_filter('learndash_admin_tabs', array($this, 'wdm_learndash_admin_tabs'), 1, 1);
                add_filter('learndash_admin_tabs_on_page', array($this, 'wdm_learndash_admin_tabs_on_page'), 3, 3);
                add_action('admin_menu', array($this, 'wdm_course_setting_menu'), 1);
            }
        }

        /**
         * Create Course Setting tab under Learndash Settings. 
         *
         * @param array $admin_tabs Contains admin tabs of Learndash
         *
         * @return array Contains all admin tabs of Learndash
         *
         * @author Foram Rambhiya
         */
        public function wdm_learndash_admin_tabs($admin_tabs)
        {
            $admin_tabs['wdm_course_access_tab'] = array(
                                        'link' => 'admin.php?page=wdm_course_setting-sfwd_lms-settings',
                                        'name' => __('Course Creation Setting', 'fcc'),
                                        'id' => 'admin_page_wdm_course_setting-sfwd_lms-settings',
                                        'menu_link' => 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses',
                                        );

            return $admin_tabs;
        }

        /**
         * Adds tab on the page.
         *
         * @param Array  $admin_tabs_on_page Array of tabs on pages
         * @param array  $admin_tabs         Array of all learndash tabs
         * @param string $current_page_id    Current Page Id
         *
         * @return array Array of tabs on pages
         *
         * @author Foram Rambhiya 
         */
        public function wdm_learndash_admin_tabs_on_page($admin_tabs_on_page, $admin_tabs, $current_page_id)
        {

            // var_dump($current_page_id);

            if (empty($admin_tabs_on_page['admin_page_wdm_course_setting-sfwd_lms-settings']) || !count($admin_tabs_on_page['admin_page_wdm_course_setting-sfwd_lms-settings'])) {
                $admin_tabs_on_page['admin_page_wdm_course_setting-sfwd_lms-settings'] = array();
            }

            //add custom admin tab in custom tabs page
            $admin_tabs_on_page['admin_page_wdm_course_setting-sfwd_lms-settings'] = array_merge($admin_tabs_on_page['sfwd-courses_page_sfwd-lms_sfwd_lms_post_type_sfwd-courses'], (array) $admin_tabs_on_page['admin_page_wdm_course_setting-sfwd_lms-settings']);

            foreach ($admin_tabs as $key => $value) {
                if ($value['id'] == $current_page_id && $value['menu_link'] == 'edit.php?post_type=sfwd-courses&page=sfwd-lms_sfwd_lms.php_post_type_sfwd-courses') {
                    $admin_tabs_on_page[$current_page_id][] = 'wdm_course_access_tab';

                    return $admin_tabs_on_page;
                }
            }

            return $admin_tabs_on_page;
        }

        /**
         * Adds page for Course Setting tab.
         */
        public function wdm_course_setting_menu()
        {

            //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ); 
            add_submenu_page('learndash-lms-non-existant', 'wdm_course_setting-sfwd_lms-settings', 'wdm_course_setting-sfwd_lms-settings', 'manage_options', 'wdm_course_setting-sfwd_lms-settings', array($this, 'wdm_course_setting_menu_callback'));
        }

        /**
         * Allows admin to set post status for front-end course creation.
         */
        public function wdm_course_setting_menu_callback()
        {
            $prev_set = get_option('wdm_fcc_post_status', true);

            // echo $prev_set;

            if (isset($_POST['submit']) && $_POST['submit'] == 'Save Settings') {
                // echo $_POST['course_setting'];
                update_option('wdm_fcc_post_status', $_POST['course_setting']);
                $prev_set = $_POST['course_setting'];
            }

            ?>
<form name="frm_course_setting" method="POST">	
	<table>
		<tr>
			<th><h4><?php echo __('Select Course Publish Status :', 'fcc');
            ?> </h4></th>
	<td>
			<input type="radio" name="course_setting" value="publish" <?php echo ($prev_set == 'publish') ? 'checked' : '' ?> ><?php echo __('Publish', 'fcc');
            ?>
			<input type="radio" name="course_setting" value="draft" <?php echo ($prev_set == 'draft') ? 'checked' : '' ?> ><?php echo __('Draft', 'fcc');
            ?>
			</td>
			</tr>
			<tr></tr>
			<tr></tr>
			<tr></tr>
			<tr>
				<td>
			<input type="submit" name="submit" value="<?php echo __('Save Settings', 'fcc');
            ?>" class="button-primary">
			</td>
			</tr>
			</table>
			</form>
			<?php

        }
    }
}
new Wdm_Course_Setting();
