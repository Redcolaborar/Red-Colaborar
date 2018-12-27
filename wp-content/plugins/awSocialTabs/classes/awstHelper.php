<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if( !defined( 'RC_LIKES_META_KEY' ) ) {
    define( 'RC_LIKES_META_KEY', 'awst_like' );
}

if( !defined( 'RC_LIKES_USERMETA_KEY' ) ) {
    define( 'RC_LIKES_USERMETA_KEY', 'awst_user_likes' );
}

class AwstHelper {

  function __construct() {
    add_action( 'plugins_loaded', array( $this, 'init' ) );
    add_action( 'shutdown', array( $this, 'migrate_old_likes' ) );
  }

  public function init() {



  }

  //checks will only be done against the object, the usermeta is only to show a list of likes in the user profile, etc

  /******* HELPERS ********/

  public static function has_user_liked_object( $user_id, $object_id, $object_type = 'post' ) {

    $object_likes = array();
    switch( $object_type ) :
      case 'post':
        $object_likes = self::get_post_likes( $object_id );
      break;
      case 'activity':
        $object_likes = self::get_activity_likes( $object_id );
      break;
      case 'post_comment':
        $object_likes = self::get_comment_likes( $object_id );
      break;
    endswitch;

    return in_array( $user_id, $object_likes );

  }

  public static function count_object_likes( $object_id, $object_type = 'post' ) {
    $object_likes = self::get_object_likes( $object_id, $object_type );
    // var_dump( $object_likes );
    return count( $object_likes );
  }

  /******* GET ********/

  // get list of post ids that a user has liked
  public static function get_user_likes_by_object_type( $user_id, $object_type = 'post' ) {
    $user_likes = get_user_meta( $user_id, RC_LIKES_USERMETA_KEY, true);
    $user_object_likes = ( empty( $user_likes ) || !isset( $user_likes[ $object_type ] ) ) ? array() : $user_likes[ $object_type ];

    return $user_object_likes;
  }

  public static function get_user_activity_likes( $user_id ) {
    return self::get_user_likes_by_object_type( $user_id, 'activity' );
  }

  public static function get_user_post_likes( $user_id ) {
    return self::get_user_likes_by_object_type( $user_id, 'post' );
  }

  public static function get_user_comment_likes( $user_id ) {
    return self::get_user_likes_by_object_type( $user_id, 'post_comment' );
  }

  // get list of users who have liked a given post

  public static function get_object_likes( $object_id, $object_type = "post" ) {
    $object_likes = array();
    switch( $object_type ) :
      case "post":
        $object_likes = get_post_meta( $object_id, RC_LIKES_META_KEY, true );
      break;
      case "activity":
        $object_likes = bp_activity_get_meta( $object_id, RC_LIKES_META_KEY, true );
      break;
      case "post_comment":
        $object_likes = get_comment_meta( $object_id, RC_LIKES_META_KEY, true );
      break;
    endswitch;

    if( empty( $object_likes ) )
      $object_likes = array();

    return $object_likes;
  }

  public static function get_post_likes( $post_id ) {
    return self::get_object_likes( $post_id, "post" );
  }

  public static function get_activity_likes( $activity_id ) {
    return self::get_object_likes( $activity_id, "activity" );
  }

  public static function get_comment_likes( $comment_id ) {
    return self::get_object_likes( $comment_id, "post_comment" );
  }

  /******* SETTERS ********/

  public static function toggle_user_like_for_object( $user_id, $object_id, $object_type = "post" ) {

    $has_liked = self::has_user_liked_object( $user_id, $object_id, $object_type );
    $object_likes = self::get_object_likes( $object_id, $object_type );
    $user_likes = self::get_user_likes_by_object_type( $user_id, $object_type );

    // error_log( "user_id_just_in_case => " . var_export( $user_id, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );
    // error_log( "object_id_just_in_case => " . var_export( $object_id, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );
    // error_log( "has_liked => " . var_export( $has_liked, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );
    // error_log( "object_likes => " . var_export( $object_likes, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );
    // error_log( "user_likes => " . var_export( $user_likes, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );

    if( $has_liked ) {
      $object_likes = array_diff( $object_likes, [ $user_id ] );
      $user_likes = array_diff( $user_likes, [ $object_id ] );
    } else {
      $object_likes[] = $user_id;
      $user_likes[] = $object_id;
    }

    self::save_object_likes( $object_id, $object_likes, $object_type );
    self::save_user_likes( $user_id, $user_likes, $object_type );

  }

  public static function save_object_likes( $object_id, $object_likes, $object_type = "post" ) {

    switch( $object_type ) :
      case "post":
        update_post_meta( $object_id, RC_LIKES_META_KEY, $object_likes );
      break;
      case "activity":
        bp_activity_update_meta( $object_id, RC_LIKES_META_KEY, $object_likes );
      break;
      case "post_comment":
        update_comment_meta( $object_id, RC_LIKES_META_KEY, $object_likes );
      break;
    endswitch;

  }

  public static function save_user_likes( $user_id, $user_likes, $object_type = "post" ) {

    $user_likes_meta = get_user_meta( $user_id, RC_LIKES_USERMETA_KEY, true );
    $user_likes_meta = is_array( $user_likes_meta ) ? $user_likes_meta : array();

    $user_likes_meta[$object_type] = $user_likes;

    // error_log( "user_likes_meta => " . var_export( $user_likes_meta, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );

    update_user_meta( $user_id, RC_LIKES_USERMETA_KEY, $user_likes_meta );

  }

  public function migrate_old_likes() {

    // $likes = self::get_user_activity_likes( 1 );
    // var_dump( $likes );
    // wp_die();

    $ver = "1.2";
    $has_updated = get_option( "rc_likes_updated" );

    if( /*($ver == $has_updated) ||*/ empty($_GET["run_migrator"]) || empty($_GET["migrator_step"]) ) return;

    update_option( "rc_likes_updated", $ver );

    global $wpdb;

    // clean everything new
    // test functions user has liked object

    // $delete_query = "DELETE FROM {$wpdb->prefix}postmeta WHERE meta_key = '" . RC_LIKES_META_KEY . "'";
    // echo $delete_query;
    // wp_die();

    // 1.clear all likes on postmeta table
    if( $_GET["migrator_step"] == '1' ) {
      $updated = $wpdb->delete( $wpdb->prefix . 'postmeta', array( 'meta_key' => RC_LIKES_META_KEY ) );
      $updated2 = $wpdb->delete( $wpdb->prefix . 'bp_activity_meta', array( 'meta_key' => RC_LIKES_META_KEY ) );
      $updated3 = $wpdb->delete( $wpdb->prefix . 'usermeta', array( 'meta_key' => RC_LIKES_USERMETA_KEY ) );
      var_dump( $updated );
      var_dump( $updated2 );
      var_dump( $updated3 );
      wp_die();
    }

    // 2. get all likes from usermeta table
    $users = get_users( array( 'fields' => 'id' ));
    // print_r( $users );
    // wp_die();

    // error_log( "**process_started** \n", 3, WP_CONTENT_DIR . "/migrator.log" );
    // error_log( var_export( $users, 1 ) . "\n", 3, WP_CONTENT_DIR . "/migrator.log" );

    foreach( $users as $user_id ) {

      $awst_legacy_likes = get_user_meta( $user_id, RC_LIKES_META_KEY, true );

      // if there is no likes from this user, move on
      if( empty( $awst_legacy_likes ) ) continue;

      $awst_legacy_likes = array_unique( $awst_legacy_likes );

      // 3. foreach like in the usermeta table, add it using the new awst helper. A new usermeta key will be used,
      foreach( $awst_legacy_likes as $like_object_id ) {

        // 4. test if it is an activity. We give priority to activities
        $activity = new BP_Activity_Activity( $like_object_id );

        if( !empty( $activity->id ) ) {
          unset( $activity );
          self::toggle_user_like_for_object( $user_id, $like_object_id, "activity" );
          // var_dump( self::get_user_activity_likes( $user->ID ) );
          continue;
        }

        // 5. test if it is a post
        $post = get_post( $like_object_id );
        if( !empty( $post ) ) {
          unset( $post );
          self::toggle_user_like_for_object( $user_id, $like_object_id, "post" );
          // var_dump( self::get_user_post_likes( $user->ID ) );
          continue;
        }

      }

    }

    // error_log( "**FINISHED** \n", 3, WP_CONTENT_DIR . "/migrator.log" );
    echo "All Done!";
    wp_die();

  }

}

new AwstHelper();

?>
