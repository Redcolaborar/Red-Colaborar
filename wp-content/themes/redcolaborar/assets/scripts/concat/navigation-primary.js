/**
 * File: navigation-primary.js
 *
 * Helpers for the primary navigation.
 */
window.wdsPrimaryNavigation = {};
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
			subMenuContainer: $( '.main-navigation .sub-menu' ),
			subMenuParentItem: $( '.main-navigation li.menu-item-has-children' ),
			siteHeader: $( '.site-header' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.window.on( 'load', app.addDownArrow );
		app.$c.subMenuParentItem.find( 'a' ).on( 'focusin focusout', app.toggleFocus );
		app.$c.window.on( 'scroll', app.scrollHelper );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.subMenuContainer.length;
	};

	// Add the down arrow to submenu parents.
	app.addDownArrow = function() {
		app.$c.subMenuParentItem.find( '> a' ).append( '<span class="caret-down" aria-hidden="true"></span>' );
	};

	// Toggle the focus class on the link parent.
	app.toggleFocus = function() {
		$( this ).parents( 'li.menu-item-has-children' ).toggleClass( 'focus' );
	};

	// Add helper to condense nav on scroll for desktop
	app.scrollHelper = function() {
		let width = app.$c.window.width(),
			scroll = app.$c.window.scrollTop();

		if ( '900' <= width ) {

			if ( '25' <= scroll ) {
				app.$c.siteHeader.addClass( 'scrolling' );
			} else {
				app.$c.siteHeader.removeClass( 'scrolling' );
			}
		} else {
			app.$c.siteHeader.removeClass( 'scrolling' );
		}
	};

	// Engage!
	$( app.init );

}( window, jQuery, window.wdsPrimaryNavigation ) );
