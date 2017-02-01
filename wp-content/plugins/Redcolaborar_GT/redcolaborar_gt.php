<?php
/*
Plugin Name: Red Colaborar Google Translator Plugin
Plugin URI: http://redcolaborar.org
Description: Adds a Google Translator code to the <head> of your theme, by hooking to wp_head.
Author: Maurizio Bricola
Version: 1.0
 */
function redco_google_translator() { ?>

<div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: 'es', includedLanguages: 'es,pt,fr,en', layout: google.translate.TranslateElement.InlineLayout.SIMPLE, autoDisplay: false, multilanguagePage: true, gaTrack: true, gaId: 'UA-75774528-2'}, 'google_translate_element');
}

</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>


<?php }
add_action( 'wp_head', 'redco_google_translator', 10 );