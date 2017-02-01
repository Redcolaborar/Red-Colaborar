<?php
// Exit if the file is accessed directly over web
//fallback view for activity media grid
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
/*** 
 * 
 * Media List attached to an activity
 * This is a fallback template for new media types
 * 
 */
$activity_id = bp_get_activity_id();
//_mpp_attached_media_id_embed

$data = bp_activity_get_meta( $activity_id, '_mpp_attached_media_id', true );
$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_displayable_media_ids( $activity_id ) ) );
//echo '<pre>';print_r($mppq);echo '<pre>';		
if( $mppq->have_media() ):?>

<?php
    //$post_type = $mppq->post_type;

    $data_posts = $mppq->posts ;
    //echo '<pre>';print_r($data_posts['post_type']);echo '<pre>';
    //var_dump($data_posts['post_type']);
    $embed_code_content = '';
?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list">

		<?php while( $mppq->have_media() ): $mppq->the_media(); ?>
                <?php
                 $embeded_data_id = mpp_get_media_ID() ;                 
                 $post_type = get_post_type($embeded_data_id);
                    $embed_post_code = get_post_meta( $embeded_data_id, 'embed_post_code',true );                 
                 if($embed_post_code == 'yes')
                 {
                    $content_post = get_post($embeded_data_id);
                    $embed_code_title = $content_post->post_content;
                    $embed_code_content = $content_post->post_content;                    
                    $embed_code = wp_oembed_get( $embed_code_content, 600);
                    echo '<p>'.$embed_code.'</p>';                     
                 }
                 else {
                     ?>
                            <a href="<?php mpp_media_permalink();?>" ><img src="<?php mpp_media_src( 'thumbnail' );?>" class='mpp-attached-media-item' data-mpp-activity-id="<?php echo $activity_id;?>" title="<?php echo esc_attr( mpp_get_media_title() );?>" /></a>
                     <?php
                     
                 }
                ?>

		<?php endwhile; ?>
	</div>
<?php endif; ?>
<?php mpp_reset_media_data();


//wp_reset_query();



?>