<?php
global $wpdb;
$sql = "SELECT meta_key,meta_value FROM $table WHERE user_id = ".get_current_user_id()." AND meta_key like 'wdm_question_id_%'";
// if (constant('WP_ALLOW_MULTISITE')) {
if (is_multisite()) {
    $temp =  $wpdb->prefix;
    $temp = explode(get_current_blog_id(), $temp);
    $temp_prefix = $temp[0];
    $sql = "SELECT meta_key,meta_value FROM ".$temp_prefix."usermeta WHERE user_id = ".get_current_user_id()." AND meta_key like 'wdm_question_id_%'";
}

$results = $wpdb->get_results($sql);
//echo "<pre>";print_R($results);echo "</pre>";
$sql = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_content like '%[wdm_question_creation]%' AND post_status like 'publish'";
$course_result = $wpdb->get_var($sql);
$link =	get_permalink($course_result);
//echo "<pre>";print_r($results);echo "</pre>";


?>
<button id="wdmAddNewButton" onclick="location.href = '<?php echo $link; ?>';"  style="float:right;margin-bottom: 10px;"><?php _e('Add new', 'fcc'); ?></button>
<table id="wdm_question_list">
	<thead>
		<tr>
			<th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Quiz','fcc'); ?></th>
			<th><?php echo __('Category','fcc'); ?></th>
			<th><?php echo __('Points','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
			
		</tr>
	</thead>
<tfoot>
            <tr id="wdmHideTfoot">
                <th><?php echo __('Title','fcc'); ?></th>
			<th><?php echo __('Assigned Quiz','fcc'); ?></th>
			<th><?php echo __('Category','fcc'); ?></th>
			<th><?php echo __('Points','fcc'); ?></th>
			<th><?php echo __('Edit','fcc'); ?></th>
            </tr>
        </tfoot>
	<tbody>
		<?php
		foreach($results as $k=>$v){
			$meta_key = explode('_',$v->meta_key);
			$id = $meta_key[count($meta_key)-1];
			$sql = "SELECT online FROM {$wpdb->prefix}wp_pro_quiz_question WHERE id = $id";
			$online = $wpdb->get_var($sql);
			//echo $online;
			if($online == 1) {
			$meta_value = explode('_',$v->meta_value);
			$quiz_id = $meta_value[count($meta_value)-1];
			$sql = "SELECT title FROM {$wpdb->prefix}wp_pro_quiz_question WHERE id = $id";
			$title = $wpdb->get_var($sql);
			if($title != ""){
				$sql = "SELECT points FROM {$wpdb->prefix}wp_pro_quiz_question WHERE id = $id";
				$points = $wpdb->get_var($sql);
				$sql = "SELECT category_name FROM {$wpdb->prefix}wp_pro_quiz_question q JOIN {$wpdb->prefix}wp_pro_quiz_category c ON q.category_id = c.category_id WHERE id = $id";
				$category_name = $wpdb->get_var($sql);
			//echo $id;
			?>
		<tr>
			<td><?php echo $title; ?></td>
			<td><?php echo get_the_title($quiz_id); ?></td>
			<td><?php echo ($category_name != '' ? $category_name : '-'); ?></td>
			<td><?php echo $points; ?></td>
			<td><a href = "<?php echo add_query_arg(array("questionid" => $id), $link); ?>"><img src="<?php echo plugins_url('images/edit.png',dirname(dirname( __FILE__ ))); ?>" width="25" height="25"></a></td>
			<!--<td><?php //echo $v->post_modified; ?></td>-->
		</tr>
		<?php
			}
			}
		} ?>
	</tbody>
</table>
<?php 
$quiz_list = array();
$sql = "SELECT post_title FROM {$wpdb->prefix}posts WHERE post_author = ".get_current_user_id()." AND post_type like 'sfwd-quiz' AND post_status IN ('draft','publish')"; 
$results = $wpdb->get_col($sql);
//echo '<pre>';print_R($results);echo '</pre>';
if(!empty($results)){
foreach ($results as $k=>$v){
	if(!in_array($v,$quiz_list)){
	$quiz_list[] = $v;
	}
}
}

$quiz_list[] = '-';
$temp = implode("','",$quiz_list);
$quiz_names = "['".$temp."']";
?>
<script>
jQuery(document).ready(function($){

 $('#wdm_question_list').dataTable({
	 "bSort" : false
 }).columnFilter({
			aoColumns: [ { type: "text" },
				     { type: "select",
					 values: <?php echo $quiz_names; ?>
				},
					 { type: "select" },
				     null,
					 null
				]

		});
});
</script>