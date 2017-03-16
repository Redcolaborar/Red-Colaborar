<?php
namespace AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader;

require_once(APTOBPLOADER_PLUGIN_DIR . 'Interfaces/iAPtoBPLoader.php' );

class LibAPtoBPLoader implements \AnsPressToBuddyPressLoader\Interfaces\iAPtoBPLoader\iAPtoBPLoader
{
	public $_table_aptobploader_mapping				= 'aptobploader_mapping';
	public $categoryName 		= '';
	public $termID				= array();
	public $questionID			= array();
	public $parentID			= array();
	
	public function install()
	{
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		global $wpdb;
		
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql	= 
			'CREATE TABLE IF NOT EXISTS '. $wpdb->prefix . $this->_table_aptobploader_mapping .'(
			  `aptobploader_id` INT NOT NULL AUTO_INCREMENT,
			  `ap_category` TEXT NULL,
			  `bp_category` TEXT NULL,
			  PRIMARY KEY (`aptobploader_id`))
			ENGINE = InnoDB;

		'.$charset_collate;
		
		dbDelta( $sql );
		
		add_option( 'aptobploader_db_version', APTOBPLOADER_DB_VERSION );
	}
	
	public function uninstall()
	{
		global $wpdb;		
		
		$sql	= 'DROP TABLE IF EXISTS '. $wpdb->prefix . $this->_table_aptobploader_mapping;
		$wpdb->query( $sql );		
	}
	
	public function getBPCategory()
	{
		global $wpdb;
		
		$url 	= $_SERVER['REQUEST_URI'];
		$path	= parse_url($url);
		$path	= explode('/', $path['path']);
		$slug	= end(array_filter($path));
		
		$sql	= "SELECT name FROM {$wpdb->prefix}bp_groups WHERE slug = '$slug'";
		$result = $wpdb->get_row($sql);
		
		$this->categoryName = $result->name;
	}
	
	public function getAPCategory()
	{
		global $wpdb;

		$sql	= "SELECT ap_category FROM {$wpdb->prefix}{$this->_table_aptobploader_mapping} WHERE bp_category = '$this->categoryName'";		
		$result	= $wpdb->get_row($sql);	
		
		if(isset($result) && is_object($result))
		{
			$terms	= explode(",", $result->ap_category);
			$terms	= "'".implode("','",$terms)."'";
			
			$sql	= "SELECT term_id FROM $wpdb->terms WHERE name IN ($terms)";

			$results = $wpdb->get_results($sql);		

			$this->termID = $results;			
		}
	}
	
	public function getQuestionsAPByCategory()
	{
		global $wpdb;
		
		$term_taxonomy_id	= "'".implode("','",$this->termID)."'";		
				
		$sql	= "SELECT object_id FROM $wpdb->term_relationships 
					WHERE term_taxonomy_id IN ($term_taxonomy_id)
					ORDER BY object_id DESC";
		
		$results = $wpdb->get_results($sql);		
				
		foreach($results as $key => $val)
		{
			$this->questionID[] = $val->object_id;
		}
	}	
	
	public function getAnswersAP()
	{
		global $wpdb;
		
		$id		= absint($id);
		$sql	= "SELECT * FROM $wpdb->posts 
					WHERE post_parent = $this->parentID AND post_type='answer' AND post_status='publish' 
					ORDER BY ID DESC";
		$results = $wpdb->get_results($sql);		
		
		return $results;
	}
}