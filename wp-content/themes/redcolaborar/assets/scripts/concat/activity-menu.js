/**
 * [...] Activity Menu.
 *
 * @since   NEXT
 * @package Red Colaborar
 */

/* globals jQuery */
if ( ! window.hasOwnProperty( 'wdsBPActivityMenu' ) ) {

	/**
	 * Activity Menu.
	 *
	 * @since           NEXT
	 * @return {Object} Public object.
	 */
	window.wdsBPActivityMenu = ( function( $, pub ) {

		/**
		 * Clicking the [...] button.
		 *
		 * @author Aubrey Portwood
		 * @since  NEXT
		 *
		 * @param  {Object} e Event object.
		 */
		function click( e ) {

			// Get the button.
			const $target = $( e.target ).closest( '.bp-activity-menu-trigger' );

			// Find the menu near the button.
			const $menu = $target.siblings( '.bp-activity-menu' );

			// Toggle the menu.
			$menu.fadeToggle();
		}

		// Delegate any clicks on body .bp-activity-menu-trigger.
		$( 'body' ).on( 'click.wdsBPActivityMenu', '.bp-activity-menu-trigger', click );

		return pub; // Return public things.
	} ( jQuery, {} ) );
} // End if().
