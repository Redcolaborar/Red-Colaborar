<?php
/**
 * Customizer sections.
 *
 * @package Red Colaborar
 */

/**
 * Register the section sections.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_sections( $wp_customize ) {

	// Register additional scripts section.
	$wp_customize->add_section(
		'wds_redcolaborar_additional_scripts_section',
		array(
			'title'    => esc_html__( 'Additional Scripts', 'redcolaborar' ),
			'priority' => 10,
			'panel'    => 'site-options',
		)
	);

	// Register a social links section.
	$wp_customize->add_section(
		'wds_redcolaborar_social_links_section',
		array(
			'title'       => esc_html__( 'Social Media', 'redcolaborar' ),
			'description' => esc_html__( 'Links here power the display_social_network_links() template tag.', 'redcolaborar' ),
			'priority'    => 90,
			'panel'       => 'site-options',
		)
	);

	// Register a header section.
	$wp_customize->add_section(
		'wds_redcolaborar_header_section',
		array(
			'title'    => esc_html__( 'Header Customizations', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'site-options',
		)
	);

	// Register a footer section.
	$wp_customize->add_section(
		'wds_redcolaborar_footer_section',
		array(
			'title'    => esc_html__( 'Footer Customizations', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'site-options',
		)
	);

	// Register a featured post block section.
	$wp_customize->add_section(
		'wds_redcolaborar_featured_post_posts',
		array(
			'title'    => esc_html__( 'Featured Posts', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'featured-post-block',
		)
	);

	// Featured Posts for Logged-In Users.
	$wp_customize->add_section(
		'wds_redcolaborar_featured_post_logged_in',
		array(
			'title'    => esc_html__( 'Logged-In Users', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'featured-post-block',
		)
	);

	// Featured Posts for Logged-Out Users.
	$wp_customize->add_section(
		'wds_redcolaborar_featured_post_logged_out',
		array(
			'title'    => esc_html__( 'Logged-Out Users', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'featured-post-block',
		)
	);

	// Register a featured post block section.
	$wp_customize->add_section(
		'wds_redcolaborar_featured_post_cta',
		array(
			'title'    => esc_html__( 'Call To Action', 'redcolaborar' ),
			'priority' => 90,
			'panel'    => 'featured-post-block',
		)
	);
}
add_action( 'customize_register', 'wds_redcolaborar_customize_sections' );
