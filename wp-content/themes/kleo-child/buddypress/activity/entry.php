<?php

/**
 * BuddyPress - Activity Stream (Single Item)
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php do_action( 'bp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">
	<div class="activity-avatar rounded">
		<a href="<?php bp_activity_user_link(); ?>">

			<?php bp_activity_avatar(); ?>

		</a>

	</div>

	<div class="activity-content">

		<div class="activity-header">

			<?php bp_activity_action(); ?>

		</div>

		<?php if ( bp_activity_has_content() ) : ?>

			<div class="activity-inner">

				<?php

				global $activities_template;
				if ( empty( $activities_template->activity->action ) && ! empty( $activities_template->activity->content ) ) {
			        $activities_template->activity->content = bp_insert_activity_meta( $activities_template->activity->content );
			    }
			    //echo apply_filters_ref_array( 'bp_get_activity_content_body', array( $activities_template->activity->content, &$activities_template->activity ) );

			    $post_id = $activities_template->activity->secondary_item_id;

			    if ($post_id) {

				    $tpost = get_post($post_id);
				    echo get_the_post_thumbnail($tpost->ID, 'post-thumbnail', array('class' => 'redcolaborar-activity-thumbnail'));
				    echo get_excerpt_by_id( $tpost->ID );

				    if ($tpost->post_type == 'post') {
					    echo '<div class="redcolaborar-tags-container">';
						    echo '<div class="redcolaborar-tags">';
						    echo __( 'Categorias').': '.get_the_category_list(', ', '', $tpost->ID);
						    echo '</div>';

						    echo '<div class="redcolaborar-tags">';
						    echo __('Etiquetas').': '.get_the_tag_list( '',', ' , '', $tpost->ID );
						    echo '</div>';
						echo '</div>';
					}
					if ($tpost->post_type == 'question') {
						echo '<div class="redcolaborar-tags-container">';
						    echo '<div class="redcolaborar-tags">';
						    echo __( 'Categorias').': '.get_the_term_list($tpost->ID, 'question_category', '', ', ', '');
						    echo '</div>';

						    echo '<div class="redcolaborar-tags">';
						    echo __('Etiquetas').': '.get_the_term_list($tpost->ID, 'question_tag', '', ', ', '');
						    echo '</div>';
						echo '</div>';
					}

					echo '<span class="kleo-love">';
    			    do_action('kleo_show_love');
	                echo '</span>';

				}
				else {
					bp_activity_content_body();
				}

				?>

			</div>

		<?php endif; ?>

		<?php do_action( 'bp_activity_entry_content' ); ?>

		<div class="activity-meta">

			<?php if ( bp_get_activity_type() == 'activity_comment' ) : ?>

				<a href="<?php bp_activity_thread_permalink(); ?>" class="button view bp-secondary-action" title="<?php esc_attr_e( 'View Conversation', 'buddypress' ); ?>"><?php _e( 'View Conversation', 'buddypress' ); ?></a>

			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php if ( bp_activity_can_comment() ) : ?>

					<a href="<?php bp_activity_comment_link(); ?>" class="button acomment-reply bp-primary-action" id="acomment-comment-<?php bp_activity_id(); ?>"><?php printf( __( 'Comment %s', 'buddypress' ), '<span>' . bp_activity_get_comment_count() . '</span>' ); ?></a>

				<?php endif; ?>

				<?php if ( bp_activity_can_favorite() ) : ?>

					<?php if ( !bp_get_activity_is_favorite() ) : ?>

						<a href="<?php bp_activity_favorite_link(); ?>" class="button fav bp-secondary-action" title="<?php esc_attr_e( 'Mark as Favorite', 'buddypress' ); ?>"><?php //_e( 'Favorite', 'buddypress' ); ?></a>

					<?php else : ?>

						<a href="<?php bp_activity_unfavorite_link(); ?>" class="button unfav bp-secondary-action" title="<?php esc_attr_e( 'Remove Favorite', 'buddypress' ); ?>"><?php //_e( 'Remove Favorite', 'buddypress' ); ?></a>

					<?php endif; ?>

				<?php endif; ?>

				<?php if ( bp_activity_user_can_delete() ) bp_activity_delete_link(); ?>

			<?php endif; ?>

			<?php do_action( 'bp_activity_entry_meta' ); ?>

		</div>

	</div>

	<?php do_action( 'bp_before_activity_entry_comments' ); ?>

	<?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>

		<div class="activity-comments">

			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>

				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-avatar rounded"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'buddypress' ); ?>" /> &nbsp; <a href="#" class="ac-reply-cancel"><?php _e( 'Cancel', 'buddypress' ); ?></a>
						<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
					</div>

					<?php do_action( 'bp_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>

		</div>

	<?php endif; ?>

	<?php do_action( 'bp_after_activity_entry_comments' ); ?>
<div class="activity-timeline"></div>
</li>

<?php do_action( 'bp_after_activity_entry' ); ?>
