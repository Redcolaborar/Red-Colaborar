<?php
global $title, $hook_suffix, $current_screen, $wp_locale, $pagenow, $wp_version,
		$update_title, $total_update_count, $parent_file, $swift_performance_setup;

		$current 	= $swift_performance_setup->current_step['index'];
		$current_id	= (isset($swift_performance_setup->current_step['id']) ? $swift_performance_setup->current_step['id'] : 'start-wizard');

		$is_activated = get_option('swift-performance-user-key');

		$scores = get_option('swift-performance-pagespeed-scores');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head>
	<meta name="viewport" content="width=device-width" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php wp_title();?></title>
	<script type="text/javascript">
	addLoadEvent = function(func){if(typeof jQuery!="undefined")jQuery(document).ready(func);else if(typeof wpOnload!='function'){wpOnload=func;}else{var oldonload=wpOnload;wpOnload=function(){oldonload();func();}}};
	var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
		pagenow = '',
		typenow = '',
		adminpage = '',
		thousandsSeparator = '<?php echo addslashes( $wp_locale->number_format['thousands_sep'] ); ?>',
		decimalPoint = '<?php echo addslashes( $wp_locale->number_format['decimal_point'] ); ?>',
		isRtl = <?php echo (int) is_rtl(); ?>;
	</script>
	<?php do_action( 'swift_performance_setup_enqueue_scripts' ); ?>
	<?php do_action( 'admin_print_styles' ); ?>
	<?php do_action( 'admin_print_scripts' );?>
	<?php do_action( 'admin_head' ); ?>
</head>
<body class="wp-core-ui swift-setup<?php echo esc_attr(' swift-setup-' . $current_id);?><?php echo(!empty($is_activated) ? ' swift-setup-activated' : '')?>">
	<?php if($_GET['subpage'] == 'setup'):?>
	<ul class="swift-setup-steps">
		<?php foreach ($swift_performance_setup->steps as $key => $step):?>
		<li<?php echo ($key == $swift_performance_setup->current_step['index'] ? ' class="active"' : ($key > $swift_performance_setup->current_step['index'] ? ' class="disabled"' : ''))?>>
			<?php echo esc_html($step['title']); ?>
		</li>
		<?php endforeach;?>
	</ul>
	<?php endif;?>
	<div class="swift-setup-wrapper">
		<form method="post" id="swift-setup-form" action="<?php echo esc_url(wp_nonce_url(add_query_arg(array('subpage' => $_GET['subpage'], 'step' => ($current+1)), menu_page_url('swift-performance', false)), 'swift-performance-setup', 'swift-nonce'));?>">
