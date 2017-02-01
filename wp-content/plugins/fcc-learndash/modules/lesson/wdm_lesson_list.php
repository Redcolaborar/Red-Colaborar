<?php
$sql = "SELECT ID,post_modified FROM $table WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-lessons' AND post_status IN ('draft','publish')";
$results = $wpdb->get_results($sql);
$sql = "SELECT ID FROM $table WHERE post_content like '%[wdm_lesson_creation]%' AND post_status like 'publish'";
$course_result = $wpdb->get_var($sql);
$link =	get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";


?>
<button id="wdmAddNewButton" onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_lesson_list">
	<thead>
		<tr>
			<th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Course','fcc'); ?></th>
			<th><?php echo __('Tags','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
			<th><?php echo __('Date','fcc'); ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Course','fcc'); ?></th>
			<th><?php echo __('Tags','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
			<th><?php echo __('Date','fcc'); ?></th>
		</tr>
	</tfoot>
	<tbody>
		<?php foreach($results as $k=>$v){ ?>
		<tr>
			<td><?php echo get_the_title($v->ID); ?></td>
			<td><?php // $post_categories = wp_get_post_categories( $v->ID ); 
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
			<td><?php $post_tags = wp_get_post_tags( $v->ID ); 
			//echo "<pre>";print_R($post_tags);echo "</pre>";
			if(count($post_tags) > 0){
				$tags = array();
				foreach($post_tags as $c){
	
	$tags[] = $c->name;
}
			if(count($tags) > 0)
			echo implode(', ',$tags);	
			}else{
				echo '-';
			}
			
			?></td>
			<td><a href = "<?php echo add_query_arg(array("lessonid" => $v->ID), $link); ?>"><img src="<?php echo plugins_url('images/edit.png',dirname(dirname( __FILE__ ))); ?>" width="25" height="25"></a></td>
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
$course_names = "['".$temp."']"; ?>
<script>
jQuery(document).ready(function($){

 $('#wdm_lesson_list').dataTable()
		  .columnFilter({
			aoColumns: [ { type: "text" },
				     { type: "select",
					 values: <?php echo $course_names; ?>
				},
				     null,
					 null,
					 null
				]
		});
});
</script>