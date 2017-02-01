<?php
$sql = "SELECT ID,post_modified FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-quiz' AND post_status IN ('draft','publish')";
$results = $wpdb->get_results($sql);
$sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_quiz_creation]%' AND post_status like 'publish'";
$course_result = $wpdb->get_var($sql);
$link =	get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";


?>
<button id="wdmAddNewButton" onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_quiz_list">
	<thead>
		<tr>
			<th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Course','fcc'); ?></th>
			<th><?php echo __('Assigned Lesson','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
			<th><?php echo __('Date','fcc'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Course','fcc'); ?></th>
			<th><?php echo __('Assigned Lesson','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
			<th><?php echo __('Date','fcc'); ?></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($results as $k=>$v){ ?>
		<tr>
			<td><?php echo get_the_title($v->ID); ?></td>
			<td><?php //$post_categories = wp_get_post_categories( $v->ID ); 
//			if(count($post_categories) > 0){
//				$cats = array();
//				foreach($post_categories as $c){
//	$cat = get_category( $c );
//	$cats[] = $cat->name;
//}
//			if(count($cats) > 0)
//			echo implode(', ',$cats);	
//			}
			
			$course_id = get_post_meta($v->ID,'course_id',true);
			echo (($course_id != 0) ? get_the_title($course_id) : '-' );
			
			?></td>
			<td><?php $lesson_id = get_post_meta($v->ID,'lesson_id',true);
			echo (($lesson_id != 0) ? get_the_title($lesson_id) : '-' );
			
			?></td>
			<td><a href = "<?php echo add_query_arg(array("quizid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/edit.png',dirname(dirname( __FILE__ ))); ?>" width="25" height="25"></a></td>
			<td><?php echo $v->post_modified; ?></td>
			
			
		</tr>	
			
		<?php } ?>
		
	</tbody>
</table>
<?php 
$course_list = array();
$sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-courses' AND post_status IN ('draft','publish')"; 
$results = $wpdb->get_col($sql);
//echo '<pre>';print_R($results);echo '</pre>';
if(!empty($results)){
foreach ($results as $k=>$v){
	if(!in_array($v,$course_list)){
	$course_list[] = $v;
	}
}
}

$course_list[] = '-';
$temp = implode("','",$course_list);
$course_names = "['".$temp."']";
$lesson_list = array();
$sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('draft','publish')"; 
$results = $wpdb->get_col($sql);
//echo '<pre>';print_R($results);echo '</pre>';
if(!empty($results)){
foreach ($results as $k=>$v){
	if(!in_array($v,$lesson_list)){
	$lesson_list[] = $v;
	}
}
}

$lesson_list[] = '-';
$temp = implode("','",$lesson_list);
$lesson_names = "['".$temp."']";
//echo $course_names;
?>
<script>
jQuery(document).ready(function($){

 $('#wdm_quiz_list').dataTable()
		  .columnFilter({
			aoColumns: [ { type: "text" },
				     { type: "select",
					 values: <?php echo $course_names; ?>
				},
					 { type: "select",
					 values: <?php echo $lesson_names; ?>
				 },
				     null,
					 null
				]

		});
});
</script>