<?php global $swift_performance_setup;?>
<h1><?php esc_html_e('Optimization', 'swift-performance'); ?></h1>
<h2><?php esc_html_e('Optimize static resources', 'swift-performance')?></h2>
<ul class="swift-performance-box-select three">
      <li>
            <input type="radio" name="optimize-assets" value="cache-only" id="cache-only">
            <label for="cache-only">
                  <h3><?php esc_html_e('Cache only', 'swift-performance');?></h3>
                  <span><?php echo sprintf(esc_html__('Use %s only for caching', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME);?></span>
            </label>
      </li>
      <li>
            <input type="radio" name="optimize-assets" value="merge-only" id="merge-only" checked>
            <label for="merge-only">
                  <h3><?php esc_html_e('Minimal optimization', 'swift-performance');?></h3>
                  <span><?php esc_html_e('Use caching and optimize static resources', 'swift-performance')?></span>
            </label>
      </li>
      <li>
            <input type="radio" name="optimize-assets" value="full" id="full">
            <label for="full">
                  <h3><?php esc_html_e('Full optimization', 'swift-performance');?></h3>
                  <span><?php esc_html_e('Caching + Optimize static resources + Critical CSS', 'swift-performance')?></span>
            </label>
            <?php if (Swift_Performance_Lite::check_option('purchase-key', '')): ?>
            <span class="swift-performance-warning swift-performance-compute-api-warning"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Add a valid purchase key in order to use Compute API', 'swift-performance');?></span>
            <?php endif;?>
      </li>
</ul>

<div id="disable-emojis-container" class="swift-p-row">
      <input type="checkbox" name="disable-emojis" value="enabled" id="disable-emojis" checked>
      <label for="disable-emojis">
            <?php esc_html_e('Disable Emojis', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('Disable default emojis.', 'swift-performance')?></em></p>
</div>

<div id="bypass-import-container" class="swift-p-row">
      <input type="checkbox" name="bypass-css-import" value="enabled" id="bypass-css-import" checked>
      <label for="bypass-css-import">
            <?php esc_html_e('Bypass CSS Import', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('Include imported CSS files in merged styles.', 'swift-performance')?></em></p>
</div>

<div id="minify-html-container" class="swift-p-row">
      <input type="checkbox" name="minify-html" value="enabled" id="minify-html" checked>
      <label for="minify-html">
            <?php esc_html_e('Minify HTML', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('Remove unnecessary whitespaces from HTML.', 'swift-performance')?></em></p>
</div>

<div class="swift-hidden swift-p-row" id="optimize-prebuild-only-container">
      <input type="checkbox" name="optimize-prebuild-only" value="enabled" id="optimize-prebuild-only" checked>
      <label for="optimize-prebuild-only">
            <?php esc_html_e('Optimize Prebuild Only', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize the page, only when prebuild cache process is running.', 'swift-performance')?></em></p>
</div>
<div class="swift-hidden swift-p-row" id="merge-background-only-container">
      <input type="checkbox" name="merge-background-only" value="enabled" id="merge-background-only">
      <label for="merge-background-only">
            <?php esc_html_e('Optimize in Background', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('In some cases optimizing the page takes some time. If you enable this option the plugin will optimize page in the background.', 'swift-performance')?></em></p>
</div>

<div class="swift-hidden swift-p-row" id="limit-threads-container">
      <input type="checkbox" name="limit-threads" value="enabled" id="limit-threads">
      <label for="limit-threads">
            <?php esc_html_e('Limit Simultaneous Threads', 'swift-performance');?>
      </label>
      <div class="swift-performance-max-threads">
            <label><?php esc_html_e('Maximum threads', 'swift-performance')?> </label>
            <input type="number" min="1" name="max-threads" value="3">
      </div>
      <p><em><?php esc_html_e('Limit maximum simultaneous threads. It can be useful on shared hosting environment to avoid 508 errors.', 'swift-performance')?></em></p>
</div>
