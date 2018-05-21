<?php
/**
 * Customizer panels.
 *
 * @package Red Colaborar
 */

/**
 * Add a custom panels to attach sections too.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_panels( $wp_customize ) {

	// Register a new panel.
	$wp_customize->add_panel(
		'site-options', array(
			'priority'       => 10,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Site Options', 'redcolaborar' ),
			'description'    => esc_html__( 'Other theme options.', 'redcolaborar' ),
		)
	);

	// Featured Post Block.
	$wp_customize->add_panel(
		'featured-post-block', array(
			'priority'       => 10,
			'capability'     => 'edit_theme_options',
			'theme_supports' => '',
			'title'          => esc_html__( 'Featured Posts Section', 'redcolaborar' ),
			'description'    => esc_html__( 'Customize the text in the logged-out user featured post block.', 'redcolaborar' ),
		)
	);
}
add_action( 'customize_register', 'wds_redcolaborar_customize_panels' );
