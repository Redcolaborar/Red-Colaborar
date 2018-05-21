<?php
/**
 * The sidebar containing the main widget area.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Red Colaborar
 */
?>

<aside class="secondary" role="complementary">
	<?php do_action( 'redcolaborar_sidebar' ); ?>

	<?php
	// Display the member search form on the directory page.
	if ( function_exists( 'bp_is_members_directory' ) && bp_is_members_directory() ) {
		dynamic_sidebar( 'directory-sidebar' );
	} elseif ( is_front_page() || function_exists( 'bp_is_activity_directory' ) && bp_is_activity_directory() ) {
		dynamic_sidebar( 'activity-sidebar' );
	} else {
		dynamic_sidebar( 'default-sidebar' );
	}
	?>
</aside><!-- .secondary -->
