<?php
global $wp_query, $wpdb;

$user_id = get_current_user_id();

if ($user_id) {
	$action = 'navigate';
	$post_id = 0;
	$extra = '';

	if (is_single()) {
		$action = 'post';
		$post_id = $wp_query->post->ID;
	}
	if (is_page()) {
		$action = 'page';
		$post_id = $wp_query->post->ID;
	}
	if (is_tag()) {
		$action = 'tag';
		$post_id = get_queried_object()->term_id;
	}
	if (is_search()) {
		$action = 'search';
		$post_id = 0;
		$extra = get_search_query();
	}
	if (is_tax()) {
		$action = 'taxonomy';
		$post_id = get_queried_object()->term_id;
	}
	if (is_archive()) {
		if (is_date()) {
			$action = 'month archive';
			$post_id = 0;

			$year     = get_query_var('year');
			$monthnum = get_query_var('monthnum');
			$day      = get_query_var('day');

			$extra = $day.'/'.$monthnum.'/'.$year;

			if (is_year()) {
				$action = 'year archive';
				$extra = $year;
			}
			if (is_month()) {
				$action = 'month archive';
				$extra = $monthnum;
			}
			if (is_day()) {
				$action = 'day archive';
				$extra = $day.'/'.$monthnum.'/'.$year;
			}

		}
		else {
			$action = 'archive';
			$post_id = get_queried_object()->term_id;
		}
	}
	if (is_front_page()) {
		$action = 'front page';
		$post_id = $wp_query->post->ID;
	}
	if (is_attachment()) {
		$action = 'attachment/media';
		$post_id = $wp_query->post->ID;
	}
	

	$date = time();
	$last_date_difference = '';

	$wp_track_table = $wpdb->prefix . self::$tblname;
	$results = $wpdb->get_results( "SELECT * FROM ".$wp_track_table." WHERE user_id = '".$user_id."' ORDER BY id DESC LIMIT 0, 1", OBJECT );

	if (!$results) {
		$last_date_difference = '';
	}
	else {
		$last_date_difference = $date-$results[0]->date;
		if ($last_date_difference <= 2) {
			return;
		}
	}

	$wpdb->insert( 
		$wp_track_table, 
		array( 
			'user_id' 				=> $user_id, 
			'post_id' 				=> $post_id,
			'action'				=> $action,
			'date'					=> $date,
			'last_date_difference'	=> $last_date_difference,
			'extra'					=> $extra
		)
	);
}