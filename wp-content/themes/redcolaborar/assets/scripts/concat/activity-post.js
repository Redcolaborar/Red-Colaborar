/* globals console, jQuery */
if ( ! window.hasOwnProperty( 'wdsActivityPost' ) ) {

	/**
	 * Activity post.
	 */
	window.wdsActivityPost = ( function( $, pub ) {
		$( document ).ready( function() {
			var $body = $( 'body' );
			var $dropZone = $( '#mpp-upload-dropzone-activity' );
			var $whatsNew = $( '#whats-new' );
			var $uploadButton = $( '#mpp-all-upload' );
			var $selectFile = $( '#mpp-upload-media-button-activity' );

			// Are we disabling this JS temporarily via ?disableWdsActivityPost.
			var disabled = ( 1 === window.location.search.indexOf( 'disableWdsActivityPost' ) );
			if ( disabled ) {
				return;
			}

			if ( window.innerWidth < 900 ) {

				// Bail on mobile.
				return;
			}

			// Requirements.
			if ( $whatsNew.length && $dropZone.length && $uploadButton.length ) {

				// Tell SASS to do it's thing!
				$body.addClass( 'activity-post-js-ready' );

				// Make sure the dropzone is activated the first time!
				$uploadButton.click();

				// When the upload camera icon is clicked from here on...
				$uploadButton.on( 'click', function() {

					// Click the "Select File" button in the dropzone that's hidden.
					$selectFile.click();
				} );
			} else {

				// Bail, the required elements we need aren't there.
				return;
			}

			/**
			 * Activate the dropzone placement.
			 *
			 * This places the dropzone over the posting area when files
			 * are dragged over it.
			 *
			 * @author Aubrey Portwood
			 * @since  Friday, 11 24, 2017
			 *
			 * @param  {Object} e Event object.
			 */
			function activateDropzonePlacement( e ) {
				var dt = e.originalEvent.dataTransfer;
				var files = dt.types && ( dt.types.indexOf ? -1 !== dt.types.indexOf( 'Files' ) : dt.types.contains( 'Files' ) );

				if ( files && $dropZone.length ) {
					$dropZone.addClass( 'active' );
				}
			}

			/**
			 * Deactivate the dropzone placement.
			 *
			 * This makes the dropzone placement totally hidden.
			 *
			 * @author Aubrey Portwood
			 * @since  Friday, 11 24, 2017
			 */
			function deactivateDropzonePlacement() {
				$dropZone.removeClass( 'active' );
			}

			/**
			 * Focus on the post textarea.
			 *
			 * This sets the cursor in the posting textarea
			 * and should activate the other items, etc.
			 *
			 * @author Aubrey Portwood
			 * @since  Friday, 11 24, 2017
			 */
			function focusOnPost() {
				$whatsNew.focus();
			}

			// When I drag something over the post textarea activate the dropzone over it.
			$whatsNew.on( 'dragover', activateDropzonePlacement );

			// Focus on the post textarea when something is dropped on the dropzone.
			$dropZone.on( 'drop.post', focusOnPost );

			// Deactivate the dropzone placement when something is dropped on the dropzone.
			$dropZone.on( 'drop.deactivateDropzonePlacement', deactivateDropzonePlacement );

			// Deactivate the dropzone placement when something leaves the window.
			$( window ).on( 'mouseleave', deactivateDropzonePlacement );

		} ); return pub; // Return public things.
	} ( jQuery, {} ) );
} // End if().
