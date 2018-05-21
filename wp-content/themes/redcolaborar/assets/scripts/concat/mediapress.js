/**
 * MediaPress Upload Script.
 */
window.WDS_mediaPress_Object = {};
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
			window: $(window),
			mediaPressSelector: $( '#whats-new-options' ),
			mediaUploader: $( '#mpp-upload-dropzone-activity.mpp-dropzone' )
		};
	};

	// Combine all events.
	app.bindEvents = function() {
		app.$c.mediaPressSelector.bind( 'DOMSubtreeModified', app.doMediaPress );
	};

	// Do we meet the requirements?
	app.meetsRequirements = function() {
		return app.$c.mediaPressSelector.length;
	};

	// Hide MediaPress if parent dialoge is clicked.
	app.doMediaPress = function() {
		
		let active = $( this );

		if ( app.$c.mediaPressSelector.attr( 'style' ).indexOf( 'none' ) === -1 ) {
			app.$c.mediaUploader.hide();
		}
	};

	// Engage!
	$( app.init );

})( window, jQuery, window.WDS_mediaPress_Object );