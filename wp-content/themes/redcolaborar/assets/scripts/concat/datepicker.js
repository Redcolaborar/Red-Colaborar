/**
 * Datepicker IE11 Script.
 */
window.datePickerObject = {};
( function( window, $, app ) {

	// Constructor.
	app.init = function() {
		app.cache();

		if ( app.meetsRequirements() ) {
			app.doDatePicker();
		}
	};

	// Cache all the things.
	app.cache = function() {
		app.$c = {
			window: $( window ),
			body: $( 'body' ),
			filters: $( '#wds-recolaborar-sidebar-filters' ),
			dateBeforePicker: $( '#date-before' ),
			dateAfterPicker: $( '#date-after' )
		};
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.filters.length;
	};

	// Some function.
	app.doDatePicker = function() {

		var dateFormat = {
			dateFormat: 'yy-mm-dd'
		};

		app.$c.dateBeforePicker.datepicker( dateFormat );

		app.$c.dateAfterPicker.datepicker( dateFormat );
	};

	// Engage!
	$( app.init );

} ( window, jQuery, window.datePickerObject ) );
