jQuery(document).ready(function($){
	mayo_login_screen_live_style();
	var $preview = $('#login_screen_preview');
	$('.color_field').wpColorPicker({
		change: function(event, ui){
			setTimeout(
				function(){
					mayo_login_screen_live_style();
				}, 10);
			},
	});
	//-- Live Style --
	$('select.live_style').change(function(){
		mayo_login_screen_live_style();
	});
	$('span.remove').click(function(){
		$(this).prev('input').val('');
		mayo_login_screen_live_style();
	});
	
	function mayo_login_screen_live_style(){
		var $css = $('#mayo_login_screen_live_style_template').html();
		if($('#login_screen_form_button_bg_color').val()!==''){
			var $button_bg_color = tinycolor($('#login_screen_form_button_bg_color').val());
			$('#login_screen_form_button_border_color').val(tinycolor.darken($button_bg_color, amount = 10));
			
			$('#login_screen_form_button_text_color').val('#fff');
			if(get_lightness($button_bg_color) > 0.75){
				$('#login_screen_form_button_text_color').val('#222');
			}
		}
		
		if($('#login_screen_form_bg_color').val()!==''){
			$('#login_screen_label_text_color').val('#f6f6f6');
			if(get_lightness(tinycolor($('#login_screen_form_bg_color').val())) > 0.75){
				$('#login_screen_label_text_color').val('#444');
			}
		}
		
		if( $('#login_screen_bg_image').val() !== '' ) {
			$css = ".login { background-image: url('" + $('#login_screen_bg_image').val() + "'); }" + $css;
		}
		
		$('.live_style').each(function(){
			var $this_id = $(this).attr('id');
			var $a = '%%'+$this_id+'%%';
			var regex = new RegExp($a, 'g');
			$css = $css.replace(regex, $('#'+$this_id).val());
		});
		
		if($('#login_screen_logo').val()!==''){
			$css = $css + ".login #login h1 a { background: transparent url('"+$('#login_screen_logo').val()+"') no-repeat bottom center; margin-left: -200px; margin-right: -200px; height: 400px; margin-top: -290px; text-indent: -9999px; display: block; width: 720px; background-size: auto;}";
		}
		
		$('#mayo_login_screen_live_style').html($css);
		$('#login_screen_css').val($css);
	}
	
	$('#mayo_login_screen_reset').click(function(e){
		var $confirm = confirm("Reset all your login screen settings to Wordpress default.\n\nThis will also remove all foot print of this plugin in your database.\n(You should do this before you delete this plugin)")
		if($confirm === true){
			return true;
			$(this).parents().filter("form").submit();
		}
		else{
			return false;
		}
	});
	
	$('#mayo_login_screen_import').click(function(e){
		var $confirm = confirm("Importing settings will replace your current settings.\n\nComfirm to continue?")
		if($confirm === true){			$('#mayo_login_screen_import_file').click();			$('#mayo_login_screen_import_file').change( function(){				$(this).parents().filter("form").submit();			});
			//$(this).parents().filter("form").submit();			return false;
		}
		else{
			return false;
		}
	});
	
	function get_lightness($tiny_color){
		var $tiny_color_hsl = $tiny_color.toHsl();
		var $l = $tiny_color_hsl['l'];
		var $s = $tiny_color_hsl['s'];
		
		var $return;
		if($s !== 0){
			$return = (($l + $s)/2).toFixed(2);
		}
		else{
			$return = $l.toFixed(2);
		}
		return $return;
	}
	
	var media;

	media = {

		frame: function( $type, $title, $action_text, $target ) {
			if ( this._frame )
				return this._frame;

			this._frame = wp.media( {
				title: $title,
				button: {
					text: $action_text
				},
				multiple: false,
				library: {
					type: $type
				}
			} );

			this._frame.on( 'ready', this.ready );

			this._frame.state( 'library' ).on( 'select', this.select );

			return this._frame;
		},

		ready: function() {
			$( '.media-modal' ).addClass( 'no-sidebar smaller' );
		},

		select: function() {
			var settings = wp.media.view.settings,
				selection = this.get( 'selection' ).single();
			
			var $url = selection.get('url');
			$('input.insert_media_here').val($url).removeClass('insert_media_here');
			mayo_login_screen_live_style();
		},

		init: function() {
			$('.open_media_button').on( 'click', function( e ) {
				var $this = $(this);
				e.preventDefault();
				
				$($this.attr('data-target')).addClass('insert_media_here');
				
				media.frame( $this.attr('data-type'), $this.attr('title'), $this.attr('action_text') ).open();
			});
		}
	};

	$( media.init );
	
	function rgb_to_hex(rgb) {
		if (/^#[0-9A-F]{6}$/i.test(rgb)) return rgb;

		rgb = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
		function hex(x) {
			return ("0" + parseInt(x).toString(16)).slice(-2);
		}
		return "#" + hex(rgb[1]) + hex(rgb[2]) + hex(rgb[3]);
	}

});