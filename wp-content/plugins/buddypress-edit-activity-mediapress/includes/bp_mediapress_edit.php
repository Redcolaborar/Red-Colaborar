<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

require_once( BUDDYBOSS_EDIT_ACTIVITY_PLUGIN_DIR  . 'includes/main-class.php' );

if(  class_exists( 'BuddyBoss_Edit_Activity' ) ):

class BP_Edit_Mediapress extends BuddyBoss_Edit_Activity {

  private $default_options = array(
		'user_access'		=> 'author',//whether only admin can edit an activity or the activity's original author as well
		'editable_types'	=> array( 'mpp_media_upload' ),//what can be edited
		'editable_timeout'	=> false,//how long after posting, the activity is editable? always editable by default
		'exclude_admins'	=> 'yes',//whether admins are excluded from timeout limitation and can always edit activity.
	);

	public $options = array();

	public $network_activated = false;

	public static function instance(){
		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance )
		{
			$instance = new BP_Edit_Mediapress();
			$instance->setup_globals();
			$instance->setup_actions();
			$instance->setup_textdomain();
		}

		// Always return the instance
		return $instance;
	}

	private function __construct() { /* Do nothing here */ }

	public function __clone() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddypress-edit-activity' ), '1.7' ); }

	public function __wakeup() { _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'buddypress-edit-activity' ), '1.7' ); }

	public function __isset( $key ) { return isset( $this->data[$key] ); }

	public function __get( $key ) { return isset( $this->data[$key] ) ? $this->data[$key] : null; }

	//public function __set( $key, $value ) { $this->data[$key] = $value; }

	public function __unset( $key ) { if ( isset( $this->data[$key] ) ) unset( $this->data[$key] ); }

	public function __call( $name = '', $args = array() ) { unset( $name, $args ); return null; }

	private function setup_globals(){

		// DEFAULT CONFIGURATION OPTIONS
		$default_options = $this->default_options;

		$saved_options = $this->network_activated ?  get_site_option( 'b_e_a_m_plugin_options' ) : get_option( 'b_e_a_m_plugin_options' );
		$saved_options = maybe_unserialize( $saved_options );

		$this->options = wp_parse_args( $saved_options, $default_options );
	}

	private function setup_actions(){

		// Hook into BuddyPress init
		add_action( 'bp_init', array( $this, 'bp_loaded' ) );
	}

	public function bp_loaded(){
		add_action( 'bp_activity_entry_meta',		array( $this, 'btn_edit_activity' ), 3, 0 );
		add_action( 'bp_activity_comment_options',	array( $this, 'btn_edit_activity_comment' ) );

		if ( ! is_admin() && ! is_network_admin() ){
			add_action( 'wp_enqueue_scripts',	array( $this, 'assets_mp' ) );
			add_action( 'wp_footer',			array( $this, 'print_edit_activity_template' ) );
		}

		add_action( 'wp_ajax_buddypress-edit-activity-mp-get', array( $this, 'ajax_get_activity_content' ) );
		add_action( 'wp_ajax_buddypress-edit-activity-mp-save', array( $this, 'ajax_save_activity_content' ) );

    // add_action( 'beamp_saved_activity', 'mpp_activity_mark_attached_media_for_groups_wall', 1, 4 );
	}

  public function assets_mp() {

		$assets_url = trailingslashit( BUDDYBOSS_EDIT_MP_ACTIVITY_PLUGIN_URL ) . 'assets/';
		//wp_enqueue_script( 'buddyboss-edit-activity', $assets_url . 'js/buddypress-edit-activity.js', array('jquery'), '1.0.5', true );
    wp_enqueue_script( 'alertifyjs', $assets_url . 'js/alertify.js', array(),  '1.0.10', true );
    wp_enqueue_script( 'buddyboss-edit-activity-mediapress', $assets_url . 'js/buddypress-edit-activity-mediapress.js', array('jquery', 'alertifyjs', 'mpp_core'), '1.0', true );
    wp_enqueue_style( 'alertify', $assets_url . 'css/alertify.css');
    wp_enqueue_style( 'buddyboss-edit-activity-mediapress', $assets_url . 'css/buddypress-edit-activity-mediapress.css');

    $data = array(
			'loading_gif'	=> $assets_url . 'images/loading.gif',
			'button_text'		=> array(
				'edit'			=> __( 'Editar', 'buddypress-edit-activity' ),
				'save'			=> __( 'Salvar', 'buddypress-edit-activity' ),
				'cancel'		=> __( 'Cancelar', 'buddypress-edit-activity' ),
			),
		);

		wp_localize_script( 'buddyboss-edit-activity-mediapress', 'BEAM', $data );

    //mppdata to get allowed types message when editing
    $settings = array(
			'enable_activity_lightbox' => mpp_get_option( 'enable_activity_lightbox' ) ? true : false,
			'enable_gallery_lightbox' => mpp_get_option( 'enable_gallery_lightbox' ) ? true : false,
		);
		$active_types = mpp_get_active_types();

		$extensions = $type_erros = array();
		$allowed_type_messages = array();
		foreach( $active_types as $type => $object ) {
			$type_extensions = mpp_get_allowed_file_extensions_as_string( $type, ',' );

			$extensions[$type] = array( 'title'=> sprintf( 'Select %s', ucwords( $type ) ), 'extensions' => $type_extensions );
			$readable_extensions = mpp_get_allowed_file_extensions_as_string( $type, ', ' );
			$type_erros[$type] = sprintf( _x( 'This file type is not allowed. Allowed file types are: %s', 'type error message', 'mediapress' ), $readable_extensions );
			$allowed_type_messages[$type] = sprintf( _x( ' Please only select : %s', 'type error message', 'mediapress' ),  $readable_extensions );
		}

		$settings['types'] = $extensions;
		$settings['type_errors'] = $type_erros;
		$settings['allowed_type_messages'] = $allowed_type_messages;

		if( mpp_is_single_gallery() ) {

			$settings['current_type'] = mpp_get_current_gallery()->type;
		}

		$settings['loader_src'] = mpp_get_asset_url( 'assets/images/loader.gif', 'mpp-loader' );

		$settings = apply_filters( 'mpp_localizable_data', $settings );

		wp_localize_script( 'mpp_core', '_mppData', $settings );
		//_mppData

  }

	public function option( $key ){
		$key    = strtolower( $key );
		$option = isset( $this->options[$key] )
		        ? $this->options[$key]
		        : null;

		$option = apply_filters( 'b_e_a_m_plugin_option', $option );

		// Option specific filter name is converted to lowercase
		$filter_name = sprintf( 'b_e_a_m_plugin_option_%s', strtolower( $key  ) );
		$option = apply_filters( $filter_name,  $option );

		return $option;
	}

	public function print_edit_activity_template(){
		if ( is_user_logged_in() ):
		?>
		<div id="buddypress-edit-activity-wrapper-mp" style="display:none">
      <?php do_action( 'bb_before_print_edit_activity_template' ) ?>
			<form id="frm_buddypress-edit-activity-mp" method="POST" onsubmit="return false;">
				<input type="hidden" name="action_get" value="buddypress-edit-activity-mp-get" >
				<input type="hidden" name="action_save" value="buddypress-edit-activity-mp-save" >
				<input type="hidden" name="buddypress_edit_activity_nonce" value="<?php echo wp_create_nonce( 'buddypress-edit-activity');?>" >
				<input type="hidden" name="activity_id" value="">
				<div class="field ac-textarea">
        <textarea class="bp-suggestions" id="whats-new" cols="50" rows="10" style="height: 50px; margin: 0px; width: 95%; min-height: 20vh;" name="activity_content"></textarea>

        <?php
          // if( bp_is_group() ) {
          //   echo do_shortcode("[mpp-uploader component=\"groups\"]") ;
          // } else if( bp_is_user() ) {
          //   echo do_shortcode("[mpp-uploader component=\"members\"]") ;
          // } else {
          //   echo do_shortcode("[mpp-uploader component=\"sitwide\"]") ;
          // }

          echo do_shortcode("[mpp-uploader]") ;
        ?>

        </div>
			</form>
            <?php do_action( 'bb_after_print_edit_activity_template' ) ?>
		</div>
		<?php
		endif;
	}

	public function btn_edit_activity(){
		if( $this->can_edit_activity() ){
			?>
			<a href="#" class="button bp-secondary-action action-edit buddyboss_edit_activity" onclick="return buddypress_edit_activity_mp_initiate(this);" data-activity_id="<?php bp_activity_id() ;?>">
				<?php _e( 'Editar', 'buddypress-edit-activity' ); ?>
			</a>
			<?php
		}
	}

  /**
	 * Check if current user can edit given activity.
	 *
	 * @global type $activities_template
	 * @param object $activity
	 * @return boolean
	 */
	private function can_edit_activity( $activity=false ){
		if( !is_user_logged_in() )
			return false;

		global $activities_template;

		// Try to use current activity if none was passed
		if ( empty( $activity ) && ! empty( $activities_template->activity ) ) {
			$activity = $activities_template->activity;
		}

		$can_edit = false;
		/**
		 * User must be either an admin or the author of activity himself/herself, to be adle to edit it.
		 */
		if( current_user_can( 'level_10' ) ){
			$can_edit = true;
		} else {
			if( $this->option( 'user_access' )=='author' ){
				if ( isset( $activity->user_id ) && ( (int) $activity->user_id === bp_loggedin_user_id() ) ) {
					$can_edit = true;
				}
			}
		}

		/**
		 * Activity must be of type 'activity_update', 'activity_comment',
		 * whatever is selected in plugin settings.
		 */
		if( $can_edit===true ){
			if( !in_array( $activity->type, $this->option( 'editable_types' ) ) ){
				$can_edit = false;
			}

            /**
             * Do not let edit an activity with an empty content,
             * usually such activity has been added by some 3rd party plugin
             */
            if ( empty( $activity->content ) ) {
                $can_edit = false;
            }
		}

		/**
		 * is a timeout defined and has the current activity passed the timeout?
		 * Timeout is not applicable for admins by default ( unless overridden in settings)
		 */
		if( $can_edit===true && ( !current_user_can( 'level_10' ) || $this->option( 'exclude_admins' ) != 'yes' ) ){
			if( ( $timeout = (int)$this->option( 'editable_timeout' ) ) != 0 ){
				$activity_time = strtotime( $activity->date_recorded );
				$current_time = time();

				$diff = (int) abs( $current_time - $activity_time );
				if( floor( $diff/60 ) >= $timeout ){
					//timeout must be in minutes!
					$can_edit = false;
				}
			}
		}

		return apply_filters( 'b_e_a_can_edit_activity', $can_edit, $activity );
	}

  public function ajax_save_activity_content(){
		// error_log("ajax_save_activity_content :: post -> " . var_export( $_POST, true ) . "\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
        // Turn off display_errors during AJAX events to prevent malformed JSON
        if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
            @ini_set( 'display_errors', 0 );
        }
        $GLOBALS['wpdb']->hide_errors();

		check_ajax_referer( 'buddypress-edit-activity', 'buddypress_edit_activity_nonce' );
		$retval = array(
			'status'	=> false,
			'content'	=> __( 'Error!', 'buddypress-edit-activity' ),
		);

    $retval['media_deleted'] = false;

    if( isset( $_POST['media_to_delete'] ) ) {
      $media_to_delete = $_POST['media_to_delete'];

      $retval['media_deleted'] = true;

      foreach($media_to_delete as $att_id) {
        //force media delete
        wp_delete_attachment($att_id, true);
        //only trash
        // wp_delete_attachment($att_id, false);
        // error_log("ajax_save_activity_content :: deleted media post id $att_id\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
      }

    }

		$activity_id = isset( $_POST['activity_id'] ) ? (int)$_POST['activity_id'] : false;
		if( !$activity_id ){
			// error_log("ajax_save_activity_content :: ret1 " . var_export($retval, true) . " \n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
			die( json_encode( $retval ) );
		}

		$args = array(
			'activity_id'	=> $activity_id,
			'content'		=> isset( $_POST['content'] ) ? $_POST['content'] : '',
		);
		$retval['content'] = $this->save_activity_content( $args );

    ob_start();

    if( bp_has_activities( bp_ajax_querystring( 'activity' ) . "&include=$activity_id") ) :

      while ( bp_activities() ) : bp_the_activity();

        locate_template( array( 'buddypress/activity/entry.php' ), true, false );

        //I want only one
        break;
      endwhile;

    endif;

    $retval['activity_html'] = ob_get_clean();
		$retval['status'] = true;

		die( json_encode( $retval ) );
	}

	private function save_activity_content( $args ){
    // error_log("save_activity_content custom \n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
		$activity = new BP_Activity_Activity( $args['activity_id'] );
		if( !$activity || is_wp_error( $activity ) )
			return false;

		if( !$this->can_edit_activity( $activity ) )
			return false;

    do_action( 'bea_before_save_activity_content', $activity->id );

    // do_action('beamp_saved_activity', $retval['content'] , current_user_id(), $group_id, $activity_id  );
    // if(function_exists('mpp_activity_mark_attached_media')) {
    //   mpp_activity_mark_attached_media( $activity->id );
    // }

    if(function_exists('mpp_activity_update_attached_media_ids') && !empty($_POST['mpp-attached-media']) ) {
      $media_ids = $_POST['mpp-attached-media'];
    	$media_ids = explode( ',', $media_ids ); //make an array

    	$media_ids = array_filter( array_unique( $media_ids ) );

      mpp_activity_update_attached_media_ids( $activity->id, $media_ids );
    }


    $activity->content = apply_filters( 'beam_activity_content',  $args['content'], $activity->id );
		$activity->save();

		$activity_updated_html_content = '';

		if( $activity->type == 'mpp_media_upload' ){
			$content = apply_filters( 'bp_get_activity_content', $activity->content );

			$activity_updated_html_content = apply_filters( 'bp_mpp_media_upload_content', $content );
		}

		return $activity_updated_html_content;
	}

}

endif;
