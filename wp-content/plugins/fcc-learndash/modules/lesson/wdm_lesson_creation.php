<?php global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/lesson_listing';
if(isset($_GET['lessonid'])){
	if(!is_numeric( $_GET[ 'lessonid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table = $wpdb->posts;
$id = $_GET['lessonid'];

$sql = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-lessons' AND post_author = ".get_current_user_id();
$results = $wpdb->get_results($sql);
if(count($results) == 0 ){
	echo __("Sorry, Something went wrong",'fcc');
	return;
}
}
$title = "";
$content = "";
$featured_image = "";
$sfwd_lessons_course = "";
$sfwd_lessons_forced_lesson_time = "";
$sfwd_lessons_lesson_assignment_upload = "";
$sfwd_lessons_lesson_assignment_points_enabled = "";
$sfwd_lessons_lesson_assignment_points_amount = "";
$sfwd_lessons_auto_approve_assignment = "";
$sfwd_lessons_sample_lesson = "";
$sfwd_lessons_visible_after_specific_date = "";
$sfwd_lessons_visible_after = "";
$preview_url = '';
$menu_order	= 0;

$table = $wpdb->prefix."posts";
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$course_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$course_list[] = $v->ID;
		
	}
	
}

$term_table = $wpdb->prefix."terms";
$taxonomy = $wpdb->prefix."term_taxonomy";
$sql = "SELECT b.term_taxonomy_id, a.name FROM $term_table a JOIN $taxonomy b ON a.term_id=b.term_id WHERE taxonomy like 'category'";
$results = $wpdb->get_results($sql);
$category = array();

if(count($results) > 0){
	foreach($results as $k=>$v){
		$category[$v->term_taxonomy_id] = $v->name;
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

$term_relationship = $wpdb->prefix."term_relationships";
$selected_category = array();
if(isset($_GET['lessonid'])){
	$sql = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = ".$_GET['lessonid'];
	$results = $wpdb->get_results($sql);
	if(count($results) >0 ){
		foreach($results as $k => $v){
			$selected_category[] = $v->term_taxonomy_id;
		}
		
	}
	
}

$selected_tag	 = array();
if ( isset( $_GET[ 'lessonid' ] ) ) {
	$sql	 = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = " . $_GET[ 'lessonid' ];
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) > 0 ) {
		foreach ( $results as $k => $v ) {
			$selected_tag[] = $v->term_taxonomy_id;
		}
	}
}

if(isset($_GET['lessonid'])){
	 $id = $_GET['lessonid'];
	$title = get_the_title($id);
	$content_post = get_post($id);
	$content = $content_post->post_content;
	$menu_order = $content_post->menu_order;
	$content = apply_filters('the_content', $content);
	$lesson_meta = maybe_unserialize(get_post_meta($id,'_sfwd-lessons'));
	//echo "<pre>";print_r($lesson_meta);echo "</pre>";
	if(isset($lesson_meta[0]['sfwd-lessons_course']))
		$sfwd_lessons_course = $lesson_meta[0]['sfwd-lessons_course'];
	if(isset($lesson_meta[0]['sfwd-lessons_forced_lesson_time']))
		$sfwd_lessons_forced_lesson_time = $lesson_meta[0]['sfwd-lessons_forced_lesson_time'];
	if(isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_upload']))
		$sfwd_lessons_lesson_assignment_upload = $lesson_meta[0]['sfwd-lessons_lesson_assignment_upload'];
	if (isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_points_enabled']))
		$sfwd_lessons_lesson_assignment_points_enabled = $lesson_meta[0]['sfwd-lessons_lesson_assignment_points_enabled'];

	if (isset($lesson_meta[0]['sfwd-lessons_lesson_assignment_points_amount']))
		$sfwd_lessons_lesson_assignment_points_amount = $lesson_meta[0]['sfwd-lessons_lesson_assignment_points_amount'];

	if(isset($lesson_meta[0]['sfwd-lessons_auto_approve_assignment']))
		$sfwd_lessons_auto_approve_assignment = $lesson_meta[0]['sfwd-lessons_auto_approve_assignment'];
	if(isset($lesson_meta[0]['sfwd-lessons_sample_lesson']))
		$sfwd_lessons_sample_lesson = $lesson_meta[0]['sfwd-lessons_sample_lesson'];
	if(isset($lesson_meta[0]['sfwd-lessons_visible_after']))
		$sfwd_lessons_visible_after = $lesson_meta[0]['sfwd-lessons_visible_after'];
	if(isset($lesson_meta[0]['sfwd-lessons_visible_after_specific_date']))
		$sfwd_lessons_visible_after_specific_date = $lesson_meta[0]['sfwd-lessons_visible_after_specific_date'];
			
	$preview_url = add_query_arg(array('wdm_preview'=>1),get_permalink($id));
}


?>
<?php if(isset($_SESSION['update'])){ ?>
<?php if($_SESSION['update'] == 2) { ?>
<div class="wdm-update-message"><?php echo __('Lesson Updated Successfully.','fcc'); ?></div>
	
<?php }else if($_SESSION['update'] == 1){ ?>
	<div class="wdm-update-message"><?php echo __('Lesson Added Successfully.','fcc'); ?></div>
	
 <?php }
 unset($_SESSION['update']);

} 
if (defined('WDM_ERROR')) { ?>

		<div class="wdm-error-message"><?php echo WDM_ERROR; ?>
		</div>

	

<?php
	
} ?>
	
	<input type="button" value="<?php echo __('Add new', 'fcc'); ?>" onclick="location.href = '<?php echo $link; ?>';"  style="float:right;">
	<input type="button" value="<?php echo __('Back','fcc'); ?>" onclick="location.href = '<?php echo $back_url; ?>';" style="float: right;">
			<?php if($preview_url != ''){ ?>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="<?php echo __('Preview','fcc'); ?>" style="float:right;margin-right: 2%;"onclick="window.open('<?php echo $preview_url; ?>')">
			<?php }
			?>
<br><br><br>	
<form method="post" enctype="multipart/form-data">
<div id="accordion">
	<h3><?php echo __('Content','fcc'); ?></h3>
	<div>
		<span><?php echo __('Title','fcc'); ?></span><br>
		<input type="text" name="title" required="required"	style="width:100%;" value = "<?php echo $title; ?>"><br><br>
		<span><?php echo __('Content','fcc'); ?></span>
		<?php
   ///$content	 = '';
   $editor_id	 = 'wdm_content';
   
   wp_editor( $content, $editor_id );
      
  // do_action('admin_print_scripts');
   ?>
		
		<br>
		<?php if(count($category) == -1){ ?>
		<span ><?php echo __('Categories:','fcc'); ?></span><br>
		<?php foreach($category as $k=>$v){ ?>
		<input type="checkbox" name="category[]" value="<?php echo $k; ?>" <?php echo (in_array($k,$selected_category) ? 'checked' : ''); ?>><?php echo $v; ?></input>
			
		<?php } ?>
		
		<br>
		<?php } ?>
		<?php if ( count( $tag ) > 0 ) { ?>
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
			<?php } ?>
				<input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc');?>">
		<br>
		<span ><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" ></span>
		<?php if ( isset( $_GET[ 'lessonid' ] ) && has_post_thumbnail($_GET['lessonid']) ) { ?>
				<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
<?php } ?>
		<br>
			<div>
				<label for="order_number"><?php _e('Order','fcc');?></label>
				<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
			</div>
	</div>
	<h3><?php echo __('Features','fcc'); ?></h3>
	
	
	<div>
<div class="sfwd sfwd_options sfwd-lessons_settings">
   <div class="sfwd_input " id="sfwd-lessons_course">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_course_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Associated Course','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div">
            <select name="sfwd-lessons_course">
               <option value="0"><?php _e('-- Select a Course --', 'fcc');?></option>
               <?php if(count($course_list) > 0){ ?>
						<?php foreach($course_list as $k=>$v){ ?>
							
						<option value="<?php echo $v; ?>" <?php echo (($sfwd_lessons_course == $v ) ? 'selected' : ''); ?>><?php echo get_the_title($v); ?></option>	
							
						<?php } ?>
						<?php } ?>
            </select>
         </div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_course_tip"><label class="sfwd_help_text"><?php echo __('Associate with a course','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-lessons_forced_lesson_time">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_forced_lesson_time_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Forced Lesson Timer','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_forced_lesson_time" type="text" size="57" value="<?php echo $sfwd_lessons_forced_lesson_time; ?>"></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_forced_lesson_time_tip"><label class="sfwd_help_text"><?php echo __('Minimum time a user has to spend on Lesson page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_upload">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_upload_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Upload Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_upload" type="checkbox" <?php echo (($sfwd_lessons_lesson_assignment_upload != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_upload_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to make it mandatory to upload assignment','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-lessons_auto_approve_assignment">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_auto_approve_assignment_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Auto Approve Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_auto_approve_assignment" type="checkbox" <?php echo (($sfwd_lessons_auto_approve_assignment != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_auto_approve_assignment_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to auto-approve the uploaded assignment','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>

   <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_enabled">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Award Points for Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_enabled" type="checkbox" <?php echo (($sfwd_lessons_lesson_assignment_points_enabled != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_points_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow this assignment to be assigned points when it is approved.','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>

   <div class="sfwd_input " id="sfwd-lessons_lesson_assignment_points_amount" style="display: none;">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_lesson_assignment_points_amount_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Set Number of Points for Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_lesson_assignment_points_amount" type="number" min='0' size="57" value="<?php echo $sfwd_lessons_lesson_assignment_points_amount; ?>" ></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_lesson_assignment_points_amount_tip"><label class="sfwd_help_text"><?php echo __('Assign the max amount of points someone can earn for this assignment.','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>


   <div class="sfwd_input " id="sfwd-lessons_sample_lesson">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_sample_lesson_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Sample Lesson','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_sample_lesson" type="checkbox" <?php echo (($sfwd_lessons_sample_lesson != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_sample_lesson_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want this lesson and all its topics to be available for free','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-lessons_visible_after">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_visible_after_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Make lesson visible X days after sign-up','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_visible_after" type="text" size="57" value="<?php echo $sfwd_lessons_visible_after; ?>" ></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_visible_after_tip"><label class="sfwd_help_text"><?php echo __('Make lesson visible ____ days after sign-up','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-lessons_visible_after_specific_date">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-lessons_visible_after_specific_date_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Make lesson visible on specific date','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-lessons_visible_after_specific_date" type="text" size="57" value="<?php echo $sfwd_lessons_visible_after_specific_date; ?>" id="dp1424081713948" ></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-lessons_visible_after_specific_date_tip"><label class="sfwd_help_text"><?php echo __('Set the date that you would like this lesson to become available','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
</div>
	
	</div>
<!--	<h3><?php echo __('Settings','fcc'); ?></h3>
	<div>
		</div>-->
</div>
	<input type ="hidden" name="wdm_lesson_action" value="<?php echo (isset($_GET['lessonid']) ? 'edit' : 'add'); ?>">
<?php if(isset($_GET['lessonid'])) { ?>
<input type ="hidden" name ="lessonid" value ="<?php echo $_GET['lessonid']; ?>">
<?php } ?>
<input type="submit" value="<?php _e('Submit', 'fcc');?>" id='wdm_lesson_submit'>
</form>
