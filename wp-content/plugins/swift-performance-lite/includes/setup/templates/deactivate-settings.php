<?php
	$custom_htaccess = Swift_Performance_Lite::get_option('custom-htaccess');
	$custom_htaccess = trim($custom_htaccess);
?>
<h1><?php echo sprintf(esc_html__('Deactivate %s', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME); ?></h1>
<div class="swift-p-row">
      <input type="checkbox" name="keep-settings" value="enabled" id="keep-settings" checked>
      <label for="keep-settings">
            <?php esc_html_e('Keep Settings', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option, the plugin will keep current settings in DB after deactivate the plugin.', 'swift-performance')?></em></p>
</div>
<?php if (!empty($custom_htaccess) && Swift_Performance_Lite::server_software() == 'apache'):?>
<div class="swift-p-row">
      <input type="checkbox" name="keep-custom-htaccess" value="enabled" id="keep-custom-htaccess" checked>
      <label for="keep-custom-htaccess">
            <?php esc_html_e('Keep Custom htaccess', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option, the plugin will keep custom htaccess rules after deactivate the plugin.', 'swift-performance')?></em></p>
</div>
<?php endif; ?>
<div class="swift-p-row">
      <input type="checkbox" name="keep-warmup-table" value="enabled" id="keep-warmup-table" checked>
      <label for="keep-warmup-table">
            <?php esc_html_e('Keep Warmup Table', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option, the plugin will keep Warmup Table in DB after deactivate the plugin.', 'swift-performance')?></em></p>
</div>
<div class="swift-p-row">
      <input type="checkbox" name="keep-logs" value="enabled" id="keep-logs">
      <label for="keep-logs">
            <?php esc_html_e('Keep Logs', 'swift-performance');?>
      </label>
      <p><em><?php esc_html_e('If you enable this option, the plugin will keep logs after deactivate the plugin.', 'swift-performance')?></em></p>
</div>
