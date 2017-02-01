<?php
/*
Plugin Name: Recoleccion de Metricas
Plugin URI: http://www.creators.toys/
Description: Recoleccion de metricas para http://redcolaborar.org/.
Version: 1.0
Author: Maurizio Bricola
Author URI: http://www.creators.toys/
*/


class RM_Metricas {

	public static $tblname = 'recoleccion_metricas';

	function __construct() {

		add_action( 'admin_enqueue_scripts', function() {
    		wp_register_style( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
			wp_enqueue_style( 'jquery-ui' );

			if ($_GET['page'] == 'rm_metricas') {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js', array('jquery'), time() );
				wp_enqueue_script( 'jquery-ui-datepicker' );
			}
		} );

		/* Create database */
		register_activation_hook( __FILE__, array( $this, 'create_plugin_database_table' ) );

		/* Log different actions */
		add_action( 'wp_head', array( $this, 'wp_head' ) );

		/* Add menu page */
		add_action( 'admin_menu' , function() {
			add_menu_page(
				__( 'Metricas', 'wnachv' ), 
				__( 'Metricas', 'wnachv' ), 
				'administrator', 
				'rm_metricas', 
				array( $this, 'show_page' ),
				'dashicons-analytics',
				25
			);
		} );
	}

	function create_plugin_database_table() {
	    include_once('create-table.php');
	}

	/* WP HEAD HOOK */
	/* Let's log all the information related to page/post navigation */
	function wp_head() {
		include_once('head-hook.php');
	}

	function show_page() {
		error_reporting( E_STRICT );
		global $wp_query, $wpdb;
		
		$wp_track_table = $wpdb->prefix . self::$tblname;

		/* TRUNCATE DB , only for debugging */
		if (@$_GET['truncate']) {
			$sql_q = "TRUNCATE TABLE ".$wp_track_table;
			$results = $wpdb->get_results( $sql_q, OBJECT );
		}

		/* Let's start the sql query */
		$sql_q = "SELECT * FROM ".$wp_track_table;

		if (isset($_GET['filter_user'])) {
			$wheres = array();

			/* Filter by user */
			if ($_GET['filter_user'] != 0) {
				$wheres[] = "user_id = '".$_GET['filter_user']."'";
			}

			/* Filter by action */
			if ($_GET['filter_action']) {
				$wheres[] = "action = '".$_GET['filter_action']."'";
			}

			/* Dates */
			$wheres[] = "date >= '".strtotime($_GET['filter_from'])."'";
			$wheres[] = "date <= '".strtotime($_GET['filter_to'])."'";

			if (count($wheres) > 0) {
				$sql_q .= " WHERE ".implode(' AND ', $wheres);
			}
		}

		$sql_q .= " ORDER BY id DESC";

		
		/* Get the results */
		$results = $wpdb->get_results( $sql_q, OBJECT );


		/* Let's first set the header and footer texts */
		$header = $footer = array(
			'User',
			'Email',
			'Action',
			'Page / post / search',
			'Date and Time',
			'Session time'
		);

		$total_seconds 		= 0;
		$total_logins 		= 0;
		$results_grouped 	= array();
		$table_tr 			= array();
		$last_user_login = array();

		/* If results are grouped, lets create a temporary array with all the grouped data */
		if (@$_GET['filter_group_by'] != '') {

			/* Grouped by time */
			if ($_GET['filter_group_by'] == 'time') {
				$header[5] = 'Time';
				$header[4] = 'Last login';
				$footer[4] = 'Total time';
				$footer[5] = '';

				

				foreach ($results as $user_row) {
					if (!isset($results_grouped[$user_row->user_id]['time'])) {
						$results_grouped[$user_row->user_id]['time'] = 0;
					}

					if (($user_row->last_date_difference) && ($user_row->last_date_difference <= 3600)) {
						$results_grouped[$user_row->user_id]['time'] = $results_grouped[$user_row->user_id]['time']+$user_row->last_date_difference;
					}

					/* Lets save the last login for this user */
					if (!isset($last_user_login[$user_row->user_id])) {
						$last_user_login[$user_row->user_id] = $user_row->date;
					}
					if ($user_row->date > $last_user_login[$user_row->user_id]) {
						$last_user_login[$user_row->user_id] = $user_row->date;
					}


				}

				foreach ($results_grouped as $user_id => $user_row) {
					$seconds = $user_row['time'];
					$total_seconds = $total_seconds + $seconds;

					$table_tr[] = array(
						'<a href="'.get_edit_user_link($user_id).'">'.get_user_by('id', $user_id)->first_name.' '.get_user_by('id', $user_id)->last_name.' ('.get_user_by('id', $user_id)->user_login.')</a>',
						get_userdata($user_id)->user_email,
						'',
						'',
						$last_user_login[$user_id],
						$seconds,
					);
				}
			}

			/* Grouped by number of logins */
			if ($_GET['filter_group_by'] == 'logins') {
				$header[5] = 'Logins';
				$header[4] = 'Last login';
				$footer[4] = 'Total logins';
				$footer[5] = '';

				foreach ($results as $user_row) {
					if (!isset($results_grouped[$user_row->user_id]['logins'])) {
						$results_grouped[$user_row->user_id]['logins'] = 0;
					}

					if (($user_row->last_date_difference) && ($user_row->last_date_difference <= 3600)) {
						/* nothing */
					}
					else {
						$results_grouped[$user_row->user_id]['logins']++;
					}

					/* Lets save the last login for this user */
					if (!isset($last_user_login[$user_row->user_id])) {
						$last_user_login[$user_row->user_id] = $user_row->date;
					}
					if ($user_row->date > $last_user_login[$user_row->user_id]) {
						$last_user_login[$user_row->user_id] = $user_row->date;
					}
				}

				foreach ($results_grouped as $user_id => $user_row) {
					$logins = $user_row['logins'];
					$total_logins = $total_logins + $logins;

					$table_tr[] = array(
						'<a href="'.get_edit_user_link($user_id).'">'.get_user_by('id', $user_id)->first_name.' '.get_user_by('id', $user_id)->last_name.' ('.get_user_by('id', $user_id)->user_login.')</a>',
						get_userdata($user_id)->user_email,
						'',
						'',
						$last_user_login[$user_id],
						$logins,
					);
				}
			}
		}
		else {
			foreach ($results as $user_row) {

				$user_id = $user_row->user_id;

				/* Set the action TD value */
				switch ($user_row->action) {
					case 'post':
					case 'page':
					case 'front page':
						$action_td = '<a href="'.get_post_permalink($user_row->post_id).'">'.get_the_title($user_row->post_id).'</a>';
						break;
					
					case 'search':
						$action_td = "Searched for:<br><strong>".$user_row->extra."</strong>";
						break;

					case 'tag':
					case 'taxonomy':
					case 'archive':
						$action_td = get_the_category_by_ID($user_row->post_id);
						break;

					case 'month archive':
					case 'date archive':
					case 'day archive':
					case 'year archive':
						$action_td = $user_row->extra;
						break;

					default:
						$action_td = '#'.$user_row->post_id; 
						break;
				}

				if (($user_row->last_date_difference) && ($user_row->last_date_difference <= 3600)) {

					$seconds = $user_row->last_date_difference;
					$total_seconds = $total_seconds + $seconds;

					$time_td = $seconds;
				}
				else {
					$time_td = '<strong>SESSION START</strong>';
				}

				/* Set the line content */
				$table_tr[] = array(
					'<a href="'.get_edit_user_link($user_id).'">'.get_user_by('id', $user_id)->first_name.' '.get_user_by('id', $user_id)->last_name.' ('.get_user_by('id', $user_id)->user_login.')</a>',
					get_userdata($user_id)->user_email,
					$user_row->action,
					$action_td,
					$user_row->date,
					$time_td
				);

			}
		}

		/* Extra filtering */
		$filter_higher_than = $_GET['filter_higher_than'];
		if ($filter_higher_than) {
			foreach ($table_tr as $i => $tr_line) {
				if ($tr_line[5] < $filter_higher_than) {
					/* Lets 1st remove this number from the total column */
					if ($_GET['filter_group_by'] == 'time') {
						$total_seconds = $total_seconds-$tr_line[5];
					}
					if ($_GET['filter_group_by'] == 'logins') {
						$total_logins = $total_logins-$tr_line[5];
					}
					/* Unset item from the table tr list */
					unset($table_tr[$i]);
				}
			}
		}

		/* Ordering */
		$to_sort = $table_tr;
		if ($_GET['filter_order_by'] == 'date') {
			$table_tr = wnachv_array_orderby($to_sort, '4', SORT_DESC);
		}
		if ($_GET['filter_order_by'] == 'logins_time') {
			$table_tr = wnachv_array_orderby($to_sort, '5', SORT_DESC);
		}

		

		/* Set dates and times */
		foreach ($table_tr as $i => $tr_line) {
			/* dates */
			$table_tr[$i][4] = date( 'd/m/Y g:i a', $tr_line[4] );

			/* time */
			if ($_GET['filter_group_by'] != 'logins') {
				if (is_numeric($table_tr[$i][5])) {
					$seconds = $table_tr[$i][5];

					$hours = floor($seconds / 3600);
					$mins = floor($seconds / 60 % 60);
					$secs = floor($seconds % 60);

					$timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
					$table_tr[$i][5] = $timeFormat;
				}
			}
		}

		/* Table Footer */
		if (@$_GET['filter_group_by'] != '') {
			/* Grouped by time */
			if ($_GET['filter_group_by'] == 'time') {
				$hours = floor($total_seconds / 3600);
				$mins = floor($total_seconds / 60 % 60);
				$secs = floor($total_seconds % 60);
				$timeFormat = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
				$footer[5] = $timeFormat;
			}
			/* Grouped by number of logins */
			if ($_GET['filter_group_by'] == 'logins') {
				$footer[5] = $total_logins;
			}
		}

		/* Generate export */
		if ($_GET['generate_file']) {

			$csv_separator = ';';
			if ($_GET['generate_file'] == 'excel') {
				$csv_separator = ',';
			}

			$upload_dir = wp_upload_dir();

			$metricas_dirname = $upload_dir['basedir'].'/metricas';
			$file = '/export_metricas_'.time().'.csv';
			$filename = $metricas_dirname.$file;
			$fileurl = $upload_dir['baseurl'].'/metricas'.$file;

			if (!file_exists($metricas_dirname)) {
			    mkdir($metricas_dirname, 0777, true);
			}

			$fp = fopen($filename, 'w');

			fputcsv($fp, $header, $csv_separator);
			foreach ($table_tr as $tr) {
				/* Strip tags */
				$tr[0] = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $tr[0]);
				$tr[3] = preg_replace('#<a.*?>(.*?)</a>#i', '\1', $tr[3]);
				$tr[5] = strip_tags($tr[5]);

			    fputcsv($fp, $tr, $csv_separator);
			}
			fputcsv($fp, $footer, $csv_separator);

			?>
			<div class="export_file_download" style="margin-bottom: 30px;">
				<h3>Export</h3>
				<a class="button button-secondary" href="<?php echo $fileurl; ?>">Download File</a>
			</div>
			<script>
			jQuery(document).ready(function() {
				jQuery('.export_file_download').detach().insertAfter(jQuery('.metrica-filters'));
			});
			</script>
			<?php
		}

		?>
		<div class="wrap">
			<h1 class="wp-heading-inline">Metricas</h1>

			<?php 
			include_once('filters-box.php'); 
			?>

			<table class="metrica-list wp-list-table widefat fixed striped pages">
				<thead>
					<tr>
						<?php
						foreach ($header as $hitem) {
							?>
							<th><?php echo $hitem; ?></th>
							<?php
						}
						?>
					</tr>
				</thead>

				<tbody id="the-list">
					<?php
					foreach ($table_tr as $tr) {
						?>
						<tr>
							<?php
							foreach ($tr as $td) {
								?>
								<td><?php echo $td; ?></td>
								<?php	
							}
							?>
						</tr>
						<?php
					}
					?>
				</tbody>

				<tfoot>
					<tr>
						<?php
						foreach ($footer as $fitem) {
							?>
							<th><?php echo $fitem; ?></th>
							<?php
						}
						?>
					</tr>
				</tfoot>

			</table>
		</div>
		<?php
	}
}

$metricas = new RM_Metricas();




if (!function_exists('wnachv_array_orderby')) {
	function wnachv_array_orderby()
	{
	    $args = func_get_args();
	    $data = array_shift($args);
	    foreach ($args as $n => $field) {
	        if (is_string($field)) {
	            $tmp = array();
	            foreach ($data as $key => $row)
	                $tmp[$key] = $row[$field];
	            $args[$n] = $tmp;
	            }
	    }
	    $args[] = &$data;
	    call_user_func_array('array_multisort', $args);
	    return array_pop($args);
	}
}