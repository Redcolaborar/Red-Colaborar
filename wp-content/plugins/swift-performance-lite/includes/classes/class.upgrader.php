<?php

class Swift_Performance_Upgrader {

      public $api_url = 'http://api.swteplugins.com/sp_v2.2/';

      public function __construct(){
            add_action('admin_init', array($this, 'upgrader'), PHP_INT_MAX);
      }

      /**
       * Get download link and start upgrade process if purchase key is valid
       */
      public function upgrader(){
            if (defined('DOING_AJAX') || !current_user_can('install_plugins')){
                  return;
            }

            if (isset($_POST['swift-performance-upgrade']) && isset($_POST['purchase-key']) && !empty($_POST['purchase-key'])){
                  $response = wp_remote_get($this->api_url . 'update/?purchase_key=' . $_POST['purchase-key'] . '&site=' . Swift_Performance_Lite::home_url());
                  if (!is_wp_error($response)){
                        if ($response['response']['code'] == 200){
                              $update_info = json_decode($response['body'], true);
                              $download_url = str_replace('[[PARAMETERS]]', '?purchase_key=' . $_POST['purchase-key'] . '&site=' . Swift_Performance_Lite::home_url(), $update_info['download_url']);
                              try {
                                    $this->do_upgrade($download_url);
                              }
                              catch (Exception $e){
                                    Swift_Performance_Lite::add_notice(sprintf(__('Upgrade failed. Error: %s'), $e->getMessage()), 'error');
                              }
                        }
                        else if ($response['response']['code'] == 401){
                              Swift_Performance_Lite::add_notice(__('Invalid purchase key'), 'error');
                        }
                        else {
                              Swift_Performance_Lite::add_notice(__('Couldn\'t connect to API server. Error code: ') . $response['response']['code'], 'error');
                        }
                  }
                  else {
                        Swift_Performance_Lite::add_notice(sprintf(__('Couldn\'t connect to API server. Error: %s') , $response->get_error_message()), 'error');
                  }
            }
      }

      public function do_upgrade($download_url){

            // Stop prebuild cache
            delete_transient('swift_performance_prebuild_cache_hit');

            $download = wp_remote_get($download_url, array('timeout' => 60));

            // Download failed
		if (is_wp_error($download)){
			throw new Exception($download->get_error_message());
		}
		else{
			// Save temporary zip file
			WP_Filesystem();
			global $wp_filesystem;
			$wp_upload_dir = wp_upload_dir();
			$filename = trailingslashit($wp_upload_dir['path']) . 'swift-performance.zip';

                  // Check is plugin dir already exists
                  if (file_exists(WP_PLUGIN_DIR . '/swift-performance')){
				throw new Exception(esc_html__('Plugin directory already exists. Please delete or rename it, and try again.', 'swift-performance'));
			}

			// Check permissions
			if (!is_writable($wp_upload_dir['path'])){
				throw new Exception(esc_html__('The upload directory isn\'t writable, please change the permissions.', 'swift-performance'));
			}
			if (!is_writable(WP_PLUGIN_DIR)){
				throw new Exception(esc_html__('The plugins directory isn\'t writable, please change the permissions.', 'swift-performance'));
			}

			// Save the downloaded zip file
			if ( ! $wp_filesystem->put_contents( $filename, $download['body'], FS_CHMOD_FILE) ) {
				throw new Exception(esc_html__('File I/O error while creating file: ', 'swift-performance') . $filename);
			}
			else{
				// Unzip the downloaded plugin
				$unzip = unzip_file($filename, WP_PLUGIN_DIR);
				if (is_wp_error($unzip)){
					throw new Exception($unzip->get_error_message());
				}
				else{
					//Remove temporary file
					unlink ($filename);
                              $is_active_for_network = is_plugin_active_for_network(SWIFT_PERFORMANCE_PLUGIN_BASENAME);
                              deactivate_plugins(SWIFT_PERFORMANCE_PLUGIN_BASENAME, true, $is_active_for_network);

                              Swift_Performance_Lite::update_option('purchase-key', $_POST['purchase-key']);
                              delete_plugins(array(SWIFT_PERFORMANCE_PLUGIN_BASENAME));

                              activate_plugin('swift-performance/performance.php', null, $is_active_for_network, true);

                              if (is_plugin_active('swift-performance/performance.php')){
                                    wp_redirect(admin_url('tools.php?page=swift-performance'));
                              }
                              else {
                                    wp_redirect(admin_url('plugins.php'));
                              }
                              die;
				}
			}
		}
      }

}

new Swift_Performance_Upgrader();

?>
