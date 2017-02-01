<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?>
<?php while( mpp_have_media() ): mpp_the_media(); ?>

	<div class="Tester3 mpp-u <?php mpp_media_class( mpp_get_media_grid_column_class() );?>">
		
		<?php do_action( 'mpp_before_media_item' ); ?>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-top">
				<?php do_action( 'mpp_media_meta_top' );?>
		</div>
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
                    echo '<div class="embedded_code_display"><p>'.$embed_code.'</p></div>';           
                }
                else {
                ?>
		
		<div class='mpp-item-entry mpp-media-entry mpp-photo-entry'>
			<a href="<?php mpp_media_permalink() ;?>" <?php mpp_media_html_attributes( array( 'class' => 'mpp-item-thumbnail mpp-media-thumbnail mpp-photo-thumbnail' ) ); ?>>
				<img src="<?php mpp_media_src( 'thumbnail' ) ;?>" alt="<?php echo esc_attr( mpp_get_media_title() );?> "/>
			</a>
		</div>
                <?php
                }
                ?>		
		<div class="mpp-item-actions mpp-media-actions mpp-photo-actions">
			<?php mpp_media_action_links();?>
		</div>
		
		<div class="mpp-item-meta mpp-media-meta mpp-media-meta-bottom">
				<?php do_action( 'mpp_media_meta' );?>
		</div>	
		
			<?php do_action( 'mpp_after_media_item' ); ?>
	</div>

<?php endwhile; ?>
