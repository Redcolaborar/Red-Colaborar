<?php
// hacks and mods will go here
/**
 * Disables BuddyPress' registration process and fallsback to WordPress' one.
*/ 

//hide some buddypress activity updates: new registered member, joined group, created group, changed avatar

function hide_some_activities( $activity_object ) {
 
    $exclude = array( 'joined_group', 'created_group', 'new_avatar', 'new_member' );

    if( in_array( $activity_object->type, $exclude ) )
        $activity_object->type = false;
 
}
add_action('bp_activity_before_save', 'hide_some_activities', 1, 1 );

//show total user count instead of active user count

add_filter( ‘bp_get_total_member_count’, ‘bp_core_get_total_member_count’ );

?>