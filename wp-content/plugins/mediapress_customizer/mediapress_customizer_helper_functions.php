<?php
function red_legacy_theme_post_update()
{
	$bp = buddypress();

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

	error_log("request = " . var_export($_REQUEST, 1) . "\n", 3, WP_CONTENT_DIR . '/uploads/mpc_debug.log' );

        //Start saving file meta data while uploadin

        $mpp_media_title_arr = $_REQUEST['mpp-media-title'] ;
        if( !empty($mpp_media_title_arr) )
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

        if( !empty( $_REQUEST['mpp_galler_data'] ) && !empty( $_REQUEST['mpp-media-status'] ) )
        {
           $mpp_galler_data = $_REQUEST['mpp_galler_data'];

				 	 error_log("\$mpp_galler_data = $mpp_galler_data\n", 3, WP_CONTENT_DIR . '/uploads/mpc_debug.log' );

           if($mpp_galler_data == 'create_new')
           {
               $gallery_title_text = $_REQUEST['gallery_title_text'];

							 error_log("\$gallery_title_text = $gallery_title_text\n", 3, WP_CONTENT_DIR . '/uploads/mpc_debug.log' );

               if(!empty($gallery_title_text))
               {
                   $component_id = mpp_get_current_component_id();
                   $component = mpp_get_current_component();
                   $status = 'publish';
                   $type = mpp_get_media_type_from_extension( mpp_get_file_extension( $file[ $file_id ]['name'] ) );
                   $description = '' ;

								   $created_gallery_id = mpp_create_gallery( array(
                                'title'         => $gallery_title_text,
                                'description'	=> $description,
                                'type'		=> $type,
                                'status'	=> $status,
                                'creator_id'	=> get_current_user_id(),
                                'component'	=> $component,
                                'component_id'	=> $component_id
                    ) );

										error_log("\$created_gallery_id = $created_gallery_id\n", 3, WP_CONTENT_DIR . '/uploads/mpc_debug.log' );

                    if(!empty($created_gallery_id))
                    {
                        $gallery_id = $created_gallery_id ;
                    }
               }
           }
           else
           {
               $gallery_id = $mpp_galler_data  ;
           }
        }

        $media_id_arr = array();

        if ( $gallery_id ) {
                $gallery = mpp_get_gallery( $gallery_id );
        } else {
                $gallery = false; //not set
        }

        if(isset($_REQUEST['mpp-attached-media']) && $_REQUEST['mpp-attached-media'] != '')
        {
            $mpp_attached_media = $_REQUEST['mpp-attached-media'];
            $media_id_arr = explode(",",$mpp_attached_media);
        }

        //End saving file meta data while uploadin

        $content = !empty($_POST['content']) ? $_POST['content'] : '';

        if (!empty($add_more_content))
        {
            $content .= "\n{$add_more_content}";
            $content = apply_filters('bp_activity_post_update_content', $content);
        }

	// Try to get the item id from posted variables.
	if ( ! empty( $_POST['item_id'] ) ) {
		$item_id = (int) $_POST['item_id'];
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
            $add_more_content = '' ;
            $add_video_link = $_REQUEST['add_video_link'];
            if(count($add_video_link) > 0)
            {

                foreach($add_video_link as $video_embed_key=>$add_video)
                {
                    //$url = !empty($add_video) ? esc_url($add_video) : false;

                      if (filter_var($add_video, FILTER_VALIDATE_URL) === FALSE) {
                      }
                      else {
                            //$add_more_content .= "\n{$add_video}";
                      }

                    $mpp_media_status_video = $_REQUEST['mpp-media-status-video'][$video_embed_key];
                    $mpp_media_title_video = $_REQUEST['mpp-media-title-video'][$video_embed_key];
                    if(isset($_REQUEST['mpp-media-tags-embedded']))
                    {
                     $mpp_media_embeded_tags = $_REQUEST['mpp-media-tags-embedded'] ;
                    }


                    if(!empty($add_video))
                    {
                        if(empty($mpp_media_title_video))
                        {
                            $mpp_media_title_video = rand();
                        }

                        $post_insertion  = array('post_title'=>$mpp_media_title_video,
                            'post_type'=>'attachment',
                            'post_content'=>$add_video,
                            'post_author'=>get_current_user_id(),
                            'post_status' => 'inherit'
                        );

                        $embed__media_id = wp_insert_post($post_insertion);
                         $embed_post = array(
                            'ID'           => $embed__media_id,
                            'post_parent'   => $gallery_id,
                        );
                        wp_update_post( $embed_post );

                        $add_video_for_show_in_gallery = '<div class="custom_embed_code" style="width:200px;height:200px">'.$add_video.'</div>' ;
                        bp_activity_add_meta( $activity_id, '_mpp_attached_media_id', $embed__media_id );

                        add_post_meta($embed__media_id, 'embed_post_code', 'yes');

                        add_post_meta($embed__media_id, '_wp_attached_file', 'mediapress/members/embed_video_default_image.png');
                        //add_post_meta($embed__media_id, '_wp_attached_file', $add_video_for_show_in_gallery);

                        add_post_meta($embed__media_id, '_mpp_context', 'activity');
                        add_post_meta($embed__media_id, '_mpp_component_id', 1);
                        add_post_meta($embed__media_id, '_wp_attachment_metadata', '');
                        add_post_meta($embed__media_id, '_mpp_is_mpp_media', 1);


                        wp_set_object_terms( $embed__media_id, '_members', 'mpp-component');
                        wp_set_object_terms( $embed__media_id, '_public', 'mpp-status');
                        wp_set_object_terms( $embed__media_id, '_photo', 'mpp-type');

                        if(count($mpp_media_embeded_tags) > 0 && !empty($mpp_media_embeded_tags))
                        {
                            wp_set_post_tags( $embed__media_id, $mpp_media_embeded_tags,true );
                        }
                    }
                }
                bp_activity_add_meta( $activity_id, '_mpp_gallery_id', $gallery_id );
            }
        }



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
