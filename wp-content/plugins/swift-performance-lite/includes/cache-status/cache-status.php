<?php
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Swift_Performance_Cache_Status_Table extends WP_List_Table {

      function get_columns(){
            $columns = array(
                  'status'            => __('Status', 'swift-perforamce'),
                  'url'               => __('URL', 'swift-perforamce'),
                  'priority'          => __('Priority', 'swift-perforamce'),
                  'priority_editor'   => __('Prebuild priority', 'swift-perforamce'),
                  'date'              => __('Cache date', 'swift-perforamce'),
                  'timestamp'         => __('Timestamp', 'swift-perforamce'),
            );
            return $columns;
      }

      function prepare_items() {
            $items = $this->get_items();

            $columns = $this->get_columns();
            $hidden = array('priority', 'timestamp');
            $sortable = $this->get_sortable_columns();
            $this->_column_headers = array($columns, $hidden, $sortable);

            usort( $items, array( &$this, 'usort_reorder' ) );

            $per_page = 30;
            $current_page = $this->get_pagenum();
            $total_items = count($items);

            $found_data = array_slice($items,(($current_page-1)*$per_page),$per_page);

            $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page'    => $per_page
            ));
            $this->items = $found_data;
      }

      function column_default( $item, $column_name ) {
            return $item[ $column_name ];
      }

      function get_sortable_columns() {
            $sortable_columns = array(
                'url'               => array('url',false),
                'date'              => array('timestamp',false),
                'priority_editor'   => array('priority', false)
            );
            return $sortable_columns;
      }

      function get_items(){
            global $wpdb;
            $items = $missing = $urls = array();

            // Prepare URLs
            foreach (Swift_Performance_Lite::get_prebuild_urls(false) as $warmup){
                  $urls[trailingslashit($warmup['url'])] = $warmup;
            }

            // Get pages which are missing from warmup
            $cache_status = Swift_Performance_Lite::cache_status();
            foreach($cache_status['files'] as $url){
                  if (!isset($urls[trailingslashit($url)])){
                        $urls[trailingslashit($url)] = array(
                              'priority' => PHP_INT_MAX,
                              'url' => $url
                        );
                  }
            }

            $cache_files = array();

      	$basedir = trailingslashit(Swift_Performance_Lite::get_option('cache-path')) . SWIFT_PERFORMANCE_CACHE_BASE_DIR;
            $basedir_regex = trailingslashit(Swift_Performance_Lite::get_option('cache-path')) . SWIFT_PERFORMANCE_CACHE_BASE_DIR . trailingslashit('('.implode('|', apply_filters('swift_performance_enabled_hosts', array(parse_url(Swift_Performance_Lite::home_url(), PHP_URL_HOST)))).')/');

            if (@file_exists($basedir)){
                  $Directory = new RecursiveDirectoryIterator($basedir);
                  $Iterator = new RecursiveIteratorIterator($Directory);
                  $Regex = new RegexIterator($Iterator, '#'.$basedir_regex.'((.*)/)?(@prefix/([_a-z0-9]+)/)?(desktop|mobile)/(unauthenticated|(authenticated/(a-z0-9+)))/((index|404)\.html|index\.xml|index\.json)$#i', RecursiveRegexIterator::GET_MATCH);

                  foreach ($Regex as $filename=>$file){
                          $cache_files[$filename] = filectime($filename);
                  }
            }

            foreach ($urls as $url){
                  // Skip subcache
                  if (strpos($url['url'], '@prefix/')){
                        continue;
                  }

                  if (Swift_Performance_Lite::check_option('caching-mode', array('disk_cache_rewrite', 'disk_cache_php'), 'IN')){
                        // Guess cache type
                        if (!isset($cache_files[trailingslashit($basedir . parse_url($url['url'], PHP_URL_HOST) . trailingslashit(parse_url($url['url'], PHP_URL_PATH), '/')) . 'desktop/unauthenticated/index.html'])){
                              $cache_type = false;
                        }
                        else if (isset($cache_files[trailingslashit($basedir . parse_url($url['url'], PHP_URL_HOST) . trailingslashit(parse_url($url['url'], PHP_URL_PATH), '/')) . 'desktop/unauthenticated/404.html'])){
                              $cache_type = '404';
                        }
                        else {
                              $cache_type = 'html';
                        }

                        $time             = ($cache_type !== false ? $cache_files[trailingslashit($basedir . parse_url($url['url'], PHP_URL_HOST) . trailingslashit(parse_url($url['url'], PHP_URL_PATH), '/')) . 'desktop/unauthenticated/index.html'] : 0);
                  }
                  else {
                        $cache_type       = Swift_Performance_Cache::get_cache_type($url['url']);
                        $time             = Swift_Performance_Cache::get_cache_time($url['url']);
                  }
                  $cache_status     = ($cache_type === false ? 'not-cached' : ($cache_type == '404' ? '404' : 'cached'));
                  $status           = '<span title="' . esc_attr__('Cached', 'swift-performance') . '" class="dashicons dashicons-yes'.($cache_status != 'cached' ? ' swift-hidden' : '').'"></span>';
                  $status          .= '<span title="' . esc_attr__('Missing From Cache', 'swift-performance') . '" class="dashicons dashicons-no'.($cache_status != 'not-cached' ? ' swift-hidden' : '').'"></span>';
                  $status          .= '<span title="' . esc_attr__('Cached 404', 'swift-performance') . '" class="dashicons dashicons-warning'.($cache_status != '404' ? ' swift-hidden' : '').'"></span>';

                  // Filtering
                  if (isset($_REQUEST['s']) && !empty($_REQUEST['s']) && strpos(strtolower($url['url']), strtolower($_REQUEST['s'])) === false){
                        continue;
                  }
                  if (isset($_REQUEST['cache-status-filter']) && !empty($_REQUEST['cache-status-filter']) && $cache_status != $_REQUEST['cache-status-filter']){
                        continue;
                  }

                  $items[] = array(
                        'priority'         => $url['priority'],
                        'priority_editor'  => ($url['priority'] != PHP_INT_MAX ? '<form class="swift-priority-update"><span class="edit-container"><input type="number" name="priorities['.esc_attr(md5($url['url'])).']" class="priority-holder" value="'.esc_attr($url['priority']).'"><button class="swift-btn swift-btn-gray">'.esc_html__('Update', 'swift-performance').'</button></span></form>' : '-'),
                        'url'              => $url['url'],
                        'status'           => $status,
                        'date'             => ($time > 0 ? get_date_from_gmt( date( 'Y-m-d H:i:s', $time ), get_option('date_format') . ' ' .get_option('time_format') ) : '-'),
                        'timestamp'        => $time
                  );
            }

            return $items;
      }

      function column_url($item) {
            $is_cached = Swift_Performance_Cache::is_cached($item['url']);
            $actions = array(
                  'visit' => '<a href="'.esc_url($item['url']).'" target="_blank">'.esc_html__('Visit', 'swift-performance').'</a>',
                  'action' => '<a class="clear-cache'.(!$is_cached ? ' swift-hidden' : '').'" data-url="'.esc_attr($item['url']).'" data-status="'.Swift_Performance_Cache::get_cache_type($item['url']).'" href="#">'.esc_html__('Clear cache', 'swift-performance').'</a><a class="do-cache'.($is_cached ? ' swift-hidden' : '').'" data-url="'.esc_attr($item['url']).'" href="#">'.esc_html__('Cache page', 'swift-performance').'</a>',
                  'delete' => '<a class="remove-warmup-url" data-url="'.esc_attr($item['url']).'" href="#">'.esc_html__('Remove URL', 'swift-performance').'</a>'
            );


            return sprintf('%1$s %2$s', $item['url'], $this->row_actions($actions) );
      }

      function usort_reorder( $a, $b ) {
            $orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'priority';

            $order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';

            if ($orderby == 'priority'){
                  $result = ($a['priority'] > $b['priority'] ? 1 : ($a['priority'] == $b['priority'] ? 0 : -1));
            }
            else if ($orderby == 'timestamp'){
                  $result = ($a['timestamp'] > $b['timestamp'] ? 1 : ($a['timestamp'] == $b['timestamp'] ? 0 : -1));
            }
            else {
                  $result = strcmp( $a[$orderby], $b[$orderby] );
            }
            return ( $order === 'asc' ) ? $result : -$result;
      }

      protected function extra_tablenav($which){
            include_once SWIFT_PERFORMANCE_DIR . 'includes/cache-status/table-nav.php';
      }

      protected function get_table_classes(){
            return array( 'widefat', 'fixed', 'striped', 'swift-performance-list-table' );
      }

      /**
      * Message to be displayed when there are no items
      */
      public function no_items() {
            if (get_transient('swift_performance_initial_prebuild_links') !== false){
                  _e('Scanning URLs...', 'swift-performance');
            }
            else {
                  _e( 'No items found.' );
            }
      }
}
?>
