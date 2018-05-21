/**
 * Force Post Category Script.
 */
window.forcePostCategoryObject = {};
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
			whatsNewContainer: $( '#whats-new' ),
			forcePostCategorySelector: $( '.edit-category-activity' ), // select
			submitButton: $( '#whats-new-submit input' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.forcePostCategorySelector.on( 'change', app.doForcePostCategory );

		// Force input button to stay disabled until js below.
		app.$c.whatsNewContainer.focus( function() {
			app.$c.submitButton.prop( 'disabled', true );

			// Retain enabled button if selectedIndex is anything other than default.
			if ( 0 !== app.$c.forcePostCategorySelector[0].selectedIndex ) {
				app.$c.submitButton.prop( 'disabled', false );
			}
		} );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.forcePostCategorySelector.length;
	};

	// Get Selection and Show/Hide Button if no selection made.
	app.doForcePostCategory = function() {

		let selection = this.selectedIndex;

		app.$c.submitButton.prop( 'disabled', true );

        if ( 0 !== selection ) {
			app.$c.submitButton.prop( 'disabled', false );
        }
	};

	// Engage!
	$( app.init );

} ( window, jQuery, window.forcePostCategoryObject ) );
