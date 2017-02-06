<?php
if (!class_exists('Mediapresscustomizerfront')) {

    class Mediapresscustomizerfront {

        public static $instance;

        public function __construct() {
            add_action('init', array($this, 'init_hooks'));
        }

         public static function create_instance() {
            if (is_null(self::$instance))
                self::$instance = new Mediapresscustomizerfront();
            return self::$instance;
        }

        function init_hooks() {


            add_action('mpp_after_media_item', array($this, 'mpp_after_gallery_entry_callback' ) );
            add_action('wp_head', array($this, 'front_enqueue_scripts'));
            add_action('wp_footer', array($this, 'footer_script_calback'));
            add_action('mpp_after_activity_upload_medialist', array($this, 'red_after_activity_upload_buttons_callback'));

           remove_action( 'bp_after_activity_post_form', 'mpp_activity_upload_buttons' );
           add_action( 'bp_after_activity_post_form', array($this,'red_mpp_activity_upload_buttons' ));
           $this->red_add_tags_to_attachments();
           $this->load_my_transl();

        }


        public function load_my_transl()
        {
            load_plugin_textdomain('my-pluginname', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
        }

        // apply tags to attachments
        function red_add_tags_to_attachments() {
            register_taxonomy_for_object_type( 'post_tag', 'attachment' );
        }

        function mediapress_customizer_plugin_activate() {

            if (! post_type_exists( 'embeded_video' ) ) {
                $args = array(
                    'public' => true,
                  );
                  register_post_type( 'embeded_video', $args );
             }
        }


        function red_after_activity_upload_buttons_callback() {

            $args = array(
                    'orderby'          => 'date',
                    'posts_per_page'   => 10,
                    'order'            => 'DESC',
                    'post_type'        => 'mpp-gallery',
                    'post_parent'      => '',
                    'author'           => get_current_user_id(),
                    'post_status'      => 'publish',
                    'tax_query' => array(
                        array(
                        'taxonomy' => 'mpp-component',
                        'field' => 'slug',
                        'terms' => array('_members', '_groups')
                         )
                      )
            );
            $posts_array = get_posts( $args );

            if(count($posts_array) > 0)
            {
                $html = "";

                $html .='<div class="assign_gallery_container" style="display: none;"><div class="assign_laggery_label">'.__("Assign Gallery", 'mediapress_customizer').'</div><div class="galley_selection"><select name="mpp_galler_data" id="mpp_gallery_assignment" onchange="getSelectedGallery(this);">';
                foreach($posts_array as $post_data)
                {
                    $html .= "<option value='{$post_data->ID}'>{$post_data->post_title}</option>";
                }
                $html .= '<option value="create_new">'.__("Add New Gallery", 'mediapress_customizer').'</option>';
                $html .= '</select></div></div>';
                echo $html ;
            }


        }

        function pluginprefix_deactivation() {

            // Our post type will be automatically removed, so no need to unregister it

            // Clear the permalinks to remove our post type's rules
            flush_rewrite_rules();

        }

        function footer_script_calback() {

            $media = mpp_get_media();
            $media_id = $media->id;
            $component = $media->component ;

            $html_post_tags = $html = '' ;

            $statuses = mpp_get_editable_statuses( 'active', $component );
            $selected = $is_new ? mpp_get_default_status() : $gallery->status;
                ?>
                <div class="get_status_data" style="display:none;">
                    <?php
                        foreach ( $statuses as $key => $status ) {
                                $html .= "<option value='{$key}'" . selected( $selected, $key, false ) . " >{$status->label}</option>";
                        }
                        echo $html ;
                    ?>
                </div>


                <div id="post_tags_for_media" style="display:none;">
                        <?php
                        $tags = get_tags(array('hide_empty'=>false));
                        foreach ( $tags as $tag ) {
                                $html_post_tags .= '<option value='.$tag->slug.'>'.$tag->name.'</option>';
                        }
                        echo $html_post_tags;
                        ?>
                </div>
                <input type="hidden" id="check_wather_customizer_enabled" value="yes">
                <?php

        }

        function front_enqueue_scripts() {
            wp_enqueue_style('mediapress_customizer_custom_front_style', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . 'css/mediapress_customizer_custom_front.css');
            wp_enqueue_style('mediapress_customizer_chosen_style', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . 'css/chosen.css');
            wp_enqueue_script('custom-mediapress-chosen-js', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . 'js/chosen.js', array('jquery'));
            wp_enqueue_script('custom-mediapress-customizer-js', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . 'js/custom_font.js', array('jquery'));
            wp_enqueue_script('mpp-customizer-js', MEDIAPRESS_CUSTOMIZER_PLUGIN_URL . 'js/mp_custom.js', array('jquery'));

        }

        function mpp_after_gallery_entry_callback()
        {
            ?>
            <div class="title_container">
               <a href="<?php mpp_media_permalink() ;?>" <?php mpp_media_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-media-thumbnail mpp-photo-thumbnail' ) ); ?>>
                    <?php echo esc_attr( mpp_get_media_title() ); ?>
               </a>
            </div>
            <div class="description_container">
                    <?php
                        $string = mpp_get_media_description() ;
                        $trimmed_description = (strlen($string) > 60) ? substr($string,0,60).'...' : $string;
                        echo $trimmed_description ;
                    ?>
            </div>
            <?php
        }


        function red_mpp_activity_upload_buttons()
        {

            $mediapress_customizer_options = get_option( 'mediapress_customizer_options' );
            $enable_embed_url = $mediapress_customizer_options['enable_embed_url'];
            $component = mpp_get_current_component();
            if ( ! mpp_is_activity_upload_enabled( $component ) ) {
                    return;
            }

            //if we are here, the gallery activity stream upload is enabled,
            //let us see if we are on user profile and gallery is enabled
            if ( ! mpp_is_enabled( $component, mpp_get_current_component_id() ) ) {
                    return;
            }
            //if we are on group page and either the group component is not enabled or gallery is not enabled for current group, do not show the icons
            if ( function_exists( 'bp_is_group' ) && bp_is_group() && ( ! mpp_is_active_component( 'groups' ) || ! ( function_exists( 'mpp_group_is_gallery_enabled' ) && mpp_group_is_gallery_enabled() ) ) ) {
                    return;
            }
            //for now, avoid showing it on single gallery/media activity stream
            if ( mpp_is_single_gallery() || mpp_is_single_media() ) {
                    return;
            }

            ?>
            <div id="mpp-activity-upload-buttons" class="mpp-upload-buttons">
            <?php do_action( "mpp_before_activity_upload_buttons" ); //allow to add more type  ?>

                    <?php if ( mpp_is_active_type( 'photo' ) && mpp_component_supports_type( $component, 'photo' ) ): ?>
                            <a href="#" id="mpp-photo-upload" data-media-type="photo"><img title="<?php echo __("Upload Photos", 'mediapress_customizer')?>" src="<?php echo mpp_get_asset_url( 'assets/images/media-button-image.gif', 'media-photo-icon' ) ; ?>"/></a>
                    <?php endif; ?>

                    <?php if ( mpp_is_active_type( 'audio' ) && mpp_component_supports_type( $component, 'audio' ) ): ?>
                            <a href="#" id="mpp-audio-upload" data-media-type="audio"><img title="<?php echo __("Upload Audios", 'mediapress_customizer')?>" src="<?php echo mpp_get_asset_url( 'assets/images/media-button-music.gif', 'media-audio-icon' ); ?>"/></a>
                    <?php endif; ?>

                    <?php if ( mpp_is_active_type( 'video' ) && mpp_component_supports_type( $component, 'video' ) ): ?>
                            <a href="#" id="mpp-video-upload"  data-media-type="video"><img title="<?php echo __("Upload Videos", 'mediapress_customizer')?>" src="<?php echo mpp_get_asset_url( 'assets/images/media-button-video.gif', 'media-video-icon' ) ?>"/></a>
                    <?php endif; ?>
                    <?php
                    if($enable_embed_url == 'yes')
                    {
                    ?>
                            <a href="javascript:void(0);" onclick="mpp-activity-upload-button();" id="mpp-video-upload"  data-media-type="embed_video"><img title="<?php echo __("Add Embedded Codes", 'mediapress_customizer')?>" width="13" height="13" src="<?php echo MEDIAPRESS_CUSTOMIZER_PLUGIN_URL.'images/Basic-Code-icon.png' ?>"/></a>
                    <?php
                    }
                    ?>
                    <?php if ( mpp_is_active_type( 'doc' ) && mpp_component_supports_type( $component, 'doc' ) ): ?>
                            <a href="#" id="mpp-doc-upload"  data-media-type="doc"><img title="<?php echo __("Upload Docs", 'mediapress_customizer')?>" src="<?php echo mpp_get_asset_url( 'assets/images/media-button-doc.gif', 'media-doc-icon' ); ?>" /></a>
                    <?php endif; ?>
                    <?php //someone please provide me doc icon and some better icons  ?>
                    <?php do_action( 'mpp_after_activity_upload_buttons' ); //allow to add more type  ?>
            </div>
                    <?php
        }



    }

    Mediapresscustomizerfront::create_instance();
}
