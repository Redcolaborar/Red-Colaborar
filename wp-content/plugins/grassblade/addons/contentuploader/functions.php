<?php

class grassblade_xapi_content {
	public $debug = false;
	function __construct() {
	}
	function run() {
		add_action( 'init', array($this, 'grassblade_xapi_post_content') );
		add_action( 'admin_head', array($this, 'grassblade_xapi_portfolio_icons') );	
		add_action( 'add_meta_boxes', array($this, 'gb_xapi_content_box') );
		add_action( 'save_post', array($this, 'gb_xapi_content_box_save' ));
		add_action( 'post_edit_form_tag', array($this, 'grassblade_xapi_post_edit_form_tag'));
		add_action( 'the_content', array($this, 'add_xapi_shortcode' ));
		
		add_filter( 'grassblade_shortcode_return', array($this, 'show_results'), 10, 4);
		add_shortcode( 'grassblade_user_score', array($this, 'user_score'));
	}
	function upload_mimes ( $existing_mimes=array() ) {
	    // add your extension to the mimes array as below
	    $existing_mimes['zip'] = 'application/zip';
	    $existing_mimes['gz'] = 'application/x-gzip';
	    return $existing_mimes;
	}	
	function grassblade_upload_dir($upload) {
		global $post;
		$upload['subdir']	= '/grassblade';
		$upload['path']		=  $upload['basedir'] . $upload['subdir'];
		
		$upload['url']		= $upload['baseurl'] . $upload['subdir'];
		return $upload;
	}

	function grassblade_xapi_post_content() {
		$labels = array(
			'name'               => _x( 'xAPI Content', 'post type general name' ),
			'singular_name'      => _x( 'xAPI Content', 'post type singular name' ),
			'add_new'            => _x( 'Add New', 'book' ),
			'add_new_item'       => __( 'Add New xAPI Content' ),
			'edit_item'          => __( 'Edit xAPI Content' ),
			'new_item'           => __( 'New xAPI Content' ),
			'all_items'          => __( 'All xAPI Content' ),
			'view_item'          => __( 'View xAPI Content' ),
			'search_items'       => __( 'Search xAPI Content' ),
			'not_found'          => __( 'No xAPI Content found' ),
			'not_found_in_trash' => __( 'No xAPI Content found in the Trash' ), 
			'parent_item_colon'  => '',
			'menu_name'          => 'xAPI Content'
		);
		$slug = grassblade_settings("url_slug");
		$args = array(
			'labels'        => $labels,
			'description'   => 'Holds our GrassBlade xAPI Content',
			'public'        => true,
			'menu_position' => 5,
			'supports'      => array( 'title',  'editor'),
			'has_archive'   => false,
			'taxonomies' => array('category'),
			'exclude_from_search' => true,
			'publicly_queryable' => true,
			'menu_icon' => plugins_url('img/button-15.png', dirname(dirname(__FILE__))),
			'rewrite' 	=> array("slug" => $slug),
		);
		$args = apply_filters("gb_xapi_content_post_args", $args);
		register_post_type( 'gb_xapi_content', $args );	
		// wp_enqueue_media();
	}
	public function get_name_by_activity_id($activity_id) {
		global $wpdb;
		$post_id = $this->get_id_by_activity_id($activity_id);
		$xpost = get_post($post_id);

		if(!empty($post_id) && isset($xpost->post_title))
		return $xpost->post_title;
		else
		return "";
	}
	public function get_categories() {
		$args = array(
			'type'                     => 'gb_xapi_content',
			'child_of'                 => 0,
			'parent'                   => '',
			'orderby'                  => 'name',
			'order'                    => 'ASC',
			'hide_empty'               => 1,
			'hierarchical'             => 1,
			'exclude'                  => '',
			'include'                  => '',
			'number'                   => '',
			'taxonomy'                 => apply_filters('grassblade_content_taxonomies', 
														array('category')
														),
			'pad_counts'               => false ); 
		$categories = get_categories($args);
		return $this->hierarchy($categories);
	}
	function hierarchy($categories)
	{
		$catpool = $categories;
		$hierarchy = array();
		$num = count($catpool);
		foreach($catpool as $i => $cati)
		{
			$categories_withcatid[$catpool[$i]->cat_ID] = $catpool[$i];
			
			foreach($catpool as $j => $catj)
			{
				$catid = $catpool[$j]->cat_ID;
				$parent = $catpool[$j]->category_parent;
				$hierarchy[$parent][$catid] = 1;
			}
		}
		return  $this->hierarchy_rec(0, $hierarchy, $categories_withcatid);
	}
	function depth_spaces($depth) {
		$d = '';
		$i = $depth;
		while($i > 1) {
			$d .= '&nbsp;&nbsp;&nbsp;';
			$i--;
		}
		return $d;
	}
	function hierarchy_rec($find, $hierarchy,$categories, $return = array(), $depth = 0) {
		$cat_name = empty($categories[$find]->name)? "":$categories[$find]->name;
		
		if(empty($hierarchy[$find])) {
			$categories[$find]->name = $this->depth_spaces($depth).$cat_name;
			$return[] = $categories[$find];
			return $return;
		}
		else
		{
			$ret = "";
			if(!empty($categories[$find]->term_id)) {
				$categories[$find]->name = $this->depth_spaces($depth).$cat_name;
				$return[] = $categories[$find];
			}
			
			foreach($hierarchy[$find] as $k => $v)
			{
				$return = $this->hierarchy_rec($k, $hierarchy,$categories, $return, $depth + 1);
			}
			return $return;
		}
	}	
	public function get_category_selector() {
		$categories = $this->get_categories();
		$ret = '';
		$ret .= '<script>
					function xapi_content_report_change(cat) {
						jQuery(".xapi_category_all").hide();
						jQuery(".xapi_category_" + cat.value).show();
					}
				</script>';
		$ret .= "<select onChange='xapi_content_report_change(this);'>";
		$ret .= 	"<option value='all'>All</option>";
		foreach($categories as $cat) {
			$ret .= "<option value='$cat->cat_ID'>$cat->name</option>";
		}
		$ret .= "</select>";
		return $ret;
	}
	public function get_categories_by_activity_id($activity_id) {
		global $wpdb;
		$post_id = $this->get_id_by_activity_id($activity_id);

		if(empty($post_id))
			return "";
		
		return wp_get_post_categories( $post_id );
	}
	public function get_category_classes_by_activity_id($activity_id) {
		$categories = $this->get_categories_by_activity_id($activity_id);
		$r = "";
		if(!empty($categories))
		foreach($categories as $cat) {
			$r .= " xapi_category_".$cat;
		}
		return $r;
	}
	public function get_id_by_activity_id($activity_id) {
		global $wpdb;
		$post_ids = $wpdb->get_col($wpdb->prepare("
					SELECT post_id FROM $wpdb->postmeta 
					WHERE meta_key = 'xapi_activity_id'
					AND meta_value ='%s'
					", $activity_id));
		
		if(empty($post_ids) || count($post_ids) == 0)
			return 0;

		foreach ($post_ids as $post_id) {
			$post = get_post($post_id);
			if(!empty($post)) {
				if($post->post_status == "publish")
					return $post->ID;

				$existing_post = $post;
			}
		}
		if(!empty($existing_post->ID))
			return $existing_post->ID;
		else
			return 0;
	}
	function add_xapi_shortcode($content) {
		global $post;
		if(!empty($post->post_type) && $post->post_type == "gb_xapi_content")
		{
			$xapi_content = $this->get_params($post->ID);
			if(!empty($xapi_content['show_here']) || !empty($_GET["xapi_preview"]) && current_user_can("edit_post", $post->ID)) {
				if(strpos($content, "[grassblade]") === false)
				$content .= "[grassblade id='".$post->ID."']";
				else
				$content = str_replace("[grassblade]", "[grassblade id='".$post->ID."']", $content);
			}
		}
		return $content;
	}
	
	function grassblade_xapi_portfolio_icons() {
		?>
		<style type="text/css" media="screen">
			.icon32-posts-gb_xapi_content {
				background: url(<?php echo plugins_url('img/icon_30x30.png', dirname(dirname(__FILE__))) ?>) no-repeat 6px 6px !important;
			}
		</style>
	<?php }
	
	function debug($msg) {
		$original_log_errors = ini_get('log_errors');
		$original_error_log = ini_get('error_log');
		ini_set('log_errors', true);
		ini_set('error_log', dirname(__FILE__).DIRECTORY_SEPARATOR.'debug.log');
		
		global $processing_id;
		if(empty($processing_id))
		$processing_id	= time();
		
		if(isset($_GET['debug']) || !empty($this->debug))
		
		error_log("[$processing_id] ".print_r($msg, true)); //Comment This line to stop logging debug messages.
		
		ini_set('log_errors', $original_log_errors);
		ini_set('error_log', $original_error_log);		
	}
	function upload_limit() {
		$upload_size_unit = $max_upload_size = wp_max_upload_size();
        $sizes = array( 'KB', 'MB', 'GB' );

        for ( $u = -1; $upload_size_unit > 1024 && $u < count( $sizes ) - 1; $u++ ) {
                $upload_size_unit /= 1024;
        }

        if ( $u < 0 ) {
                $upload_size_unit = 0;
                $u = 0;
        } else {
                $upload_size_unit = (int) $upload_size_unit;
        }
        return $upload_size_unit.$sizes[$u];
	}
		/**
		* defines the fields used in the plugin
		*
		* @since 
		* @return void
		*/
	function define_fields($params = array()) {
		global $grassblade_xapi_companion;

		$grassblade_settings = grassblade_settings();

	    $grassblade_tincan_endpoint = $grassblade_settings["endpoint"];
	    $grassblade_tincan_user = $grassblade_settings["user"];
	    $grassblade_tincan_password = $grassblade_settings["password"];
		$grassblade_tincan_track_guest = intval($grassblade_settings["track_guest"]);

		$grassblade_tincan_width = $grassblade_settings["width"];
		$grassblade_tincan_height = $grassblade_settings["height"];
		$grassblade_tincan_version = $grassblade_settings["version"];
		$secure_token_options = $grassblade_xapi_companion->secure_token_options;
		$grassblade_tincan_secure_tokens = $secure_token_options[$grassblade_settings["secure_tokens"]];
		//$grassblade_tincan_guest = get_option( 'grassblade_tincan_guest');	

		// define the product metadata fields used by this plugin
		$versions = array(
					'1.0' => '1.0',
					'0.95' => '0.95',
					'0.9' => '0.9',
					'none' => 'Not XAPI',
					'' => 'Use Default'
				);
		$target = array(
					'' => 'In Page',
					'_blank' => 'Link to open in New Window',
					'_self' => 'Link to open in Same Window',
					'lightbox' => 'Link to open in a Popup Lightbox',
				);
		$button_type = array(
					'0' => __('Text Link', "grassblade"),
					'1' => __('Button Image', "grassblade"),
				);
		$guest = array(
					'' => 'Use Default',
					'1' => 'Allow Guests',
					'0' => 'Require Login',
				);

		$h5p_arr = array();
		if(defined("GB_H5P_SUPPORT_ENABLED")) {
			if (current_user_can( 'manage_options' )) { 
				$content_query = new H5PContentQuery(array('id', 'title'));
			}else{ 
				$content_query = new H5PContentQuery(array('id', 'title'), NULL, NULL, NULL, NULL, array(array('user_id', get_current_user_id())));
			}
			$h5p_results = $content_query->get_rows(); 
			$h5p_options = array("0" => "Select");
			if(!empty($h5p_results)) {
				foreach($h5p_results as $result) {
					$h5p_options[$result->id] = $result->title;
				}
			}		
			$h5p_arr = array( 'id' => 'h5p_content', 'label' => __( 'H5P Content', 'grassblade' ), 'title' => __( 'H5P Content', 'grassblade' ), 'placeholder' => '', 'type' => 'select', 'values'=> $h5p_options, 'never_hide' => true ,'help' => '');		
		}else{ 
			$h5p_arr = array( 'id' => 'h5p_content', 'label' => __( 'H5P Content', 'grassblade' ), 'title' => __( 'H5P Content', 'grassblade' ), 'placeholder' => '', 'type' => 'html', 'html' => sprintf(__('Please install %s to select an H5P content. You can create interactive HTML5 Tin Can content using this free plugin.', 'grassblade'), "<a href='https://h5p.org/wordpress' target='_blank'>".__("H5P Plugin", "grassblade")."</a>")."<br><br>", 'values'=> '', 'never_hide' => true );
		} 
		
		$upload_limit = $this->upload_limit();
		$this->fields = array(
			array( 'id' => 'selector', 'label' => '', 'title' => '', 'html' => $this->content_selector(), 'placeholder' => '', 'type' => 'html', 'values'=> '', 'never_hide' => true ,'help' => ''),
			array( 'id' => 'src', 'label' => __( 'Content Url', 'grassblade' ), 'title' => __( 'Content Url', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Set the content launch url, or uploading a content will automatically generate it. Needs to be a valid URL. Required', 'grassblade')),
			array( 'id' => 'xapi_content', 'label' => __( 'Upload Content', 'grassblade' ), 'title' => __( 'Content Url', 'grassblade' ), 'placeholder' => '', 'type' => 'file', 'values'=> '', 'never_hide' => true ,'help' => sprintf(__( 'Your current server upload limit: %s %s', 'grassblade'), $upload_limit, "<a href='http://www.nextsoftwaresolutions.com/increasing-file-upload-limit/' target='_blank'>".__("Help?", "grassblade")."</a>" )),
			array( 'id' => 'dropbox', 'label' => __( 'DropBox Upload', 'grassblade' ), 'title' => __( 'DropBox Upload', 'grassblade' ), 'placeholder' => '', 'type' => 'html', 'html' => $this->dropbox_chooser(), 'values'=> '', 'never_hide' => true ,'help' => __( 'Upload the file to your server from your Dropbox.', 'grassblade')),
			array( 'id' => 'video', 'label' => __( 'Video URL', 'grassblade' ), 'title' => __( 'Video URL', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => 'YouTube or Vimeo URL'),
			$h5p_arr,
			array( 'id' => 'activity_id', 'label' => __( 'Activity ID', 'grassblade' ), 'title' => __( 'A Unique URL', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => sprintf(__('Recommended to leave blank when uploading content, and use the one generated by content, or Set your own activity id, or %s to generate it.  Needs to be a unique URL. Required. Original Activity ID: %s', 'grassblade'), '<a href="#" onClick="document.getElementById(\'activity_id\').value = jQuery(\'#sample-permalink\').text()? jQuery(\'#sample-permalink\').text().replace(\'…\',\'\'):\'[GENERATE]\'; if(jQuery(\'#activity_id\').val() == \'[GENERATE]\') jQuery(\'#activity_id\').attr(\'readonly\', \'readonly\'); return false;">'.__('click here', 'grassblade').'</a>', @$params['original_activity_id'])),
			array( 'id' => 'target', 'label' => __( 'Where to launch this content?', 'grassblade' ), 'title' => __( 'Where to launch this content?', 'grassblade' ), 'placeholder' => 'Width', 'type' => 'select', 'values'=> $target, 'never_hide' => true ,'help' => __( 'Default: In Page', 'grassblade')),
			array( 'id' => 'button_type', 'label' => __( 'Button Type?', 'grassblade' ), 'title' => __( 'Button Type?', 'grassblade' ),  'type' => 'select', 'values'=> $button_type, 'never_hide' => true ,'help' => ''),
			array( 'id' => 'text', 'label' => __( 'Link text if opening in new window?', 'grassblade' ), 'title' => __( 'Link text if opening in new window?', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Default: Launch', 'grassblade')),
			array( 'id' => 'link_button_image','label' => __( 'Link Button Image?', 'grassblade' ), 'title' => __( 'Link Button Image?', 'grassblade' ), 'placeholder' => '', 'type' => 'image-selector', 'value'=> 'Select', 'never_hide' => true ,'help' => __( 'Select the image you want to show as a button.', 'grassblade')),
			array( 'id' => 'completion_tracking', 'label' => __( 'Completion Tracking', 'grassblade' ), 'title' => __( 'Completion Trigger', 'grassblade' ), 'placeholder' => '', 'type' => 'checkbox', 'values'=> '', 'never_hide' => true ,'help' => sprintf(__( 'Enable to allow completion tracking. You need to use the metabox dropdown to add content, and use %s. ', 'grassblade'), "<a href='http://www.nextsoftwaresolutions.com/grassblade-lrs-experience-api/' target='_blank'>GrassBlade LRS</a>"). "<a href='http://www.nextsoftwaresolutions.com/using-grassblade-completion-tracking-with-learndash/' target='_blank'>".__("Setup Help?", "grassblade")." </a>.". $this->test_completion_tracking()),
			array( 'id' => 'passing_percentage', 'label' => __( 'Passing Percentage', 'grassblade' ), 'title' => __( 'Passing Percentage', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Passing Percentage', 'grassblade')),
			array( 'id' => 'show_results', 'label' => __( 'Show Results to Users', 'grassblade' ), 'title' => __( 'Show Results to Users', 'grassblade' ), 'placeholder' => '', 'type' => 'checkbox', 'values'=> '', 'never_hide' => true ,'help' => __('Enable to show score to users below the xAPI Content.', 'grassblade')), 
			array( 'id' => 'show_here', 'label' => __( 'I want to show the content on this page.', 'grassblade' ), 'title' => __( 'I want to show the content on this page.', 'grassblade' ), 'placeholder' => '', 'type' => 'checkbox', 'values'=> '', 'never_hide' => true ,'help' => __( 'Check to show the content on this page. Click View above to see.', 'grassblade')),

			array( 'id' => "global_settings", 'label' => __("Override Global Settings", "grassblade"), "type" => "html", "subtype" => "field_group_start"),
			array( 'id' => 'width', 'label' => __( 'Width', 'grassblade' ), 'title' => __( 'Width', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Global', 'grassblade').": ".$grassblade_tincan_width),
			array( 'id' => 'height', 'label' => __( 'Height', 'grassblade' ), 'title' => __( 'Height', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Global', 'grassblade').": ".$grassblade_tincan_height),
			array( 'id' => 'version', 'label' => __( 'Version', 'grassblade' ), 'title' => __( 'Version', 'grassblade' ), 'placeholder' => '', 'type' => 'select', 'values'=> $versions, 'never_hide' => true ,'help' => __( 'Set the version of xAPI the content uses. ', 'grassblade'). __( 'Global', 'grassblade').": ".$versions[$grassblade_tincan_version]),
			array( 'id' => 'guest', 'label' => __( 'Guest Access', 'grassblade' ), 'title' => __( 'Guest Access', 'grassblade' ), 'placeholder' => '', 'type' => 'select', 'values'=> $guest, 'never_hide' => true ,'help' => __( 'Allow not logged in user to access content. Global: ', 'grassblade').$guest[$grassblade_tincan_track_guest]),
			array( 'id' => 'registration', 'label' => __( 'Registration', 'grassblade' ), 'title' => __( 'Registration', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Defaults to "36fc1ee0-2849-4bb9-b697-71cd4cad1b6e", type in the UUID for a specifc fixed UUID. Or "auto" if you want a unique UUID generated for a group of activities for every launch. Hence every attempt is assumed to be unique, as long as the page is refreshed before re-launch.', 'grassblade')),
			array( 'id' => 'endpoint', 'label' => __( 'Endpoint', 'grassblade' ), 'title' => __( 'Endpoint', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Global', 'grassblade').": ".$grassblade_tincan_endpoint),
			array( 'id' => 'user', 'label' => __( 'User', 'grassblade' ), 'title' => __( 'User', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Global', 'grassblade').": ".$grassblade_tincan_user),
			array( 'id' => 'pass', 'label' => __( 'Password', 'grassblade' ), 'title' => __( 'Password', 'grassblade' ), 'placeholder' => '', 'type' => 'text', 'values'=> '', 'never_hide' => true ,'help' => __( 'Global', 'grassblade').": ".$grassblade_tincan_password),
			array( 'id' => 'secure_tokens', 'label' => __( 'Secure Tokens', 'grassblade' ),   'placeholder' => '', 'type' => 'select', 'values'=> $secure_token_options, 'never_hide' => true ,'help' => __( 'Generates secure random tokens when launching xAPI Content.', 'grassblade')." ".__("Global", "grassblade").": ".$grassblade_tincan_secure_tokens ),
			array( 'id' => "global_settings_end", "type" => "html", "subtype" => "field_group_end"),
		);
	}
	function show_results($return, $params, $shortcode_atts, $attr) {
		extract($shortcode_atts);

		if(empty($src) || empty($show_results) || !in_array($target, array("iframe", "_blank", "_self", "lightbox")))
			return $return;

		$user_id = get_current_user_id();
		$content_id = $this->get_id_by_activity_id($activity_id);

		if(empty($content_id))
			return $return;

		$scores = $this->get_scores($user_id, $content_id);

		foreach ($scores as $key => $score) {
			$row = array();
			$row[__("Date", "grassblade")] = date("F j, Y H:i:s", strtotime($score["timestamp"]));
			$row[__("Score", "grassblade")] = $score["score"];
			$row[__("Status", "grassblade")] = __( $score["status"] , "grassblade");
			$row[__("Timespent", "grassblade")] = grassblade_seconds_to_time($score["timespent"]);
			$scores[$key] = $row;
		}
		ob_start();
		?>
		<div class="grassblade_show_results">
			<div><strong><?php _e("Your Results:", "grassblade"); ?></strong></div>
			<?php
			include_once(dirname(__FILE__)."/../nss_arraytotable.class.php");
			$scores = apply_filters("grassblade_your_scores", $scores, $user_id, $content_id);
			$ArrayToTable = new NSS_ArrayToTable($scores);
			$ArrayToTable->show();
			?>
		</div>
		<?php
		$html = ob_get_clean();
		$html = apply_filters("gb_show_results", $html, $scores, $user_id, $content_id);
		return $return.$html;

	}
	function get_scores($user_id, $content_id) {
		global $wpdb;
		$scores = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."grassblade_completions WHERE user_id = '%d' AND content_id = '%d' ORDER BY id DESC", $user_id, $content_id), ARRAY_A);
		$scores = apply_filters("gb_get_scores", $scores, $user_id, $content_id);
		return $scores;
	}
	function user_score($attr) {
		global $wpdb;

		$shortcode_defaults = array(
	 		'user_id' 		=> null,
	 		'content_id'	=> null,
	 		'show'			=> 'total_score',
	 		'add'			=> null,
		);
		$shortcode_defaults = apply_filters("grassblade_user_score_shortcode_defaults", $shortcode_defaults);

		$shortcode_atts = shortcode_atts ( $shortcode_defaults, $attr);
		$shortcode_atts = apply_filters("grassblade_user_score_shortcode_atts", $shortcode_atts);

		extract($shortcode_atts);

		if(empty($user_id))
		{
			$user = wp_get_current_user();
			$user_id = $user->ID;
			if(empty($user_id))
				return '';
		}

		$where_content_id = (empty($content_id))? " AND 1=1 ":$wpdb->prepare(" AND content_id = %d ", $content_id);			
		
		switch ($show) {
			case 'badgeos_points':
				$shortcode_atts["add"] = "badgeos_points";
				$return = 0;
				break;
			case 'total_score':
				$results = $wpdb->get_results( $wpdb->prepare("SELECT content_id, score, timestamp FROM `{$wpdb->prefix}grassblade_completions` where user_id = %d {$where_content_id} ORDER BY content_id ASC, timestamp ASC", $user_id), ARRAY_A);

				if(empty($results))
					$return = 0;
				else
				{
					$content_ids = array();
					$total_score = 0;
					foreach ($results as $key => $result) {
						$content_id = $result["content_id"];
						if(empty($content_ids[$content_id]))
						{
							$content_ids[$content_id] = $result["score"];
							$total_score += $result["score"];
						}
					}
					//echo "<pre>"; print_r($content_ids); echo "</pre>";
					$return = $total_score;
				}
				break;
			case 'average_percentage':
				$results = $wpdb->get_results( $wpdb->prepare("SELECT content_id, percentage, timestamp FROM `{$wpdb->prefix}grassblade_completions` where user_id = %d {$where_content_id} ORDER BY content_id ASC, timestamp ASC", $user_id), ARRAY_A);
				//echo "<pre>"; print_r($results); echo "</pre>";

				if(empty($results))
					$return = 0;
				else
				{
					$content_ids = array();
					$total_percentage = 0;
					foreach ($results as $key => $result) {
						$content_id = $result["content_id"];
						if(empty($content_ids[$content_id]))
						{
							$content_ids[$content_id] = $result["percentage"];
							$total_percentage += $result["percentage"];
						}
					}
					//echo "<pre>"; print_r($content_ids); echo "</pre>";					
					$average_percentage = empty($content_ids)? 0:$total_percentage/count($content_ids);
					$return = $average_percentage;
				}
				break;
			default:
				$return = 0;
				break;
		}

		$return = number_format($return);
		return apply_filters("grassblade_user_score", $return, $attr, $shortcode_atts);
	}
	function test_completion_tracking() {
		global $post;
		$test_completion_tracking =  "<a href='".admin_url("post.php?action=edit&test_completion_tracking=1&testing_time=3&post=".$post->ID)."'>".__("Test Setup")." </a>";
		if(empty($_GET["test_completion_tracking"]))
			return $test_completion_tracking;

		$errors = array();
		$error_html = '';
		$success_html = '';
		$xapi_content = $this->get_params($post->ID);
		if(empty($xapi_content["completion_tracking"])) {
			$errors[] = __("Completion Tracking is not enabled.", "grassblade");
		}
		if(!empty($xapi_content["activity_id"]))
		{
			$testing_time = !empty($_GET["testing_time"])? $_GET["testing_time"]:3;
			$args = array(
					"activity"  => $xapi_content["activity_id"],
					"verb"		=> "http://adlnet.gov/expapi/verbs/passed",
					"since"		=> date(DATE_ATOM, time() - $testing_time*3600),
					"email"		=> "none"
				);
			$statements = get_statement($args);
			if(empty($statements))
			{
				$args["verb"] = "http://adlnet.gov/expapi/verbs/completed";
				$statements = get_statement($args);
			}

			if(empty($statements)) {
				$errors[] = sprintf(__("We haven't seen any statements in the LRS with 'completed' or 'passed' verbs for Activity/Object ID: <u>%s</u> in past %d hours. You might need to attempt the entire content once, or fix your content.", "grassblade"), $xapi_content["activity_id"], $testing_time). " " .sprintf(__("%s to check for past %d hours.", "grassblade"), "<a href='".admin_url("post.php?action=edit&test_completion_tracking=1&testing_time=".($testing_time*2)."&post=".$post->ID)."'>".__("Click here")." </a>", $testing_time*2);
				
				if(!empty($xapi_content["original_activity_id"]) && $xapi_content["original_activity_id"] != $xapi_content["activity_id"]) {
					$args = array(
							"activity"  => $xapi_content["original_activity_id"],
							"verb"		=> "http://adlnet.gov/expapi/verbs/passed",
							"since"		=> date(DATE_ATOM, time() - $testing_time*3600),
							"email"		=> "none"
						);
					$statements = get_statement($args);

					$original_activity_id_error_message =  sprintf(__("We have found '[verb]' statements in the LRS for the content generated Activity/Object ID: <u>%s</u>. This indicates that your content doesn't accept modification of Activity ID, please change the Activity ID to  <u>%s</u>. Also, please leave the field blank when uploading new content so that content generated Activity ID is configured automatically.", "grassblade"), $xapi_content["original_activity_id"], $xapi_content["original_activity_id"]);
					if(!empty($statements)) {
						$errors[] = str_replace("[verb]", "passed", $original_activity_id_error_message);
					}
					else
					{
						$args["verb"] = "http://adlnet.gov/expapi/verbs/completed";
						$statements = get_statement($args);
						if(!empty($statements)) {
							$errors[] = str_replace("[verb]", "completed", $original_activity_id_error_message);
						}
					}
				}
			}
		}
		else {
			$errors[] = __("Empty Activity ID", "grassblade");
		}

		$posts = grassblade_get_post_with_content($post->ID);

		if(!empty($posts) && empty($errors)) 
		{
			$success_html = "<div class='updated'>".__("Everything looks good here.", "grassblade")."</div>";
		}
		
		
		$success_html .= "<div class='updated'>".sprintf(__("If the issue persists try these steps: <br>1. Make sure you have setup the required Triggers on the LRS using this url: <u>%s</u>. <br>2. %s to read the setup guide again.", "grassblade"), admin_url("admin-ajax.php?action=grassblade_completion_tracking"), "<a href='http://www.nextsoftwaresolutions.com/using-grassblade-completion-tracking-with-learndash/' target='_blank'>Click here</a>")."</div>";

		if(!empty($errors))
		{
			foreach ($errors as $key => $error_text) {
				$error_html .= "<div class='error'>".$error_text."</div>";
			}
		}
		return $test_completion_tracking.$error_html.$success_html;
	}
	function dropbox_chooser() {
		$grassblade_settings = grassblade_settings();
		$grassblade_dropbox_app_key = $grassblade_settings['dropbox_app_key'];
		
		if(empty($grassblade_dropbox_app_key))
		return sprintf(__("Please %s to configure your Dropbox App Key"), "<a href='".admin_url("admin.php?page=grassblade-lrs-settings")."' target='_blank'>".__("click here")."</a>");
		else
		return '<script type="text/javascript" src="https://www.dropbox.com/static/api/1/dropins.js" id="dropboxjs" data-app-key="'.$grassblade_dropbox_app_key.'"></script>
		<input type="dropbox-chooser" name="dropbox-file" style="visibility: hidden;" data-link-type="direct"/>
		';
	}
	function content_selector() {
		return '<h2 class="nav-tab-wrapper gb-content-selector">
			<a class="nav-tab nav-tab-content-url" href="#" >Content URL</a>
			<a class="nav-tab nav-tab-video" href="#" >Video</a>		
			<a class="nav-tab nav-tab-h5p" href="#" >H5P</a>	
			<a class="nav-tab nav-tab-upload" href="#" >Upload</a>			
			<a class="nav-tab nav-tab-dropbox" href="#" >Dropbox</a>
		</h2>';
	}
	function form() {
			if(isset($_GET["test"]))
				update_option('grassblade_admin_errors', 'Upload Test: '.$this->upload_tests());

			global $post;
			$data = $this->get_params($post->ID);//get_post_meta( $post->ID, 'xapi_content', true );
			
			$this->define_fields($data);
		?>
			<div id="grassblade_xapi_content_form"><table width="100%">
			<?php
				foreach ($this->fields as $field) {
					if($field["type"] == "html" && @$field["subtype"] == "field_group_start") {
						echo "<tr><td colspan='2'  class='grassblade_field_group'>";
						echo "<div class='grassblade_field_group_label'><div class='dashicons dashicons-arrow-down-alt2'></div><span>".$field["label"]."</span></div>";
						echo "<div class='grassblade_field_group_fields' style='".@$field["style"]."'><table width='100%'>";
						continue;
					}
					if($field["type"] == "html" && @$field["subtype"] == "field_group_end") {
						echo "</table></div></td></tr>";
						continue;
					}

					$value = isset($data[$field['id']])? $data[$field['id']]:'';
					echo '<tr id="field-'.$field['id'].'"><td width="20%" valign="top"><label for="'.$field['id'].'">'.$field['label'].'</label></td><td width="100%">';
					switch ($field['type']) {
						case 'html' :
							echo $field["html"];
						break;
						case 'text' :
							echo '<input  style="width:80%" type="text"  id="'.$field['id'].'" name="'.$field['id'].'" value="'.$value.'" placeholder="'.$field['placeholder'].'"/>';
						break;
						case 'image-selector' :
							echo '<img class="gb_upload-src" src="'.$value.'"  id="'.$field['id'].'-src" style="max-width: 150px; max-height: 50px;"/>';
							echo '<input class="gb_upload-url" type="hidden"  id="'.$field['id'].'-url" name="'.$field['id'].'" value="'.$value.'"/>';
							echo '<input class="button button-secondary gb_upload_button" type="button"  id="'.$field['id'].'" value="'.$field['value'].'"  style="width: 100px;display:block"/>';
						break;
						case 'file' :
							echo '<input  style="width:80%" type="file"  id="'.$field['id'].'" name="'.$field['id'].'" value="'.$value.'" placeholder="'.$field['placeholder'].'"/>';
						break;
						case 'number' :
							echo '<input  style="width:80%" type="number" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$value.'" placeholder="'.$field['placeholder'].'"/>';
						break;
						case 'textarea' :
							echo '<textarea   style="width:80%"  id="'.$field['id'].'" name="'.$field['id'].'" placeholder="'.$field['placeholder'].'">'.$value.'</textarea>';
						break;
						case 'checkbox' :
							$checked = !empty($value) ? ' checked=checked' : '';
							echo '<input type="checkbox" id="'.$field['id'].'" name="'.$field['id'].'" value="on"'.$checked.'>';
						break;
						case 'select' :
							echo '<select id="'.$field['id'].'" name="'.$field['id'].'">';
							foreach ($field['values'] as $k => $v) :
								$selected = ($value == $k && $value != '') ? ' selected="selected"' : '';
								echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
							endforeach;
							echo '</select>';
						break;
						case 'select-multiple':
						
							echo '<select id="'.$field['id'].'" name="'.$field['id'].'[]" multiple="multiple">';

							foreach ($field['values'] as $k => $v) :
								if(!is_array($value)) $value = (array) $value;
								$selected = (in_array($k, $value)) ? ' selected="selected"' : '';
								echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
							endforeach;
							echo '</select>';

					}
					if(!empty($field['help'])) {
						echo '<br><small>'.$field['help'].'</small><br><br>';
						echo '</td></tr>';
					}
				}
				?>
				</table>
				<br>
			</div>
		<?php
	
	}	
	function gb_xapi_content_box() {
		add_meta_box( 
			'gb_xapi_content_box',
			__( 'xAPI Content Details', 'grassblade' ),
			array($this, 'gb_xapi_content_box_content'),
			'gb_xapi_content',
			'advanced',
			'high'
		);
	}
	function set_params($post_id, $params) {
		if(!empty($params["passing_percentage"]))
			$params["passing_percentage"] = number_format(floatval($params["passing_percentage"]), 2);
//echo "<pre>";print_r($params); echo "</pre>";
		update_post_meta( $post_id, 'xapi_content', $params);

		if(isset($params['activity_id']))
		update_post_meta( $post_id, 'xapi_activity_id', $params['activity_id']);
		// h5p content
		if(isset($params['h5p_content_id']))
			update_post_meta( $post_id, 'h5p_content_id', $params['h5p_content_id']);
	}
	static function get_params($post_id) {
		$xapi_content = get_post_meta( $post_id, 'xapi_content', true);
		$object_context = (isset($this) && get_class($this) == __CLASS__);
		if(!isset($xapi_content['version'])){  //For Version older than V0.5
			$xapi_content['version'] = get_post_meta( $post_id, 'xapi_version', true);
			if(!empty($xapi_content['notxapi'])){
				$xapi_content['version'] = "none";
				unset($xapi_content['notxapi']);
			}
			if($object_context) {
				$this->set_params( $post_id, $xapi_content);
				delete_post_meta( $post_id, 'xapi_version');
			}
		}
		if(isset($xapi_content['launch_url'])){
			$xapi_content['src'] = $xapi_content['launch_url'];
			unset($xapi_content['launch_url']);
			if($object_context)
			$this->set_params( $post_id, $xapi_content);
		}
		$xapi_content['activity_id'] = isset($xapi_content['activity_id'])? $xapi_content['activity_id']:"";
		return $xapi_content;
	}
	function get_shortcode($post_id, $return_params = false) {
		$xapi_content = $this->get_params($post_id);
		if(empty($xapi_content["activity_name"])) {
			$xapi_content_post = get_post($post_id);
			$xapi_content["activity_name"] = @$xapi_content_post->post_title;
		}
		if(empty($xapi_content["button_type"])) {
			unset($xapi_content["link_button_image"]);
		}
		$params = array();
		if((!isset($xapi_content['version']) || $xapi_content['version'] != "none")) {
				
			$shortcode = "[grassblade ";
			foreach($xapi_content as $k=>$v) {
				$xapi_content_fields = array("width", "height", "target", "video","activity_name", "version", "src", "text", "link_button_image", "guest","src","endpoint","user","pass","auth","registration", "activity_id", "youtube_id", "show_results");
				$xapi_content_fields = apply_filters("grassblade_xapi_content_fields", $xapi_content_fields, $xapi_content);
				if($v != '' && in_array($k, $xapi_content_fields)) {
					$shortcode .= $k.'="'.$v.'" ';
					$params[$k] = $v;
				}
			}
			$shortcode .= "]";
		}
		else
		{
			$src = $xapi_content['src'];
			$shortcode = "[grassblade ";
			foreach($xapi_content as $k=>$v) {
				$xapi_content_fields = array("width", "height", "target", "video", "activity_name","version", "src", "text", "link_button_image", "guest", "youtube_id");
				$xapi_content_fields = apply_filters("grassblade_xapi_content_fields", $xapi_content_fields, $xapi_content);
				if($v != '' && in_array($k, $xapi_content_fields)) {
					$shortcode .= $k.'="'.$v.'" ';
					$params[$k] = $v;
				}
			}
			$shortcode .= "]";
		}
		if($return_params)
			return $params;
		else
			return $shortcode;
	}
	
	function gb_xapi_content_box_content($post ){
		global $wpdb;
		wp_nonce_field( plugin_basename( __FILE__ ), 'gb_xapi_content_box_content_nonce' );
		$xapi_content = $this->get_params($post->ID);
		
		//$this->dropbox_chooser();
		$html = '';
		if(!empty($xapi_content['src']) || !empty($xapi_content['video'])) {
			//$src = grassblade(array("target" => "url") + $xapi_content);
			$preview = get_permalink($post->ID);
			$preview .= strpos($preview, "?")? "&xapi_preview=true":"?xapi_preview=true";
			$html .= '<div><a class="button button-primary button-large" href="'.$preview.'" target="_blank">'.__("Preview", "grassblade").'</a></div>';
			$html .= "<br><b>".__('Add this xAPI Content using the dropdown, or use the following shortcode in your content:', 'grassblade').'</b><br>';
			$html .= '<input style="" value="[grassblade id='.$post->ID.']" /><br><br><br>';
		}
		else
		{
			$html .= '<p style="color:red">You haven\'t uploaded any package yet. Select the TinCan zip package using the uploader below and click on Publish/Update.</p>';
		
		}
		
		echo $html; 
		echo $this->form();
		echo '<input type="submit" name="publish" id="publish" class="button button-primary button-large" value="'.__("Update", "grassblade").'"/><br><br>';

		if(!grassblade_settings("disable_statement_viewer")) {
			echo "<div id='grassblade_statementviewer'>".do_shortcode("[grassblade_statementviewer activityid='".$xapi_content['activity_id']."']")."</div>";
		}

		if(!empty($xapi_content["completion_tracking"]))
		echo "<div id='grassblade_leaderboard'><div style='font-size: 16px; margin-bottom: 10px;'><b>".__("Leaderboard (Top 20):", "grassblade")."</b></div>".do_shortcode("[gb_leaderboard id='".$post->ID."']")."<br>".__("Add the shortcode <code>[gb_leaderboard id='".$post->ID."']</code> to any page to show this Leaderboard", "grassblade")."</div>";

		if($xapi_content['version'] != "none" && $xapi_content['version'] != "0.9"  && $post->post_status != "auto-draft" && !strpos($xapi_content['activity_id'], "://")) {
			echo "<script>alert('".strpos("://", @$xapi_content['activity_id']."a").__(" Activity ID is not a valid URI", "grassblade")."');</script>";
		}
		if($xapi_content['version'] != "none" && $post->post_status != "auto-draft" && !empty($xapi_content['activity_id'])) {
			$content_ids = $wpdb->get_results($wpdb->prepare("SELECT * FROM  $wpdb->postmeta WHERE meta_key = 'xapi_activity_id' AND meta_value='%s' AND post_id <> '%d'", $xapi_content['activity_id'], $post->ID));
			if(!empty($content_ids)) {
				$content_names = array();
				foreach ($content_ids as $key => $value) {
					$cp = get_post($value->post_id);
					if($cp->post_status == "publish")
					$content_names[] = $cp->ID.". ".$cp->post_title;
				}
				if(!empty($content_names[0]))
				echo "<script> alert('".__("Activity ID already exists on another xAPI Content: ", "grassblade").implode(",", $content_names)."');</script>";
			}
		}
	}

	function save_dropbox_file() {
			if(!empty($_POST['dropbox-file'])) {
				$url = $_POST['dropbox-file'];
				$filename = grassblade_sanitize_filename(basename($url)); 
				//add_filter('upload_dir', array($this, 'grassblade_upload_dir'));
				$upload = wp_upload_dir();
				$file = $upload['path']."/".$filename;
				set_time_limit(0); // unlimited max execution time
				$return = $this->cURLdownload($url, $file); 
				//remove_filter('upload_dir', array($this, 'grassblade_upload_dir'));
				if($return === true)
				{
					$upload['file'] = realpath($file);
					return $upload;
				}
				else
				{ 
                                        grassblade_admin_notice($return, "error");
					grassblade_debug($return);
					return false;
				}
			}
			return false;
	}
	function cURLdownload($url, $file)
	{
	  if(!function_exists("curl_init"))
	  	return "FAIL: curl_init() not available.";
	  $ch = curl_init();
	  
	  if($ch)
	  {
		$fp = fopen($file, "w");
		if($fp)
		{
		  if( !curl_setopt($ch, CURLOPT_URL, $url) )
		  {
			fclose($fp); // to match fopen()
			curl_close($ch); // to match curl_init()
			return "FAIL: curl_setopt(CURLOPT_URL)";
		  }
		  
		  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		  if( !curl_setopt($ch, CURLOPT_FILE, $fp) ) return "FAIL: curl_setopt(CURLOPT_FILE)";
		  if( !curl_setopt($ch, CURLOPT_HEADER, 0) ) return "FAIL: curl_setopt(CURLOPT_HEADER)";
		  if( !curl_exec($ch) ) return array('error' => curl_error($ch));//"FAIL: curl_exec()";
		  
		  curl_close($ch);
		  fclose($fp);
		  return true;
		}
		else return "FAIL: fopen()";
	  }
	  else return "FAIL: curl_init()";
	} 	
	function gb_xapi_content_box_save( $post_id ) {
		$post = get_post( $post_id);
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
		return;

		if ( !isset($_POST['gb_xapi_content_box_content_nonce']) || !wp_verify_nonce( $_POST['gb_xapi_content_box_content_nonce'], plugin_basename( __FILE__ ) ) )
		return;
	

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can( 'edit_page', $post_id ) )
			return;
		} else {
			if ( !current_user_can( 'edit_post', $post_id ) )
			return;
		}

		//$xapi_version = isset($_POST['xapi_version'] )? $_POST['xapi_version'] : "";
		//update_post_meta( $post_id, 'xapi_version', $xapi_version );

		$this->define_fields();
		$data = $this->get_params($post->ID);
		foreach ( $this->fields as $field ) {
			if(isset($_POST[$field['id']]))
			$data[$field['id']] = esc_attr( trim($_POST[$field['id']] ));

			if($field["type"] == "checkbox")
				$data[$field['id']] = !empty($_POST[$field['id']]);
			
			if($field["id"] == "activity_id" && $data[$field['id']] == "[GENERATE]")
				$data[$field['id']] = get_permalink($post_id);
		}
		// for hp5 content
		if(!empty($data['h5p_content'])) {
			$data['h5p_content_id'] = $data['h5p_content'];
			$data['src'] = admin_url( 'admin-ajax.php' )."?action=h5p_embed&id=".$data['h5p_content'];
			$data['activity_id'] = admin_url( 'admin-ajax.php' )."?action=h5p_embed&id=".$data['h5p_content'];
		}
		else if(!empty($data['video'])) {
			$data['activity_id'] = $data['video'];
		}
		$this->set_params( $post->ID, $data);
		
		$this->debug($data);
		if (!empty($_FILES['xapi_content']['name']) || !empty($_POST['dropbox-file'])) {
			$this->debug("Has upload");
			add_filter('upload_dir', array($this, 'grassblade_upload_dir'));
			add_filter('upload_mimes', array($this, 'upload_mimes'));

			$upload = wp_upload_dir();
			$xapi_content = get_post_meta( $post_id, 'xapi_content', true );
			$upload = $upload + $xapi_content;
			unset($upload["error"]);
			unset($data["error"]);
			grassblade_debug($upload);
			//add_filter('sanitize_file_name', 'grassblade_sanitize_filename', 10);
			if(!empty($_FILES['xapi_content']['name']))
			$upload = wp_handle_upload($_FILES['xapi_content'], array('test_form' => FALSE));
			else
			$upload = $this->save_dropbox_file();
			//remove_filter('sanitize_file_name', 'grassblade_sanitize_filename', 10);

			grassblade_debug($upload);
			$this->debug($upload);
			if (!empty($upload) && !is_wp_error($upload) && empty($upload["error"])) {
				$this->debug("No upload error");
				$upload = array_merge($data, $upload);
				// no errors, do what you like
				$this->debug("Merged arrays");
				$this->debug($upload);
				$sub_dir = $post_id.'-'.$post->post_name;
				$content_at = $this->grassblade_handle_contentupload($upload, $sub_dir);
				$upload['path'] = realpath($upload['path']);
				
				//File Uploaded and Unzipped - Read tincan.xml and find the launch url
				if($content_at) {
					$upload['content_path'] = realpath($content_at['path']);
					$upload['content_url'] = $content_at['url'];
					$tincanxml_subdir = $this->grassblade_get_tincanxml($upload['content_path']);
					if(empty($tincanxml_subdir))
					$tincanxml_file = $upload['content_path'].DIRECTORY_SEPARATOR."tincan.xml";
					else
					$tincanxml_file = $upload['content_path'].DIRECTORY_SEPARATOR.$tincanxml_subdir.DIRECTORY_SEPARATOR."tincan.xml";
					
					$nonxapi_file = $upload['content_path'].DIRECTORY_SEPARATOR."player.html"; // Check if No tincan.xml Articulate Studio File
					$nonxapi_file2 = $upload['content_path'].DIRECTORY_SEPARATOR."story.html"; // Check if No tincan.xml Articulate Storyline File
					$nonxapi_file3 = $upload['content_path'].DIRECTORY_SEPARATOR."index.html"; // Check if No tincan.xml Captivate File
					$nonxapi_file4 = $upload['content_path'].DIRECTORY_SEPARATOR."presentation.html"; // Check if No tincan.xml Articulate Studio 13 File
					
					if(file_exists($tincanxml_file))
					{
						$tincanxmlstring = trim(file_get_contents($tincanxml_file));
						$tincanxml = simplexml_load_string($tincanxmlstring);
						if(!empty($tincanxml->activities->activity->launch))
						{
							$launch_file = (string)  $tincanxml->activities->activity->launch;
							$upload['original_activity_id'] = isset($tincanxml->activities->activity['id'])? $tincanxml->activities->activity['id']:"";
							if(empty($upload['activity_id']))
							$upload['activity_id'] = $upload['original_activity_id'];
						}
						else
						update_option('grassblade_admin_errors', 'XML Error:  Launch file reference not found in tincan.xml');
						
						$upload['launch_path'] = dirname($tincanxml_file).DIRECTORY_SEPARATOR.$launch_file;
						
						if(empty($tincanxml_subdir))
						$upload['src'] =  $content_at['url']."/".$launch_file;
						else
						$upload['src'] =  $content_at['url']."/".$tincanxml_subdir."/".$launch_file;
						
						if(!file_exists($upload['launch_path']))
						update_option('grassblade_admin_errors', 'Error: <i>'.$upload['launch_path'].'</i>. Launch file not found in tincan package');
						
						if(isset($upload['version']) && $upload['version'] == "none")
						$upload['version'] = "";
					}
					else if(file_exists($nonxapi_file)) //Articulate Studio  Non-TinCan Support
					{
						$upload['src'] =  $content_at['url']."/player.html";
						$upload['launch_path'] =  dirname($nonxapi_file).DIRECTORY_SEPARATOR."player.html";
						//$upload['notxapi'] = true;
						$upload['version'] = "none";
					}
					else if(file_exists($nonxapi_file2)) //Articulate Storyline Non-TinCan Support
					{
						$upload['src'] =  $content_at['url']."/story.html";
						$upload['launch_path'] =  dirname($nonxapi_file2).DIRECTORY_SEPARATOR."story.html";
						//$upload['notxapi'] = true;
						$upload['version'] = "none";
					}
					else if(file_exists($nonxapi_file3)) //Captivate Non-TinCan Support
					{
						$upload['src'] =  $content_at['url']."/index.html";
						$upload['launch_path'] =  dirname($nonxapi_file3).DIRECTORY_SEPARATOR."index.html";
						//$upload['notxapi'] = true;
						$upload['version'] = "none";
					}
                                        else if(file_exists($nonxapi_file4)) //Articulate Studio 13
                                        {
                                                $upload['src'] =  $content_at['url']."/presentation.html";
                                                $upload['launch_path'] =  dirname($nonxapi_file4).DIRECTORY_SEPARATOR."presentation.html";
                                                //$upload['notxapi'] = true;
                                                $upload['version'] = "none";
                                        }
					else
					update_option('grassblade_admin_errors', 'Package Error:  <i>'.$tincanxml_file.'</i> file not found in tincan package');
				}
				foreach($upload as $k=>$v)
				$upload[$k] = addslashes($v);
				$this->debug("Updating Array");
				$this->debug($upload);
				$this->set_params( $post_id, $upload);
				//update_post_meta( $post_id, 'xapi_content', $upload );
			}
			remove_filter('upload_dir', array($this, 'grassblade_upload_dir'));

			if(!empty($upload["error"]))
			update_option('grassblade_admin_errors', 'Upload Error: '.$upload["error"].$this->upload_tests());
			else if(is_wp_error($upload))
			update_option('grassblade_admin_errors', 'Upload Error: '.$upload->get_error_message().$this->upload_tests());
		}
	}
	function upload_tests() {
		add_filter('upload_dir', array($this, 'grassblade_upload_dir'));
		$upload = wp_upload_dir();
		$info = "<br><br><b><u>Running exhaustive Tests.</u></b><br><b>Upload Folder Path:</b> ".$upload["path"]."<br>";
		$folder_exists = file_exists($upload["path"]);
		$info .= "<b>Folder Exists?</b> ".( $folder_exists? "Yes":"No" )."<br>";
		if(empty($folder_exists)) {
			$mkdir = mkdir($upload["path"]);
			$folder_exists = file_exists($upload["path"]);
			$info .= "<b>Creating Folder:</b> ".( $folder_exists? "Success":"Failed. Need enough Permissions to create folders. Create folder <i>".$upload["path"]."</i> with 744, 774 or 777 permission, whichever works, or contact your server admin." )."<br>";
		}
		$info .= "<b>Upload Folder Permission:</b> ".decoct(fileperms($upload["path"]) & 0777)."<br>";
		$copy_file = $upload["path"]."/test.zip";
		copy(dirname(__FILE__)."/test.zip", $copy_file);
		$copy = file_exists($copy_file);
		$info .= "<b>Copy a file to Folder Path:</b> ".((!empty($copy))? "Passed":"Failed.");

		if(!file_exists($upload["path"]."/test_folder/")) {
			$mkdir = mkdir($upload["path"]."/test_folder/");
			$folder_exists = file_exists($upload["path"]."/test_folder/");
			rmdir($upload["path"]."/test_folder/");
			$info .= "<br><b>Creating test Folder:</b> ".( $folder_exists? "Success":"Failed. Need enough Permissions to create folders. Change folder permission for <i>".$upload["path"]."</i> to 755, 775 or 777, whichever works, or contact your server admin." )."<br>";
		}
		if($copy) {
			$unzip = unzip_file($upload["path"]."/test.zip", $upload["path"]);

			if($unzip === true) {
				unlink($copy_file);
				unlink($upload["path"]."/empty");
				$info .= "<b>Unzip test file: Success";
			}
			else
				$info .= "<b>Unzip test file: Failed. Need enough permissions to unzip files. Change folder permission for <i>".$upload["path"]."</i> to 744, 774 or 777, whichever works, or contact your server admin. <br>";

		}
		$info .= "<br><b>Possible Suggestions that might fix the issue:</b><br>1. Change permissions on <i>".$upload["path"]."</i> to 744, 774 or 777, whichever works, or contact your server admin.<br>";
		$user_group = $this->get_php_user_group();
		$info .= "2. Try changing the user/group of the folder to: ".$user_group.", running this command will do it on Linux/Mac: <pre><i>chown -R ".$user_group." ".$upload["path"]."</i></pre>";
		$info .= "3. Try adding this line to wp-config.php file:<i><pre>define('FS_METHOD', 'direct');</pre></i>";
		
		remove_filter('upload_dir', array($this, 'grassblade_upload_dir'));	
		return $info;
	}
	function grassblade_get_tincanxml($dir) {
		$tincanxml_file = $dir.DIRECTORY_SEPARATOR."tincan.xml";
		if(file_exists($tincanxml_file))
			return "";
		else
		{
			$dirlist = scandir($dir);
			foreach($dirlist as $d)
			{
				if($d != "." && $d != "..")
				{
					$tincanxml_file = $dir.DIRECTORY_SEPARATOR.$d.DIRECTORY_SEPARATOR."tincan.xml";
					if(file_exists($tincanxml_file))
						return $d;
				}
			}
		}
		return 0;
	}
	function get_php_user_group() {
            $php_u = null;

            if ( function_exists( 'posix_getpwuid' ) ) {
                    $u = posix_getpwuid( posix_getuid() );
                    $g = posix_getgrgid( $u['gid'] );
                    $php_u = $u['name'] . ':' . $g['name'];
            }

            if ( empty( $php_u ) and isset( $_ENV['APACHE_RUN_USER'] ) ) {
                    $php_u = $_ENV['APACHE_RUN_USER'];
                    if ( isset( $_ENV['APACHE_RUN_GROUP'] ) ) {
                            $php_u .= ':' . $_ENV['APACHE_RUN_GROUP'];
                    }
            }

            if ( empty( $php_u ) and isset( $_SERVER['USER'] ) ) {
                    $php_u = $_SERVER['USER'];
            }

            if ( empty( $php_u ) and function_exists( 'exec' ) ) {
                    $php_u = exec( 'whoami' );
            }

            if ( empty( $php_u ) and function_exists( 'getenv' ) ) {
                    $php_u = getenv( 'USERNAME' );
            }

            return $php_u;
    }

	function grassblade_handle_contentupload($upload, $sub_dir){
		$upload_dir = wp_upload_dir();
		$to = $upload_dir['path']."/".$sub_dir;
		$url = $upload_dir['url']."/".$sub_dir;
		WP_Filesystem();
		$unzip = unzip_file($upload['file'], $to);
		
		if(is_wp_error($unzip))
		update_option('grassblade_admin_errors', 'Error: '.$unzip->get_error_message().$this->upload_tests());
		else {
			unlink($upload['file']);
			return array('path' => $to, 'url' => $url);
		}
	}
	function grassblade_xapi_post_edit_form_tag() {
		echo ' enctype="multipart/form-data"';
	}

	static function is_completion_tracking_enabled($content_id) {
		$completion = get_post_meta($content_id, "xapi_content", true);
		return !empty($completion["completion_tracking"]);
	}
}

$xc = new grassblade_xapi_content();
$xc->run();
