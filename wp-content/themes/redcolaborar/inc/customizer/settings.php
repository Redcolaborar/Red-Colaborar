<?php
/**
 * Customizer settings.
 *
 * @package Red Colaborar
 */

/**
 * Register additional scripts.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_additional_scripts( $wp_customize ) {

	// Register a setting.
	$wp_customize->add_setting(
		'wds_redcolaborar_header_scripts',
		array(
			'default'           => '',
			'sanitize_callback' => 'force_balance_tags',
		)
	);

	// Create the setting field.
	$wp_customize->add_control(
		'wds_redcolaborar_header_scripts',
		array(
			'label'       => esc_html__( 'Header Scripts', 'redcolaborar' ),
			'description' => esc_html__( 'Additional scripts to add to the header. Basic HTML tags are allowed.', 'redcolaborar' ),
			'section'     => 'wds_redcolaborar_additional_scripts_section',
			'type'        => 'textarea',
		)
	);

	// Register a setting.
	$wp_customize->add_setting(
		'wds_redcolaborar_footer_scripts',
		array(
			'default'           => '',
			'sanitize_callback' => 'force_balance_tags',
		)
	);

	// Create the setting field.
	$wp_customize->add_control(
		'wds_redcolaborar_footer_scripts',
		array(
			'label'       => esc_html__( 'Footer Scripts', 'redcolaborar' ),
			'description' => esc_html__( 'Additional scripts to add to the footer. Basic HTML tags are allowed.', 'redcolaborar' ),
			'section'     => 'wds_redcolaborar_additional_scripts_section',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'wds_redcolaborar_customize_additional_scripts' );

/**
 * Register a social icons setting.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_social_icons( $wp_customize ) {

	// Create an array of our social links for ease of setup.
	$social_networks = array( 'facebook', 'googleplus', 'instagram', 'linkedin', 'twitter' );

	// Loop through our networks to setup our fields.
	foreach ( $social_networks as $network ) {

		// Register a setting.
		$wp_customize->add_setting(
			'wds_redcolaborar_' . $network . '_link',
			array(
				'default' => '',
				'sanitize_callback' => 'esc_url',
			)
		);

		// Create the setting field.
		$wp_customize->add_control(
			'wds_redcolaborar_' . $network . '_link',
			array(
				'label'   => /* translators: the social network name. */ sprintf( esc_html__( '%s URL', 'redcolaborar' ), ucwords( $network ) ),
				'section' => 'wds_redcolaborar_social_links_section',
				'type'    => 'text',
			)
		);
	}
}
add_action( 'customize_register', 'wds_redcolaborar_customize_social_icons' );

/**
 * Register copyright text setting.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_copyright_text( $wp_customize ) {

	// Register a setting.
	$wp_customize->add_setting(
		'wds_redcolaborar_copyright_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'force_balance_tags',
		)
	);

	// Create the setting field.
	$wp_customize->add_control(
		new Text_Editor_Custom_Control(
			$wp_customize,
			'wds_redcolaborar_copyright_text',
			array(
				'label'       => esc_html__( 'Copyright Text', 'redcolaborar' ),
				'description' => esc_html__( 'The copyright text will be displayed in the footer. Basic HTML tags allowed.', 'redcolaborar' ),
				'section'     => 'wds_redcolaborar_footer_section',
				'type'        => 'textarea',
			)
		)
	);
}
add_action( 'customize_register', 'wds_redcolaborar_customize_copyright_text' );

/**
 * Register header button setting.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_header_button( $wp_customize ) {

	// Register a setting.
	$wp_customize->add_setting(
		'wds_redcolaborar_header_button',
		array(
			'default'           => '',
			'sanitize_callback' => 'wds_redcolaborar_sanitize_select',
		)
	);

	// Create the setting field.
	$wp_customize->add_control(
		'wds_redcolaborar_header_button',
		array(
			'label'       => esc_html__( 'Header Button', 'redcolaborar' ),
			'description' => esc_html__( 'Display a custom button in the header.', 'redcolaborar' ),
			'section'     => 'wds_redcolaborar_header_section',
			'type'        => 'select',
			'choices'     => array(
				'none'   => esc_html__( 'No button', 'redcolaborar' ),
				'search' => esc_html__( 'Trigger a search field', 'redcolaborar' ),
				'link'   => esc_html__( 'Link to a custom URL', 'redcolaborar' ),
			),
		)
	);

	// Register a setting for the URL.
	$wp_customize->add_setting(
		'wds_redcolaborar_header_button_url',
		array(
			'default'           => '',
			'sanitize_callback' => 'esc_url',
		)
	);

	// Display the URL field... maybe!
	$wp_customize->add_control(
		'wds_redcolaborar_header_button_url',
		array(
			'label'           => esc_html__( 'Header Button URL', 'redcolaborar' ),
			'description'     => esc_html__( 'Enter the URL or email address to be used by the button in the header.', 'redcolaborar' ),
			'section'         => 'wds_redcolaborar_header_section',
			'type'            => 'url',
			'active_callback' => 'wds_redcolaborar_customizer_is_header_button_link', // Only displays if the Link option is selected above.
		)
	);

	// Register a setting for the link text.
	$wp_customize->add_setting(
		'wds_redcolaborar_header_button_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_filter_nohtml_kses',
		)
	);

	// Display the text field... maybe!
	$wp_customize->add_control(
		'wds_redcolaborar_header_button_text',
		array(
			'label'           => esc_html__( 'Header Button Text', 'redcolaborar' ),
			'description'     => esc_html__( 'Enter the text to be displayed in the button in the header.', 'redcolaborar' ),
			'section'         => 'wds_redcolaborar_header_section',
			'type'            => 'text',
			'active_callback' => 'wds_redcolaborar_customizer_is_header_button_link', // Only displays if the Link option is selected above.
		)
	);
}
add_action( 'customize_register', 'wds_redcolaborar_customize_header_button' );

/**
 * Register featured post block setting.
 *
 * @param object $wp_customize Instance of WP_Customize_Class.
 */
function wds_redcolaborar_customize_featured_post_block( $wp_customize ) {

	// Set strings for logged in/out.
	$settings_groups = array( '_logged_in', '_logged_out' );

	foreach( $settings_groups as $group ) {

		// Register a setting for the background color.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_section_bg_color' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);

		// Display the URL field.
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'wds_redcolaborar_featured_post_section_bg_color' . $group,
				array(
					'label'           => esc_html__( 'Section Background Color', 'redcolaborar' ),
					'description'     => esc_html__( 'Select the background color of the Featured Posts section.', 'redcolaborar' ),
					'section'         => 'wds_redcolaborar_featured_post' . $group,
				)
			)
		);

		// Register a setting for the CTA title.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_title' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);

		// Display the title field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_block_title' . $group,
			array(
				'label'           => esc_html__( 'Call To Action Title', 'redcolaborar' ),
				'description'     => esc_html__( 'Enter the title to be displayed in the Call To Action block.', 'redcolaborar' ),
				'section'         => 'wds_redcolaborar_featured_post' . $group,
				'type'            => 'text',
			)
		);

		// Register a setting for the link text.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_text' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'force_balance_tags',
			)
		);

		// Display the text field.
		$wp_customize->add_control(
			new Text_Editor_Custom_Control(
				$wp_customize,
				'wds_redcolaborar_featured_post_block_text' . $group,
				array(
					'label'           => esc_html__( 'Call To Action Text', 'redcolaborar' ),
					'description'     => esc_html__( 'Enter the text to be displayed in the Call To Action block.', 'redcolaborar' ),
					'section'         => 'wds_redcolaborar_featured_post' . $group,
					'type'            => 'textarea',
				)
			)
		);

		// Register a setting for the CTA title.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_button_text' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);

		// Display the title field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_block_button_text' . $group,
			array(
				'label'           => esc_html__( 'Call To Action Button Text', 'redcolaborar' ),
				'description'     => esc_html__( 'Enter the title to be used by the button in the Call To Action block.', 'redcolaborar' ),
				'section'         => 'wds_redcolaborar_featured_post' . $group,
				'type'            => 'text',
			)
		);

		// Register a setting for the URL.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_url' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'esc_url',
			)
		);

		// Display the URL field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_block_url' . $group,
			array(
				'label'           => esc_html__( 'Call To Action URL', 'redcolaborar' ),
				'description'     => esc_html__( 'Enter the URL to be used by the button in the Call To Action block.', 'redcolaborar' ),
				'section'         => 'wds_redcolaborar_featured_post' . $group,
				'type'            => 'url',
			)
		);

		// Register a setting for the background color.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_bg_color' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);

		// Display the URL field.
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'wds_redcolaborar_featured_post_block_bg_color' . $group,
				array(
					'label'           => esc_html__( 'Background Color', 'redcolaborar' ),
					'description'     => esc_html__( 'Select the background color of the CTA block.', 'redcolaborar' ),
					'section'         => 'wds_redcolaborar_featured_post' . $group,
				)
			)
		);

		// Register a setting for the Show/Hide CTA Checkbox.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_show_hide' . $group,
			array(
				'sanitize_callback' => 'wds_redcolaborar_sanitize_checkbox',
			)
		);

		// Display the checkbox field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_block_show_hide' . $group,
			array(
				'label'       => __( 'Hide CTA Block', 'redcolaborar' ),
				'section'     => 'wds_redcolaborar_featured_post' . $group,
				'description' => __( 'Check this box to hide the CTA Block.', 'redcolaborar' ),
				'type'        => 'checkbox',
			)
		);

		// Register a setting for the CTA Position Checkbox.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_block_show_first' . $group,
			array(
				'sanitize_callback' => 'wds_redcolaborar_sanitize_checkbox',
			)
		);

		// Display the checkbox field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_block_show_first' . $group,
			array(
				'label'       => __( 'Show CTA Block First', 'redcolaborar' ),
				'section'     => 'wds_redcolaborar_featured_post' . $group,
				'description' => __( 'Check this box to show the CTA Block in the first column.', 'redcolaborar' ),
				'type'        => 'checkbox',
			)
		);

		// Register a setting for the post IDs.
		$wp_customize->add_setting(
			'wds_redcolaborar_featured_post_post_ids' . $group,
			array(
				'default'           => '',
				'sanitize_callback' => 'wp_filter_nohtml_kses',
			)
		);

		// Display the title field.
		$wp_customize->add_control(
			'wds_redcolaborar_featured_post_post_ids' . $group,
			array(
				'label'           => esc_html__( 'Post IDs', 'redcolaborar' ),
				'description'     => esc_html__( 'Enter a comma-separate list of BuddyPress Activity Post IDs to feature. A maximum of three posts will be displayed. Posts will display in chronological order.', 'redcolaborar' ),
				'section'         => 'wds_redcolaborar_featured_post' . $group,
				'type'            => 'text',
			)
		);
	}

}
add_action( 'customize_register', 'wds_redcolaborar_customize_featured_post_block' );

/**
 * Sanitizes the select dropdown in the customizer.
 *
 * @param string $input  The input.
 * @param string $setting The setting.
 * @return string
 * @author Corey Collins
 */
function wds_redcolaborar_sanitize_select( $input, $setting ) {

	// Ensure input is a slug.
	$input = sanitize_key( $input );

	// Get list of choices from the control associated with the setting.
	$choices = $setting->manager->get_control( $setting->id )->choices;

	// If the input is a valid key, return it; otherwise, return the default.
	return ( array_key_exists( $input, $choices ) ? $input : $setting->default );
}

/**
 * Checks to see if the link option is selected in our button settings.
 *
 * @return boolean True/False whether or not the Link radio is selected.
 * @author Corey Collins
 */
function wds_redcolaborar_customizer_is_header_button_link() {

	// Get our button setting.
	$button_setting = get_theme_mod( 'wds_redcolaborar_header_button' );

	if ( 'link' !== $button_setting ) {
		return false;
	}

	return true;
}

/**
 * Sanitize the checkbox in the customizer.
 *
 * @param  boolean $checked True/False if the box is checked.
 * @return boolean          The checked value.
 * @author Corey Collins
 */
function wds_redcolaborar_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}
