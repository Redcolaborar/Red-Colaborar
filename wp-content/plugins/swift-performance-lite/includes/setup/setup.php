<?php

class Swift_Performance_Setup {

	/**
	 * Array of steps
	 * @var array
	 */
	public $steps = array();

	/**
	 * Current step
	 * @var array
	 */
	public $current_step = array();

	/**
	 * Show steps in footer
	 * @var boolean
	 */
	public $show_steps = true;

	/**
	 * Localization array for JS
	 * @var array
	 */
	public $localize = array();

	/**
	 * Analyze array
	 */
	public $analyze = array();

	public $disable_continue = false;

	/**
	 * Catch pseudo function calls and do nothing (we use init for early catch the page);
	 * @param string $function
	 * @param array $params
	 */
	public function __call($function, $params){
		// Do nothing
	}

	/**
	 * Create instance
	 */
	public function __construct() {
		// Ajax handlers
		add_action('wp_ajax_swift_performance_setup', array($this, 'ajax_handler'));

		// Return if page is not the Swift Performance Setup Wizard page
		if (!isset($_GET['subpage']) || !in_array($_GET['subpage'], array('setup', 'deactivate')) || !isset($_GET['page']) || $_GET['page'] != 'swift-performance' ){
			return false;
		}

		ini_set('display_errors', 0);

		// Set installer directory path
		if (!defined('SWIFT_PERFORMANCE_SETUP_DIR')){
			define ('SWIFT_PERFORMANCE_SETUP_DIR', SWIFT_PERFORMANCE_DIR . 'includes/setup/');
		}

		// Set installer directory URI
		if (!defined('SWIFT_PERFORMANCE_SETUP_URI')){
			define('SWIFT_PERFORMANCE_SETUP_URI', SWIFT_PERFORMANCE_URI . 'includes/setup/');
		}

		// Init steps
		if ($_GET['subpage'] == 'setup'){
			$this->steps = array(
					array(
						'title'		=> (isset($_REQUEST['swift-nonce']) ? esc_html__('Analyze your site', 'swift-performance') : esc_html__('Welcome', 'swift-performance')),
						'id'			=> 'analyze',
						'disable-skip'	=> true
					),
					array(
						'title'	=> esc_html__('Caching mode', 'swift-performance'),
						'id'		=> 'caching',
					),
					array(
						'title'	=> esc_html__('Optimization', 'swift-performance'),
						'id'		=> 'manage-assets',
					),
					array(
						'title'	=> esc_html__('Media', 'swift-performance'),
						'id'		=> 'media',
					),
					array(
						'title'	=> esc_html__('Finish', 'swift-performance'),
						'id'		=> 'finish',
					)
			);
		}
		else if ($_GET['subpage'] == 'deactivate'){
			$this->steps = array(
					array(
						'title'	=> esc_html__('Deactivation Settings', 'swift-performance'),
						'id'		=> 'deactivate-settings',
						'disable-skip'	=> true
					),
					array(
						'title'	=> esc_html__('Deactivate', 'swift-performance'),
						'id'		=> 'deactivate',
					)
			);
		}

		// Init
		add_action('admin_init', array($this, 'init'));

		// Change wp title
		add_action('wp_title', array($this, 'wp_title'));
	}

	/**
	 * Init setup wizard
	 */
	public function init(){
		if (!current_user_can('manage_options')){
			return;
		}

		// Localization
		$this->localize = array(
				'i18n' => array(
						'Upload' => esc_html__('Upload', 'swift-performance'),
						'Modify' => esc_html__('Modify', 'swift-performance'),
						'Please wait...' => esc_html__('Please wait...', 'swift-performance')
				),
				'ajax_url'		=> add_query_arg('page', 'swift_performance_setup', admin_url('admin-ajax.php')),
				'nonce'		=> wp_create_nonce('swift-performance-setup'),
		);

		// Enqueue Setup Wizard CSS
		wp_enqueue_style('swift-performance-setup', SWIFT_PERFORMANCE_SETUP_URI . 'css/setup.css', array(), SWIFT_PERFORMANCE_VER);

		// Enqueue Setup Wizard JS
		wp_enqueue_script('swift-performance-setup', SWIFT_PERFORMANCE_SETUP_URI . 'js/setup.js', array(), SWIFT_PERFORMANCE_VER);
		wp_localize_script('swift-performance-setup', 'swift_performance', $this->localize);

		//WP admin styles
		wp_enqueue_style( 'wp-admin' );

		// Set current step
		$step = isset($_REQUEST['step']) ? (int)$_REQUEST['step'] : 0;


		$this->current_step 		= $this->steps[$step];
		$this->current_step['index']	= $step;

		// Do current step actions
		$this->do_step();

		// Render step
		$this->render();
	}

	/**
	 * Do current step actions
	 */
	public function do_step(){
		if (isset($_REQUEST['swift-nonce']) && wp_verify_nonce($_REQUEST['swift-nonce'], 'swift-performance-setup') && current_user_can('manage_options')){
			switch ($this->current_step['id']){
				// Analyze
				case 'analyze':
					// Reset Redux
					global $swift_performance_options;
					$reduxsa = ReduxSAFrameworkInstances::get_instance('swift_performance_options');
					$swift_performance_options = $reduxsa->_default_values();
					update_option('swift_performance_options', $reduxsa->_default_values());

					// Empty cache
					Swift_Performance_Cache::clear_all_cache();

					$this->analyze();
				break;
				case 'caching':
					// Empty cache
					Swift_Performance_Cache::clear_all_cache();

					// Analyze
					$this->analyze();
					// Set caching mode to rewrites if it is available
					if (Swift_Performance_Lite::server_software() == 'apache' && $this->analyze['htaccess'] && !isset($this->analyze['missing_apache_modules']['mod_rewrite'])){
						Swift_Performance_Lite::update_option('caching-mode', 'disk_cache_rewrite');
						try {
							// Generate and write htaccess rules
							$rules = Swift_Performance_Lite::build_rewrite_rules();
							Swift_Performance_Lite::write_rewrite_rules($rules);
						}
						catch (Exception $e){
							self::print_notice(array('type' => 'error', 'message' => $e->get_error_message()));
						}

					}

				break;
				case 'manage-assets':
					// Empty cache
					Swift_Performance_Cache::clear_all_cache();

					// Set cache expiry mode
					$expiry_mode = ($_POST['cache-expiry-mode'] == 'timebased' ? 'timebased' : ($_POST['cache-expiry-mode'] == 'actionbased' ? 'actionbased' : 'intelligent'));
					Swift_Performance_Lite::update_option('cache-expiry-mode', $expiry_mode);
					// Automated prebuild cache
					Swift_Performance_Lite::update_option('automated_prebuild_cache', (isset($_POST['automated-prebuild-cache']) && $_POST['automated-prebuild-cache'] == 'enabled' ? 1 : 0));
					// Browser cache
					Swift_Performance_Lite::update_option('browser-cache', (isset($_POST['browser-cache']) && $_POST['browser-cache'] == 'enabled' ? 1 : 0));
					// Gzip
					Swift_Performance_Lite::update_option('enable-gzip', (isset($_POST['enable-gzip']) && $_POST['enable-gzip'] == 'enabled' ? 1 : 0));

					// Cloudflare
					if (isset($_POST['cloudflare-auto-purge']) && $_POST['cloudflare-auto-purge'] == 'enabled'){
						Swift_Performance_Lite::update_option('cloudflare-auto-purge', 1);
						Swift_Performance_Lite::update_option('cloudflare-email', (isset($_POST['cloudflare-email']) ? $_POST['cloudflare-email'] : ''));
						Swift_Performance_Lite::update_option('cloudflare-api-key', (isset($_POST['cloudflare-api-key']) ? $_POST['cloudflare-api-key'] : ''));
					}
					else {
						Swift_Performance_Lite::update_option('cloudflare-auto-purge', 0);
					}

					// Varnish
					if (isset($_POST['varnish-auto-purge']) && $_POST['varnish-auto-purge'] == 'enabled'){
						Swift_Performance_Lite::update_option('varnish-auto-purge', 1);
						Swift_Performance_Lite::update_option('custom-varnish-host', (isset($_POST['custom-varnish-host']) ? $_POST['custom-varnish-host'] : ''));
					}
					else {
						Swift_Performance_Lite::update_option('varnish-auto-purge', 0);
					}

				break;
				case 'media':
					// Empty cache
					Swift_Performance_Cache::clear_all_cache();

					$mode = isset($_POST['optimize-assets']) ? $_POST['optimize-assets'] : '';
					if ($mode == 'cache-only'){
						Swift_Performance_Lite::update_option('merge-scripts', 0);
						Swift_Performance_Lite::update_option('merge-styles', 0);
						Swift_Performance_Lite::update_option('merge-background-only', 0);
						Swift_Performance_Lite::update_option('optimize-prebuild-only', 0);
					}
					else if ($mode == 'merge-only'){
						Swift_Performance_Lite::update_option('merge-scripts', 1);
						Swift_Performance_Lite::update_option('merge-styles', 1);
						Swift_Performance_Lite::update_option('critical-css', 0);
						Swift_Performance_Lite::update_option('merge-background-only', (isset($_POST['merge-background-only']) && $_POST['merge-background-only'] == 'enabled' ? 1 : 0));
						Swift_Performance_Lite::update_option('optimize-prebuild-only', (isset($_POST['optimize-prebuild-only']) && $_POST['optimize-prebuild-only'] == 'enabled' ? 1 : 0));
					}
					else if ($mode == 'full'){
						Swift_Performance_Lite::update_option('use-compute-api', 1);
						Swift_Performance_Lite::update_option('merge-scripts', 1);
						Swift_Performance_Lite::update_option('merge-styles', 1);
						Swift_Performance_Lite::update_option('merge-background-only', (isset($_POST['merge-background-only']) && $_POST['merge-background-only'] == 'enabled' ? 1 : 0));
						Swift_Performance_Lite::update_option('optimize-prebuild-only', (isset($_POST['optimize-prebuild-only']) && $_POST['optimize-prebuild-only'] == 'enabled' ? 1 : 0));
					}

					// Disable emojis
					if (isset($_POST['disable-emojis']) && $_POST['disable-emojis'] == 'enabled'){
						Swift_Performance_Lite::update_option('disable-emojis', 1);
					}

					// Limit threads & bypass css import
					if (in_array($mode, array('merge-only', 'full'))){
						Swift_Performance_Lite::update_option('bypass-css-import', (isset($_POST['bypass-css-import']) && $_POST['bypass-css-import'] == 'enabled' ? 1 : 0));
						Swift_Performance_Lite::update_option('minify-html', (isset($_POST['minify-html']) && $_POST['minify-html'] == 'enabled' ? 1 : 0));

						if (isset($_POST['limit-threads']) && $_POST['limit-threads'] == 'enabled'){
							Swift_Performance_Lite::update_option('limit-threads', 1);
							Swift_Performance_Lite::update_option('max-threads', max(0, (int)$_POST['max-threads']));
						}
					}


				break;
				case 'finish':
					// Empty cache
					Swift_Performance_Cache::clear_all_cache();

					$lazyload_images 	= (isset($_POST['lazyload-images']) && $_POST['lazyload-images'] == 'enabled' ? 1 : 0);
					$lazyload_iframe 	= (isset($_POST['lazyload-iframes']) && $_POST['lazyload-iframes'] == 'enabled' ? 1 : 0);
					$optimize 		= (isset($_POST['optimize-images']) && $_POST['optimize-images'] == 'enabled' ? 1 : 0);
					$keep_original    = ($optimize && isset($_POST['keep-original-images']) && $_POST['keep-original-images'] == 'enabled' ? 1 : 0);

					Swift_Performance_Lite::update_option('lazy-load-images', $lazyload_images);
					Swift_Performance_Lite::update_option('lazyload-iframes', $lazyload_iframe);
					Swift_Performance_Lite::update_option('optimize-uploaded-images', $optimize);
					Swift_Performance_Lite::update_option('keep-original-images', $keep_original);
				break;
				case 'deactivate':
					update_option('swift-performance-deactivation-settings', array(
						'keep-settings' => (isset($_POST['keep-settings']) && $_POST['keep-settings'] == 'enabled' ? 1 : 0),
						'keep-custom-htaccess' => (isset($_POST['keep-custom-htaccess']) && $_POST['keep-custom-htaccess'] == 'enabled' ? 1 : 0),
						'keep-warmup-table' => (isset($_POST['keep-warmup-table']) && $_POST['keep-warmup-table'] == 'enabled' ? 1 : 0),
						'keep-image-optimizer-table' => (isset($_POST['keep-image-optimizer-table']) && $_POST['keep-image-optimizer-table'] == 'enabled' ? 1 : 0),
						'keep-logs' => (isset($_POST['keep-logs']) && $_POST['keep-logs'] == 'enabled' ? 1 : 0)
					), false);
				break;
			}
		}
	}

	/**
	 * Analyze environment and fill up analyze array
	 */
	public function analyze(){
		// Check other cache/minify plugins to avoid conflicts and double cacheing
		// W3TC
		if (class_exists('\\W3TC\\Root_Loader')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['W3TC'] = 'W3 Total Cache';
		}
		// WP Super Cache
		if (function_exists('wp_cache_set_home')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['WPSupercache'] = 'WP Supercache';
		}
		// WP Rocket
		if (defined('WP_ROCKET_VERSION')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['WPRocket'] = 'WP Rocket';
		}
		// WP Fastest cache
		if (class_exists('WpFastestCache')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['WPFastestCache'] = 'WP Fastest Cache';
		}
		// Autoptimize
		if (defined('AUTOPTIMIZE_PLUGIN_DIR')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['Autoptimize'] = 'Autoptimize';
		}
		// BWP Minify
		if (class_exists('BWP_MINIFY')){
			$this->disable_continue = true;
			$this->analyze['plugin_conflicts']['BWPMinify'] = 'Better WordPress Minify';
		}


		// If apache
		if (Swift_Performance_Lite::server_software() == 'apache' && function_exists('apache_get_modules')){
			// Check modules
			$this->analyze['missing_apache_modules'] = array_diff(array(
				'mod_expires',
				'mod_deflate',
				'mod_setenvif',
				'mod_headers',
				'mod_filter',
				'mod_rewrite',
			), apache_get_modules());
		}

		// Check htaccess
		$htaccess = ABSPATH . '.htaccess';

		if (!file_exists($htaccess)){
			@touch($htaccess);
			if (!file_exists($htaccess)){
				$this->analyze['htaccess'] = false;
			}
		}
		else if (!is_writable($htaccess)){
			$this->analyze['htaccess'] = false;
		}
		else {
			$this->analyze['htaccess'] = true;
		}
	}

	/**
	 * Render current step
	 */
	public function render(){
		// Run only the first time
		update_option('swift-perforomance-initial-setup-wizard', 1);

		$template = 'start-wizard';

		if (defined('DOING_AJAX')){
			$GLOBALS['hook_suffix'] = 'swift-performance';
			return;
		}

		// Verify nonce
		if (isset($_REQUEST['swift-nonce']) && wp_verify_nonce($_REQUEST['swift-nonce'], 'swift-performance-setup') && current_user_can('manage_options')){
			// Set template
			$template = $this->current_step['id'];
		}

		// Get header part
		$this->_get_template_part('admin-header');

		// Get Body
		if (!isset($_REQUEST['swift-nonce']) || !wp_verify_nonce($_REQUEST['swift-nonce'], 'swift-performance-setup') && current_user_can('manage_options')){
			$this->_get_template_part($template);
			$this->show_steps = false;
		}
		else{
			$this->_get_template_part($template);
		}

		// Get Footer
		$this->_get_template_part('admin-footer');

		// Exit
		die;
	}

	/**
	 * Print prev/next step links
	 */
	public function step_links() {
		$current 	= $this->current_step['index'];
		$prev		= isset($this->steps[$current-1]) ? '<a class="swift-btn swift-btn-gray swift-btn-lg" href="'. esc_url(wp_nonce_url(add_query_arg('step', ($current-1), add_query_arg('subpage', $_GET['subpage'], menu_page_url('swift-performance', false))), 'swift-performance-setup', 'swift-nonce')) . '">'.esc_html__('Previous step', 'swift-performance').'</a>' : '';
		if (isset($this->steps[$current+1])){
			$skip = '<a class="swift-btn swift-btn-gray swift-btn-lg swift-skip-step" href="'. esc_url(wp_nonce_url(add_query_arg('step', ($current+1), add_query_arg('subpage', $_GET['subpage'], menu_page_url('swift-performance', false))), 'swift-performance-setup', 'swift-nonce')) . '">'.esc_html__('Skip this step', 'swift-performance').'</a>';
			$next = wp_nonce_field('swift-performance-setup', 'swift-nonce').
					'<input type="hidden" name="step" value="'.($current + 1).'">'.
					'<input type="hidden" name="swift-performance-setup-action" value="'.esc_attr($this->current_step['id']).'">'.
					'<button class="swift-btn swift-btn-green swift-setup-next" ' . ($this->disable_continue == true ? 'disabled' : '') . '>'.esc_html__('Continue', 'swift-performance').'</button>';
		}
		echo '<div class="swift-setup-btn-wrapper">';
		echo $prev;
		if (!isset($this->current_step['disable-skip']) || !$this->current_step['disable-skip']){
			echo $skip;
		}
		if (!isset($this->current_step['disable-continue']) || !$this->current_step['disable-continue']){
			echo $next;
		}
		else {
			echo '<input type="hidden" id="nextpage" value="'.esc_url(wp_nonce_url(add_query_arg('step', ($current+1), add_query_arg('subpage', $_GET['subpage'], menu_page_url('swift-performance', false))), 'swift-performance-setup', 'swift-nonce')).'">';
		}
		echo '</div>';
	}

	/**
	 * Handle ajax requests
	 */
	public function ajax_handler(){
		if (!isset($_REQUEST['swift-nonce']) || !wp_verify_nonce($_REQUEST['swift-nonce'], 'swift-performance-setup') && current_user_can('manage_options')){
			wp_die(0);
		}


		wp_die();
	}

	/**
	 * Go back to the previous step
	 */
	private function _revert_step(){
		$index 				= $this->current_step['index']-1;
		$this->current_step 		= $this->steps[$index];
		$this->current_step['index']	= $index;
	}

	/**
	 * Includes the given template
	 * @param string $template
	 */
	private function _get_template_part($template) {
		if (strpos($template, '.') !== false){
			return false;
		}
		if (file_exists(SWIFT_PERFORMANCE_SETUP_DIR . 'templates/' . $template . '.php')){
			include SWIFT_PERFORMANCE_SETUP_DIR . 'templates/' . $template . '.php';
		}
	}

	/**
	 * Function to overwrite <title> tag
	 */
	public function wp_title(){
		return sprintf(esc_html__( '%s Setup Wizard - ', 'swift-performance' ), SWIFT_PERFORMANCE_PLUGIN_NAME);
	}

	/**
	 * Print admin notice
	 * @param array $message
	 */
	public static function print_notice($message){
		$class = ($message['type'] == 'success' ? 'updated' : ($message['type'] == 'warning' ? 'update-nag' : ($message['type'] == 'error' ? 'error' : 'notice')));
		echo '<div class="swift-performance-notice '.$class.'" style="padding:25px 10px 10px 10px;position: relative;display: block;"><span style="color:#888;position:absolute;top:5px;left:5px;">'.SWIFT_PERFORMANCE_PLUGIN_NAME.'</span>'.$message['message'].'</div>';
	}

}
