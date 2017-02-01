<?php
global $table_prefix, $wpdb;

$wp_track_table = $wpdb->prefix . self::$tblname;
$charset_collate = $wpdb->get_charset_collate();

#Check to see if the table exists already, if not, then create it

if($wpdb->get_var( "show tables like '".$wp_track_table."'" ) != $wp_track_table) {
	require_once(ABSPATH . '/wp-admin/upgrade-functions.php');

	$sql = "CREATE TABLE `".$wp_track_table."` (";
	$sql .= "`id` int(11) NOT NULL AUTO_INCREMENT,";
	$sql .= "PRIMARY KEY (id),";
	$sql .= "`user_id` int(11) NOT NULL,";
	$sql .= "`post_id` int(11) NOT NULL,";
	$sql .= "`action` text NOT NULL,";
	$sql .= "`date` text NOT NULL,";
	$sql .= "`extra` text NOT NULL,";
	$sql .= "`last_date_difference` text NOT NULL";
	$sql .= ") ".$charset_collate.";";
    dbDelta($sql);
}