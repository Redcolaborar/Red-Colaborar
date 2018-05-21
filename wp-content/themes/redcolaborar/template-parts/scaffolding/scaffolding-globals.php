<?php
/**
 * The template used for displaying colors & fonts in the scaffolding library.
 *
 * @package Red Colaborar
 */

?>

<section class="section-scaffolding">

	<h2 class="scaffolding-heading"><?php esc_html_e( 'Globals', 'redcolaborar' ); ?></h2>

	<?php
		// Theme colors.
		wds_redcolaborar_display_global_scaffolding_section( array( // WPCS: XSS OK.
			'global_type' => 'colors',
			'title'       => 'Colors',
			'arguments'   => array(
				'black'              => '#000001',
				'elephant'           => '#253541',
				'cloud-burst'        => '#323c50',
				'gun-powder'         => '#45435f',
				'aluminum'           => '#95989e',
				'submarine'          => '#bbc1ca',
				'seashell'           => '#f2f0f0',
				'white'              => '#ffffff',
				'eucalyptus'         => '#1f9650',
				'niagra'   => '#2cbb67',
				'light-sea-green'            => '#49d483',
				'flame'             => '#d63d3b',
				'deep-carrot-orange'         => '#fc6361',
				'faded-red'          => '#fb807e',
				'cerulean' => '#0998cb',
				'curious-blue'   => '#0fb6f2',
				'picton-blue'        => '#49cbfa',
			),
		) );

		// Theme fonts.
		wds_redcolaborar_display_global_scaffolding_section( array( // WPCS: XSS OK.
			'global_type'  => 'fonts',
			'title'        => 'Fonts',
			'arguments'    => array(
				'Header'  => '"Open Sans", sans-serif',
				'Sans'     => '"Cabin", sans-serif',
				'Code'     => 'Monaco, Consolas, "Andale Mono", "DejaVu Sans Mono", monospace',
				'Pre'      => '"Courier 10 Pitch", Courier, monospace',
			),
		) );
	?>
</section>
