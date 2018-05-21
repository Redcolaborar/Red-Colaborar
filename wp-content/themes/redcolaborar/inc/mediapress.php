<?php
/**
 * Custom Mediapress functions that act independently of the theme templates.
 *
 * @package Red Colaborar
 */

/**
 * Removes forced mediapress url rewrite pointing incorrectly to gallery page.
 */
remove_filter( 'bp_activity_get_permalink', 'mpp_filter_activity_permalink', 10 );
