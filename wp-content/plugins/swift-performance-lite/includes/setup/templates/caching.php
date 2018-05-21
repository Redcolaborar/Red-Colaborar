<?php global $swift_performance_setup; ?>
<h1><?php esc_html_e('Caching', 'swift-performance'); ?></h1>
<h2><?php esc_html_e('Cache expiry mode', 'swift-performance')?></h2>
<ul class="swift-performance-box-select three">
      <li>
            <input type="radio" name="cache-expiry-mode" value="timebased" id="timebased" checked>
            <label for="timebased">
                  <h3><?php esc_html_e('Time based mode', 'swift-performance');?></h3>
                  <span><?php esc_html_e('Best choice for most sites and webshops. WooCommerce, BBPress and Buddypress support is included', 'swift-performance')?></span>
            </label>
      </li>
      <li>
            <input type="radio" name="cache-expiry-mode" value="actionbased" id="actionbased">
            <label for="actionbased">
                  <h3><?php esc_html_e('Action based mode', 'swift-performance');?></h3>
                  <span><?php esc_html_e('Clear cache only when post/page/product/comment or other custom post type was added/updated. WooCommerce, BBPress and Buddypress support is included', 'swift-performance')?></span>
            </label>
      </li>
      <li>
            <input type="radio" name="cache-expiry-mode" value="intelligent" id="intelligent">
            <label for="intelligent">
                  <h3><?php esc_html_e('Intelligent mode', 'swift-performance');?></h3>
                  <span><?php esc_html_e('Choose intelligent mode if some parts of your site are updated arbitary from third party source (eg: live scores, currency rates, events, other APIs)', 'swift-performance')?></span>
            </label>
      </li>
</ul>

<div class="swift-p-row">
      <input type="checkbox" name="automated-prebuild-cache" value="enabled" id="automated-prebuild-cache" checked>
      <label for="automated-prebuild-cache">
            <?php esc_html_e('Prebuild Cache Automatically', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('Enable this option to prebuild the cache.', 'swift-performance')?></em></p>
</div>

<div class="swift-p-row">
      <input type="checkbox" name="browser-cache" value="enabled" id="browser-cache" checked>
      <label for="browser-cache">
            <?php esc_html_e('Enable Browser Cache', 'swift-performance');?>
            <?php if (Swift_Performance_Lite::server_software() == 'apache' && isset($swift_performance_setup->analyze['missing_apache_modules']['mod_expires'])):?>
            <span class="swift-performance-warning swift-performance-browser-cache-warning"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Please enable mod_expires in order to work', 'swift-performance');?></span>
            <?php endif;?>
      </label>
      <p><em><?php esc_html_e('If you enable this option it will generate htacess/nginx rules for browser cache.', 'swift-performance')?></em></p>
</div>

<div class="swift-p-row">
      <input type="checkbox" name="enable-gzip" value="enabled" id="enable-gzip" checked>
      <label for="enable-gzip">
            <?php esc_html_e('Enable Gzip', 'swift-performance');?>
            <?php if (Swift_Performance_Lite::server_software() == 'apache' && (isset($swift_performance_setup->analyze['missing_apache_modules']['mod_deflate']) || isset($swift_performance_setup->analyze['missing_apache_modules']['mod_filter'])) ):?>
            <span class="swift-performance-warning swift-performance-gzip-warning"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Please enable mod_deflate and mod_filter in order to work', 'swift-performance');?></span>
            <?php endif;?>
      </label>
      <p><em><?php esc_html_e(' If you enable this option it will generate htacess/nginx rules for gzip compression.', 'swift-performance')?></em></p>
</div>

<h2><?php esc_html_e('Cloudflare', 'swift-performance')?></h2>
<div class="swift-p-row">
      <input type="checkbox" name="cloudflare-auto-purge" value="enabled" id="cloudflare-auto-purge">
      <label for="cloudflare-auto-purge">
            <?php esc_html_e('Enable Auto Purge', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option the plugin will purge the cache on Cloudflare as well when it clears plugin cache. It is recommended to enable this option if you are using Cloudflare with caching.', 'swift-performance')?></em></p>
</div>
<div class="swift-p-row" id="cloudflare-email-container">
      <label for="cloudflare-email">
            <?php esc_html_e('Cloudflare Account E-mail', 'swift-performance');?>
      </label>
      <input type="text" name="cloudflare-email" id="cloudflare-email">
      <p><em><?php esc_html_e('Your e-mail address which related to the Cloudflare account what you are using for the site.', 'swift-performance')?></em></p>
</div>
<div class="swift-p-row" id="cloudflare-api-key-container">
      <label for="cloudflare-api-key">
            <?php esc_html_e('Cloudflare API Key', 'swift-performance');?>
      </label>
      <input type="text" name="cloudflare-api-key" id="cloudflare-api-key">
      <p><em><?php echo sprintf(esc_html__('  The generated API key for your Cloudflare account. %sGlobal API key%s', 'swift-performance'), '<a href="https://support.cloudflare.com/hc/en-us/articles/200167836-Where-do-I-find-my-Cloudflare-API-key-" target="_blank">', '</a>')?></em></p>
</div>

<h2><?php esc_html_e('Varnish', 'swift-performance')?></h2>
<div class="swift-p-row">
      <input type="checkbox" name="varnish-auto-purge" value="enabled" id="varnish-auto-purge">
      <label for="varnish-auto-purge">
            <?php esc_html_e('Enable Auto Purge', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option the plugin will purge Varnish cache as well when it clears plugin cache. It is recommended to enable this option if you are using Varnish cache.', 'swift-performance')?></em></p>
</div>
<div class="swift-p-row" id="custom-varnish-host-container">
      <label for="custom-varnish-host">
            <?php esc_html_e('Custom Host', 'swift-performance');?>
      </label>
      <input type="text" name="custom-varnish-host" id="custom-varnish-host">
      <p><em><?php esc_html_e(' If you are using proxy (eg: Cloudflare) you may will need this option.', 'swift-performance')?></em></p>
</div>
