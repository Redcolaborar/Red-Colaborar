/**
 * Back to Top Button Script.
 */
window.wdsBackToTop = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			body: $( 'html, body' ),
			window: $( window ),
			backToTopSelector: $( '.back-to-top-button' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'scroll', app.doBackToTopButton );
		app.$c.backToTopSelector.on( 'click', app.doBackToTop );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.backToTopSelector.length;
	};

	// Show/Hide Back to Top Button.
	app.doBackToTopButton = function() {

		let scroll = app.$c.window.scrollTop();

		if ( 250 < scroll ) {
			app.$c.backToTopSelector.fadeIn( 200 );
		} else {
			app.$c.backToTopSelector.fadeOut( 200 );
		}
	};

	// Scroll back to top on click.
	app.doBackToTop = function( e ) {

		e.preventDefault();

		app.$c.body.animate( { scrollTop: 0 }, 700 );
	};

	// Engage!
	$( app.init );

} ( window, jQuery, window.wdsBackToTop ) );
