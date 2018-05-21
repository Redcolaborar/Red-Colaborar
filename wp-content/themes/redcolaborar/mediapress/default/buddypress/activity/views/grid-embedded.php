<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/***
 * List Photos attched to an activity
 *
 * Media List attached to an activity
 *
 */
$activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_displayable_media_ids( $activity_id ) ) );

if( $mppq->have_media() ):?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-photo-list test-2 rc-embed">

	<?php while( $mppq->have_media() ): $mppq->the_media(); ?>
            <?php
            $embeded_data_id = mpp_get_media_ID() ;
                 $post_type = get_post_type($embeded_data_id);

				 // custom
				  $content_post = get_post($embeded_data_id);
				  $content_post_mime_type = $content_post->post_mime_type;
				 //end
				 // $embeded_data_id;
                  $embed_post_code = get_post_meta( $embeded_data_id, 'embed_post_code',true );
                 if($embed_post_code == 'yes')
                 {  //echo 'embed_post_code=============55555';
                    $content_post = get_post($embeded_data_id);
                    $embed_code_title = $content_post->post_content;
                    $embed_code_content = $content_post->post_content;

                    $embed_code = wp_oembed_get( $embed_code_content, 600);
                    echo '<p>'.$embed_code.'</p>';
                 }
                ?>

	<?php endwhile; ?>
	</div><!-- end of .mpp-activity-media-list -->
<?php endif; ?>
<?php mpp_reset_media_data(); ?>
