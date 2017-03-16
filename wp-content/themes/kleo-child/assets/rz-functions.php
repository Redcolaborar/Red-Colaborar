<?php

// Taiga 147
remove_filter( 'bp_activity_get_permalink', 'mpp_filter_activity_permalink', 10 );

// Autoembed links in the same line as text. Wp default behavior is to only embed if the content is in a new line
// Will be added only on activities
add_filter( 'bp_get_activity_content_body', 'rc_autoembed_same_line', 5, 1 );
function rc_autoembed_same_line( $content ) {
  $content_initial = $content;
  // (([^\r\n])|(\ ))(https?:\/\/[^\s<>"]+)

  //(?:[^\r\n]\ *)https?:\/\/[^\s<>"]+

    // if ( bp_use_embed_in_activity() ) {
    //   add_filter( 'bp_get_activity_content_body', array( &$this, 'autoembed' ), 8 );
    //   add_filter( 'bp_get_activity_content_body', array( &$this, 'run_shortcode' ), 7 );
    // }

    // Replace line breaks from all HTML elements with placeholders.
    // $content = wp_replace_in_html_tags( $content, array( "\n" => '<!-- wp-line-break -->' ) );

    // At least 3 chars before beginning of url (not in new line)
    // if ( preg_match( '#.{3,}https?:\/\/[^\s<>"]+(\s*)$#', $content ) ) {
        // Find URLs on their own line.
        // $content = preg_replace_callback( '|^(\s*)(https?://[^\s<>"]+)(\s*)$|im', array( $this, 'autoembed_callback' ), $content );
        // Find URLs in their own paragraph.
        /* $content = preg_replace_callback( '|(<p(?: [^>]*)?>\s*)(https?://[^\s<>"]+)(\s*<\/p>)|i', array( $this, 'autoembed_callback' ), $content ); */

        //Prepare content
        $content = preg_replace('#(\n{1,}\ *)#', "\n", $content);

        // Find URLs in the content line
        $matches = preg_match( '#(?:\ *)https?:\/\/[^\s<>"]+#',  $content, $result );
        // $content = preg_replace_callback( '#(([^\r\n])|(\ ))(https?:\/\/[^\s<>"]+)#', array( $this, 'autoembed_callback' ), $content );
    // }

    foreach( $result as $embed ) {
      //remove spaces
      $formatted = preg_replace('#\ *#', '', $embed);
      if( !empty( $formatted ) ) {
        $content .= "<p>$formatted</p>";
      }

    }

    //Keep square brackets
    // $content = strtr( $content, array('[' => '&lbrack;', ']' => '&rbrack;') );

    return $content;

    // // Put the line breaks back.
    // return str_replace( '<!-- wp-line-break -->', "\n", $content );
}

add_action('wp_loaded', 'rc_remove_hooks');
function rc_remove_hooks() {

  //Do not remove brackets from activity
  remove_filter( 'bp_get_activity_content_body','kleo_bp_activity_filter', 1 );

}

/*
BuddyPress Group Email Subscription v 3.7.0 is not querying activity_comment properly.
Add item_id to the list of arguments of activity_obj to create a proper link
 */
add_filter( 'bp_activity_get_permalink', 'rc_fix_group_email_notifications_links', 10, 2);
function rc_fix_group_email_notifications_links( $link, $activity_obj ) {

  if( ( 'activity_comment' != $activity_obj->type ) || !empty( $activity_obj->item_id ) ) return $link;

  // send it again to the function and let it populate the object
  $link = bp_activity_get_permalink( $activity_obj->id );

  return $link;

}

/*
[TAIGA-107] removes selection from previously invited users
*/
// add_action('wp_footer', 'rc_deselect_users_invite', 99);
function rc_deselect_users_invite() {
?>
  <script>

    if( jQuery('body.invite-anyone').length ) {
      jQuery('#invite-anyone-member-list').ready(function() {
        jQuery('#invite-anyone-member-list input[name="friends[]"]').prop('checked', false);
      });
    };

  </script>
<?php
}

//debug Filters
// add_action('wp', function(){ echo '<pre>'; print_r( $GLOBALS['wp_filter']['wc_autoship_notify_10day'] ); echo '</pre>'; exit; } );
