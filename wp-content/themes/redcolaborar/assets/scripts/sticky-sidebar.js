/**
 * Stick the BuddyPress Filter sidebar on scroll.
 *
 * @author Corey Collins
 */
window.stickySidebarScroll = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();

		// On window resize, get the new height of the content container.
		app.$c.window.on( 'resize', app.debounce( app.getNewContentHeight, 250 ) );

		if ( app.meetsRequirements() && 900 < app.$c.window.width() ) {
			app.bindEvents();
			app.debounce( app.stickSidebar, 250 )();
		}
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			window: $( window ),
			filterSidebar: $( '#wds-recolaborar-sidebar-filters' ),
			sidebar: $( '.secondary' ),
			sidebarTop: $( '.secondary' ).offset().top,
			sidebarHeight: $( '.secondary' ).outerHeight(),
			contentTop: $( '#content' ).offset().top,
			headerHeight: $( '.site-header' ).outerHeight(),
			adminBarHeight: $( '#wpadminbar' ).outerHeight(),
			footerHeight: $( '.site-footer' ).outerHeight()
		};
	};

	// Combine all events
	app.bindEvents = function() {
		app.$c.window.on( 'scroll', app.stickSidebar );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.filterSidebar.length;
	};

	// Stick the sidebar on scrolling.
	app.stickSidebar = function() {

		let scrollTop  = app.$c.window.scrollTop() + ( app.$c.headerHeight + app.$c.adminBarHeight ),
			sidebarTop = app.$c.sidebarTop,
			contentHeight = app.getNewContentHeight();

		// If scrolling past our window position, stick the sidebar.
		if ( sidebarTop < scrollTop ) {
			app.$c.sidebar.css( 'top', scrollTop - sidebarTop ).addClass( 'sticky' );

			// Don't hit the footer.
			let sidebarBottom = ( scrollTop - sidebarTop ) + app.$c.sidebarHeight,
				stickyStop    = ( app.$c.contentTop + contentHeight ) - ( app.$c.headerHeight + app.$c.adminBarHeight + app.$c.footerHeight + app.$c.sidebarHeight );

			if ( stickyStop < sidebarBottom ) {

				let stopPosition = contentHeight - ( ( app.$c.headerHeight + app.$c.adminBarHeight + app.$c.footerHeight + app.$c.sidebarHeight ) * 2 );

				app.$c.sidebar.css( 'top', stopPosition );
			}
		} else {
			app.$c.sidebar.removeAttr( 'style' );
		}
	};

	// Grab the new height of the content container.
	app.getNewContentHeight = function() {
		return $( '#content' ).height();
	};

	// Debounce: https://davidwalsh.name/javascript-debounce-function
	app.debounce = function( func, wait, immediate ) {
		var timeout;
		return function() {
			let context = this,
				args    = arguments;

			/**
			 * The later function.
			 */
			function later() {
				timeout = null;
				if ( ! immediate ) {
					func.apply( context, args );
				}
			};

			var callNow = immediate && ! timeout;

			clearTimeout( timeout );

			timeout = setTimeout( later, wait );

			if ( callNow ) {
				func.apply( context, args );
			}
		};
	};

	// Engage
	$( app.init );

}( window, jQuery, window.stickySidebarScroll ) );
