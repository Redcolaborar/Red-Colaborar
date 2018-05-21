/**
 * File google-translate-button.js
 *
 * Hide the button when we scroll to the bottom of the page so it doesn't interfere with the footer..
 */
window.WDSGoogleTranslateButton = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();

		app.bindEvents();
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			body: $( 'body' ),
			window: $( window ),
			googleContainer: $( '.goog-te-gadget' ),
			googleElement: $( '#google_translate_element' )
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.googleContainer.length;
	};

	// Combine all events.
	app.bindEvents = function() {

		// Hide contact buttons on scroll to bottom.
		app.$c.window.on( 'scroll', app.hideContainer );
	};

	// Hide Contact Buttons if nearly the bottom.
	app.hideContainer = function() {

		let scrollTop = app.$c.window.scrollTop() + 200;

		if ( 200 < scrollTop ) {
			app.$c.googleContainer.fadeOut();
			app.$c.googleElement.fadeOut();
		} else {
			app.$c.googleContainer.fadeIn();
			app.$c.googleElement.fadeIn();
		}
	};

	$( app.init );
}( window, jQuery, window.WDSGoogleTranslateButton ) );
