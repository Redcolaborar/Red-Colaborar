<?php
global $swift_performance_setup;
$swift_performance_setup->show_steps = false;
?>
<h1><?php echo sprintf(esc_html__('Deactivate %s', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME); ?></h1>
<?php if (!defined('SWIFT_PERFORMANCE_WHITELABEL')):?>
<strong><?php esc_html_e('Do you need help?', 'swift-performance'); ?></strong>
<p><em><?php _e('If you had any issue, or you are not satisfied with results it can happen because of improper configuration.<br><br> Check the <a href="https://swiftperformance.io/faq/" target="_blank">FAQ</a>, <a href="https://kb.swteplugins.com/swift-performance/" target="_blank">Knowledge Base</a>, <a href="https://swteplugins.com/support/" target="_blank">Open a support ticket</a>, or join to the <a href="https://www.facebook.com/groups/SwiftPerformanceUsers/" target="_blank">Facebook Community!</a>', 'swift-performance');?></em></p>
<p>
	<a href="<?php echo esc_url(menu_page_url('swift-performance',false)); ?>" class="swift-btn swift-btn-gray swift-btn-md"><?php esc_html_e('Cancel', 'swift-performance')?></a>
	<a href="<?php echo wp_nonce_url(sprintf( admin_url( 'plugins.php?action=deactivate&plugin=%s&plugin_status=all&paged=1&s' ), SWIFT_PERFORMANCE_PLUGIN_BASENAME ), 'deactivate-plugin_' . SWIFT_PERFORMANCE_PLUGIN_BASENAME); ?>" class="swift-btn swift-btn-brand swift-btn-md pull-right"><?php esc_html_e('Deactivate Plugin', 'swift-performance')?></a>
</p>
<?php endif;?>
