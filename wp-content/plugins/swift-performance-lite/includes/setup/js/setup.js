(function() {

	// Create IE + others compatible event handler
	var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod == "attachEvent" ? "onmessage" : "message";

	// Listen to message from child window
	eventer(messageEvent,function(e) {
		if (e.data.length == 32){
			jQuery('body').addClass('swift-loading');
			jQuery.post(swift_performance.ajax_url, {'action': 'swift_performance_setup', 'ajax-action': 'activate', 'swift-nonce': swift_performance.nonce, 'user-key': e.data}, function(){
				document.location.href = jQuery('#nextpage').val();
			});
		}
	},false);

	// Merge assets in background checkbox show/hide
	jQuery(document).on('click change','.swift-performance-box-select [name="optimize-assets"]', function(){
		jQuery('#optimize-prebuild-only-container, #merge-background-only-container, #limit-threads-container, #minify-html-container').addClass('swift-hidden');
		if (jQuery('[name="optimize-assets"]:checked').val() == 'merge-only' || jQuery('[name="optimize-assets"]:checked').val() == 'full'){
			jQuery('#optimize-prebuild-only-container, #merge-background-only-container, #limit-threads-container, #minify-html-container').removeClass('swift-hidden');
		}
	});

	// Cloudflare checkbox show/hide
	jQuery(document).on('click change','#cloudflare-auto-purge', function(){
		jQuery('#cloudflare-email-container, #cloudflare-api-key-container').addClass('swift-hidden');
		if (jQuery('#cloudflare-auto-purge').attr('checked')){
			jQuery('#cloudflare-email-container, #cloudflare-api-key-container').removeClass('swift-hidden');
		}
	});

	// Varnish checkbox show/hide
	jQuery(document).on('click change','#varnish-auto-purge', function(){
		jQuery('#custom-varnish-host-container').addClass('swift-hidden');
		if (jQuery('#varnish-auto-purge').attr('checked')){
			jQuery('#custom-varnish-host-container').removeClass('swift-hidden');
		}
	});

	// Keep original options
	jQuery(document).on('click change','#optimize-images-enabled', function(){
		jQuery('#keep-original-images-container').addClass('swift-hidden');
		if (jQuery('#optimize-images-enabled:checked').length > 0){
			jQuery('#keep-original-images-container').removeClass('swift-hidden');
		}
	});

	jQuery(function(){
		// Fire selects on Load
		// merge assets
		jQuery('.swift-performance-box-select [name="optimize-assets"]').trigger('change');
		// Cloudflare
		jQuery('#cloudflare-auto-purge').trigger('change');
		// Varnish
		jQuery('#varnish-auto-purge').trigger('change');

		// Pagespeed
		if (jQuery('.swift-pagespeed').length > 0){
			jQuery('.swift-pagespeed').each(function(){
				if (jQuery(this).hasClass('cached')){
					return true;
				}
				var that = jQuery(this);
				var strategy = jQuery(that).hasClass('strategy-mobile') ? 'mobile' : 'desktop';
				var timer = setTimeout(function(){
					jQuery(that).removeClass('swift-loading');
					jQuery(that).addClass('timeout');
					jQuery(that).empty().html('<span class="dashicons dashicons-clock"></span>');
				}, 120000);
				jQuery.post(swift_performance.ajax_url, {'action': 'swift_performance_setup', 'ajax-action': 'pagespeed', 'swift-nonce': swift_performance.nonce, 'strategy': strategy}, function(response){
					if (response > 0){
						if (response < 60) {
							jQuery(that).addClass('red');
						}
						else if (response < 90) {
							jQuery(that).addClass('yellow');
						}
						else {
							jQuery(that).addClass('green');
						}
					}
					jQuery(that).removeClass('swift-loading');
					jQuery(that).empty().text(response);
					clearTimeout(timer);
				});
			});
		}
	});

	/**
	 * Localization
	 * @param string text
	 * @return string
	 */
	function __(text){
		if (typeof swift_performance_setup.i18n[text] !== 'undefined'){
			return swift_performance_setup.i18n[text];
		}
		else {
			return text;
		}
	}
})();
