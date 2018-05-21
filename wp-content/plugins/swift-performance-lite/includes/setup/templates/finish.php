<?php
global $swift_performance_setup;
$swift_performance_setup->show_steps = false;
?>
<h1><?php esc_html_e('Your website is ready!', 'swift-performance'); ?></h1>
<p>
	<?php if (Swift_Performance_Lite::check_option('purchase-key', '', '!=')):?>
		<a href="<?php echo esc_url(add_query_arg('subpage', 'image-optimizer', menu_page_url('swift-performance',false))); ?>" class="swift-btn swift-btn-green"><?php echo esc_html__('Optimize images', 'swift-performance'); ?></a>
	<?php endif;?>
	<a href="<?php echo esc_url(add_query_arg('subpage', 'settings', menu_page_url('swift-performance',false))); ?>" class="swift-btn swift-btn-gray"><?php echo sprintf(esc_html__('%s Settings', 'swift-performance'), SWIFT_PERFORMANCE_PLUGIN_NAME); ?></a>
	<a href="<?php echo admin_url(); ?>" class="swift-btn swift-btn-gray"><?php echo esc_html__('Back to dashboard', 'swift-performance'); ?></a>
</p>
<p><?php esc_html_e('What\'s next?', 'swift-performance'); ?></p>
<div class="swift-setup-row">
	<div class="swift-setup-col">
		<ul>
			<li><a href="https://kb.swteplugins.com/swift-performance/" target="_blank"><?php esc_html_e('Knowledge Base', 'swift-performance'); ?></a></li>
			<li><a href="https://swteplugins.com/support/" target="_blank"><?php esc_html_e('Support', 'swift-performance'); ?></a></li>
			<li><a href="https://www.facebook.com/swiftplugin/" target="_blank"><?php esc_html_e('Follow us on Facebook', 'swift-performance'); ?></a></li>
			<li><a href="https://twitter.com/swiftplugin" target="_blank"><?php esc_html_e('Follow us on Twitter', 'swift-performance'); ?></a></li>
		</ul>
	</div>
</div>
