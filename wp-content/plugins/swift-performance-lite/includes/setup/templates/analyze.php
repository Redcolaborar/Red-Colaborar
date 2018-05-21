<?php global $swift_performance_setup;?>
<h1><?php esc_html_e('Requirements', 'swift-performance'); ?></h1>
<ul class="swift-performance-setup-errors">
<?php if(!empty($swift_performance_setup->analyze['plugin_conflicts'])):?>
<li class="swift-performance-fail"><span class="dashicons dashicons-no"></span><?php esc_html_e('Plugin conflicts', 'swift-performance');?></li>
<li class="swift-performance-setup-errors-section-details">
      <?php echo sprintf(esc_html__('Please %sdeactivate%s the following plugins and refresh this page', 'swift-performance'), '<a href="' . admin_url('plugins.php') . '" target="_blank">', '</a>');?> <a class="swift-refresh" href="<?php echo esc_url(site_url($_SERVER['REQUEST_URI']))?>"><span class="dashicons dashicons-update"></span></a>
      <ul>
            <?php foreach($swift_performance_setup->analyze['plugin_conflicts'] as $plugin):?>
                  <li class="swift-performance-fail"><?php echo esc_html($plugin)?></li>
            <?php endforeach;?>
      </ul>
</li>
<?php else:?>
      <li class="swift-performance-pass"><span class="dashicons dashicons-yes"></span><?php esc_html_e('Plugin conflicts', 'swift-performance');?></li>
<?php endif;?>

<?php if(!empty($swift_performance_setup->analyze['missing_apache_modules'])):?>
<li class="swift-performance-warning"><span class="dashicons dashicons-warning"></span><?php esc_html_e('Enable Apache Modules', 'swift-performance');?></li>
<li class="swift-performance-setup-errors-section-details">
      <?php esc_html_e('Please enable the following Apache modules to get better performance', 'swift-performance');?>
      <ul>
      <?php foreach($swift_performance_setup->analyze['missing_apache_modules'] as $module):?>
            <li><?php echo esc_html($module)?></li>
      <?php endforeach;?>
      </ul>
</li>
<?php else:?>
      <li class="swift-performance-pass"><span class="dashicons dashicons-yes"></span><?php esc_html_e('Enabled Apache modules', 'swift-performance');?></li>
<?php endif;?>

<?php if(Swift_Performance_Lite::server_software() == 'apache' && !$swift_performance_setup->analyze['htaccess']):?>
<li class="swift-performance-warning"><span class="dashicons dashicons-warning"></span><?php esc_html_e('htaccess is not writable. Please change file permissions to get better performance', 'swift-performance');?></li>
<?php elseif(Swift_Performance_Lite::server_software() == 'apache'):?>
      <li class="swift-performance-pass"><span class="dashicons dashicons-yes"></span><?php esc_html_e('Rewrites are working', 'swift-performance');?></li>
<?php endif;?>
