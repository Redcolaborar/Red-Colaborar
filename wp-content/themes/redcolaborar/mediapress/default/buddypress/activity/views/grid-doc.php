<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php $activity_id = bp_get_activity_id();

$mppq = new MPP_Cached_Media_Query( array( 'in' => mpp_activity_get_displayable_media_ids( $activity_id ) ) );

if ( $mppq->have_media() ) : ?>
	<div class="mpp-container mpp-media-list mpp-activity-media-list mpp-activity-audio-list mpp-activity-audio-player testt-4 rc-doc">

		<?php
			$i = 1;
			while( $mppq->have_media() ) :
			$mppq->the_media();

			$embeded_data_id = mpp_get_media_ID() ;
			$post_type       = get_post_type( $embeded_data_id );
			$embed_post_code = get_post_meta( $embeded_data_id, 'embed_post_code', true );

			// check for display only audio-content
			$content_post           = get_post( $embeded_data_id );
			$content_post_mime_type = $content_post->post_mime_type;

			$gallery_id = $content_post->post_parent;

			if ( 'yes' == $embed_post_code ) :

				$content_post       = get_post( $embeded_data_id );
				$embed_code_title   = $content_post->post_content;
				$embed_code_content = $content_post->post_content;

				echo wpautop( esc_html( $embed_code_content ) );

			else :

				$pattern = '/application/';

				// var_dump( $content_post->post_title );

				if ( preg_match( $pattern, $content_post_mime_type, $matches ) ) :

					?>
					<div class="mpp-media-holder" data-rc-media-type='doc' data-mpp-gallery-id="<?php echo esc_attr( $gallery_id ); ?>" data-mpp-activity-id="<?php echo esc_attr( $activity_id );?>"  <?php if( (2 < $i) && !bp_is_single_activity() ) echo 'style="display:none"' ?>>
						<div class="mpp-item-content mpp-audio-content mpp-audio-player">
							<?php mpp_media_content(); ?>
						</div>

						<a data-mpp-media-id="<?php echo esc_attr( $embeded_data_idskyps );?>" data-mpp-activity-id="<?php echo esc_attr( $activity_id );?>" aria-hidden="true" class="beam_delete_media_btn" href="javascript:void(0)" title="<?php echo esc_attr( 'Delete Media', 'redcolaborar' ); ?>"><i class="fa fa-trash-o" aria-hidden="true"></i></a>
					</div>

					<?php if( (3 == $i)  && !bp_is_single_activity() ) : ?>
						<a href="javascript:void(0)" class="rc-link-view-more-media" data-media-type='doc' data-activity-id="<?php echo $activity_id;?>">Ver más Docs</a>
					<?php endif; ?>

				<?php

					// just update $i if media was printed
					$i++;
				endif;
			endif;

		endwhile;
	?>

		<script type="text/javascript">
			//mpp_mejs_activate(<?php echo esc_html( bp_get_activity_id() ); ?>);
		</script>

	</div>
<?php endif; ?>
<?php mpp_reset_media_data(); ?>
