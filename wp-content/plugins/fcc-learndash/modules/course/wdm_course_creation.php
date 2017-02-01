<?php
global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/';
if(isset($_GET['redirect'])){
	$back_url = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
}
if ( isset( $_GET[ 'courseid' ] ) ) {
	if(!is_numeric( $_GET[ 'courseid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table	 = $wpdb->posts;
	$id		 = $_GET[ 'courseid' ];

	$sql	 = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-courses' AND post_author = " . get_current_user_id();
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) == 0 ) {
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
}
$title											 = "";
$content										 = "";
$featured_image									 = "";
$sfwd_courses_course_materials					 = "";
$sfwd_courses_course_price_type					 = "";
$sfwd_courses_custom_button_url					 = "";
$sfwd_courses_course_price						 = "";
$course_price_billing_p3						 = "";
$course_price_billing_t3						 = "";
$sfwd_courses_course_access_list				 = "";
$sfwd_courses_course_lesson_orderby				 = "";
$sfwd_courses_course_lesson_order				 = "";
$sfwd_courses_course_prerequisite				 = "";
$sfwd_courses_course_disable_lesson_progression	 = "";
$sfwd_courses_expire_access						 = "";
$sfwd_courses_expire_access_days				 = "";
$sfwd_courses_expire_access_delete_progress		 = "";
$menu_order										 = 0;
$cerficates = array();
$preview_url = '';
$sfwd_courses_certificate = "";
$table	 = $wpdb->prefix . "posts";
$sql	 = "SELECT ID FROM $table WHERE post_author = " . get_current_user_id() . " AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";
if ( isset( $_GET[ 'courseid' ] ) ) {
	$sql .= " AND ID != " . $_GET[ 'courseid' ];
}
$results	 = $wpdb->get_results( $sql );
$course_list = array();
if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$course_list[] = $v->ID;
	}
}
$sql	 = "SELECT ID FROM $table WHERE post_type like 'sfwd-certificates' AND post_status IN ('publish','draft')";
$results	 = $wpdb->get_results( $sql );
if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$cerficates[] = $v->ID;
	}
}
$term_table	 = $wpdb->prefix . "terms";
$taxonomy	 = $wpdb->prefix . "term_taxonomy";
$sql		 = "SELECT b.term_taxonomy_id, a.name FROM $term_table a JOIN $taxonomy b ON a.term_id=b.term_id WHERE taxonomy like 'category'";
$results	 = $wpdb->get_results( $sql );
$category	 = array();

if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$category[ $v->term_taxonomy_id ] = $v->name;
	}
}
$sql		 = "SELECT b.term_taxonomy_id, a.name FROM $term_table a JOIN $taxonomy b ON a.term_id=b.term_id WHERE taxonomy like 'post_tag'";
$results	 = $wpdb->get_results( $sql );
$tag	 = array();

if ( count( $results ) > 0 ) {
	foreach ( $results as $k => $v ) {
		$tag[ $v->term_taxonomy_id ] = $v->name;
	}
}
//$categories = get_terms( 'category', 'orderby=count&hide_empty=0' );

$term_relationship	 = $wpdb->prefix . "term_relationships";
$selected_category	 = array();
if ( isset( $_GET[ 'courseid' ] ) ) {
	$sql	 = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = " . $_GET[ 'courseid' ];
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) > 0 ) {
		foreach ( $results as $k => $v ) {
			$selected_category[] = $v->term_taxonomy_id;
		}
	}
}

$selected_tag	 = array();
if ( isset( $_GET[ 'courseid' ] ) ) {
	$sql	 = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = " . $_GET[ 'courseid' ];
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) > 0 ) {
		foreach ( $results as $k => $v ) {
			$selected_tag[] = $v->term_taxonomy_id;
		}
	}
}




if ( isset( $_GET[ 'courseid' ] ) ) {
	$id												 = $_GET[ 'courseid' ];
	$title											 = get_the_title( $id );
	$content_post									 = get_post( $id );
	$content										 = $content_post->post_content;
	$content										 = apply_filters( 'the_content', $content );
	$menu_order										 = $content_post->menu_order;
	$course_meta									 = maybe_unserialize( get_post_meta( $id, '_sfwd-courses' ) );
	//echo "<pre>";print_R($course_meta);echo "</pre>";
	$course_price_billing_p3						 = get_post_meta( $id, 'course_price_billing_p3', true );
	$course_price_billing_t3						 = get_post_meta( $id, 'course_price_billing_t3', true );
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_materials' ] ) )
		$sfwd_courses_course_materials					 = $course_meta[ 0 ][ 'sfwd-courses_course_materials' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_price_type' ] ) )
		$sfwd_courses_course_price_type					 = $course_meta[ 0 ][ 'sfwd-courses_course_price_type' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_custom_button_url' ] ) )
		$sfwd_courses_custom_button_url					 = $course_meta[ 0 ][ 'sfwd-courses_custom_button_url' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_price' ] ) )
		$sfwd_courses_course_price						 = $course_meta[ 0 ][ 'sfwd-courses_course_price' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_access_list' ] ) )
		$sfwd_courses_course_access_list				 = $course_meta[ 0 ][ 'sfwd-courses_course_access_list' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_orderby' ] ) )
		$sfwd_courses_course_lesson_orderby				 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_orderby' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_lesson_order' ] ) )
		$sfwd_courses_course_lesson_order				 = $course_meta[ 0 ][ 'sfwd-courses_course_lesson_order' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite' ] ) )
		$sfwd_courses_course_prerequisite				 = $course_meta[ 0 ][ 'sfwd-courses_course_prerequisite' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_course_disable_lesson_progression' ] ) )
		$sfwd_courses_course_disable_lesson_progression	 = $course_meta[ 0 ][ 'sfwd-courses_course_disable_lesson_progression' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access' ] ) )
		$sfwd_courses_expire_access						 = $course_meta[ 0 ][ 'sfwd-courses_expire_access' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access_days' ] ) )
		$sfwd_courses_expire_access_days				 = $course_meta[ 0 ][ 'sfwd-courses_expire_access_days' ];
	if ( isset( $course_meta[ 0 ][ 'sfwd-courses_expire_access_delete_progress' ] ) )
		$sfwd_courses_expire_access_delete_progress		 = $course_meta[ 0 ][ 'sfwd-courses_expire_access_delete_progress' ];
	if(isset($course_meta[0]['sfwd-courses_certificate']))
		$sfwd_courses_certificate = $course_meta[0]['sfwd-courses_certificate'];
	$preview_url = add_query_arg(array('wdm_preview'=>1),get_permalink($id));
}
?>
<?php //session_start();
//echo "<prE>";print_r($_SESSION);echo "</pre>";  ?>
<?php if ( isset( $_SESSION['update'] ) ) { ?>
	<?php if ( $_SESSION['update'] == 2 ) { ?>
		<div class="wdm-update-message"><?php echo __('Course Updated Successfully.','fcc'); ?></div>
	<?php } else if ( $_SESSION['update'] == 1 ) { ?>
		<div class="wdm-update-message"><?php echo __('Course Added Successfully.','fcc'); ?></div>

	<?php }
	unset($_SESSION['update']);
}
?>
<?php if (defined('WDM_ERROR')) { ?>

		<div class="wdm-error-message"><?php echo WDM_ERROR; ?>
		</div>

	

<?php
	
}
if(isset($_SESSION['wdm_error'])){
			if($_SESSION['wdm_error'] != '') {  ?>
				<div class="wdm-error-message"><?php echo $_SESSION['wdm_error']; ?>
		</div>
			<?php }
 unset($_SESSION['wdm_error']);
			} ?>
		<input type="button" value="<?php echo __('Back','fcc'); ?>" onclick="location.href = '<?php echo $back_url; ?>';" style="float: right;">
			<?php if($preview_url != ''){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo __('Preview','fcc'); ?>" style="float:right;margin-right: 2%;"onclick="window.open('<?php echo $preview_url; ?>')">
			<?php }
			?>
<br><br><br>
<form method="post" enctype="multipart/form-data">
	<div id="accordion">
		<h3><?php _e('Content', 'fcc');?></h3>
		<div>
			<span><?php echo __('Title','fcc'); ?></span><br>
			<input type="text" name="title" style="width:100%;" value = "<?php echo $title; ?>"><br><br>
			<span><?php echo __('Description','fcc'); ?></span>
<?php
///$content	 = '';
$editor_id = 'wdm_content';

wp_editor( $content, $editor_id );

// do_action('admin_print_scripts');
?>

			<br>
			<?php if ( count( $category ) > 0 ) { ?>
				<span><?php echo __('Categories:','fcc'); ?></span><br>
				<select name="category[]" multiple>
	<?php foreach ( $category as $k => $v ) { ?>
					<option value="<?php echo $k; ?>" <?php echo (in_array( $k, $selected_category ) ? 'selected' : ''); ?>><?php echo $v; ?></option>

				<?php } ?>
					</select>
				<br>
			<?php } ?>
			<br>
				<span><?php echo __('Tags:','fcc'); ?></span><br>
				<div id='wdm_tag_list'>
					<select name="tag[]" multiple>
					<?php if ( count( $tag ) > 0 ) { ?>
	<?php foreach ( $tag as $k => $v ) { ?>
						<option value="<?php echo $k; ?>" <?php echo (in_array( $k, $selected_tag ) ? 'selected' : ''); ?>><?php echo $v; ?></option>
	<?php } ?>
				<?php } ?>
					</select>
				</div>
				<br>
			<input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc');?>">
			<br>
			<div>
				<label for="order_number"><?php _e('Order','fcc');?></label>
				<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
			</div>
		</div>
		<h3><?php echo __('Features','fcc'); ?></h3>


		<div>


			<div class="sfwd sfwd_options sfwd-courses_settings">
				<div class="sfwd_input " id="sfwd-courses_course_materials">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_materials_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Course Materials','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><textarea name="sfwd-courses_course_materials" rows="2" cols="57"><?php echo $sfwd_courses_course_materials; ?></textarea></div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_materials_tip"><label class="sfwd_help_text"><?php echo __('Options for Course Materials','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_price_type">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_type_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Course Price Type','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<select name="sfwd-courses_course_price_type">
								<!- <option value="open" <?php echo (($sfwd_courses_course_price_type == 'open' ) ? 'selected' : ''); ?>><?php echo __('Open','fcc'); ?></option> ->
								<!- <option value="closed" <?php echo (($sfwd_courses_course_price_type == 'closed' ) ? 'selected' : ''); ?>><?php echo __('Closed','fcc'); ?></option> ->
								<option value="free" <?php echo (($sfwd_courses_course_price_type == 'free' ) ? 'selected' : ''); ?>><?php echo __('Free','fcc'); ?></option>
								<option value="paynow" <?php echo (($sfwd_courses_course_price_type == 'paynow' ) ? 'selected' : ''); ?>><?php echo __('Buy Now','fcc'); ?></option>
								<option value="subscribe" <?php echo (($sfwd_courses_course_price_type == 'subscribe' ) ? 'selected' : ''); ?>><?php echo __('Recurring','fcc'); ?></option>
							</select>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_type_tip"><label class="sfwd_help_text"><?php echo __('Is it open to all, free join, one time purchase, or a recurring subscription?','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_custom_button_url" style="display: none;">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_custom_button_url_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Custom Button URL','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_custom_button_url" type="text" size="57" placeholder="Optional" value="<?php echo $sfwd_courses_custom_button_url; ?>">
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_custom_button_url_tip"><label class="sfwd_help_text"><?php echo __('Entering a URL in this field will enable the "Take This Course" button. The button will not display if this field is left empty','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_price" style="display: none;">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Price','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_course_price" type="text" size="57" value="<?php echo $sfwd_courses_course_price; ?>">
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_tip"><label class="sfwd_help_text"><?php echo __('Enter course price here. Leave empty if the course is free.','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_price_billing_cycle" style="display: none;">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_price_billing_cycle_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Billing Cycle','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<input name="course_price_billing_p3" type="text" value="<?php echo $course_price_billing_p3; ?>" size="2"> 
							<select class="select_course_price_billing_p3" name="course_price_billing_t3">
								<option value="D" <?php echo (($course_price_billing_t3 == 'D' ) ? 'selected' : ''); ?>><?php echo __('day(s)','fcc'); ?></option>
								<option value="W" <?php echo (($course_price_billing_t3 == 'W' ) ? 'selected' : ''); ?>><?php echo __('week(s)','fcc'); ?></option>
								<option value="M" <?php echo (($course_price_billing_t3 == 'M' ) ? 'selected' : ''); ?>><?php echo __('month(s)','fcc'); ?></option>
								<option value="Y" <?php echo (($course_price_billing_t3 == 'Y' ) ? 'selected' : ''); ?>><?php echo __('year(s)','fcc'); ?></option>
							</select>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_price_billing_cycle_tip"><label class="sfwd_help_text"><?php echo __('Billing Cycle for the recurring payments in case of a subscription.','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_access_list">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_access_list_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Course Access List','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><textarea name="sfwd-courses_course_access_list" rows="2" cols="57"><?php echo $sfwd_courses_course_access_list; ?></textarea></div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_access_list_tip"><label class="sfwd_help_text"><?php echo __('This field is auto-populated with the UserIDs of those who have access to this course.','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				
				<div class="sfwd_input " id="sfwd-courses_course_prerequisite">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_prerequisite_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Course Prerequisites','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<select name="sfwd-courses_course_prerequisite">
								<option value="0"><?php _e('-- Select a Course --', 'fcc');?></option>
<?php if ( count( $course_list ) > 0 ) { ?>
	<?php foreach ( $course_list as $k => $v ) { ?>

										<option value="<?php echo $v; ?>" <?php echo (($sfwd_courses_course_prerequisite == $v ) ? 'selected' : ''); ?>><?php echo get_the_title( $v ); ?></option>	

									<?php } ?>
<?php } ?>
							</select>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_prerequisite_tip"><label class="sfwd_help_text"><?php echo __('Select a course as prerequisites to view this course','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_disable_lesson_progression">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_disable_lesson_progression_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Disable Lesson Progression','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_course_disable_lesson_progression" type="checkbox" <?php echo (($sfwd_courses_course_disable_lesson_progression != '' ) ? 'checked' : ''); ?>>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_disable_lesson_progression_tip"><label class="sfwd_help_text"><?php echo __('Disable the feature that allows attempting lessons only in allowed order','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_certificate">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-courses_certificate_tip');"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>"><label class="sfwd_label textinput"><?php echo __('Associated Certificate','fcc'); ?></label></a></span>
					<span class="sfwd_option_input"><div class="sfwd_option_div"><select name="sfwd-courses_certificate">
								<option value="0"><?php echo __('-- Select a Certificate --','fcc'); ?></option>
								<?php if(!  empty($cerficates)){
									foreach($cerficates as $k=>$v){
									?>
	<option value="<?php echo $v; ?>" <?php echo ($v == $sfwd_courses_certificate) ? 'selected' : ''; ?>><?php echo get_the_title($v); ?></option>
								<?php 
									}
									} ?>
</select>
</div><div class="sfwd_help_text_div" style="display: none;" id="sfwd-courses_certificate_tip"><label class="sfwd_help_text"><?php echo __('Select a certificate to be awarded upon course completion (optional).','fcc'); ?></label></div></span><p style="clear:left"></p></div>
				
			</div>

		</div>
		<h3><?php echo __('Settings','fcc'); ?></h3>
		<div>
			<span><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" id="featured_image"></span>
			<?php if ( isset( $_GET[ 'courseid' ] ) && has_post_thumbnail($_GET['courseid']) ) { ?>
				<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
<?php } ?>
			<br><br>
			<div class="sfwd_input " id="sfwd-courses_expire_access">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php _e('Expire Access','fcc');?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_expire_access" type="checkbox" <?php echo (($sfwd_courses_expire_access != '' ) ? 'checked' : ''); ?>>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_tip"><label class="sfwd_help_text"><?php echo __('Leave this field unchecked if access never expires','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
			<div class="sfwd_input " id="sfwd-courses_expire_access_days" style="display: none;">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_days_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Expire Access After (days)','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_expire_access_days" type="number" size="57" value="<?php echo $sfwd_courses_expire_access_days; ?>">
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_days_tip"><label class="sfwd_help_text"><?php echo __('Enter the number of days a user has access to this course','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_expire_access_delete_progress" style="display: none;">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_expire_access_delete_progress_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Delete Course and Quiz Data After Expiration','fcc'); ?></label></a></span>

					<span class="sfwd_option_input">
						<div class="sfwd_option_div"><input name="sfwd-courses_expire_access_delete_progress" type="checkbox" <?php echo (($sfwd_courses_expire_access_delete_progress != '' ) ? 'checked' : ''); ?>>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_expire_access_delete_progress_tip"><label class="sfwd_help_text"><?php echo __("Select this option if you want the user's course progress to be deleted when their access expires",'fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
			<div class="sfwd_input " id="sfwd-courses_course_lesson_orderby">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_orderby_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Sort Lesson By','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<select name="sfwd-courses_course_lesson_orderby">
								<option  value=""><?php echo __('Use Default','fcc'); ?></option>
								<option value="title" <?php echo (($sfwd_courses_course_lesson_orderby == 'title' ) ? 'selected' : ''); ?>><?php echo __('Title','fcc'); ?></option>
								<option value="date" <?php echo (($sfwd_courses_course_lesson_orderby == 'date' ) ? 'selected' : ''); ?>><?php echo __('Date','fcc'); ?></option>
							<!-	<option value="menu_order" <?php echo (($sfwd_courses_course_lesson_orderby == 'menu_order' ) ? 'selected' : ''); ?>><?php echo __('Menu Order','fcc'); ?></option> ->
							</select>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_orderby_tip"><label class="sfwd_help_text"><?php echo __('Choose the sort order of lessons in this course','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
				<div class="sfwd_input " id="sfwd-courses_course_lesson_order">
					<span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility( 'sfwd-courses_course_lesson_order_tip' );"><img src="<?php echo plugins_url( 'images/question.png', dirname(dirname( __FILE__ )) ); ?>" /><label class="sfwd_label textinput"><?php echo __('Sort Lesson Direction','fcc'); ?></label></a></span>
					<span class="sfwd_option_input">
						<div class="sfwd_option_div">
							<select name="sfwd-courses_course_lesson_order">
								<option  value=""><?php echo __('Use Default','fcc'); ?></option>
								<option value="ASC" <?php echo (($sfwd_courses_course_lesson_order == 'ASC' ) ? 'selected' : ''); ?>><?php echo __('Ascending','fcc'); ?></option>
								<option value="DESC" <?php echo (($sfwd_courses_course_lesson_order == 'DESC' ) ? 'selected' : ''); ?>><?php echo __('Descending','fcc'); ?></option>
							</select>
						</div>
						<div class="sfwd_help_text_div" style="display:none" id="sfwd-courses_course_lesson_order_tip"><label class="sfwd_help_text"><?php echo __('Choose the sort order of lessons in this course','fcc'); ?></label></div>
					</span>
					<p style="clear:left"></p>
				</div>
		</div>
	</div>
	<br><br>
	<input type ="hidden" name="wdm_course_action" value="<?php echo (isset( $_GET[ 'courseid' ] ) ? 'edit' : 'add'); ?>">
<?php if ( isset( $_GET[ 'courseid' ] ) ) { ?>
		<input type ="hidden" name ="courseid" value ="<?php echo $_GET[ 'courseid' ]; ?>">
<?php } ?>
		<div id="wdm_editor_tp"></div>
	<input type="submit" value="<?php _e('Submit', 'fcc'); ?>" id="wdm_course_submit">
</form>
