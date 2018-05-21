/**
 * Autocomplete.
 *
 * This uses https://github.com/yuku-t/textcomplete to add
 * ad in-line autocomplete to the activity textarea.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

/* globals jQuery, Textcomplete, wdsRedColaborarHashtags */
if ( ! window.hasOwnProperty( 'wdsRedColaborarAutocomplete' ) ) {

	/**
	 * Autocomplete.
	 */
	window.wdsRedColaborarAutocomplete = ( function( $, pub ) {

		/**
		 * Because of how the autocomplete injects tags, we have to fix the spacing issue.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 */
		function correctTags() {
			$( this ).val( $( this ).val().replace( /(([a-z|A-Z|0-9]))\#/g, '$& #' ).replace( '# #', ' #' ) );
		}

		/**
		 * Setup the textarea with autosuggest.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 *
		 * @return {undefined} Early bail if the <textarea> isn't present.
		 */
		function init() {

			// The BP Textarea.
			var $textarea = $( '#whats-new-textarea textarea' );

			if ( -1 === $textarea.length ) {
				return;
			}

			// The textarea.
			var editor = new Textcomplete.editors.Textarea( $textarea[0] );

			// The autocomplete class.
			var textcomplete = new Textcomplete( editor );

			// Register a reaction.
			textcomplete.register( [ {

				// Match #<char>.
				match: /(^|\s)#(\w+)$/,

				/**
				 * Search.
				 *
				 * @author Aubrey Portwood
				 * @since  1.0.0
				 *
				 * @param  {String}   term     The matched term from above.
				 * @param  {Function} callback The callback.
				 */
				search: function( term, callback ) {

					/**
					 * Discover the word chosen.
					 *
					 * @author Aubrey Portwood
					 * @since  1.0.0
					 *
					 * @param  {String}      The word.
					 * @return {String}      The chosen word.
					 */
					callback( $.map( wdsRedColaborarHashtags, function( word ) {
						return 0 === word.indexOf( term ) ? word : null;
					} ) );
				},

				/**
				 * Replace with word with specific format.
				 *
				 * @author Aubrey Portwood
				 * @since  1.0.0
				 *
				 * @param  {String} value The chosen word.
				 * @return {String}       The chosen word with # before it.
				 */
				replace: function( value ) {
					return '#' + value;
				}
			} ] );

			// Correct tags when they are placed.
			$textarea.on( 'keyup keydown change mousemove mouseenter mouseleave focus blur', correctTags );
		}

		// Init.
		$( document ).ready( init );

		return pub; // Return public things.
	} ( jQuery, {} ) );
} // End if().
