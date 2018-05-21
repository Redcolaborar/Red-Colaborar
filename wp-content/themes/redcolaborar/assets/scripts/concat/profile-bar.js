/**
 * Profile Bar Upload Script.
 */
window.WDSProfileBarObject = {};
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
			window: $( window ),
			profileBarSelector: $( '.button-dropdown' ),
			checkBox: $( '.dropdown-open' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.profileBarSelector.on( 'click', app.doProfileMenu );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.profileBarSelector.length;
	};

	// Menu
	app.doProfileMenu = function() {

		if ( app.$c.profileBarSelector.hasClass( 'menu-open' ) ) {
			app.$c.profileBarSelector.removeClass( 'menu-open' );
		} else {
			app.$c.profileBarSelector.addClass( 'menu-open' );
		}
	};

	// Engage!
	$( app.init );

} ( window, jQuery, window.WDSProfileBarObject ) );
