<?php
/**
 * BuddyPress - Members Loop
 *
 * Querystring is set via AJAX in _inc/ajax.php - bp_legacy_theme_object_filter()
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

/**
 * Fires before the display of the members loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_members_loop' ); ?>

<?php if ( bp_get_current_member_type() ) : ?>
	<p class="current-member-type"><?php bp_current_member_type_message() ?></p>
<?php endif; ?>

<?php if ( bp_has_members( bp_ajax_querystring( 'members' ) ) ) : ?>

	<div id="pag-top" class="pagination">

		<div class="pag-count" id="member-dir-count-top">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-top">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

	<?php

	/**
	 * Fires before the display of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_before_directory_members_list' ); ?>

	<ul id="members-list" class="item-list" aria-live="assertive" aria-relevant="all">

	<?php while ( bp_members() ) : bp_the_member(); ?>

		<li <?php bp_member_class(); ?>>
			<div class="item-header">
				<div class="item-avatar">
					<a href="<?php bp_member_permalink(); ?>"><?php bp_member_avatar(); ?></a>
				</div>

				<div class="item">
					<div class="item-title">
						<a href="<?php bp_member_permalink(); ?>"><?php echo esc_html( wds_redcolaborar_get_users_name( bp_get_member_user_id() ) ); ?></a>
					</div>

					<div class="item-meta">
						<span class="username">
							@<?php echo bp_core_get_username( bp_get_member_user_id() ); ?>
						</span>
					</div><!-- .item-meta -->

					<?php

					/**
					 * Fires inside the display of a directory member item.
					 *
					 * @since 1.1.0
					 */
					do_action( 'bp_directory_members_item' ); ?>

					<?php
					 /***
					  * If you want to show specific profile fields here you can,
					  * but it'll add an extra query for each member in the loop
					  * (only one regardless of the number of fields you show):
					  *
					  * bp_member_profile_data( 'field=the field name' );
					  */
					?>
				</div><!-- .item -->

				<?php wds_redcolaborar_bp_dir_send_private_message_button(); ?>

			</div><!-- .item-header -->

			<div class="latest-update">

				<ul class="update">
					<?php
					// Fields.
					$organization = bp_get_profile_field_data(
						array(
							'field'   => 'Organizacion de pertenencia',
							'user_id' => bp_get_member_user_id(),
						)
					);

					$location = bp_get_profile_field_data(
						array(
							'field'   => 'Pais',
							'user_id' => bp_get_member_user_id(),
						)
					);
					?>

					<?php if ( ! empty( $organization ) ) : ?>
						<li>
							<strong><?php esc_html_e( 'Organization: ', 'redcolaborar' ); ?></strong>
							<?php echo esc_html( $organization ); ?>
						</li>
					<?php endif; ?>

					<?php if ( ! empty( $location ) ) : ?>
						<li class="location">
							<strong><?php esc_html_e( 'Location: ', 'redcolaborar' ); ?></strong>
							<?php echo esc_html( $location ); ?>
							<?php
							wds_redcolaborar_display_svg( array(
								'icon'   => 'icon-marker',
								'title'  => 'Location',
								'height' => '15',
								'width'  => '15',
								'fill'   => '#bbc1ca',
							) );
							?>
						</li><!-- .location -->
					<?php endif; ?>

					<li class="activity">
						<strong>
							<?php esc_html_e( 'Active: ', 'redcolaborar' ); ?>
						</strong>
						<span class="activity-date" data-livestamp="<?php bp_core_iso8601_date( bp_get_member_last_active( array( 'relative' => false ) ) ); ?>">
							<?php bp_member_last_active(); ?>
						</span>
					</li>
				</ul><!-- .update -->

			</div><!-- .latest-update -->

			<div class="action">

				<?php

				/**
				 * Fires inside the members action HTML markup to display actions.
				 *
				 * @since 1.1.0
				 */
				do_action( 'bp_directory_members_actions' ); ?>

			</div>

			<div class="clear"></div>
		</li>

	<?php endwhile; ?>

	</ul>

	<?php

	/**
	 * Fires after the display of the members list.
	 *
	 * @since 1.1.0
	 */
	do_action( 'bp_after_directory_members_list' ); ?>

	<?php bp_member_hidden_fields(); ?>

	<div id="pag-bottom" class="pagination">

		<div class="pag-count" id="member-dir-count-bottom">

			<?php bp_members_pagination_count(); ?>

		</div>

		<div class="pagination-links" id="member-dir-pag-bottom">

			<?php bp_members_pagination_links(); ?>

		</div>

	</div>

<?php else: ?>

	<div id="message" class="info">
		<p><?php _e( "Sorry, no members were found.", 'redcolaborar' ); ?></p>
	</div>

<?php endif; ?>

<?php

/**
 * Fires after the display of the members loop.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_members_loop' );
