/**
 * Activity Feed Category Menu Hack.
 *
 * When this is loaded, it will highlight
 * the Activity topics as the current menu.
 *
 * Load this conditionally or it will happen on all pages.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

/* globals jQuery */
if ( ! window.hasOwnProperty( 'wdsRedColaborarActivitySubmenuHack' ) ) {

	/**
	 * Activity Feed Category Menu Hack.
	 */
	window.wdsRedColaborarActivitySubmenuHack = ( function( $, pub ) {

		/**
		 * When the Activity Topics are loaded use this hack to highlight the right menu.
		 *
		 * Posts always get highlighted when ?post_type= is not a normal CPT,
		 * like activity in BP. So we have to un-highlight it, and highlight the
		 * activity category menu.
		 *
		 * @author Aubrey Portwood
		 * @since  1.0.0
		 */
		function hackActivityMenu() {
			var activeClasses = 'wp-has-current-submenu wp-menu-open';
			var $postsMenu = $( '#menu-posts' );
			var $bpActivityMenu = $( '#toplevel_page_bp-activity' );

			// Remove active classes from posts.
			$postsMenu.removeClass( activeClasses );

			// Add normal non-active classes to posts.
			$postsMenu.addClass( 'wp-has-submenu wp-not-current-submenu menu-top' );

			// Remove active classes from href link inside posts menu.
			$( '> a', $postsMenu ).removeClass( activeClasses );

			// Add active classes to activity.
			$bpActivityMenu.addClass( activeClasses );

			// Add active classes to href in activity menu.
			$( '> a', $bpActivityMenu ).addClass( activeClasses );

			// The pages you want to make sure their menu is activated.
			var pages = [
				'edit-tags.php?taxonomy=bp-activity-topics&post_type=bp-activity',
				'edit-tags.php?taxonomy=bp-activity-hashtags&post_type=bp-activity'
			];

			for ( var index in pages ) {
				var page = pages[ index ];

				// If we have this page loaded.
				if ( -1 !== window.location.href.indexOf( page ) ) {

					// Make the activity categories the current selected menu.
					$( 'a[href="' + page + '"]' ).parent( 'li' ).addClass( 'current' );
				}
			}
		}

		// When the menu loads.....reassign the menu.
		$( '#adminmenuwrap' ).ready( hackActivityMenu );

		return pub; // Return public things.
	} ( jQuery, {} ) );
} // End if().
