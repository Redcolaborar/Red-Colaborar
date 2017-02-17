<?php
function red_legacy_theme_post_update()
{
	$bp = buddypress();
        global $wpdb ;
	// Bail if not a POST action.
	if ( 'POST' !== strtoupper( $_SERVER['REQUEST_METHOD'] ) )
		return;

	// Check the nonce.
	check_admin_referer( 'post_update', '_wpnonce_post_update' );

	if ( ! is_user_logged_in() )
		exit( '-1' );

	if ( empty( $_POST['content'] ) )
		exit( '-1<div id="message" class="error bp-ajax-message"><p>' . __( 'Please enter some content to post.', 'buddypress' ) . '</p></div>' );

	$activity_id = 0;
	$item_id     = 0;
	$object      = '';

        //Start saving file meta data while uploadin

        $mpp_media_title_arr = $_REQUEST['mpp-media-title'] ;
        if(count($mpp_media_title_arr) > 0 )
        {
            foreach($mpp_media_title_arr as $key=>$value)
            {
                $mpp_media_status = $_REQUEST['mpp-media-status'][$key] ;
                $mpp_media_associated_tags = $_REQUEST['mpp-media-associated-tags'][$key] ;

                if(!empty($value))
                {
                    $media_id = mpp_update_media( array(
                    'title'			=> $value,
                    'status'                => $mpp_media_status,
                    'id'			=> $key,
                    ));

                    if(count($mpp_media_associated_tags) > 0 && !empty($mpp_media_associated_tags))
                    {
                        wp_set_post_tags( $media_id, $mpp_media_associated_tags,true );
                    }
                }
            }
        }

        $component_id = mpp_get_current_component_id();
        $component = mpp_get_current_component();

        $media_id_arr = array();
        if(isset($_REQUEST['mpp-attached-media']) && $_REQUEST['mpp-attached-media'] != '')
        {
            $mpp_attached_media = $_REQUEST['mpp-attached-media'];
            $media_id_arr = explode(",",$mpp_attached_media);
        }


       if(isset($_REQUEST['add_video_link']))
        {
            $add_video_link = $_REQUEST['add_video_link'];
            if(count($add_video_link) > 0)
            {
                foreach($add_video_link as $video_embed_key=>$add_video)
                {
                      $data_validation = validateIframe($add_video) ;
                      if (empty($data_validation)) {
                          exit( '-1<div id="message" class="error bp-ajax-message"><p>' . __( 'Please enter valid embedded code!', 'mediapress-customizer' ) . '</p></div>' );
                      }
                }
            }
        }

        //End saving file meta data while uploadin


        $content = !empty($_POST['content']) ? $_POST['content'] : '';


	// Try to get the item id from posted variables.
        $mpp_component_tems = '_members';
	if ( ! empty( $_POST['item_id'] ) ) {
		$item_id = (int) $_POST['item_id'];

                $component = 'groups';
                $component_id = $item_id;
                $mpp_component_tems = '_groups';
	}


	// Try to get the object from posted variables.
	if ( ! empty( $_POST['object'] ) ) {
		$object  = sanitize_key( $_POST['object'] );

	// If the object is not set and we're in a group, set the item id and the object
	} elseif ( bp_is_group() ) {
		$item_id = bp_get_current_group_id();
		$object = 'groups';
	}

	if ( ! $object && bp_is_active( 'activity' ) ) {
		$activity_id = bp_activity_post_update( array( 'content' => $content, 'error_type' => 'wp_error' ) );

	} elseif ( 'groups' === $object ) {
		if ( $item_id && bp_is_active( 'groups' ) )
			$activity_id = groups_post_update( array( 'content' => $content, 'group_id' => $item_id, 'error_type' => 'wp_error' ) );

	} else {

		/** This filter is documented in bp-activity/bp-activity-actions.php */
		$activity_id = apply_filters( 'bp_activity_custom_update', false, $object, $item_id, $content );
	}

	if ( false === $activity_id ) {
		exit( '-1<div id="message" class="error bp-ajax-message"><p>' . __( 'There was a problem posting your update. Please try again.', 'buddypress' ) . '</p></div>' );
	} elseif ( is_wp_error( $activity_id ) && $activity_id->get_error_code() ) {
		exit( '-1<div id="message" class="error bp-ajax-message"><p>' . $activity_id->get_error_message() . '</p></div>' );
	}

	$last_recorded = ! empty( $_POST['since'] ) ? date( 'Y-m-d H:i:s', intval( $_POST['since'] ) ) : 0;
	if ( $last_recorded ) {
		$activity_args = array( 'since' => $last_recorded );
		$bp->activity->last_recorded = $last_recorded;
		add_filter( 'bp_get_activity_css_class', 'bp_activity_newest_class', 10, 1 );
	} else {
		$activity_args = array( 'include' => $activity_id );
	}

        $gallery_id = mpp_activity_get_gallery_id($activity_id);

				$gallery_title_text_first_time = $_REQUEST['gallery_title_text_first_time'];

        $gallery_title_text = empty($gallery_title_text_first_time) ? $_REQUEST['gallery_title_text'] : $gallery_title_text_first_time;

        // if(!empty($gallery_id))
        // {
        //     if(!empty($gallery_title_text_first_time))
        //     {
        //         $update_existing_post = array(
        //           'ID'           => $gallery_id,
        //           'post_title'   => $gallery_title_text_first_time,
        //       );
        //       wp_update_post( $update_existing_post );
				//
        //     }
        // }

        if ( $gallery_id ) {
                $gallery = mpp_get_gallery( $gallery_id );
        } else {
                $gallery = false; //not set
        }

				// error_log("create_gallery request " . var_export($_REQUEST, 1) . "\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
				// error_log("create_gallery gallery_id " . var_export($gallery_id, 1) . "\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
				// error_log("create_gallery gallery_title_text " . var_export($gallery_title_text, 1) . "\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );
				// error_log("create_gallery gallery_title_text_first_time " . var_export($gallery_title_text_first_time, 1) . "\n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );

        if( isset($_REQUEST['mpp_galler_data']) || !empty($gallery_title_text_first_time) )
        {

					// error_log("create_gallery Entrei \n", 3, WP_CONTENT_DIR . '/uploads/bea_debug.log' );

           $mpp_galler_data = $_REQUEST['mpp_galler_data'];
           if($mpp_galler_data == 'create_new'  || !empty($gallery_title_text_first_time) )
           {
               if(!empty($gallery_title_text))
               {
                   $status = 'publish';
                   $type = $gallery->type ;
                   $description = '' ;


                   if($gallery->title == 'Wall video Gallery' || $gallery->title == 'Wall photo Gallery' || $gallery->title == 'Wall audio Gallery'
                           || $gallery->title == 'Wall doc Gallery'
                   )
                   {

                         $update_existing_post = array(
                            'ID'           => $gallery_id,
                            'post_title'   => $gallery_title_text,
                        );
                        wp_update_post( $update_existing_post );
                   }
                   else {
                            $created_gallery_id = mpp_create_gallery( array(
                                      'title'         => $gallery_title_text,
                                      'description'	=> $description,
                                      'type'		=> $type,
                                      'status'	=> $status,
                                      'creator_id'	=> get_current_user_id(),
                                      'component'	=> $component,
                                      'component_id'	=> $component_id
                          ) );
                         if(!empty($created_gallery_id))
                         {
                              $gallery_id = $created_gallery_id ;
                         }
                   }

               }
           }
           else {
                    $gallery_id =   $mpp_galler_data ;
           }
        }

        if ( $gallery_id ) {
                $gallery = mpp_get_gallery( $gallery_id );
        } else {
                $gallery = false; //not set
        }

        if(count($media_id_arr) > 0)
        {
            foreach($media_id_arr as $media_id)
            {
                 $my_post = array(
                    'ID'           => $media_id,
                    'post_parent'   => $gallery_id,
                );
                wp_update_post( $my_post );
            }
        }

        if(isset($_REQUEST['add_video_link']))
        {
            $add_video_link = $_REQUEST['add_video_link'];
            if(count($add_video_link) > 0)
            {
                if ( ! $gallery ) {
                    $gallery_title_text_first_time = $_REQUEST['gallery_title_text_first_time'];
                    $gallery_title_text = 'Wall Embedded Code Gallery';
                    if(!empty($gallery_title_text_first_time))
                    {
                        $gallery_title_text = $gallery_title_text_first_time ;
                    }
                    $status = 'publish';
                    $type = 'embedded'; // custom embedded type
                    $description = '' ;
                    $gallery_id = mpp_create_gallery( array(
                                'title'            => $gallery_title_text,
                                'description'	=> $description,
                                'type'		=> $type,
                                'status'           => $status,
                                'creator_id'	=> get_current_user_id(),
                                'component'	=> $component,
                                'component_id'	=> $component_id
                    ) );
		}
                foreach($add_video_link as $video_embed_key=>$add_video)
                {
                    preg_match('/src="([^"]+)"/', stripslashes($add_video), $match);
                    if(count($match) > 0 && !empty($match))
                    {
                        $add_video_embed_code = $match[1];
                    }
                    else {
                        $add_video_embed_code = $add_video ;
                    }

                    $mpp_media_status_video = $_REQUEST['mpp-media-status-video'][$video_embed_key];
                    $mpp_media_title_video = $_REQUEST['mpp-media-title-video'][$video_embed_key];
                    if(isset($_REQUEST['mpp-media-tags-embedded']))
                    {
                        $mpp_media_embeded_tag_comma_seprated = $_REQUEST['mpp-media-tags-embedded'][$video_embed_key] ;
                        $mpp_media_embeded_tag_comma_seprated ;
                        $mpp_media_embeded_tags_data = explode(",",$mpp_media_embeded_tag_comma_seprated);
                    }

                    if(!empty($add_video_embed_code))
                    {
                        if(empty($mpp_media_title_video))
                        {
                            $mpp_media_title_video = rand();
                        }


                        $post_insertion  = array('post_title'=>$mpp_media_title_video,
                            'post_type'=>'attachment',
                            'post_content'=> $add_video_embed_code,
                            'post_author'=> get_current_user_id(),
                            'post_status' => 'inherit',
							'post_mime_type' => 'embedded' // custom embedded type
                        );
                        $embed__media_id = wp_insert_post($post_insertion);

//                        $wpdb->query(
//                            "INSERT INTO $wpdb->posts (post_title, post_type, post_content,post_author,post_status) VALUES ( %s, %s,%s,%d, %s )",
//                            array(
//                                $mpp_media_title_video,
//                                'attachment',
//                                stripslashes($add_video_embed_code),
//                                get_current_user_id(),
//                                'inherit'
//                            )
//                        );



//                        $wpdb->query("INSERT INTO {$wpdb->posts} (post_title, post_type, post_content,post_author,post_status) VALUES ('".$mpp_media_title_video."','attachment','".stripslashes($add_video_embed_code)."',".get_current_user_id().",'inherit' );");
//                        $embed__media_id = $wpdb->insert_id;
                        $embed_post = array(
                            'ID'           => $embed__media_id,
                            'post_parent'   => $gallery_id,
                        );
                        wp_update_post( $embed_post );

                        $add_video_for_show_in_gallery = '<div class="custom_embed_code" style="width:200px;height:200px">'.$add_video_embed_code.'</div>' ;
                        bp_activity_add_meta( $activity_id, '_mpp_attached_media_id', $embed__media_id );

                        add_post_meta($embed__media_id, 'embed_post_code', 'yes');

                        add_post_meta($embed__media_id, '_wp_attached_file', 'mediapress/members/embed_video_default_image.png');
                        //add_post_meta($embed__media_id, '_wp_attached_file', $add_video_for_show_in_gallery);

                        add_post_meta($embed__media_id, '_mpp_context', 'activity');
                        add_post_meta($embed__media_id, '_mpp_component_id', $component_id);
                        add_post_meta($embed__media_id, '_wp_attachment_metadata', '');
                        add_post_meta($embed__media_id, '_mpp_is_mpp_media', 1);

                        wp_set_object_terms( $embed__media_id, $mpp_component_tems, 'mpp-component');
                        wp_set_object_terms( $embed__media_id, '_public', 'mpp-status');
                        wp_set_object_terms( $embed__media_id, '_embedded', 'mpp-type'); // custom embedded type

                        if(count($mpp_media_embeded_tags_data) > 0 && !empty($mpp_media_embeded_tags_data))
                        {
                            wp_set_post_tags( $embed__media_id, $mpp_media_embeded_tags_data,true );
                        }
                    }
                }
                bp_activity_add_meta( $activity_id, '_mpp_gallery_id', $gallery_id );
            }
        }
        if ( $gallery_id ) {
                $gallery = mpp_get_gallery( $gallery_id );
        } else {
                $gallery = false; //not set
        }

        $group_id_of_gallery = get_post_meta( $gallery_id, 'gallery_group_id', true );


        if(empty($group_id_of_gallery))
        {
            $group_id_of_gallery = $item_id ;
            if(empty($item_id))
            {
                if(isset($_REQUEST['red_hdn_item_id']))
                {
                  $group_id_of_gallery  = $_REQUEST['red_hdn_item_id'] ;
                }
            }
        }

        update_post_meta($gallery_id, 'gallery_group_id', $group_id_of_gallery);
        updateActivityStreamTable($group_id_of_gallery,$activity_id);


	if ( bp_has_activities ( $activity_args ) ) {
		while ( bp_activities() ) {
			bp_the_activity();
			bp_get_template_part( 'activity/entry' );
		}
	}

	if ( ! empty( $last_recorded ) ) {
		remove_filter( 'bp_get_activity_css_class', 'bp_activity_newest_class', 10, 1 );
	}

	exit;
}


function validateIframe($embed_code)
{
    $return_status = false ;

    $valid_url_arr = array('www.player.vimeo.com','vimeo.com','vimeo.com/channels','player.vimeo.com','www.vimeo.com'
        ,'www.vimeo.com/channels','www.youtube.com','youtube.com','youtu.be','www.youtu.be'
    );

    preg_match('/src="([^"]+)"/', stripslashes($embed_code), $match);
    if(trim($embed_code)==''?TRUE:FALSE)
    {
            $return_status = false ;
    }
    elseif(count($match) > 0 && !empty($match))
    {
        $vide_embed_url = $match[1];
        $url = parse_url($vide_embed_url);
        if (in_array($url['host'], $valid_url_arr)) {
            $return_status = true ;
        }
    }
    else
    {
        $url_data = parse_url($embed_code);
        if (in_array($url_data['host'], $valid_url_arr)) {
            $return_status = true ;
        }
    }
    return $return_status;
}

function getGroupIdByGalleryId($gallery_id)
{
    global $wpdb;
    $gallery = mpp_get_gallery( $gallery_id );
    $type = $gallery->type ;
    $where_meta_key = '' ;
    $group_id_data = '' ;
     if($type == 'audio' )
    {
         $where_meta_key = " AND meta_key = '_mpp_wall_audio_gallery_id'" ;
    }
    elseif($type == 'video' )
    {
        $where_meta_key = " AND meta_key = '_mpp_wall_video_gallery_id'" ;
    }
    elseif($type == 'doc' )
    {
        $where_meta_key = " AND meta_key = '_mpp_wall_doc_gallery_id'" ;
    }
    else
    {
        $where_meta_key = " AND meta_key = '_mpp_wall_photo_gallery_id'" ;
    }


    $table = $wpdb->prefix . 'bp_groups_groupmeta';
    $results = $wpdb->get_results( "SELECT * FROM $table WHERE meta_value = $gallery_id $where_meta_key ", OBJECT );



    if(count($results) > 0)
    {
        foreach($results as $res)
        {
            $group_id_data = $res->group_id ;
        }
    }
    // var_dump($group_id_data);
    return $group_id_data ;
}

function updateActivityStreamTable($group_id,$activity_id)
{
    global $wpdb;
    $table = $wpdb->prefix . 'bp_activity';
    $wpdb->query(
	"
	UPDATE $table
	SET component = 'groups',
        item_id = $group_id
	WHERE id = $activity_id
	"
    );
}
