jQuery(function() {
	var jQuerywrapper = jQuery('#wrapper');

	// theme switcher
	var theme_match = String(window.location).match(/[?&]theme=([a-z0-9]+)/);
	var theme = (theme_match && theme_match[1]) || 'default';
	var themes = ['default','legacy','bootstrap2','bootstrap3'];
	jQuery('head').append('<link rel="stylesheet" href="../dist/css/selectize.' + theme + '.css">');

	//var jQuerythemes = jQuery('<div>').addClass('theme-selector').insertAfter('h1');
	for (var i = 0; i < themes.length; i++) {
		//jQuerythemes.append('<a href="?theme=' + themes[i] + '"' + (themes[i] === theme ? ' class="active"' : '') + '>' + themes[i] + '</a>');
	}

	// display scripts on the page
	jQuery('script', jQuerywrapper).each(function() {
		var code = this.text;
		if (code && code.length) {
			var lines = code.split('\n');
			var indent = null;

			for (var i = 0; i < lines.length; i++) {
				if (/^[	 ]*jQuery/.test(lines[i])) continue;
				if (!indent) {
					var lineindent = lines[i].match(/^([ 	]+)/);
					if (!lineindent) break;
					indent = lineindent[1];
				}
				lines[i] = lines[i].replace(new RegExp('^' + indent), '');
			}

			var code = jQuery.trim(lines.join('\n')).replace(/	/g, '    ');
			var jQuerypre = jQuery('<pre>').addClass('js').text(code);
			jQuerypre.insertAfter(this);
		}
	});

	// show current input values
	jQuery('select.selectized,input.selectized', jQuerywrapper).each(function() {
		var jQuerycontainer = jQuery('<div>').addClass('value').html('Current Value: ');
		var jQueryvalue = jQuery('<span>').appendTo(jQuerycontainer);
		var jQueryinput = jQuery(this);
		var update = function(e) { jQueryvalue.text(JSON.stringify(jQueryinput.val())); }

		jQuery(this).on('change', update);
		update();

		jQuerycontainer.insertAfter(jQueryinput);
	});
});