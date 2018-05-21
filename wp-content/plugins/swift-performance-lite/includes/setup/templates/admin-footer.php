<?php
	global $swift_performance_setup;
	if ($swift_performance_setup->show_steps){
		$swift_performance_setup->step_links();
	}
?>
	</form>
</div>
<?php if ($swift_performance_setup->current_step['id'] != 'finish'):?>
<a class="back-to-dashboard" href="<?php echo admin_url(); ?>"><?php esc_html_e('Back to Dashboard', 'swift-performance')?></a>
<?php endif; ?>
<?php do_action( 'admin_print_footer_scripts' ); ?>
</body>
</html>
