<?php
/**
 * Shared functionality.
 *
 * @since 1.0.0
 * @package  WebDevStudios\RedColaborar
 */

namespace WebDevStudios\RedColaborar;

/**
 * Shared functionality between several areas.
 *
 * @author Aubrey Portwood
 * @since 1.0.0
 */
class Shared {

	/**
	 * Is this the activity page.
	 *
	 * @author Aubrey Portwood
	 * @since  1.0.0
	 *
	 * @return boolean True if so, false if not.
	 */
	public function is_activity_page() {

		// Are we doing a load more request? @codingStandardsIgnoreLine: REQUEST Okay here.
		$activity_ajax = (boolean) isset( $_REQUEST['action'] ) && 'activity_get_older_updates' === $_REQUEST['action'];

		// If it's the activity front page, the activity directory, or an AJAX request (load more).
		return (boolean) bp_is_activity_front_page() || bp_is_activity_directory() || $activity_ajax;
	}
}
