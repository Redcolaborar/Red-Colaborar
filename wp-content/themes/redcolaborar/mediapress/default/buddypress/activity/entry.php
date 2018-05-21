<?php
// Exit if the file is accessed directly over web
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * MediaPress - Activity Stream (Single Item like media, gallery or inside the lightbox)
 *
 */

?>

<?php do_action( 'mpp_before_activity_entry' ); ?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">

	<div class="mpp-activity-avatar">
		<a href="<?php bp_activity_user_link(); ?>">
			<?php bp_activity_avatar(); ?>
		</a>
	</div>

	<div class="mpp-activity-content">

		<div class="mpp-activity-header">
			<?php bp_activity_action(); ?>
		</div>

		<?php if ( bp_activity_has_content() ) : ?>
			<div class="mpp-activity-inner">
				<?php bp_activity_content_body(); ?>
			</div>
		<?php endif; ?>

		<?php

		/**
		 * Fires after the display of an activity entry content.
		 *
		 *
		 */
		do_action( 'mpp_activity_entry_content' ); ?>

		<div class="mpp-activity-meta">

			<?php if ( bp_get_activity_type() == 'activity_comment' ) : ?>
				<a href="<?php bp_activity_thread_permalink(); ?>" class="button view mpp-bp-secondary-action"
				   title="<?php esc_attr_e( 'View Conversation', 'redcolaborar' ); ?>"><?php _e( 'View Conversation', 'redcolaborar' ); ?></a>
			<?php endif; ?>

			<?php if ( is_user_logged_in() ) : ?>

				<?php if ( bp_activity_can_comment() ) : ?>
					<a href="<?php bp_activity_comment_link(); ?>" class="mpp-acomment-reply mpp-bp-primary-action" id="mpp-acomment-comment-<?php bp_activity_id(); ?>">
						<?php printf( __( 'Reply <span>%s</span>', 'redcolaborar' ), bp_activity_get_comment_count() ); ?>
					</a>
				<?php endif; ?>

				<?php if ( bp_activity_can_favorite() ) : ?>
					<?php if ( ! bp_get_activity_is_favorite() ) : ?>
						<a href="<?php bp_activity_favorite_link(); ?>" class="fav mpp-bp-secondary-action" title="<?php esc_attr_e( 'Bookmark', 'redcolaborar' ); ?>"><?php _e( 'Bookmark', 'redcolaborar' ); ?></a>
					<?php else : ?>
						<a href="<?php bp_activity_unfavorite_link(); ?>" class="unfav mpp-bp-secondary-action" title="<?php esc_attr_e( 'Remove Bookmark', 'redcolaborar' ); ?>"><?php _e( 'Remove Bookmark', 'redcolaborar' ); ?></a>
					<?php endif; ?>

				<?php endif; ?>

				<?php
					if ( bp_activity_user_can_delete() ) {
						bp_activity_delete_link();
					}
				?>

				<?php do_action( 'mpp_activity_entry_meta' ); ?>
			<?php endif; ?>

		</div>

	</div>

	<?php

	do_action( 'mpp_before_activity_entry_comments' ); ?>

	<?php if ( ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>

		<div class="mpp-activity-comments">

			<?php mpp_activity_comments(); ?>

			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>

				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="mpp-ac-form-<?php bp_activity_id(); ?>" class="mpp-ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
					<div class="mpp-ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="mpp-ac-reply-content">
						<div class="mpp-ac-textarea">
							<label for="mpp-ac-input-<?php bp_activity_id(); ?>" class="screen-reader-text"><?php _e( 'Comment', 'redcolaborar' ); ?></label>
							<textarea id="mpp-ac-input-<?php bp_activity_id(); ?>" class="mpp-ac-input bp-suggestions" name="mpp_ac_input_<?php bp_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" name="mpp_ac_form_submit" class="button small" value="<?php esc_attr_e( 'Post', 'redcolaborar' ); ?>"/> &nbsp; <a href="#" class="mpp-ac-reply-cancel"><?php _e( 'Cancel', 'redcolaborar' ); ?></a>
						<input type="hidden" name="mpp_comment_form_id" value="<?php bp_activity_id(); ?>"/>
					</div>

					<?php

					/**
					 * Fires after the activity entry comment form.
					 *
					 *
					 */
					do_action( 'mpp_activity_entry_comments' ); ?>

					<?php wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' ); ?>

				</form>

			<?php endif; ?>
		</div>

	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of the activity entry comments.
	 *
	 *
	 */
	do_action( 'mpp_after_activity_entry_comments' ); ?>
</li>

<?php

/**
 * Fires after the display of an activity entry.
 *
 *
 */
do_action( 'mpp_after_activity_entry' ); ?>
