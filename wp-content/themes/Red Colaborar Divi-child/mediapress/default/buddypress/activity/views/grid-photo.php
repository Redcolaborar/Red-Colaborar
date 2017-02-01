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
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-photo-list test-2">

	<?php while( $mppq->have_media() ): $mppq->the_media(); ?>
            <?php
            $embeded_data_id = mpp_get_media_ID() ;                 
                 $post_type = get_post_type($embeded_data_id);
				 
				 // custom
				  $content_post = get_post($embeded_data_id);
				  $content_post_mime_type = $content_post->post_mime_type;  
				 //end
                 $embed_post_code = get_post_meta( $embeded_data_id, 'embed_post_code',true );
				// embed video will not display by this template
                 if($embed_post_code == 'yes' &&  1 == 2)
                 {
                    $content_post = get_post($embeded_data_id);
                    $embed_code_title = $content_post->post_content;
                    $embed_code_content = $content_post->post_content;                    
                                     
                    $embed_code = wp_oembed_get( $embed_code_content, 600);
                    echo '<p>'.$embed_code.'</p>';                 
                 }else {
						$pattern = '/image/';
						if(preg_match($pattern, $content_post_mime_type, $matches)){
				 //if($content_post_mime_type == 'image/png' || $content_post_mime_type == 'image/jpeg'|| $content_post_mime_type == 'image/jpg'|| $content_post_mime_type == 'image/gif'){
					 
					 // echo 'image-template==========================';
				
                        ?>
		<a href="<?php mpp_media_permalink();?>" ><img src="<?php mpp_media_src( 'thumbnail' );?>" class='mpp-attached-media-item' data-mpp-activity-id="<?php echo $activity_id;?>" title="<?php echo esc_attr( mpp_get_media_title() );?>" /></a>
               <?php }
               }
                
                ?>

	<?php endwhile; ?>
	</div><!-- end of .mpp-activity-media-list -->
<?php endif; ?>
<?php mpp_reset_media_data(); ?>