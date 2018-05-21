/**
 * Hide the BuddyPress Comments after they have been displayed.
 *
 * @author Corey Collins
 */
window.ShowHideBPComments = {};
( function( window, $, app ) {

	// Constructor
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.bindEvents();
		}
	};

	// Cache all the things
	app.cache = function() {
		app.$c = {
			window: $( window ),
			commentsContainer: $( '.activity-comments' ),
			trigger: $( '.show-hide-comments-trigger' ),
			bpShowCommentsLink: $( 'li.show-all > a' )
		};
	};

	// Combine all events
	app.bindEvents = function() {

		// Listen for a click on our trigger.
		app.$c.trigger.on( 'click', app.showHideComments );

		// Listen for a click on BP's trigger.
		app.$c.bpShowCommentsLink.on( 'click', app.bpShowHideComments );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.commentsContainer.length;
	};

	// Show/Hide the comments on click.
	app.bpShowHideComments = function() {

		let parentUL = $( this ).closest( 'ul' ),
			showHideTrigger = parentUL.siblings( '.show-hide-comments-trigger' );

		// Make our hide/show button visible.
		parentUL.addClass( 'bp-visible' );
		parentUL.siblings( '.show-hide-comments-trigger' ).addClass( 'is-visible' );

		// Loop through all of the sibling LIs.
		$( this ).parent( 'li' ).siblings( 'li' ).each( function() {

			// If an LI is set to display: none, give it a class we can use.
			if ( 'none' === $( this ).css( 'display' ) ) {
				$( this ).addClass( 'comment-to-hide' ); // Give it a class so we can do things with CSS.
				$( this ).removeAttr( 'style' ); // Remove the inline styles.
			}
		} );

		// Count the number of LIs, now that the Show All link has been removed.
		const commentCount = $( this ).closest( '.activity-comments' ).find( 'li' ).length - 1;

		// Add the comment count to our trigger data attribute.
		showHideTrigger.attr( 'data-comment-count', commentCount );

		// Append our number to our link.
		showHideTrigger.attr( 'data-show-text', showHideTrigger.attr( 'data-show-text' ) + ' (' + commentCount + ')' );
	};

	// Show/Hide the comments on click.
	app.showHideComments = function() {

		let trigger = $( this );

		// Loop through all of the sibling LIs.
		trigger.siblings( 'ul' ).toggleClass( 'hide-extra-comments' );

		// If comments are hidden, change the trigger text.
		if ( trigger.siblings( 'ul' ).hasClass( 'hide-extra-comments' ) ) {
			trigger.text( trigger.attr( 'data-show-text' ) );
		} else {
			trigger.text( trigger.attr( 'data-hide-text' ) );
		}
	};

	// Engage
	$( app.init );

}( window, jQuery, window.ShowHideBPComments ) );
