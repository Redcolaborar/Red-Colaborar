/**
 * Open Sidebar Activity Filter Script.
 */
window.WDSOpenSidebarObject = {};
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
			openSidebarContainer: $( '#wds-recolaborar-sidebar-filters' ),
			openSidebarSelector: $( '#search-field' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		$( document ).on( 'click', app.doCloseSidebar );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.openSidebarContainer.length;
	};

	app.doCloseSidebar = function( e ) {
		if ( ! app.$c.openSidebarContainer.is( e.target ) && 0 === app.$c.openSidebarContainer.has( e.target ).length ) {
			app.$c.openSidebarContainer.removeClass( 'open-sidebar' );
        } else {
			app.$c.openSidebarContainer.addClass( 'open-sidebar' );
        }
	};

	// Engage!
	$( app.init );

} ( window, jQuery, window.WDSOpenSidebarObject ) );
