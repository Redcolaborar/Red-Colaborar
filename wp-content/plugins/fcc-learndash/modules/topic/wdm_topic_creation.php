<?php
global $wpdb;
$back_url = bp_core_get_user_domain(get_current_user_id() ).'listing/topic_listing';
if(isset($_GET['topicid'])){
	if(!is_numeric( $_GET[ 'topicid' ] )){
		echo __("Sorry, Something went wrong",'fcc');
		return;
	}
	$table = $wpdb->posts;
$id = $_GET['topicid'];

$sql = "SELECT ID FROM $table WHERE ID = $id AND post_type like 'sfwd-topic' AND post_author = ".get_current_user_id();
$results = $wpdb->get_results($sql);
if(count($results) == 0 ){
	echo __("Sorry, Something went wrong",'fcc');
	return;
}
	

	
	
}



$title = "";
$content = "";
$featured_image = "";
$sfwd_topic_course = "";
$sfwd_topic_lesson = "";
$sfwd_topic_forced_lesson_time = "";
$sfwd_topic_lesson_assignment_upload = "";
$sfwd_topic_auto_approve_assignment = "";
$sfwd_topic_assignment_points_enabled = "";
$sfwd_topic_assignment_points_amount = "";
$menu_order	= 0;
$preview_url = '';
$table = $wpdb->prefix."posts";
$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$course_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$course_list[] = $v->ID;
		
	}
	
}

$sql = "SELECT ID FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('publish','draft')";

$results = $wpdb->get_results($sql);
$lesson_list = array();
if(count($results) > 0){
	foreach($results as $k=>$v){
		$lesson_list[] = $v->ID;
		
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
if(isset($_GET['topicid'])){
	$sql = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = ".$_GET['topicid'];
	$results = $wpdb->get_results($sql);
	if(count($results) >0 ){
		foreach($results as $k => $v){
			$selected_category[] = $v->term_taxonomy_id;
		}
		
	}
	
}
$selected_tag	 = array();
if ( isset( $_GET[ 'topicid' ] ) ) {
	$sql	 = "SELECT term_taxonomy_id FROM $term_relationship WHERE object_id = " . $_GET[ 'topicid' ];
	$results = $wpdb->get_results( $sql );
	if ( count( $results ) > 0 ) {
		foreach ( $results as $k => $v ) {
			$selected_tag[] = $v->term_taxonomy_id;
		}
	}
}
if(isset($_GET['topicid'])){
	 $id = $_GET['topicid'];
	$title = get_the_title($id);
	$content_post = get_post($id);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$menu_order = $content_post->menu_order;
	$topic_meta = maybe_unserialize(get_post_meta($id,'_sfwd-topic'));
	//echo "<pre>";print_r($topic_meta);echo "</pre>";
	if(isset($topic_meta[0]['sfwd-topic_course']))
		$sfwd_topic_course = $topic_meta[0]['sfwd-topic_course'];
	if(isset($topic_meta[0]['sfwd-topic_lesson']))
		$sfwd_topic_lesson = $topic_meta[0]['sfwd-topic_lesson'];
	if(isset($topic_meta[0]['sfwd-topic_forced_lesson_time']))
		$sfwd_topic_forced_lesson_time = $topic_meta[0]['sfwd-topic_forced_lesson_time'];
	if(isset($topic_meta[0]['sfwd-topic_lesson_assignment_upload']))
		$sfwd_topic_lesson_assignment_upload = $topic_meta[0]['sfwd-topic_lesson_assignment_upload'];
	if(isset($topic_meta[0]['sfwd-topic_auto_approve_assignment']))
		$sfwd_topic_auto_approve_assignment = $topic_meta[0]['sfwd-topic_auto_approve_assignment'];
	if (isset($topic_meta[0]['sfwd-topic_lesson_assignment_points_enabled']))
		$sfwd_topic_assignment_points_enabled = $topic_meta[0]['sfwd-topic_lesson_assignment_points_enabled'];
	if (isset($topic_meta[0]['sfwd-topic_lesson_assignment_points_amount']))
		$sfwd_topic_assignment_points_amount = $topic_meta[0]['sfwd-topic_lesson_assignment_points_amount'];
	$preview_url = add_query_arg(array('wdm_preview'=>1),get_permalink($id));
}
?>
<?php if(isset($_SESSION['update'])){ ?>
<?php if($_SESSION['update'] == 2) { ?>
<div class="wdm-update-message"><?php echo __('Topic Updated Successfully.','fcc'); ?></div>
	
<?php }else if($_SESSION['update'] == 1){ ?>
	<div class="wdm-update-message"><?php echo __('Topic Added Successfully.','fcc'); ?></div>
	
 <?php }
 unset($_SESSION['update']);

}
if (defined('WDM_ERROR')) { ?>

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
		<input type="text" name="title" style="width:100%;" value = "<?php echo $title; ?>"><br><br>
		<span><?php echo __('Content','fcc'); ?></span>
		<?php
   ///$content	 = '';
   $editor_id	 = 'wdm_content';
   
   wp_editor( $content, $editor_id );
      
  // do_action('admin_print_scripts');
   ?>
		
<!--		<br>
		<?php if(count($category) > 0){ ?>
		<span>Categories:</span><br>
		<?php foreach($category as $k=>$v){ ?>
		<input type="checkbox" name="category[]" value="<?php echo $k; ?>" <?php echo (in_array($k,$selected_category) ? 'checked' : ''); ?>><?php echo $v; ?></input>
			
		<?php } ?>
		
		<br>
		<?php } ?>
		<br>-->
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
			<input type='text' name='wdm_tag' id='wdm_tag'><input type='button' id='wdm_add_tag' value="<?php _e('Add Tag', 'fcc'); ?>">
<br><br>
		<span><?php echo __('Featured Image:','fcc'); ?> <input type="file" name="featured_image" ></span>
		<?php if ( isset( $_GET[ 'topicid' ] ) && has_post_thumbnail($_GET['topicid']) ) { ?>
				<?php echo get_the_post_thumbnail( $id, array( 100, 100 ) ); ?>
<?php } ?>
			<div>
				<label for="order_number"><?php _e('Order','fcc');?></label>
				<input type="number" min=0 id="order_number" name="order_number" value="<?php echo $menu_order; ?>"/>
			</div>
	</div>
	<h3><?php echo __('Features','fcc'); ?></h3>
	<div>
<div class="sfwd sfwd_options sfwd-topic_settings">
   <div class="sfwd_input " id="sfwd-topic_course">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_course_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Associated Course','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div">
            <select name="sfwd-topic_course">
               <option value="0"><?php _e('-- Select a Course --', 'fcc');?></option>
              <?php if(count($course_list) > 0){ ?>
						<?php foreach($course_list as $k=>$v){ ?>
							
						<option value="<?php echo $v; ?>" <?php echo (($sfwd_topic_course == $v ) ? 'selected' : ''); ?>><?php echo get_the_title($v); ?></option>	
							
						<?php } ?>
						<?php } ?>
            </select>
         </div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_course_tip"><label class="sfwd_help_text"><?php echo __('Associate with a course','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-topic_lesson">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Associated Lesson','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div">
            <select name="sfwd-topic_lesson">
               <option value="0"><?php echo __('-- Select a Lesson --', 'fcc');?></option>
               <?php if(count($lesson_list) > 0){ ?>
						<?php foreach($lesson_list as $k=>$v){ ?>
							
						<option value="<?php echo $v; ?>" <?php echo (($sfwd_topic_lesson == $v ) ? 'selected' : ''); ?>><?php echo get_the_title($v); ?></option>	
							
						<?php } ?>
						<?php } ?>
            </select>
         </div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_tip"><label class="sfwd_help_text"><?php echo __('Optionally associate a topic with a lesson','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-topic_forced_lesson_time">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_forced_lesson_time_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Forced Topic Timer','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-topic_forced_lesson_time" type="text" size="57" value="<?php echo $sfwd_topic_forced_lesson_time; ?>"></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_forced_lesson_time_tip"><label class="sfwd_help_text"><?php echo __('Minimum time a user has to spend on Topic page before it can be marked complete. Examples: 40 (for 40 seconds), 20s, 45sec, 2m 30s, 2min 30sec, 1h 5m 10s, 1hr 5min 10sec','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-topic_lesson_assignment_upload">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_assignment_upload_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Upload Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-topic_lesson_assignment_upload" type="checkbox" <?php echo (($sfwd_topic_lesson_assignment_upload != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_assignment_upload_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to make it mandatory to upload assignment','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>
   <div class="sfwd_input " id="sfwd-topic_auto_approve_assignment">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_auto_approve_assignment_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>"><label class="sfwd_label textinput"><?php echo __('Auto Approve Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-topic_auto_approve_assignment" type="checkbox" <?php echo (($sfwd_topic_auto_approve_assignment != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_auto_approve_assignment_tip"><label class="sfwd_help_text"><?php echo __('Check this if you want to auto-approve the uploaded assignment','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>

   <div class="sfwd_input " id="sfwd-topic_lesson_assignment_points_enabled">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_lesson_assignment_points_enabled_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Award Points for Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-topic_assignment_points_enabled" type="checkbox" <?php echo (($sfwd_topic_assignment_points_enabled != '' ) ? 'checked' : ''); ?>></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_lesson_assignment_points_enabled_tip"><label class="sfwd_help_text"><?php echo __('Allow this assignment to be assigned points when it is approved.','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>

   <div class="sfwd_input " id="sfwd-topic_assignment_points_amount" style="display: none;">
      <span class="sfwd_option_label" style="text-align:right;vertical-align:top;"><a class="sfwd_help_text_link" style="cursor:pointer;" title="Click for Help!" onclick="toggleVisibility('sfwd-topic_assignment_points_amount_tip');"><img src="<?php echo plugins_url('images/question.png',dirname(dirname( __FILE__ ))); ?>" /><label class="sfwd_label textinput"><?php echo __('Set Number of Points for Assignment','fcc'); ?></label></a></span>
      <span class="sfwd_option_input">
         <div class="sfwd_option_div"><input name="sfwd-topic_assignment_points_amount" type="number" min='0' size="57" value="<?php echo $sfwd_topic_assignment_points_amount; ?>" ></div>
         <div class="sfwd_help_text_div" style="display:none" id="sfwd-topic_assignment_points_amount_tip"><label class="sfwd_help_text"><?php echo __('Assign the max amount of points someone can earn for this assignment.','fcc'); ?></label></div>
      </span>
      <p style="clear:left"></p>
   </div>

</div>
		
	</div>
</div>
	<input type ="hidden" name="wdm_topic_action" value="<?php echo (isset($_GET['topicid']) ? 'edit' : 'add'); ?>">
<?php if(isset($_GET['topicid'])) { ?>
<input type ="hidden" name ="topicid" value ="<?php echo $_GET['topicid']; ?>">
<?php } ?>
<input type="submit" value="<?php _e('Submit', 'fcc');?>" id='wdm_topic_submit'>
</form>
