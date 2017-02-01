<?php
//require_once(dirname(__FILE__)."/../nss_xapi.class.php");
//require_once(dirname(__FILE__)."/pv_xapi.class.php");
add_action( 'wp_ajax_nopriv_grassblade_completion_tracking', 'grassblade_grassbladelrs_process_triggers' );
add_action( 'wp_ajax_grassblade_completion_tracking', 'grassblade_grassbladelrs_process_triggers' );

add_action( 'wp_ajax_nopriv_grassblade_xapi_track', 'grassblade_grassbladelrs_xapi_track' );
add_action( 'wp_ajax_grassblade_xapi_track', 'grassblade_grassbladelrs_xapi_track' );

add_action('admin_menu', 'grassblade_grassbladelrs_menu', 1);
function grassblade_grassbladelrs_menu() {
	add_submenu_page("grassblade-lrs-settings", "GrassBlade LRS", "GrassBlade LRS",'manage_options','grassbladelrs-settings', 'grassblade_grassbladelrs_menupage');
}

function grassblade_grassbladelrs_menupage()
{
   //must check that the user has the required capability 
    if (!current_user_can('manage_options'))
    {
      wp_die( __('You do not have sufficient permissions to access this page.') );
    }

    $grassblade_settings = grassblade_settings();
    $endpoint = $grassblade_settings["endpoint"];
    $api_user = $grassblade_settings["user"];
    $api_pass = $grassblade_settings["password"];
    $sso_auth = grassblade_file_get_contents_curl($endpoint."?api_user=".$api_user."&api_pass=".$api_pass."&t=".time());
    if(!empty($_GET['test'])) {
        echo $endpoint."?api_user=".$api_user."&api_pass=".$api_pass."&t=".time();
        print_r($sso_auth);
    }

    $sso_auth = json_decode($sso_auth);
    if(!empty($sso_auth) && !empty($sso_auth->sso_auth_token)) {
        $grassblade_lrs_launch_url = apply_filters("grassblade_lrs_launch_url", $endpoint."?sso_auth_token=".$sso_auth->sso_auth_token, $endpoint, $sso_auth->sso_auth_token);
    	?>
		<div class="wrap">
    	<iframe width="100%" height="1000px" src="<?php echo $grassblade_lrs_launch_url; ?>"></iframe>
    	</div>
    	<?php
    }
    else {
	?>
		<div class=wrap>
		<h2><img style="top: 6px; position: relative;" src="<?php echo plugins_url('img/icon_30x30.png', dirname(dirname(__FILE__))); ?>"/>
		GrassBlade LRS</h2>
		<br>
		<?php echo sprintf(__("Please install %s and configure the API credentials to use this LRS Management Page"), "<a href='http://www.nextsoftwaresolutions.com/grassblade-lrs-experience-api/' target='_blank'>GrassBlade LRS</a>"); ?>
		</div>
	<?php
	}
}

function grassblade_grassbladelrs_xapi_track() {
    if(empty($_REQUEST["grassblade_trigger"]))
        return;

    if(empty($_REQUEST["statement"]) || empty($_REQUEST["objectid"]) || empty($_REQUEST["agent_id"]))
    {
        echo "Incomplete Data";
        exit;
    }
    $statement = stripcslashes($_REQUEST["statement"]);
    $statement_array = json_decode($statement);
    $objectid = urldecode(stripcslashes($_REQUEST["objectid"]));
    $objectid = explode("#", $objectid);
    $objectid = $objectid[0];
    $xapi_content_id = grassblade_xapi_content::get_id_by_activity_id($objectid);
    if(empty( $xapi_content_id)) {
        echo "Activity [".$objectid."] not linked to any content";
        exit;
    }

    $email = rawurldecode(stripcslashes($_REQUEST["agent_id"]));
    $user = get_user_by_grassblade_email($email);
    if(empty($user->ID)) {
        echo "Unknown user: ".$email;
        exit;
    }

    $statement = apply_filters("grassblade_xapi_tracked_pre", $statement, $xapi_content_id, $user);
    if(!empty($statement)) {
       // update_user_meta($user->ID, "completed_".$xapi_content_id, $statement);
        do_action("grassblade_xapi_tracked", $statement, $xapi_content_id, $user);
    }
    echo "Processed ".$xapi_content_id;
}
add_action("parse_request", "grassblade_grassbladelrs_process_triggers");
function grassblade_grassbladelrs_process_triggers() {
    if(empty($_REQUEST["grassblade_trigger"]) || empty($_REQUEST["grassblade_completion_tracking"]))
        return;

    if(empty($_REQUEST["statement"]) || empty($_REQUEST["objectid"]) || empty($_REQUEST["agent_id"]))
    {
        echo "Incomplete Data";
        exit;
    }
    $statement = stripcslashes($_REQUEST["statement"]);
    $statement_array = json_decode($statement);
    $objectid = urldecode(stripcslashes($_REQUEST["objectid"]));
    $xapi_content_id = grassblade_xapi_content::get_id_by_activity_id($objectid);
    if(empty( $xapi_content_id)) {
        echo "Activity [".$objectid."] not linked to any content";
        exit;
    }

    $email = rawurldecode(stripcslashes($_REQUEST["agent_id"]));
    $user = get_user_by_grassblade_email($email);
    if(empty($user->ID)) {
        echo "Unknown user: ".$email;
        exit;
    }

    $statement = apply_filters("grassblade_completed_pre", $statement, $xapi_content_id, $user);
    if(!empty($statement)) {
        update_user_meta($user->ID, "completed_".$xapi_content_id, $statement);
        do_action("grassblade_completed", $statement, $xapi_content_id, $user);
    }
    echo "Processed ".$xapi_content_id;
    exit;
}

add_action("grassblade_completed", "grassblade_lrs_store_completion", 10, 3);
function grassblade_lrs_store_completion($statement_json, $xapi_content_id, $user) {
        $user_id = $user->ID;
        $statement = json_decode($statement_json);
        $result = @$statement->result;

        $score = !empty($statement->result->score->raw)? $statement->result->score->raw:(!empty($statement->result->score->scaled)? $statement->result->score->scaled*100:0);
        $percentage = !empty($statement->result->score->scaled)? $statement->result->score->scaled*100:((!empty($statement->result->score->max) && isset($statement->result->score->raw))? $statement->result->score->raw*100/($statement->result->score->max - @$statement->result->score->min):100);
        $percentage = round($percentage, 2);
        $timespent = isset($statement->result->duration)? grassblade_duration_to_seconds($statement->result->duration):null;
		
        $timestamp = !empty($statement->timestamp)? strtotime($statement->timestamp):time();
        $passed_text = __("Passed", "grassblade");
        $failed_text = __("Failed", "grassblade");
        $completed_text = __("Completed", "grassblade");

        $xapi_content = get_post_meta($xapi_content_id, "xapi_content", true);
		if(isset($xapi_content["passing_percentage"]) && trim($xapi_content["passing_percentage"]) == "")
			$status = "Completed";
		else
		{
        	$pass = ($percentage >= @$xapi_content["passing_percentage"])? 1:0;
			$status = !empty($pass)? "Passed":"Failed";
		}
		$data = array(
				"content_id" => $xapi_content_id,
				"user_id" => $user_id,
				"percentage" => $percentage,
				"status" => $status,
                "score" => $score,
				"statement" => $statement_json,
				"timespent" => $timespent,
				"timestamp" => date("Y-m-d H:i:s", $timestamp),
			);

		global $wpdb;
		$wpdb->insert($wpdb->prefix."grassblade_completions", $data);
}
add_action('delete_user', 'delete_grassblade_data');

function delete_grassblade_data($user_id) {
    global $wpdb;
    if (!empty($user_id) && is_numeric($user_id)) { 
        $wpdb->delete($wpdb->prefix."grassblade_completions", array('user_id' => $user_id));
    }
    return true;
}
/*
add_action('delete_post', 'delete_xapi_content', 10);
function delete_xapi_content($post_id) { 
    if (!empty($post_id) && is_numeric($post_id)) {     
        delete_post_meta($post_id, "xapi_activity_id");
        delete_post_meta($post_id, "xapi_content");
    }
    return true;
}
*/
