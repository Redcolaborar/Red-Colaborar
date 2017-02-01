<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php $activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_displayable_media_ids( $activity_id ) ) );

if( $mppq->have_media() ):?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-audio-list mpp-activity-audio-player">

		<?php while( $mppq->have_media() ): $mppq->the_media(); ?>
                <?php
                     $embeded_data_id = mpp_get_media_ID() ;                 
                     $post_type = get_post_type($embeded_data_id);
					 
					 // check for display only audio-content
						$content_post = get_post($embeded_data_id);
						 $content_post_mime_type = $content_post->post_mime_type;  
					
				 
                     $embed_post_code = get_post_meta( $embeded_data_id, 'embed_post_code',true );                 
                     if($embed_post_code == 'yes' &&  1 == 2)
                     {
                        $content_post = get_post($embeded_data_id);
                        $embed_code_title = $content_post->post_content;
                        $embed_code_content = $content_post->post_content;                    
                        echo '<p>'.$embed_code_content.'</p>';                     
                     }
                     else {
						// echo $content_post_mime_type;
						 $pattern = '/audio/';
						if(preg_match($pattern, $content_post_mime_type, $matches)){
						 // if($content_post_mime_type == 'audio/mpeg' || $content_post_mime_type == 'audio/midi'
						  //|| $content_post_mime_type == 'audio/x-mid' || $content_post_mime_type == 'audio/x-midi' || $content_post_mime_type == 'audio/wav' || $content_post_mime_type == 'audio/wav' || $content_post_mime_type == 'audio/x-wav'){
						 ?>
				
			<div class="mpp-item-content mpp-audio-content mpp-audio-player">
				<?php mpp_media_content() ;?>
			</div>
                    <?php

                     }
                     }
                    ?>
		<?php endwhile; ?>
		<script type='text/javascript'>
		console.log(<?php echo bp_get_activity_id();?>);
		currentActId = "<?php echo bp_get_activity_id();?>";
		//alert(<?php echo bp_get_activity_id();?>);
			mpp_mejs_activate(<?php echo bp_get_activity_id();?>);
		</script>
	</div>
<?php endif; ?>
<?php mpp_reset_media_data(); ?>