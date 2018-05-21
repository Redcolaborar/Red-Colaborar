<?php
/**
 * BuddyPress - Users Cover Image Header
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 */

?>

<?php

/**
 * Fires before the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_before_member_header' ); ?>

<div id="cover-image-container">
	<a id="header-cover-image" href="<?php bp_displayed_user_link(); ?>"></a>

	<div class="container">
		<div id="item-header-cover-image">
			<div id="item-header-avatar">
				<a href="<?php bp_displayed_user_link(); ?>">

					<?php bp_displayed_user_avatar( 'type=full' ); ?>

				</a>
			</div><!-- #item-header-avatar -->

			<div id="item-header-content">

				<?php if ( bp_is_active( 'activity' ) && bp_activity_do_mentions() ) : ?>
						<h2 class="user-name">
							<?php echo esc_html( wds_redcolaborar_get_users_name(  bp_displayed_user_id() ) ); ?>
						</h2>
					<h3 class="user-nicename">@<?php bp_displayed_user_mentionname(); ?></h3>
				<?php endif; ?>

				<?php if ( ! empty( bp_get_profile_field_data( 'field=Organizacion de pertenencia' ) ) ) : ?>
					<strong><?php esc_html_e( 'Organization of membership:', 'redcolaborar' ); ?></strong><br/>
					<?php bp_profile_field_data( 'field=Organizacion de pertenencia' ); ?>
				<?php endif; ?>

				<div id="item-buttons"><?php

					/**
					 * Fires in the member header actions section.
					 *
					 * @since 1.2.6
					 */
					do_action( 'bp_member_header_actions' ); ?></div><!-- #item-buttons -->

				<?php

				/**
				 * Fires before the display of the member's header meta.
				 *
				 * @since 1.2.0
				 */
				do_action( 'bp_before_member_header_meta' ); ?>

				<div id="item-meta">

					<?php if ( bp_is_active( 'activity' ) ) : ?>

						<div id="latest-update">

							<?php bp_activity_latest_update( bp_displayed_user_id() ); ?>

						</div>

					<?php endif; ?>

					<?php

					/**
					 * Fires after the group header actions section.
					*
					* If you'd like to show specific profile fields here use:
					* bp_member_profile_data( 'field=About Me' ); -- Pass the name of the field
					*
					* @since 1.2.0
					*/
					do_action( 'bp_profile_header_meta' );

					?>

				</div><!-- #item-meta -->

			</div><!-- #item-header-content -->

		</div><!-- #item-header-cover-image -->

		<div class="item-header-profile-info">
			<div class="location-ability">
				<?php if ( ! empty( bp_get_profile_field_data( 'field=Pais' ) ) ) : ?>
					<div class="location">
						<?php wds_redcolaborar_display_svg( array(
							'icon'  => 'icon-marker',
							'title' => 'Location',
						) ); ?>
						<?php bp_profile_field_data( 'field=Pais' ); ?>
					</div><!-- .location -->
				<?php endif; ?>
				<?php
				// Get multiselect abilities field.
				$abilities = xprofile_get_field_data( 'Habilidades', get_the_author_id(), $multi_format = 'comma' );

				if ( ! empty( $abilities ) ) :
				?>
					<div class="abilities">
						<strong>
							<?php echo esc_html__( 'Abilities: ', 'redcolaborar' ); ?>
						</strong>
						<?php echo esc_html( $abilities ); ?>
					</div><!-- .abilities -->
				<?php endif; ?>
			</div><!-- .location-ability -->

			<?php if ( ! empty( bp_get_profile_field_data( 'field=Bio' ) ) ) :
				$bio_text = bp_get_profile_field_data( 'field=Bio' ); ?>
				<div class="bio">
					<p>
						<?php echo wds_recolaborar_limit_text( $bio_text, 49 ); ?>
					</p>
				</div><!-- .bio -->
			<?php endif; ?>
		</div><!--.item-header-profile-info -->
	</div><!-- .container -->
</div><!-- #cover-image-container -->

<?php

/**
 * Fires after the display of a member's header.
 *
 * @since 1.2.0
 */
do_action( 'bp_after_member_header' ); ?>

<div id="template-notices" role="alert" aria-atomic="true">
	<?php

	/** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
	do_action( 'template_notices' ); ?>

</div>
