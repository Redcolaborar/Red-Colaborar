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
        // $content = preg_replace('#(\n{3,}\ *)#', "</br></br></br><br></br>\n <br>\n <br>\n <br>\n \n \n \n abcde", $content);

        $content_without_tags = strip_tags( $content );

        // Find URLs in the content line
        $matches = preg_match_all( '/(?:.{3,})(https?:\/\/[^\s<>"]+)/',  $content_without_tags, $result );
        // $content = preg_replace_callback( '#(([^\r\n])|(\ ))(https?:\/\/[^\s<>"]+)#', array( $this, 'autoembed_callback' ), $content );
    // }
    // $content .= "<pre>" . var_export( $result[1], 1 ) . "</pre>";

    if( !empty($result[1]) ) {
      foreach( $result[1] as $embed ) {
        //remove spaces
        $formatted = preg_replace('#\ *#', '', $embed);
        if( !empty( $formatted ) ) {
          $content .= "<p>$formatted</p>";
        }

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
add_filter( 'bp_activity_get_permalink', 'rc_fix_group_email_notifications_links_for_comments', 10, 2);
function rc_fix_group_email_notifications_links_for_comments( $link, $activity_obj ) {

  if( ( 'activity_comment' != $activity_obj->type ) || !empty( $activity_obj->item_id ) ) return $link;

  // send it again to the function and let it populate the object
  $link = bp_activity_get_permalink( $activity_obj->id );

  return $link;

}

add_filter( 'bp_activity_get_permalink', 'rc_fix_group_email_notifications_links_for_learndash_updates', 10, 2);
function rc_fix_group_email_notifications_links_for_learndash_updates( $link, $activity_obj ) {

  if( ( 'activity_update' != $activity_obj->type ) ) return $link;

  $check_meta = bp_activity_get_meta( $activity_obj->id, 'bp_learndash_group_activity_markup_courseid', true);

  if( empty( $check_meta ) ) return $link;

  $course_link = get_permalink( $check_meta );

  //if content is empty this is a notification that some user has completed a lession
  return ( empty( $activity_obj->content ) ) ? $course_link : $course_link . "#comments";

}

/* This is the only way to fix the links in email notifications for mediapress activities without changing any plugin */
// add_filter( 'mpp_record_activity_args', 'rc_fix_primary_link_mediapress', 10, 2 );
// function rc_fix_primary_link_mediapress( $activity_args, $default ) {
//
//   $type = $default['type'];
//
//   if( in_array( $type, array( 'media_upload', 'add_media' ) ) )  {
//     $media_id = $default['media_id'];
//
//     if( !empty( $media_id ) ) {
//       $activity_args['primary_link'] = wp_get_attachment_url( $media_id );
//     }
//
//   }
//
//   return $activity_args;
//
// }

// add_filter( 'mpp_format_activity_action_media_upload', 'rc_fix_primary_link_mediapress', 10, 4 );
// function rc_fix_primary_link_mediapress( $action, $activity, $media_id, $media_ids ) {
//
//   $activity = new BP_Activity_Activity( $activity_id );
//
//   $activity->primary_link = wp_get_attachment_url( $media_id );
//   $activity->save();
//
//   error_log( "aaaa\n", 3, __DIR__ . "/teste.log" );
//
//   return $action;
//
// }

// add_action( 'added_activity_meta', 'rc_fix_primary_link_mediapress', 10, 4  );
// function rc_fix_primary_link_mediapress( $mid, $object_id, $mkey, $mvalue ) {
//
//   if( $mkey != "_mpp_gallery_id" ) return;
//
//   $activity = new BP_Activity_Activity( $object_id );
//
//   if( !empty( $activity) ) {
//
//     $media_ids = bp_get_activity_meta( $object_id, '_mpp_attached_media_id', false );
//
//     if( count( $media_ids ) == 1 ) {
//       $activity->primary_link = wp_get_attachment_url( $media_ids[0] );
//     } else {
//       $activity->primary_link = wp_get_attachment_url( $mvalue );
//     }
//
//     $activity->save();
//
//   }
//
// }

// add_filter( 'bp_activity_get_permalink', 'rc_fix_group_email_notifications_links_for_mediapress_uploads', 10, 2);
// function rc_fix_group_email_notifications_links_for_mediapress_uploads( $link, $activity_obj ) {
//
//   var_dump( $activity_obj );
//   die();
//
//   if( ( 'mpp_media_upload' != $activity_obj->type ) ) return $link;
//
//   $media_ids = bp_activity_get_meta( $activity_obj->id, '_mpp_attached_media_id', false);
//   $gallery_id = bp_activity_get_meta( $activity_obj->id, '_mpp_gallery_id', true);
//
//
//   var_dump($media_ids);
//   die();
//
//   if( empty( $check_meta ) ) return $link;
//
//   $course_link = get_permalink( $check_meta );
//
//   //if content is empty this is a notification that some user has completed a lession
//   return ( empty( $activity_obj->content ) ) ? $course_link : $course_link . "#comments";
//
// }

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
/*
[TAIGA-167] Facebook Share Button to Activity Updates
*/
add_action( 'bp_activity_entry_meta',	'rc_share_fb_activity_update', 5, 0);
function rc_share_fb_activity_update(){
?>

<div class="fb-share-button"
  data-href="<?php bp_activity_thread_permalink() ?>"
  data-layout="button_count">
</div>

<a href="https://twitter.com/share" data-url="<?php bp_activity_thread_permalink() ?>" data-text=" " class="twitter-share-button" data-show-count="false">Tweet</a><script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>
<?php
}

add_action('kleo_after_body', 'rc_embed_fb_js_sdk');
function rc_embed_fb_js_sdk() {
?>
<!-- Load Facebook SDK for JavaScript -->
  <div id="fb-root"></div>
  <script>(function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s); js.id = id;
    js.src = "//connect.facebook.net/es_ES/sdk.js#xfbml=1&version=v2.8";
    fjs.parentNode.insertBefore(js, fjs);
  }(document, 'script', 'facebook-jssdk'));</script>
<?php
}

// [TAIGA-167] Facebook title and description when sharing activities
add_filter('fb_og_title', 'rc_fb_title_activities', 10, 1);
add_filter('fb_og_desc', 'rc_fb_desc_activities', 10, 1);
add_filter('fb_og_image', 'rc_fb_image_activities', 10, 1);
// add_filter('fb_og_image_additional', 'rc_fb_image_additional_activities', 10, 1);
function rc_fb_title_activities( $title ) {

  if( !function_exists( 'bp_is_activity_component' ) ) return $title;

  if( bp_is_activity_component() ) {
    return get_bloginfo( 'name' ) . ' | ' . $title;
  }

  return $title;
}

function rc_fb_desc_activities( $desc ) {

  if( !function_exists( 'bp_is_single_activity' ) ) return $desc;

  if( bp_is_single_activity() ) {

    $activity_id = get_query_var('page');

    if( empty($activity_id) ) return $desc;

    $activity = new BP_Activity_Activity( $activity_id );

    if( empty($activity) ) return $desc;

    $pos = strpos( $activity->content, ' ', 300 );
    $description = substr( $activity->content, 0, $pos );

    if( empty($description) ) return $desc;

    $description .= '...';

    $desc = $description;

  }

  return $desc;
}

/* Show user avatar in the activity share */
function rc_fb_image_activities( $image_link ) {

  if( !function_exists( 'bp_is_single_activity' ) ) return $desc;

  if( bp_is_single_activity() ) {

    $activity_id = get_query_var('page');

    if( empty($activity_id) ) return $image_link;

    $activity = new BP_Activity_Activity( $activity_id );

    if( empty($activity) ) return $image_link;

    $user_avatar = bp_core_fetch_avatar( array( 'item_id' => $activity->user_id, 'type' => 'full', 'html' => false ) );

    if( !empty($user_avatar) ) $image_link = $user_avatar;

  }

  return $image_link;

}

// function rc_fb_image_additional_activities( $images ) {
//
//   $images[] = array(
//     'fb_image' => trim($image_link),
//     'png_overlay' => false,
//   );
//
//   return $images;
//
// }

/*
[TAIGA-177] El tamaño de la imagen adjunta en el post se queda muy pequeña
*/
add_action( 'wp_head', 'rz_taiga177' );
function rz_taiga177() {
?>
<style>
  .mpp-lightbox-content {
    height: auto;
  }
  .mpp-lightbox-media-container {
    height: auto;
  }
  .twitter-share-button {
    width: 70px !important;
    max-width: none !important;
    display: inline-block;
    vertical-align: text-top;
    margin-left: 8px;
    margin-right: 8px;
  }
  .fb_iframe_widget {
    margin-left: 8px;
    margin-right: 8px;
  }
</style>
<?php
}

/* Permalinks fix */
add_filter( 'bp_activity_permalink_access', 'rc_fix_activity_permalinks_permissions', 10, 2);
function rc_fix_activity_permalinks_permissions( $has_access, $activity ) {

  $bp = buddypress();

  if ( isset( $bp->groups->id ) && $activity->component == $bp->groups->id ) {

		// Check to see if the group is not public, if so, check the
		// user has access to see this activity.
		if ( empty($activity->item_id) ) {

      return true;

		}
	}

  return $has_access;

}

/* Show homepage to everyone. Show the other pages to logged in users */
add_action( 'template_redirect', 'rc_only_show_homepage', 1);
function rc_only_show_homepage() {

  $is_logged_in = is_user_logged_in();
  $slug_no_user = 'home-no-user';
  $slug_register = 'register';

  $page_no_user = get_page_by_path( $slug_no_user , OBJECT );

  if( !$is_logged_in && !is_page( $slug_no_user ) && !is_page( $slug_register ) && !is_admin() ) {

    if( !empty($page_no_user) ) {
      wp_redirect( get_permalink($page_no_user->ID) );
    } else {
      auth_redirect();
    }

    exit;
  }

}

/*
image and description homepage facebook
*/

//debug Filters
// add_action('wp', function(){ echo '<pre>'; print_r( $GLOBALS['wp_filter']['wc_autoship_notify_10day'] ); echo '</pre>'; exit; } );
