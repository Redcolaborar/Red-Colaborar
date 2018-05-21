<?php
/**
 * BuddyPress - Activity Stream (Single Item).
 *
 * This template is used by activity-loop.php and AJAX functions to show
 * each activity.
 *
 * @package Red Colaborar
 * @subpackage bp-legacy
 *
 * @since  NEXT
 */

/**
 * Fires before the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_activity_entry' );

$is_featured = get_query_var( 'rc_is_featured' );

?>

<li class="<?php bp_activity_css_class(); ?>" id="activity-<?php bp_activity_id(); ?>">
	<div class="activity-content">
		<div class="bp-activity-header-container">
			<div class="activity-avatar">
				<a href="<?php bp_activity_user_link(); ?>">
					<?php bp_activity_avatar(); ?>
				</a>
			</div><!-- .activity-avatar -->
			<div class="activity-header">
				<?php bp_activity_action(); ?>
			</div><!-- .activity-header -->
		</div><!-- .bp-activity-header-container -->

		<?php if ( bp_activity_has_content() ) : ?>
			<div class="activity-inner">
				<div class="rc-activity-col-content">
					<?php bp_activity_content_body(); ?>
				</div><!-- .rc-activity-col-content -->

				<?php

				/**
				 * Fires after the display of an activity entry content.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_activity_entry_content' );

				?>
			</div><!-- .activity-inner -->
		<?php endif; ?>

		<?php if( !empty( $is_featured ) && $is_featured ) : ?>
			<div class="rc-featured-view-more-container">
				<a href="<?php echo bp_activity_get_permalink( bp_get_activity_id() ) ?>"><strong>Ver más</strong></a>
			</div>
		<?php endif; ?>

		<div class="activity-meta">
			<?php if ( bp_get_activity_type() === 'activity_comment' ) : ?>
				<a href="<?php bp_activity_thread_permalink(); ?>" class="view bp-secondary-action"><?php esc_html_e( 'View Conversation', 'redcolaborar' ); ?></a>
			<?php endif; ?>

			<div class="bp-activity-container">
				<?php if ( is_user_logged_in() ) : ?>

					<?php if ( bp_activity_can_comment() ) : ?>
						<div class="acomment-container">

							<?php if( !empty( $is_featured ) && $is_featured ) : ?>

								<a href="<?php echo bp_activity_get_permalink( bp_get_activity_id() ); ?>#ac-form-<?php echo bp_get_activity_id() ?>" class="acomment-reply bp-primary-action no-styles" id="acomment-comment-<?php bp_activity_id(); ?>">
									<?php
									// Translators: %s is the number of comments.
									echo wp_kses_post( sprintf( __( '%s Comment', 'redcolaborar' ), '<span>' . bp_activity_get_comment_count() . '</span>' ) );
									?>
								</a>

							<?php else: ?>

								<a href="<?php bp_activity_comment_link(); ?>" class="acomment-reply bp-primary-action no-styles" id="acomment-comment-<?php bp_activity_id(); ?>">
									<?php
									// Translators: %s is the number of comments.
									echo wp_kses_post( sprintf( __( '%s Comment', 'redcolaborar' ), '<span>' . bp_activity_get_comment_count() . '</span>' ) );
									?>
								</a>

							<?php endif; ?>

						</div>
					<?php endif; ?>

					<div class="bp-activity-menu-container">
						<button class="bp-activity-menu-trigger no-styles">
							<?php
							wds_redcolaborar_display_svg( array(
								'title' => __( 'Activity Menu', 'redcolaborar' ),
								'icon'  => 'icon-more',
								'fill'  => '#95989e',
							) );
							?>
						</button>
						<div class="bp-activity-menu">

							<?php if ( bp_activity_can_favorite() ) : ?>
								<div class="bp-activity-menu-item">
									<?php if ( ! bp_get_activity_is_favorite() ) : ?>
										<span class="bp-activity-menu-item-icon">
											<?php
											wds_redcolaborar_display_svg( array(
												'title' => __( 'Bookmark', 'redcolaborar' ),
												'icon'  => 'icon-bookmark',
											) );
											?>
										</span>
										<a href="<?php bp_activity_favorite_link(); ?>" class="fav bp-secondary-action"><?php esc_html_e( 'Bookmark', 'redcolaborar' ); ?></a>
									<?php else : ?>

										<span class="bp-activity-menu-item-icon">
											<?php
											wds_redcolaborar_display_svg( array(
												'title' => __( 'Remove Bookmark', 'redcolaborar' ),
												'icon'  => 'icon-bookmark',
											) );
											?>
										</span>
										<a href="<?php bp_activity_unfavorite_link(); ?>" class="unfav bp-secondary-action"><?php esc_html_e( 'Remove Bookmark', 'redcolaborar' ); ?></a>
									<?php endif; ?>
								</div><!-- .bp-activity-menu-item -->
							<?php endif; ?>

							<div class="bp-activity-menu-item">
								<span class="bp-activity-menu-item-icon">
									<?php
									wds_redcolaborar_display_svg( array(
										'title' => __( 'Facebook', 'redcolaborar' ),
										'icon'  => 'icon-facebook',
										'fill'  => '#3b5998',
									) );
									?>
								</span>
								<a href="<?php echo esc_url( wds_redcolaborar_get_facebook_share_url( bp_activity_get_permalink( bp_get_activity_id() ) ) ); ?>" class="bp-activity-menu-item-link" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=475, height=505' ); return false;"><?php esc_html_e( 'Share on Facebook', 'redcolaborar' ); ?></a>
							</div>

							<div class="bp-activity-menu-item">
								<span class="bp-activity-menu-item-icon">
									<?php
									wds_redcolaborar_display_svg( array(
										'title' => __( 'Twitter', 'redcolaborar' ),
										'icon'  => 'icon-twitter',
										'fill'  => '#00aced',
									) );
									?>
								</span>
								<a href="<?php echo esc_url( wds_redcolaborar_get_twitter_share_url( bp_activity_get_permalink( bp_get_activity_id() ) ) ); ?>" class="bp-activity-menu-item-link" onclick="window.open(this.href, 'targetWindow', 'toolbar=no, location=no, status=no, menubar=no, scrollbars=yes, resizable=yes, top=150, left=0, width=475, height=505' ); return false;"><?php esc_html_e( 'Share on Twitter', 'redcolaborar' ); ?></a>
							</div>

							<?php do_action( 'bp_entry_after_twitter_icon' ); ?>

							<?php if ( bp_activity_user_can_delete() ) : ?>
								<div class="bp-activity-menu-item">
									<span class="bp-activity-menu-item-icon">
										<?php
										wds_redcolaborar_display_svg( array(
											'title' => __( 'Delete', 'redcolaborar' ),
											'icon'  => 'icon-delete',
										) );
										?>
									</span>

									<?php bp_activity_delete_link(); ?>
								</div><!-- .bp-activity-menu-item -->

							<?php endif; ?>
						</div><!-- .bp-activity-menu -->
					</div><!-- .bp-activity-menu-container -->

					<?php

					/**
					 * Fires at the end of the activity entry meta data area.
					 *
					 * @since 1.2.0
					 */
					do_action( 'bp_activity_entry_meta' );

					?>
				<?php endif; ?>
			</div><!-- .bp-activity-container -->
		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_before_activity_entry_comments' );

	/**
	 * Should we show the comments?
	 *
	 * @author Aubrey Portwood
	 * @since  NEXT
	 *
	 * @param boolean $show_comments True to show, false to not show.
	 */
	$show_comments = apply_filters( 'wds_redcolaborar_entry_php_show_comments', true );

	?>

	<?php if ( $show_comments && ( bp_activity_get_comment_count() || bp_activity_can_comment() ) || bp_is_single_activity() ) : ?>
		<div class="activity-comments">
			<?php wds_redcolaborar_display_show_hide_comments_link(); ?>
			<?php bp_activity_comments(); ?>

			<?php if ( is_user_logged_in() && bp_activity_can_comment() ) : ?>
				<form action="<?php bp_activity_comment_form_action(); ?>" method="post" id="ac-form-<?php bp_activity_id(); ?>" class="ac-form"<?php bp_activity_comment_form_nojs_display(); ?>>
					<div class="ac-reply-avatar"><?php bp_loggedin_user_avatar( 'width=' . BP_AVATAR_THUMB_WIDTH . '&height=' . BP_AVATAR_THUMB_HEIGHT ); ?></div>
					<div class="ac-reply-content">
						<div class="ac-textarea">
							<label for="ac-input-<?php bp_activity_id(); ?>" class="bp-screen-reader-text">
								<?php

								// Translators: accessibility text.
								esc_html_e( 'Comment', 'redcolaborar' );
								?>
							</label>
							<textarea id="ac-input-<?php bp_activity_id(); ?>" class="ac-input bp-suggestions" name="ac_input_<?php bp_activity_id(); ?>"></textarea>
						</div>
						<input type="submit" class="button small" name="ac_form_submit" value="<?php esc_attr_e( 'Post', 'redcolaborar' ); ?>" /> &nbsp; <a href="#" class="ac-reply-cancel"><?php esc_html_e( 'Cancel', 'redcolaborar' ); ?></a>
						<input type="hidden" name="comment_form_id" value="<?php bp_activity_id(); ?>" />
					</div>

					<?php

					/**
					 * Fires after the activity entry comment form.
					 *
					 * @since 1.5.0
					 */
					do_action( 'bp_activity_entry_comments' );

					// Nonce the form.
					wp_nonce_field( 'new_activity_comment', '_wpnonce_new_activity_comment' );
					?>
				</form>
			<?php endif; ?>

		</div>
	<?php endif; ?>

	<?php

	/**
	 * Fires after the display of the activity entry comments.
	 *
	 * @since 1.2.0
	 */
	do_action( 'bp_after_activity_entry_comments' );
	?>

</li>
<?php

/**
 * Fires after the display of an activity entry.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_activity_entry' );

set_query_var( 'rc_is_featured', false );
