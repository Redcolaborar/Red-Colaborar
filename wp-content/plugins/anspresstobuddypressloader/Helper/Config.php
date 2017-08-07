<?php
namespace AnsPressToBuddyPressLoader\Helper\Config;

require_once(APTOBPLOADER_PLUGIN_DIR . 'Library/LibAPtoBPLoader.php' );

class Config
{
	public $APtoBPLoader;
	
	public $_plugin_title				= 'AnsPress to BuddyPress Loader';
	public $_plugin_name				= 'anspresstobuddypressloader';	
	
	public function __construct()
	{				
		add_action( 'wp_ajax_anspresstobuddypressloader_load_questions', 
				   	array(&$this, 'loadQuestions')
				  );
		add_action( 'wp_ajax_nopriv_anspresstobuddypressloader_load_questions', 
				   	array(&$this, 'loadQuestions')
				  );
		
		add_action( 'wp_ajax_anspresstobuddypressloader_save_answer_questions', 
				   	array(&$this, 'saveAnswerQuestions')
				  );
		
		$this->_init();
		
		add_action( 'wp_head', 
				   	array(&$this, 'anspresstobuddypressloader_head_scripts')
				  );
		
		add_action( 'wp_enqueue_scripts', 
				   	array(&$this, 'anspresstobuddypressloader_enqueue_js')
				  );
		
		add_action( 'init', array( $this, 'load_textdomain' ), 0 );

	}
	
	private function _init()
	{

		if (is_admin())
		{			
			add_action('admin_menu', array(&$this,'admin_menu'));				
		}
		
		$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;

		$LibAPtoBPLoader->getBPCategory();
		$LibAPtoBPLoader->getAPCategory();


		$this->APtoBPLoader = $LibAPtoBPLoader;
	}


	public function admin_menu()
	{
		add_menu_page( $this->_plugin_name, $this->_plugin_title, 'manage_options', $this->_plugin_name, array(&$this, 'pluginOptions'), APTOBPLOADER_ICON, 'Tools' );		
	}

	public function pluginOptions() {
		global $wpdb;
		
		$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You are not allowed to access this part of the site' ) );
		}
		
		wp_reset_query();

		$sql		= "SELECT * FROM {$wpdb->prefix}{$this->_table_aptobploader_mapping}";
		$results	= $wpdb->get_results($sql);
		
		$plugin_title	= $this->_plugin_title;
		$plugin_name	= $this->_plugin_name;
		
		$this->_saveOptions();
		$this->_deleteOptions();
		
		$sql	= "SELECT * FROM {$wpdb->prefix}{$LibAPtoBPLoader->_table_aptobploader_mapping}";
		$cat	= $wpdb->get_results($sql);			

		include(APTOBPLOADER_PLUGIN_DIR.'Viewer/plugin.options.php');
	}
	
	private function _saveOptions()
	{
		global $wpdb;
		
		$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;
		
		if(isset($_POST[$this->_plugin_name.'_saveoptions']))
		{
			$ap_category	= $_POST[$this->_plugin_name.'_anspress'];
			$ap_category	= trim($ap_category);
			
			$bp_category	= $_POST[$this->_plugin_name.'_bbpress'];
			$bp_category	= trim($bp_category);
			
			if(strlen($ap_category) > 2 && strlen($bp_category) > 2)
			{
				$wpdb->insert( 
						$wpdb->prefix.$LibAPtoBPLoader->_table_aptobploader_mapping, 
								array(
									'ap_category'	=> $ap_category,	// string
									'bp_category'	=> $bp_category	// string
								), 
								array( 
									'%s',	// post_content
									'%s',	// post_status
								)
							);				
			}			
		}	
	}

	private function _deleteOptions()
	{
		global $wpdb;
		
		$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;
		
		if(isset($_POST[$this->_plugin_name.'_deleteoptions']))
		{
			$id = $_POST['aptobploader_id'];
			
			foreach($id as $key=>$val)
			{
				$wpdb->delete( $wpdb->prefix.$LibAPtoBPLoader->_table_aptobploader_mapping, array( 'aptobploader_id' => intval($val) ) );
			}

		}	
	}	
	public function loadQuestions()
	{
		global $wpdb;
		
		$aptobploader_term_id				= $_REQUEST['aptobploader_term_id'];
		
		$termID = array();
		
		foreach($aptobploader_term_id as $key => $val)
		{
			$termID[] = $val['term_id'];
		}
		
		$LibAPtoBPLoader 	= new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;
		$LibAPtoBPLoader->termID = $termID;
		
		$LibAPtoBPLoader->getQuestionsAPByCategory();
		
		$questionID	= $LibAPtoBPLoader->questionID;
				
		if(isset($questionID))
		{
			foreach($questionID as $key => $val)
			{				
				$sql = "SELECT * FROM $wpdb->posts 
							WHERE ID = $val AND post_type='question' AND post_status='publish' 
							ORDER BY ID DESC";
												
				$post 	= $wpdb->get_row( $sql );
				
				if($wpdb->num_rows)
				{
					$LibAPtoBPLoader->parentID = $val;
					$answers 		= $LibAPtoBPLoader->getAnswersAP();
					$num_answers 	= count($answers);

					echo '
						<li class="groups mpp_media_upload activity-item bottom-to-top" id="questions-'.$val.'">
						<div class="activity-avatar rounded"> '.get_avatar($post->post_author, 50).'  </div>
						<div class="activity-content">
						  <div class="activity-header">
							<p>'.get_author_name($post->post_author).'<span class="time-since">'.$post->post_date.'</span></p>
							<p><strong>'.$post->post_title.'</strong></p>
							<p>'.$post->post_content.'</p>
						  </div>
						  <div style="margin-bottom: 10px;">
							<a href="'.$post->guid.'" class="link" style="border: 1px solid #E5E5E5; border-radius: 5px; padding: 2px 5px 2px 5px;"> ';
						_e( 'Answers', 'anspresstobuddypressloader' );
					echo ' <span>'.$num_answers.'</span></a>
						  </div>
						</div>
						<div class="activity-comments">
						<ul>';
						if(isset($answers))
						{
							foreach($answers as $k=>$v)
							{
								echo'
								<li id="answers-'.$v->ID.'">
								  <div class="acomment-avatar rounded"> '.get_avatar($v->post_author, 50).'</div>
								  <div class="acomment-meta"> '.get_author_name($v->post_author).'	</div>
								  <span class="time-since">'.$v->post_date.'</span>
								  <div class="acomment-content">						  	
									<p><strong>'.$v->post_title.'</strong></p>
									<p>'.$v->post_content.'</p>
								  </div>
								</li>';					
							}
						}

					echo '
						  </ul>
						</div>
						<div class="activity-timeline"></div>
					  </li>
				  		';						
				}
			}
		}
		else
		{
			return false;
		}
		die();		
	}
	
	public function saveAnswerQuestions()
	{
		$LibAPtoBPLoader = new \AnsPressToBuddyPressLoader\Library\LibAPtoBPLoader\LibAPtoBPLoader;
		$LibAPtoBPLoader->saveAnswerQuestions();

		if ( wp_get_referer() )
		{
			wp_safe_redirect( wp_get_referer() );
		}
		else
		{
			wp_safe_redirect( get_home_url() );
		}
		die();
	}
	
	public function anspresstobuddypressloader_enqueue_js() {
		wp_register_script( 'anspresstobuddypressloader_js', 
						   	APTOBPLOADER_PLUGIN_URL . 'assets/js/anspresstobuddypressloader.js', 
						   	array ('jquery'),
						   	APTOBPLOADER_VERSION,
						  	true);
		
		wp_enqueue_script( 'anspresstobuddypressloader_js' );
	}
	
	public function anspresstobuddypressloader_head_scripts() {
		echo "<script>\n// <![CDATA[\n";
		echo "var APTOBPLOADER_SITE_URL = ", json_encode( site_url('/') ), ";\n";
		echo "var APTOBPLOADER_AJAX_URL = ", json_encode( admin_url('/admin-ajax.php') ), ";\n";
		echo "var APTOBPLOADER_TERM_ID = ", json_encode( $this->APtoBPLoader->termID ), ";\n";
		echo "var APTOBPLOADER_TEXT_QUESTIONS = '", _e('Questions', $this->_plugin_name), "';\n";
		echo "// ]]>\n</script>\n";
	}
	
	public function load_textdomain()
	{
		load_plugin_textdomain( $this->_plugin_name, false, $this->_plugin_name.'/languages'); 	
	}	
}