<?php

function grassblade_get_groups( $args ) {
    global $wp_xmlrpc_server;
    $wp_xmlrpc_server->escape( $args );

    $blog_id  = $args[0];
    $username = $args[1];
    $password = $args[2];

    if ( ! $user = $wp_xmlrpc_server->login( $username, $password ) )
        return $wp_xmlrpc_server->error;

    $params = $args[3];
    return apply_filters("grassblade_groups", array(), $params);
}
function grassblade_get_group_leaders($group) {
    return apply_filters("grassblade_group_leaders", array(), $group);
}
function grassblade_get_group_users($group) {
    return apply_filters("grassblade_group_users", array(), $group);
}
function grassblade_xmlrpc_methods( $methods ) {
    $methods['grassblade.getGroups'] = 'grassblade_get_groups';
    return $methods;   
}
add_filter( 'xmlrpc_methods', 'grassblade_xmlrpc_methods');
