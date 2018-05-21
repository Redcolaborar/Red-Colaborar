<?php

add_filter("reduxsa/swift-performance/field/class/premium_only", function(){
      return SWIFT_PERFORMANCE_DIR . 'includes/framework/inc/premium_only/field_premium_only.php';
});

class ReduxSA_VendorURL{
      public static function get_url($handle){
            switch ($handle) {
                  case 'select2-css':
                        return ReduxSAFramework::$_url . 'assets/css/select2.css';
                  case 'select2-js':
                        return ReduxSAFramework::$_url . 'assets/js/select2.min.js';
                  case 'ace-editor-js':
                        return ReduxSAFramework::$_url . 'assets/js/ace.js';
            }

      }
}

    /**
     * ReduxSAFramework Config File
     */

    if ( ! class_exists( 'ReduxSA' ) ) {
        return;
    }

    // Get post types
    $reduxsa_post_types = Swift_Performance_Lite::get_post_types();
    $reduxsa_post_types = array_combine($reduxsa_post_types, $reduxsa_post_types);

    // Get page list
    global $wpdb;
    $reduxsa_pages = array();
    foreach ($wpdb->get_results("SELECT ID, post_title FROM {$wpdb->posts} WHERE post_type = 'page' AND post_status = 'publish'", ARRAY_A) as $_page){
          $reduxsa_pages[$_page['ID']] = $_page['post_title'];
    }

    // Check IP source automatically for GA
    foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'HTTP_CF_CONNECTING_IP','REMOTE_ADDR') as $source) {
         if (isset($_SERVER[$source]) && !empty($_SERVER[$source])){
                     $reduxsa_ga_ip_source = $source;
                     break;
         }
   }

    // Basic options
    $cache_modes = array(
          'disk_cache_rewrite' => esc_html__('Disk Cache with Rewrites', 'swift-performance'),
          'disk_cache_php' => esc_html__('Disk Cache with PHP', 'swift-performance'),
    );

    if (class_exists('Memcached')){
          $cache_modes['memcached_php'] = esc_html__('Memcached with PHP', 'swift-performance');
    }

    // Memcached support
    if (false && class_exists('Memcached')){
      $cache_modes['memcached_php'] = esc_html__('Memcached with PHP', 'swift-performance');
    }

    // Plugin based options
    $active_plugins = get_option('active_plugins');
    $is_woocommerce_active = apply_filters('swift_performance_is_woocommerce_active', in_array('woocommerce/woocommerce.php',$active_plugins));


      /**
       * Check is the cache path exists and writable
       * @param array $field
       * @param mixed $value
       * @param mixed $existing_value
       * @return array
       */
      function swift_performance_validate_cache_path_callback($field, $value, $existing_value) {
            $return['value']  = $value;
            $error = false;

            if (!file_exists($value)){
                  @mkdir($value, 0777, true);
                  if (!file_exists($value)){
                        $error = true;
                        $field['msg'] = esc_html__('Cache directory doesn\'t exists', 'swift-performance');
                  }
            }
            else if (!is_dir($value)){
                  $error = true;
                  $field['msg'] = esc_html__('Cache directory should be a directory', 'swift-performance');
            }
            else if (!is_writable($value)){
                  $error = true;
                  $field['msg'] = esc_html__('Cache directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance');
            }

            if ($error == true) {
                  $return['value']  = $existing_value;
                  $return['error']  = $field;
            }
            return $return;
    }

    /**
     * Check is the log path exists and writable
     * @param array $field
     * @param mixed $value
     * @param mixed $existing_value
     * @return array
     */
    function swift_performance_validate_log_path_callback($field, $value, $existing_value) {
          $return['value']  = $value;
          $error = false;

          // Stop here if logging isn't enabled at all
          parse_str(urldecode($_REQUEST['data']), $data);
          if (!isset($data['swift_performance_options']['enable-logging']) || $data['swift_performance_options']['enable-logging'] != 1){
               return $return;
          }

          if (!file_exists($value)){
                @mkdir($value, 0777, true);
                if (!file_exists($value)){
                      $error = true;
                      $field['msg'] = esc_html__('Log directory doesn\'t exists', 'swift-performance');
                }
          }
          else if (!is_dir($value)){
                $error = true;
                $field['msg'] = esc_html__('Log directory should be a directory', 'swift-performance');
          }
          else if (!is_writable($value)){
                $error = true;
                $field['msg'] = esc_html__('Log directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance');
          }

          if ($error == true) {
                $return['value']  = $existing_value;
                $return['error']  = $field;
          }
          return $return;
 }

    /**
     * Check is the mu-plugins path exists and writable
     * @param array $field
     * @param mixed $value
     * @param mixed $existing_value
     * @return array
     */
    function swift_performance_validate_muplugins_callback($field, $value, $existing_value) {
          $muplugins_dir = WPMU_PLUGIN_DIR;
          $return['value']  = $value;
          $error = false;

          if ($value == 1){
                if (!file_exists($muplugins_dir)){
                      @mkdir($muplugins_dir, 0777);
                      if (!file_exists($muplugins_dir)){
                            $error = true;
                            $field['msg'] = esc_html__('MU Plugins directory doesn\'t exists', 'swift-performance');
                      }
                }
                else if (!is_writable($muplugins_dir)){
                      $error = true;
                      $field['msg'] = esc_html__('MU Plugins directory isn\'t writable for WordPress. Please change the permissions.', 'swift-performance');
                }
          }

          if ($error == true) {
                $return['value']  = $existing_value;
                $return['error']  = $field;
          }
          return $return;
 }

    /**
     * Check is htaccess writable
     * @param array $field
     * @param mixed $value
     * @param mixed $existing_value
     * @return array
     */
    function swift_performance_validate_cache_mode_callback($field, $value, $existing_value) {
          $return['value']  = $value;
          $error = false;

          // Check htaccess only for Apache
          if ($value != 'disk_cache_rewrite' || Swift_Performance_Lite::server_software() != 'apache'){
                return $return;
          }

          $htaccess = ABSPATH . '.htaccess';

          if (!file_exists($htaccess)){
                @touch($htaccess);
                if (!file_exists($htaccess)){
                      $error = true;
                      $field['msg'] = esc_html__('htaccess doesn\'t exists', 'swift-performance');
                }
          }
          else if (!is_writable($htaccess)){
                $error = true;
                $field['msg'] = esc_html__('htaccess isn\'t writable for WordPress. Please change the permissions.', 'swift-performance');
          }

          if ($error == true) {
                $return['value']  = $existing_value;
                $return['error']  = $field;
          }
          return $return;
 }


    $opt_name = "swift_performance_options";

    $args = array(
        'opt_name'             => $opt_name,
        'display_name'         => esc_html__('Settings', 'swift-performance'),
        'display_version'      => false,
        'menu_type'            => 'submenu',
        'allow_sub_menu'       => true,
        'menu_title'           => 'Swift Performance',
        'page_title'           => SWIFT_PERFORMANCE_PLUGIN_NAME,
        'google_api_key'       => '',
        'google_update_weekly' => false,
        'async_typography'     => true,
        'admin_bar'            => false,
        'admin_bar_icon'       => 'dashicons-dashboard',
        'admin_bar_priority'   => 50,
        'global_variable'      => '',
        'dev_mode'             => false,
        'update_notice'        => false,
        'customizer'           => true,
        'page_priority'        => 2,
        'page_parent'          => 'tools.php',
        'page_permissions'     => 'manage_options',
        'menu_icon'            => '',
        'last_tab'             => '',
        'page_icon'            => 'icon-dashboard',
        'page_slug'            => SWIFT_PERFORMANCE_SLUG,
        'save_defaults'        => true,
        'default_show'         => false,
        'default_mark'         => '',
        'show_import_export'   => true,
        'transient_time'       => 60 * MINUTE_IN_SECONDS,
        'output'               => false,
        'output_tag'           => false,
        'database'             => '',
        'use_cdn'              => false,
        'footer_credit'        => ' ',
        'hints'                => array(
              'icon'          => 'el el-question-sign',
              'icon_position' => 'right',
              'icon_color'    => 'lightgray',
              'icon_size'     => 'normal',
              'tip_style'     => array(
                  'color'   => 'light',
                  'shadow'  => true,
                  'rounded' => false,
                  'style'   => '',
              ),
              'tip_position'  => array(
                  'my' => 'top left',
                  'at' => 'bottom right',
              ),
              'tip_effect'    => array(
                  'show' => array(
                      'effect'   => 'slide',
                      'duration' => '500',
                      'event'    => 'mouseover',
                  ),
                  'hide' => array(
                      'effect'   => 'slide',
                      'duration' => '500',
                      'event'    => 'click mouseleave',
                  ),
            ),
        )
      );

     ReduxSA::setArgs( $opt_name, $args );

    /*
     * ---> END ARGUMENTS
     */

    /*
     *
     * ---> START SECTIONS
     *
     */

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'General', 'swift-performance' ),
                 'id' => 'general-tab',
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'General', 'swift-performance' ),
                 'id' => 'general-sub',
                 'subsection' => true,
                 'fields' => array (
                        array(
                           'id'	=> 'cookies-disabled',
                           'type'	=> 'checkbox',
                           'title' => esc_html__( 'Disable Cookies', 'swift-performance' ),
                           'subtitle' => sprintf(esc_html__('You can prevent Swift Performance to create cookies on frontend.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                           'default' => 0
                        ),
                        array(
                             'id'	=> 'whitelabel',
                             'type'	=> 'premium_only',
                             'title' => esc_html__( 'Hide Footprints', 'swift-performance' ),
                             'subtitle' => sprintf(esc_html__('Prevent to add %s response header and HTML comment.', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME),
                             'default' => 0
                        ),
                        array(
                             'id'         => 'use-compute-api',
                             'type'       => 'premium_only',
                             'title'      => esc_html__( 'Use Compute API', 'swift-performance' ),
                             'subtitle'   => esc_html__('Speed up merging process and decrease CPU usage.', 'swift-performance'),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'remote-cron',
                             'type'       => 'premium_only',
                             'title'      => esc_html__( 'Enable Remote Cron', 'swift-performance' ),
                             'default'    => 0,
                        ),
                        array(
                           'id'         => 'remote-cron-frequency',
                           'type'       => 'select',
                           'title'	     => esc_html__( 'Remote Cron Frequency', 'swift-performance' ),
                           'required'  => array('remote-cron', '=', 1),
                           'options'    => array(
                                  'daily'   => esc_html__('Daily', 'swift-performance'),
                                  'twicedaily' => esc_html__('Twice a day', 'swift-performance'),
                                  'hourly' => esc_html__('Hourly', 'swift-performance'),
                           ),
                           'default'    => 'daily'
                       ),
                        array(
                             'id'	=> 'enable-logging',
                             'type'	=> 'checkbox',
                             'title' => esc_html__( 'Debug Log', 'swift-performance' ),
                             'subtitle' => esc_html__('Enable debug logging', 'swift-performance'),
                             'default' => 0
                        ),
                        array(
                            'id'         => 'loglevel',
                            'type'       => 'select',
                            'title'	     => esc_html__( 'Loglevel', 'swift-performance' ),
                            'required'  => array('enable-logging', '=', 1),
                            'options'    => array(
                                  '9'   => esc_html__('All', 'swift-performance'),
                                  '6' => esc_html__('Warning', 'swift-performance'),
                                  '1' => esc_html__('Error', 'swift-performance'),
                            ),
                            'default'    => '1'
                       ),
                        array(
                              'id'	      => 'log-path',
                              'type'	=> 'text',
                              'title'	=> esc_html__( 'Log Path', 'swift-performance' ),
                              'default'   => WP_CONTENT_DIR . '/swift-logs-'.hash('crc32', NONCE_SALT).'/',
                              'required'  => array('enable-logging', '=', 1),
                              'validate_callback' => 'swift_performance_validate_log_path_callback',
                        ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Tweaks', 'swift-performance' ),
                 'id' => 'general-tweaks',
                 'subsection' => true,
                 'fields' => array (
                        array(
                             'id'	=> 'normalize-static-resources',
                             'type'	=> 'checkbox',
                             'title'	=> esc_html__( 'Normalize Static Resources', 'swift-performance' ),
                             'subtitle' => esc_html__('Remove unnecessary query string from CSS, JS and image files.', 'swift-performance'),
                             'default' => 1
                        ),
                        array(
                             'id'         => 'dns-prefetch',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Prefetch DNS', 'swift-performance' ),
                             'subtitle'   => esc_html__('Prefetch DNS automatically.', 'swift-performance'),
                             'default'    => 1,
                        ),
                        array(
                             'id'         => 'dns-prefetch-js',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Collect domains from scripts', 'swift-performance' ),
                             'subtitle'   => esc_html__('Collect domains from scripts for DNS Prefetch.', 'swift-performance'),
                             'default'    => 1,
                             'required'   => array('dns-prefetch', '=', 1),
                        ),
                        array(
                             'id'         => 'exclude-dns-prefetch',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__('Exclude DNS Prefetch', 'swift-performance'),
                             'subtitle'   => esc_html__('Exclude domains from DNS prefetch.', 'swift-performance'),
                             'required'   => array('dns-prefetch', '=', 1),
                        ),
                        array(
                             'id'         => 'gravatar-cache',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Gravatar Cache', 'swift-performance' ),
                             'subtitle'   => esc_html__('Cache avatars.', 'swift-performance'),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'gravatar-cache-expiry',
                             'type'       => 'text',
                             'title'      => esc_html__( 'Gravatar Cache Expiry', 'swift-performance' ),
                             'subtitle'   => esc_html__('Avatar cache expiry.', 'swift-performance'),
                             'default'    => 3600,
                             'required'   => array('gravatar-cache', '=', 1),
                        ),
                        array(
                             'id'         => 'custom-htaccess',
                             'type'       => 'ace_editor',
                             'title'	=> esc_html__( 'Custom Htaccess', 'swift-performance' ),
                             'subtitle'   => esc_html__( 'You can add custom rules before Swift Performance rules in the generated htaccess', 'swift-performance' ),
                             'mode'       => 'text',
                             'theme'      => 'monokai',
                             'class'      => ''
                        ),
                  )
            )
      );


      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Heartbeat', 'swift-performance' ),
                 'id' => 'general-heartbeat',
                 'subsection' => true,
                 'fields' => array (
                        array(
                           'id'	=> 'disable-heartbeat',
                           'type'	=> 'checkbox',
                           'title' => esc_html__( 'Disable Heartbeat', 'swift-performance' ),
                           'options' => array(
                                  'index.php'                                            => esc_html__('Dashboard', 'swift-performance'),
                                  'edit.php,post.php,post-new.php'                       => esc_html__('Posts/Pages', 'swift-performance'),
                                  'upload.php,media-new.php'                             => esc_html__('Media', 'swift-performance'),
                                  'edit-comments.php,comment.php'                        => esc_html__('Comments', 'swift-performance'),
                                  'nav-menus.php'                                        => esc_html__('Menus', 'swift-performance'),
                                  'widgets.php'                                          => esc_html__('Widgets', 'swift-performance'),
                                  'theme-editor.php,plugin-editor.php'                   => esc_html__('Theme/Plugin Editor', 'swift-performance'),
                                  'users.php,user-new.php,user-edit.php,profile.php'     => esc_html__('Users', 'swift-performance'),
                                  'tools.php'                                            => esc_html__('Tools', 'swift-performance'),
                                  'options-general.php'                                  => esc_html__('Settings', 'swift-performance'),
                           ),
                           'default' => 'default'
                        ),
                        array(
                             'id'         => 'heartbeat-frequency',
                             'type'       => 'select',
                             'title'	=> esc_html__('Heartbeat Frequency', 'swift-performance'),
                             'subtitle'	=> esc_html__('Override heartbeat frequency in seconds', 'swift-performance'),
                             'options'    => array(
                                   10 => 10,
                                   20 => 20,
                                   30 => 30,
                                   40 => 40,
                                   50 => 50,
                                   60 => 60,
                                   70 => 70,
                                   80 => 80,
                                   90 => 90,
                                   100 => 100,
                                   110 => 110,
                                   120 => 120,
                                   130 => 130,
                                   140 => 140,
                                   150 => 150,
                                   160 => 160,
                                   170 => 170,
                                   180 => 180,
                                   190 => 190,
                                   200 => 200,
                                   210 => 210,
                                   220 => 220,
                                   230 => 230,
                                   240 => 240,
                                   250 => 250,
                                   260 => 260,
                                   270 => 270,
                                   280 => 280,
                                   290 => 290,
                                   300 => 300
                             ),
                        )
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Google Analytics', 'swift-performance' ),
                 'id' => 'general-ga',
                 'subsection' => true,
                 'fields' => array (
                        array(
                           'id'         => 'bypass-ga',
                           'type'       => 'checkbox',
                           'title'      => esc_html__( 'Bypass Google Analytics', 'swift-performance' ),
                           'default'    => 0,
                        ),
                        array(
                          'id'         => 'ga-tracking-id',
                          'type'       => 'text',
                          'title'      => esc_html__( 'Tracking ID', 'swift-performance' ),
                          'subtitle'   => esc_html__('Eg: UA-123456789-12', 'swift-performance'),
                          'required'   => array('bypass-ga', '=', 1),
                        ),
                        array(
                             'id'         => 'ga-ip-source',
                             'type'       => 'select',
                             'title'	=> esc_html__('IP Source', 'swift-performance'),
                             'subtitle'	=> sprintf(esc_html__('Select IP source if your server is behind proxy (eg: Cloudflare). Recommended: %s', 'swift-performance'), $reduxsa_ga_ip_source),
                             'options'    => array(
                                   'HTTP_CLIENT_IP' => 'HTTP_CLIENT_IP',
                                   'HTTP_X_FORWARDED_FOR' => 'HTTP_X_FORWARDED_FOR',
                                   'HTTP_X_FORWARDED' => 'HTTP_X_FORWARDED',
                                   'HTTP_X_CLUSTER_CLIENT_IP' => 'HTTP_X_CLUSTER_CLIENT_IP',
                                   'HTTP_FORWARDED_FOR' => 'HTTP_FORWARDED_FOR',
                                   'HTTP_FORWARDED' => 'HTTP_FORWARDED',
                                   'HTTP_CF_CONNECTING_IP' => 'HTTP_CF_CONNECTING_IP',
                                   'REMOTE_ADDR' => 'REMOTE_ADDR'
                             ),
                             'default'    => $reduxsa_ga_ip_source,
                             'required'   => array('bypass-ga', '=', 1),
                        ),
                        array(
                          'id'         => 'ga-anonymize-ip',
                          'type'       => 'checkbox',
                          'title'      => esc_html__( 'Anonymize IP', 'swift-performance' ),
                          'required'   => array('bypass-ga', '=', 1),
                          'default'    => 0
                        ),
                        array(
                           'id'         => 'delay-ga-collect',
                           'type'       => 'checkbox',
                           'title'      => esc_html__( 'Delay Collect', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Send AJAX request only after the first user interaction', 'swift-performance' ),
                           'default'    => 1,
                           'required'   => array('bypass-ga', '=', 1),
                        ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                  'title' => esc_html__( 'Media', 'swift-performance' ),
                  'id' => 'general-media',
                  'icon' => 'el el-picture',
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                  'title' => esc_html__( 'Images', 'swift-performance' ),
                  'id' => 'media-images',
                  'subsection' => true,
                  'fields' => array (
                         array(
                              'id'         => 'optimize-uploaded-images',
                              'type'       => 'premium_only',
                              'title'      => esc_html__( 'Optimize Images on Upload', 'swift-performance' ),
                              'subtitle'   => sprintf(esc_html__('Enable if you would like to optimize the images during the upload using the our Image Optimization API service. Already uploaded images can be optimized %shere%s', 'swift-performance'), '<a href="'.esc_url(add_query_arg('page', 'swift-performance-optimize-images', admin_url('upload.php'))).'" target="_blank">', '</a>'),
                              'default'    => 0,

                         ),
                         array(
                             'id'         => 'jpeg-quality',
                             'type'       => 'premium_only',
                             'min'        => 0,
                             'max'        => 100,
                             'title'      => esc_html__('JPEG quality', 'swift-performance'),
                             'subtitle'   => esc_html__('Default JPEG quality (in %) for Image Optimizer. Use 100% for lossless optimization.', 'swift-performance'),
                             'default'    => 100,
                         ),
                         array(
                             'id'         => 'png-quality',
                             'type'       => 'premium_only',
                             'min'        => 0,
                             'max'        => 100,
                             'title'      => esc_html__('PNG quality', 'swift-performance'),
                             'subtitle'   => esc_html__('Default PNG quality (in %) for Image Optimizer. Use 100% for lossless optimization.', 'swift-performance'),
                             'default'    => 100,
                         ),
                         array(
                             'id'         => 'resize-large-images',
                             'type'       => 'premium_only',
                             'title'      => esc_html__('Resize Large Images', 'swift-performance'),
                             'subtitle'   => esc_html__('Resize images which are larger than maximum width', 'swift-performance'),
                         ),
                         array(
                             'id'         => 'maximum-image-width',
                             'type'       => 'text',
                             'title'      => esc_html__('Maximum Image Width', 'swift-performance'),
                             'subtitle'   => esc_html__('Specify maximum image width (px)', 'swift-performance'),
                             'default'    => '1920',
                             'required'   => array('resize-large-images', '=', 1)
                         ),
                         array(
                             'id'         => 'keep-original-images',
                             'type'       => 'premium_only',
                             'title'      => esc_html__('Keep Original Images', 'swift-performance'),
                             'subtitle'   => esc_html__('If you enable this option the image optimizer will keep original images.', 'swift-performance'),
                             'default'    => 1,
                         ),
                         array(
                             'id'         => 'base64-small-images',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Inline Small Images', 'swift-performance' ),
                             'subtitle'   => esc_html__('Use base64 encoded inline images for small images', 'swift-performance'),
                             'default'    => 0,
                         ),
                         array(
                             'id'         => 'base64-small-images-size',
                             'type'       => 'text',
                             'title'      => esc_html__( 'File Size Limit (bytes)', 'swift-performance' ),
                             'subtitle'   => esc_html__('File size limit for inline images', 'swift-performance'),
                             'default'    => '1000',
                             'required'   => array('base64-small-images', '=', 1),
                         ),
                         array(
                              'id'         => 'exclude-base64-small-images',
                              'type'       => 'multi_text',
                              'title'	=> esc_html__('Exclude Images', 'swift-performance'),
                              'subtitle'   => esc_html__('Exclude images from being embedded if one of these strings is found in the match.', 'swift-performance'),
                              'required'   => array('base64-small-images', '=', 1),
                         ),
                         array(
                              'id'         => 'lazy-load-images',
                              'type'       => 'checkbox',
                              'title'      => esc_html__( 'Lazyload', 'swift-performance' ),
                              'subtitle'   => esc_html__('Enable if you would like lazy load for images.', 'swift-performance'),
                              'default'    => 1
                         ),
                         array(
                              'id'         => 'exclude-lazy-load',
                              'type'       => 'multi_text',
                              'title'	=> esc_html__('Exclude Images', 'swift-performance'),
                              'subtitle'   => esc_html__('Exclude images from being lazy loaded if one of these strings is found in the match.', 'swift-performance'),
                              'required'   => array('lazy-load-images', '=', 1),
                         ),
                         array(
                              'id'         => 'load-images-on-user-interaction',
                              'type'       => 'checkbox',
                              'title'      => esc_html__( 'Load Images on User Interaction', 'swift-performance' ),
                              'subtitle'   => esc_html__('Enable if you would like to load full images only on user interaction (mouse move, sroll, touchstart)', 'swift-performance'),
                              'default'    => 0,
                              'required'   => array('lazy-load-images', '=', 1),
                         ),
                         array(
                             'id'         => 'base64-lazy-load-images',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Inline Lazy Load Images', 'swift-performance' ),
                             'subtitle'   => esc_html__('Use base64 encoded inline images for lazy load', 'swift-performance'),
                             'default'    => 1,
                             'required'   => array('lazy-load-images', '=', 1),
                         ),
                         array(
                             'id'         => 'force-responsive-images',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Force Responsive Images', 'swift-performance' ),
                             'subtitle'   => esc_html__('Force all images to use srcset attribute if it is possible', 'swift-performance'),
                             'default'    => 0,
                         ),
                   )
             )
      );

      ReduxSA::setSection ( $opt_name, array (
                  'title' => esc_html__( 'Embeds', 'swift-performance' ),
                  'id' => 'media-embeds',
                  'subsection' => true,
                  'fields' => array (
                         array(
                              'id'         => 'lazyload-iframes',
                              'type'       => 'checkbox',
                              'title'      => esc_html__( 'Lazy Load Iframes', 'swift-performance' ),
                              'subtitle'   => esc_html__('Enable if you would like lazy load for iframes.', 'swift-performance'),
                              'default'    => 0
                         ),
                         array(
                              'id'         => 'exclude-iframe-lazyload',
                              'type'       => 'multi_text',
                              'title'	=> esc_html__('Exclude Iframes', 'swift-performance'),
                              'subtitle'   => esc_html__('Exclude iframes from being lazy loaded if one of these strings is found in the match.', 'swift-performance'),
                              'required'   => array('lazyload-iframes', '=', 1),
                         ),
                         array(
                              'id'         => 'load-iframes-on-user-interaction',
                              'type'       => 'checkbox',
                              'title'      => esc_html__( 'Load Iframes on User Interaction', 'swift-performance' ),
                              'subtitle'   => esc_html__('Enable if you would like to load iframes only on user interaction (mouse move, sroll, touchstart)', 'swift-performance'),
                              'default'    => 0,
                              'required'   => array('lazyload-iframes', '=', 1),
                         ),
                   )
             )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Optimization', 'swift-performance' ),
                 'id' => 'asset-manager-tab',
                 'icon' => 'el el-list-alt',
           )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'General', 'swift-performance' ),
                 'id' => 'asset-manager-general',
                 'subsection' => true,
                 'fields' => array (
                        array(
                             'id'         => 'merge-assets-logged-in-users',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Merge Assets for Logged in Users', 'swift-performance' ),
                             'subtitle'   => esc_html__('Enable if you would like to merge styles and scripts for logged in users as well.', 'swift-performance'),
                             'default'    => 0
                        ),
                        array(
                             'id'         => 'optimize-prebuild-only',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Optimize Prebuild Only', 'swift-performance' ),
                             'subtitle'   => esc_html__('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize the page, only when prebuild cache process is running.', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array('enable-caching', '=', 1)
                        ),
                        array(
                             'id'         => 'merge-background-only',
                             'type'       => 'checkbox',
                             'title'      => esc_html__( 'Optimize in Background', 'swift-performance' ),
                             'subtitle'   => esc_html__('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize page in the background.', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array('enable-caching', '=', 1)
                        ),
                        array(
                             'id'         => 'html-auto-fix',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Fix Invalid HTML', 'swift-performance' ),
                             'subtitle'	=> esc_html__( 'Try to fix invalid HTML', 'swift-performance' ),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'minify-html',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Minify HTML', 'swift-performance' ),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'disable-emojis',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Disable Emojis', 'swift-performance' ),
                             'default'    => 0,
                        ),
                        array(
                             'id'	=> 'limit-threads',
                             'type'	=> 'checkbox',
                             'title' => esc_html__( 'Limit Simultaneous Threads', 'swift-performance' ),
                             'subtitle' => esc_html__('Limit maximum simultaneous threads. It can be useful on shared hosting environment to avoid 508 errors.', 'swift-performance'),
                             'default' => 0
                        ),
                        array(
                             'id'         => 'max-threads',
                             'type'       => 'text',
                             'title'	=> esc_html__('Maximum Threads', 'swift-performance'),
                             'subtitle'   => esc_html__('Number of maximum simultaneous threads.', 'swift-performance'),
                             'default'    => 3,
                             'required'   => array('limit-threads', '=', 1),
                        ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Scripts', 'swift-performance' ),
                 'id' => 'asset-manager-js',
                 'subsection' => true,
                 'fields' => array (
                        array(
                             'id'         => 'merge-scripts',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Merge Scripts', 'swift-performance' ),
                             'subtitle'   => esc_html__('Merge javascript files to reduce number of HTML requests ', 'swift-performance'),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'async-scripts',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Async Execute (BETA)', 'swift-performance' ),
                             'subtitle'   => esc_html__('Execute merged javascript files asynchronously', 'swift-performance'),
                             'required'   => array(
                                   array('merge-scripts', '=', 1),
                             )
                        ),
                        array(
                             'id'         => 'merge-scripts-exlude-3rd-party',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Exclude 3rd Party Scripts', 'swift-performance' ),
                             'subtitle'   => esc_html__('Exclude 3rd party scripts from merged scripts', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'exclude-scripts',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__('Exclude Scripts', 'swift-performance'),
                             'subtitle'   => esc_html__('Exclude scripts from being merged if one of these strings is found in the match.', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'exclude-inline-scripts',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__('Exclude Inline Scripts', 'swift-performance'),
                             'subtitle'   => esc_html__('Exclude scripts from being merged if one of these strings is found in the match.', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'exclude-script-localizations',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Exclude Script Localizations', 'swift-performance'),
                             'subtitle'   => esc_html__('Exclude javascript localizations from merged scripts.', 'swift-performance'),
                             'default'    => 1,
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'minify-scripts',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Minify Javascripts', 'swift-performance' ),
                             'default'    => 1,
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'use-script-compute-api',
                             'type'       => 'premium_only',
                             'title'	=> esc_html__( 'Minify with API', 'swift-performance' ),
                             'subtitle'   => esc_html__('Use Compute API for minify. Regarding that this minify method can be slower, use this option only if default JS minify cause javascript errors. ', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array(
                                   array('exclude-script-localizations', '=', 1),
                                   array('merge-scripts', '=', 1),
                                   array('minify-scripts', '=', 1),
                             )
                        ),
                        array(
                             'id'         => 'proxy-3rd-party-assets',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Proxy 3rd Party Assets', 'swift-performance' ),
                             'subtitle'	=> esc_html__( 'Proxy 3rd party javascript and CSS files which created by javascript (eg: Google Analytics)', 'swift-performance' ),
                             'default'    => 0,
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'include-3rd-party-assets',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__( '3rd Party Assets', 'swift-performance' ),
                             'subtitle'   => esc_html__('List scripts (full URL) which should being proxied.', 'swift-performance'),
                             'required'   => array('proxy-3rd-party-assets', '=', 1),
                        ),
                        array(
                             'id'         => 'separate-js',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Separate Scripts', 'swift-performance' ),
                             'subtitle'   => esc_html__('If you enable this option the plugin will save merged JS files for pages separately', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'inline-merged-scripts',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Print merged scripts inline', 'swift-performance' ),
                             'subtitle'   => esc_html__('Enable if you would like to print merged scripts into the footer, instead of a seperated file.', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'lazy-load-scripts',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__('Lazy Load Scripts', 'swift-performance'),
                             'subtitle'   => esc_html__('Load scripts only after first user interaction, if one of these strings is found in the match.', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                        array(
                             'id'         => 'include-scripts',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__( 'Include Scripts', 'swift-performance' ),
                             'subtitle'   => esc_html__('Include scripts manually. With this option you can preload script files what are loaded with javascript', 'swift-performance'),
                             'required'   => array('merge-scripts', '=', 1),
                        ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Styles', 'swift-performance' ),
                 'id' => 'asset-manager-css',
                 'subsection' => true,
                 'fields' => array (
                        array(
                             'id'         => 'merge-styles',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Merge Styles', 'swift-performance' ),
                             'subtitle'   => esc_html__('Merge CSS files to reduce number of HTML requests', 'swift-performance'),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'critical-css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Generate Critical CSS', 'swift-performance' ),
                             'default'    => 0,
                             'required'   => array('merge-styles', '=', 1),
                        ),
                        array(
                             'id'         => 'extra-critical-css',
                             'type'       => 'ace_editor',
                             'title'	=> esc_html__( 'Extra Critical CSS', 'swift-performance' ),
                             'subtitle'   => esc_html__( 'You can add extra CSS to Critical CSS here', 'swift-performance' ),
                             'mode'       => 'css',
                             'theme'    => 'monokai',
                             'required'   => array(
                                  array('merge-styles', '=', 1),
                                  array('critical-css', '=', 1),
                              ),
                        ),
                        array(
                             'id'         => 'disable-full-css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Disable Full CSS', 'swift-performance'),
                             'subtitle'   => esc_html__('Load Critical CSS only. Be careful, it may can cause styling issues.', 'swift-performance'),
                             'required'   => array(
                                   array('merge-styles', '=', 1),
                                   array('critical-css', '=', 1),
                             ),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'compress-css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Compress CSS', 'swift-performance' ),
                             'subtitle'	=> esc_html__( 'Extra compress for critical CSS', 'swift-performance' ),
                             'default'    => 0,
                             'required'   => array(
                                   array('merge-styles', '=', 1),
                                   array('critical-css', '=', 1),
                             ),
                        ),
                        array(
                             'id'         => 'remove-keyframes',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Remove Keyframes', 'swift-performance' ),
                             'subtitle'	=> esc_html__( 'Remove CSS animations from critical CSS', 'swift-performance' ),
                             'default'    => 1,
                             'required'   => array(
                                   array('merge-styles', '=', 1),
                                   array('critical-css', '=', 1),
                             ),
                        ),
                        array(
                             'id'         => 'inline_critical_css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Print critical CSS inline', 'swift-performance'),
                             'subtitle'   => esc_html__('Enable if you would like to print the critical CSS into the header, instead of a seperated CSS file.', 'swift-performance'),
                             'required'   => array(
                                   array('merge-styles', '=', 1),
                                   array('critical-css', '=', 1),
                             ),
                             'default'    => 1,
                        ),
                        array(
                             'id'         => 'inline_full_css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Print full CSS inline', 'swift-performance'),
                             'subtitle'   => esc_html__('Enable if you would like to print the merged CSS into the footer, instead of a seperated CSS file.', 'swift-performance'),
                             'required'   => array('merge-styles', '=', 1),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'separate-css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Separate Styles', 'swift-performance' ),
                             'subtitle'   => esc_html__('If you enable this option the plugin will save merged CSS files for pages separately', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array('merge-styles', '=', 1),
                        ),
                        array(
                             'id'         => 'minify-css',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__( 'Minify CSS', 'swift-performance' ),
                             'default'    => 1,
                             'required'   => array('merge-styles', '=', 1),
                        ),
                        array(
                             'id'         => 'bypass-css-import',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Bypass CSS Import', 'swift-performance'),
                             'subtitle'   => esc_html__('Include imported CSS files in merged styles.', 'swift-performance'),
                             'default'    => 0,
                             'required'   => array('merge-styles', '=', 1),
                        ),
                        array(
                             'id'         => 'merge-styles-exclude-3rd-party',
                             'type'       => 'checkbox',
                             'title'	=> esc_html__('Exclude 3rd Party CSS', 'swift-performance'),
                             'subtitle'   => esc_html__('Exclude 3rd party CSS files (eg: Google Fonts CSS) from merged styles', 'swift-performance'),
                             'required'   => array('merge-styles', '=', 1),
                             'default'    => 0,
                        ),
                        array(
                             'id'         => 'exclude-styles',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__( 'Exclude Styles', 'swift-performance' ),
                             'subtitle'   => esc_html__('Exclude style from being merged if one of these strings is found in the match. ', 'swift-performance'),
                             'required'   => array('merge-styles', '=', 1),
                        ),
                        array(
                             'id'         => 'include-styles',
                             'type'       => 'multi_text',
                             'title'	=> esc_html__( 'Include Styles', 'swift-performance' ),
                             'subtitle'   => esc_html__('Include styles manually. With this option you can preload css files what are loaded with javascript', 'swift-performance'),
                             'required'   => array('merge-styles', '=', 1),
                        ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Caching', 'swift-performance' ),
                 'id' => 'cache-tab',
                 'icon' => 'el el-graph'
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'General', 'swift-performance' ),
                 'id' => 'cache-general',
                 'subsection' => true,
                 'fields' => array (
                       array(
                             'id'         => 'enable-caching',
                             'type'	      => 'checkbox',
                             'title'      => esc_html__( 'Enable Caching', 'swift-performance' ),
                             'default'    => 1,
                       ),
                       array(
                             'id'                => 'caching-mode',
                             'type'              => 'select',
                             'title'	       => esc_html__( 'Caching Mode', 'swift-performance' ),
                             'options'           => $cache_modes,
                             'default'           => 'disk_cache_php',
                             'required'          => array('enable-caching', '=', 1),
                             'validate_callback' => 'swift_performance_validate_cache_mode_callback'
                       ),
                       array(
                              'id'	      => 'memcached-host',
                              'type'	=> 'text',
                              'title'	=> esc_html__( 'Memcached Host', 'swift-performance' ),
                              'default'   => 'localhost',
                              'required'  => array(
                                    array('caching-mode', '=', 'memcached_php'),
                                    array('enable-caching', '=', 1)
                              ),
                       ),
                       array(
                              'id'	      => 'memcached-port',
                              'type'	=> 'text',
                              'title'	=> esc_html__( 'Memcached Port', 'swift-performance' ),
                              'default'   => '11211',
                              'required'  => array(
                                    array('caching-mode', '=', 'memcached_php'),
                                    array('enable-caching', '=', 1)
                              ),
                       ),
                       array(
                              'id'	      => 'early-load',
                              'type'	=> 'checkbox',
                              'title'	=> esc_html__( 'Early Loader', 'swift-performance' ),
                              'subtitle'  => sprintf(esc_html__( 'Use %s Loader mu-plugin ', 'swift-performance' ), SWIFT_PERFORMANCE_PLUGIN_NAME),
                              'default'   => 1,
                              'required'  => array(
                                    array('enable-caching', '=', 1)
                              ),
                              'validate_callback' => 'swift_performance_validate_muplugins_callback',
                       ),
                       array(
                              'id'	      => 'cache-path',
                              'type'	=> 'text',
                              'title'	=> esc_html__( 'Cache Path', 'swift-performance' ),
                              'default'   => WP_CONTENT_DIR . '/cache/',
                              'required'  => array(
                                    array('caching-mode', 'contains', 'disk_cache'),
                                    array('enable-caching', '=', 1)
                              ),
                              'validate_callback' => 'swift_performance_validate_cache_path_callback',
                       ),
                       array(
                            'id'         => 'cache-expiry-mode',
                            'type'       => 'select',
                            'title'	     => esc_html__( 'Cache Expiry Mode', 'swift-performance' ),
                            'required'   => array('enable-caching', '=', 1),
                            'options'    => array(
                                  'timebased'   => esc_html__('Time based mode', 'swift-performance'),
                                  'actionbased' => esc_html__('Action based mode', 'swift-performance'),
                                  'intelligent' => esc_html__('Intelligent mode', 'swift-performance'),
                            ),
                            'default'    => 'timebased'
                       ),
                       array(
                              'id'	      => 'cache-expiry-time',
                              'type'	=> 'select',
                              'title'	=> esc_html__( 'Cache Expiry Time', 'swift-performance' ),
                              'subtitle'  => esc_html__( 'Clear cached pages after specified time', 'swift-performance' ),
                              'options'   => array(
                                    '1800'      => '30 mins',
                                    '3600'      => '1 hour',
                                    '7200'      => '2 hours',
                                    '21600'     => '6 hours',
                                    '43200'     => '12 hours',
                                    '86400'     => '1 day',
                                    '172800'    => '2 days'
                              ),
                              'default' => '43200',
                              'required'  => array('cache-expiry-mode', '=', 'timebased')
                       ),
                       array(
                              'id'	      => 'cache-garbage-collection-time',
                              'type'	=> 'select',
                              'title'	=> esc_html__( 'Garbage Collection Interval', 'swift-performance' ),
                              'subtitle'  => esc_html__( 'How often should check the expired cached pages (in seconds)', 'swift-performance' ),
                              'options'   => array(
                                    '600'       => '10 mins',
                                    '1800'      => '30 mins',
                                    '3600'      => '1 hour',
                                    '7200'      => '2 hours',
                                    '21600'     => '6 hours',
                                    '43200'     => '12 hours',
                                    '86400'     => '1 day',
                              ),
                              'default'   => '1800',
                              'required'  => array('cache-expiry-mode', '=', 'timebased')
                       ),
                       array(
                           'id'         => 'clear-page-cache-after-post',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Clear Page Cache After Post Update', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages where cache should be cleared after publish/update post.', 'swift-performance' ),
                           'options'    => $reduxsa_pages,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'resource-saving-mode',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Resource saving mode', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'This option will reduce intelligent cache check requests. Recommended for limited resource severs', 'swift-performance' ),
                           'default'     => 1,
                           'required'  => array('cache-expiry-mode', '=', 'intelligent')
                       ),
                       array(
                           'id'          => 'disable-instant-reload',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Disable Instant Reload', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'If you disable instant reload the plugin will override the cache if intelligent cache detect changes, however it won\'t replace the page content instantly for the user.', 'swift-performance' ),
                           'default'     => 1,
                           'required'  => array('cache-expiry-mode', '=', 'intelligent')
                       ),
                       array(
                           'id'          => 'enable-caching-logged-in-users',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Enable Caching for logged in users', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'This option can increase the total cache size, depending on the count of your users.', 'swift-performance' ),
                           'default'     => 0,
                           'required'    => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'shared-logged-in-cache',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Shared Logged in Cache', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'If you enable this option logged in users won\'t have separate private cache, but they will get content from public cache', 'swift-performance' ),
                           'default'     => 0,
                           'required'    => array(
                                 array('enable-caching', '=', 1),
                                 array('enable-caching-logged-in-users', '=', 1),
                           )
                       ),
                       array(
                           'id'          => 'mobile-support',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Enable Mobile Device Support', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'You can create separate cache for mobile devices, it can be useful if your site not just responsive, but it has a separate mobile theme/layout (eg: AMP). ', 'swift-performance' ),
                           'default'     => 0,
                           'required'    => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'browser-cache',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Enable Browser Cache', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'If you enable this option it will generate htacess/nginx rules for browser cache. (Expire headers should be configured on your server as well)', 'swift-performance' ),
                           'default'     => 1,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'enable-gzip',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Enable Gzip', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'If you enable this option it will generate htacess/nginx rules for gzip compression. (Compression should be configured on your server as well)', 'swift-performance' ),
                           'default'     => 1,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => '304-header',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Send 304 Header', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'cache-404',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Cache 404 pages', 'swift-performance' ),
                           'default'     => 1,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'         => 'exclude-post-types',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Exclude Post Types', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select post types which shouldn\'t be cached.', 'swift-performance' ),
                           'required'   => array('enable-caching', '=', 1),
                           'options'    => $reduxsa_post_types
                       ),
                       array(
                           'id'         => 'exclude-pages',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Exclude Pages', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages which shouldn\'t be cached.', 'swift-performance' ),
                           'required'   => array('enable-caching', '=', 1),
                           'options'    => $reduxsa_pages
                       ),
                       array(
                           'id'         => 'exclude-strings',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Exclude URLs', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'URLs which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance' ),
                           'required'   => array('enable-caching', '=', 1),
                           'default'    => 0,
                       ),
                       array(
                           'id'         => 'exclude-content-parts',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Exclude Content Parts', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Pages which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance' ),
                           'required'   => array('enable-caching', '=', 1),
                           'default'    => 0,
                       ),
                       array(
                           'id'         => 'exclude-useragents',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Exclude User Agents', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'User agents which contains that string won\'t be cached. Use leading/trailing # for regex', 'swift-performance' ),
                           'required'   => array('enable-caching', '=', 1),
                           'default'    => 0,
                       ),
                       array(
                           'id'          => 'exclude-crawlers',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Exclude Crawlers', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'Exclude known crawlers from cache', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'dynamic-caching',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Enable Dynamic Caching', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'If you enable this option you can specify cacheable $_GET and $_POST requests', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'         => 'cacheable-dynamic-requests',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Cacheable Dynamic Requests', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Specify $_GET and/or $_POST keys what should be cached. Eg: "s" to cache search requests', 'swift-performance' ),
                           'default'    => 0,
                           'required'   => array('dynamic-caching', '=', 1)
                       ),
                       array(
                           'id'         => 'cacheable-ajax-actions',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Cacheable AJAX Actions', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'With this option you can cache resource-intensive AJAX requests', 'swift-performance' ),
                           'default'    => 0,
                           'required'   => array('enable-caching', '=', 1)
                       ),
                       array(
                           'id'         => 'ajax-cache-expiry-time',
                           'type'	    => 'text',
                           'title'	    => esc_html__( 'AJAX Cache Expiry Time', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Cache expiry time for AJAX requests in seconds', 'swift-performance' ),
                           'default'    => '1440',
                           'required'   => array('enable-caching', '=', 1),
                       ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Warmup', 'swift-performance' ),
                 'id' => 'cache-warmup',
                 'subsection' => true,
                 'fields' => array (
                       array(
                           'id'          => 'enable-remote-prebuild-cache',
                           'type'        => 'premium_only',
                           'title'       => esc_html__( 'Enable Remote Prebuild Cache', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Use API to prebuild cache.', 'swift-performance' ),
                           'default'     => 0,
                       ),
                       array(
                           'id'          => 'automated_prebuild_cache',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Prebuild Cache Automatically', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'This option will prebuild the cache after it was cleared', 'swift-performance' ),
                           'default'     => 0,
                       ),
                       array(
                           'id'          => 'discover-warmup',
                           'type'        => 'premium_only',
                           'title'       => esc_html__( 'Discover New Pages', 'swift-performance' ),
                           'subtitle'    => esc_html__( 'Let the plugin to discover new pages for warmup (eg: pagination, plugin-created pages, etc)', 'swift-performance' ),
                           'default'     => 1,
                       ),
                       array(
                           'id'          => 'cache-author',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Prebuild Author Pages', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'cache-archive',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Prebuildhe Archive', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'cache-rest',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Prebuild REST URLs', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                       array(
                           'id'          => 'cache-feed',
                           'type'        => 'checkbox',
                           'title'       => esc_html__( 'Prebuild Feed', 'swift-performance' ),
                           'default'     => 0,
                           'required'   => array('enable-caching', '=', 1),
                       ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Varnish', 'swift-performance' ),
                 'id' => 'cache-varnish',
                 'subsection' => true,
                 'fields' => array (
                       array(
                             'id'         => 'varnish-auto-purge',
                             'type'	      => 'checkbox',
                             'title'      => esc_html__( 'Enable Auto Purge', 'swift-performance' ),
                             'default'    => 0,
                       ),
                       array(
                          'id'         => 'custom-varnish-host',
                          'type'	    => 'text',
                          'title'	    => esc_html__( 'Custom Host', 'swift-performance' ),
                          'subtitle'   => esc_html__( 'If you are using proxy (eg: Cloudflare) you may will need this option', 'swift-performance' ),
                          'default'    => '',
                          'required'  => array(
                                 array('varnish-auto-purge', '=', '1')
                          )
                       ),
                  )
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'Appcache', 'swift-performance' ),
                 'id' => 'appcache',
                 'subsection' => true,
                 'fields' => array (
                       array(
                             'id'         => 'appcache-warning_caching-mode',
                             'type'	      => 'info',
                             'title'      => esc_html__( 'Appcache requires disk cache', 'swift-performance' ),
                             'style'      => 'warning',
                             'desc'       => esc_html__( 'You need to change caching mode to "Disk Cache with Rewrites" or Disk Cache with PHP in order to enable using Appcache', 'swift-performance' ),
                             'required'   => array('caching-mode', 'not_contain', 'disk_cache'),
                       ),
                       array(
                             'id'         => 'appcache-warning_cookies-disabled',
                             'type'	      => 'info',
                             'title'      => esc_html__( 'Cookies Disabled', 'swift-performance' ),
                             'style'      => 'warning',
                             'desc'       => esc_html__( 'Appcache requires cookies', 'swift-performance' ),
                             'required'   => array('cookies-disabled', '=', 1),
                       ),
                       array(
                            'id'         => 'appcache-warning_caching',
                            'type'	      => 'info',
                            'title'      => esc_html__( 'Enable caching in order to use Appcache', 'swift-performance' ),
                            'style'      => 'warning',
                            'required'   => array('enable-caching', '!=', 1),
                       ),
                       array(
                             'id'         => 'appcache-desktop',
                             'type'	      => 'checkbox',
                             'title'      => esc_html__( 'Enable Appcache for Desktop', 'swift-performance' ),
                             'default'    => 0,
                             'required'   => array('caching-mode', 'contains', 'disk_cache'),
                       ),
                       array(
                          'id'            => 'appcache-desktop-mode',
                          'type'          => 'select',
                          'title'         => esc_html__( 'Appcache Mode', 'swift-performance' ),
                          'options'       => array(
                                'full-site'      => esc_html__('Full site', 'swift-performance'),
                                'specific-pages' => esc_html__('Specific pages only', 'swift-performance'),
                          ),
                          'default'       => 'full-site',
                          'required'      => array(
                                 array('appcache-desktop', '=', '1')
                          )
                       ),
                       array(
                         'id'            => 'appcache-desktop-max',
                         'type'          => 'text',
                         'title'         => esc_html__( 'Desktop Max Size', 'swift-performance' ),
                         'subtitle'      => esc_html__( 'Appcache maximum full size on desktop devices', 'swift-performance' ),
                         'default'       => '104857600',
                         'required'      => array(
                                 array('appcache-desktop', '=', '1')
                         )
                       ),
                       array(
                           'id'         => 'appcache-desktop-included-pages',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Include Pages', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages which should be cached with Appcache.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-desktop', '=', 1),
                              array('appcache-desktop-mode', '=', 'specific-pages'),
                           ),
                           'options'    => $reduxsa_pages
                       ),
                       array(
                           'id'         => 'appcache-desktop-included-strings',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Include Strings', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Cache pages with Appcache only if one of these strings is found in the URL.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-desktop', '=', 1),
                              array('appcache-desktop-mode', '=', 'specific-pages'),
                           ),
                       ),
                       array(
                           'id'         => 'appcache-desktop-excluded-pages',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Exclude Pages', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages which shouldn\'t be cached with Appcache.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-desktop', '=', 1),
                              array('appcache-desktop-mode', '=', 'full-site'),
                           ),
                           'options'    => $reduxsa_pages
                       ),
                       array(
                           'id'         => 'appcache-desktop-excluded-strings',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Exclude Strings', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Exclude pages from Appcache if one of these strings is found in the URL.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-desktop', '=', 1),
                              array('appcache-desktop-mode', '=', 'full-site'),
                           ),
                       ),


                       array(
                             'id'         => 'appcache-mobile',
                             'type'	      => 'checkbox',
                             'title'      => esc_html__( 'Enable Appcache for Mobile', 'swift-performance' ),
                             'default'    => 0,
                             'required'   => array('caching-mode', 'contains', 'disk_cache'),
                       ),
                       array(
                          'id'            => 'appcache-mobile-mode',
                          'type'          => 'select',
                          'title'         => esc_html__( 'Appcache Mode', 'swift-performance' ),
                          'options'       => array(
                                'full-site'      => esc_html__('Full site', 'swift-performance'),
                                'specific-pages' => esc_html__('Specific pages only', 'swift-performance'),
                          ),
                          'default'       => 'full-site',
                          'required'      => array(
                                 array('appcache-mobile', '=', '1')
                          )
                       ),
                       array(
                         'id'            => 'appcache-mobile-max',
                         'type'          => 'text',
                         'title'         => esc_html__( 'Mobile Max Size', 'swift-performance' ),
                         'subtitle'      => esc_html__( 'Appcache maximum full size on desktop devices', 'swift-performance' ),
                         'default'       => '5242880',
                         'required'      => array(
                                 array('appcache-mobile', '=', '1')
                         )
                       ),
                       array(
                           'id'         => 'appcache-mobile-included-pages',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Include Pages', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages which should be cached with Appcache.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-mobile', '=', 1),
                              array('appcache-mobile-mode', '=', 'specific-pages'),
                           ),
                           'options'    => $reduxsa_pages
                       ),
                       array(
                           'id'         => 'appcache-mobile-included-strings',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Include Strings', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Cache pages with Appcache only if one of these strings is found in the URL.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-mobile', '=', 1),
                              array('appcache-mobile-mode', '=', 'specific-pages'),
                           ),
                       ),
                       array(
                           'id'         => 'appcache-mobile-excluded-pages',
                           'type'       => 'select',
                           'multi'      => true,
                           'title'      => esc_html__( 'Exclude Pages', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Select pages which shouldn\'t be cached with Appcache.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-mobile', '=', 1),
                              array('appcache-mobile-mode', '=', 'full-site'),
                           ),
                           'options'    => $reduxsa_pages
                       ),
                       array(
                           'id'         => 'appcache-mobile-excluded-strings',
                           'type'       => 'multi_text',
                           'title'      => esc_html__( 'Exclude Strings', 'swift-performance' ),
                           'subtitle'   => esc_html__( 'Exclude pages from Appcache if one of these strings is found in the URL.', 'swift-performance' ),
                           'required'   => array(
                              array('appcache-mobile', '=', 1),
                              array('appcache-mobile-mode', '=', 'full-site'),
                           ),
                       ),
                  )
            )
      );

      if ($is_woocommerce_active){
            ReduxSA::setSection ( $opt_name, array (
                       'title' => esc_html__( 'WooCommerce', 'swift-performance' ),
                       'id' => 'cache-woocommerce',
                       'subsection' => true,
                       'fields' => array (
                             array(
                                   'id'         => 'cache-empty-minicart',
                                   'type'	      => 'checkbox',
                                   'title'      => esc_html__( 'Cache Empty Minicart', 'swift-performance' ),
                                   'default'    => 0,
                             ),
                        )
                  )
            );
      }

      ReduxSA::setSection ( $opt_name, array (
                  'title' => esc_html__( 'CDN', 'swift-performance' ),
                  'desc' => __('Speed up your website with', 'swift-performance').' <a href="//tracking.maxcdn.com/c/258716/3968/378" target="_blank">MaxCDN</a>',
                  'id' => 'cdn-tab',
                  'icon' => 'el el-tasks',
            )
      );

      ReduxSA::setSection ( $opt_name, array (
                 'title' => esc_html__( 'General', 'swift-performance' ),
                 'id' => 'cdn-general',
                 'subsection' => true,
                 'fields' => array (
                       array(
                                   'id'	=> 'enable-cdn',
                                   'type'	=> 'checkbox',
                                   'title' => esc_html__( 'Enable CDN', 'swift-performance' ),
                                   'default' => 0
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-master',
                                   'type'	=> 'text',
                                   'title'	=> esc_html__( 'CDN Hostname', 'swift-performance' ),
                                   'required' => array('enable-cdn', '=', 1)
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-slot-1',
                                   'type'	=> 'text',
                                   'title' => esc_html__( 'CDN Hostname for Javascript ', 'swift-performance' ),
                                   'required' => array('cdn-hostname-master', '!=', ''),
                                   'subtitle' => esc_html__('Use different hostname for javascript files', 'swift-performance'),
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-slot-2',
                                   'type'	=> 'text',
                                   'title'	=> esc_html__( 'CDN Hostname for Media files', 'swift-performance' ),
                                   'required' => array('cdn-hostname-slot-1', '!=', ''),
                                   'subtitle' => esc_html__('Use different hostname for media files', 'swift-performance'),
                       ),
                       array(
                                   'id'	=> 'enable-cdn-ssl',
                                   'type'	=> 'checkbox',
                                   'title'	=> esc_html__( 'Enable CDN on SSL', 'swift-performance' ),
                                   'default' => 0,
                                   'subtitle' => esc_html__('You can specify different hostname(s) for SSL, or leave them blank for use the same host on HTTP and SSL', 'swift-performance'),
                                   'required' => array('enable-cdn', '=', 1)
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-master-ssl',
                                   'type'	=> 'text',
                                   'title'	=> esc_html__( 'SSL CDN Hostname', 'swift-performance' ),
                                   'required' => array('enable-cdn-ssl', '=', 1)
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-slot-1-ssl',
                                   'type'	=> 'text',
                                   'title'	=> esc_html__( 'CDN Hostname for Javascript ', 'swift-performance' ),
                                   'required' => array('cdn-hostname-master-ssl', '!=', ''),
                                   'subtitle' => esc_html__('Use different hostname for javascript files', 'swift-performance'),
                       ),
                       array(
                                   'id'	=> 'cdn-hostname-slot-2-ssl',
                                   'type'	=> 'text',
                                   'title'	=> esc_html__( 'CDN Hostname for Media files', 'swift-performance' ),
                                   'required' => array('cdn-hostname-slot-1-ssl', '!=', ''),
                                   'subtitle' => esc_html__('Use different hostname for media files', 'swift-performance'),
                       ),
                  )

            ));

            ReduxSA::setSection ( $opt_name, array (
                       'title' => esc_html__( 'Cloudflare', 'swift-performance' ),
                       'id' => 'cdn-cloudflare',
                       'subsection' => true,
                       'fields' => array (
                             array(
                                   'id'         => 'cloudflare-auto-purge',
                                   'type'	      => 'checkbox',
                                   'title'      => esc_html__( 'Enable Auto Purge', 'swift-performance' ),
                                   'default'    => 0,
                             ),
                             array(
                                'id'         => 'cloudflare-email',
                                'type'	    => 'text',
                                'title'	    => esc_html__( 'Cloudflare Account E-mail', 'swift-performance' ),
                                'default'    => '',
                                'required'  => array(
                                       array('cloudflare-auto-purge', '=', '1')
                                )
                             ),
                             array(
                               'id'         => 'cloudflare-api-key',
                               'type'	    => 'text',
                               'title'	    => esc_html__( 'Cloudflare API Key', 'swift-performance' ),
                               'default'    => '',
                               'required'  => array(
                                       array('cloudflare-auto-purge', '=', '1')
                               )
                             ),
                        )
                  )
            );

            ReduxSA::setSection ( $opt_name, array (
                       'title' => esc_html__( 'MaxCDN (StackPath)', 'swift-performance' ),
                       'id' => 'cdn-maxcdn',
                       'subsection' => true,
                       'fields' => array (
                       array(
                                  'id'         => 'maxcdn-info',
                                  'type'       => 'info',
                                  'title'      => esc_html__( 'Enable CDN', 'swift-performance' ),
                                  'style'      => 'warning',
                                  'desc'       => esc_html__( 'You need to enable and configure CDN on General tab in order to use Max CDN', 'swift-performance' ),
                                  'required'   => array('enable-cdn', '=', 0),
                       ),
                       array(
                                   'id'   	=> 'maxcdn-alias',
                                   'type' 	=> 'text',
                                   'title'	=> esc_html__( 'MAXCDN Alias', 'swift-performance' ),
                       ),
                       array(
                                   'id'   	=> 'maxcdn-key',
                                   'type' 	=> 'text',
                                   'title'	=> esc_html__( 'MAXCDN Consumer Key', 'swift-performance' ),
                       ),
                       array(
                                   'id'   	=> 'maxcdn-secret',
                                   'type' 	=> 'text',
                                   'title'      => esc_html__( 'MAXCDN Consumer Secret', 'swift-performance' ),
                       ),
                  )
            )
      );

    /*
     *
     * ---> END SECTIONS
     *
     */

     // Show extra htaccess only on Apache
     if (Swift_Performance_Lite::server_software() !== 'apache'){
           ReduxSA::hideField($opt_name, 'custom-htaccess');
     }

     add_action( 'admin_menu', 'remove_reduxsa_menu',12 );
     function remove_reduxsa_menu() {
         remove_submenu_page('tools.php','reduxsa-about');
     }

?>
