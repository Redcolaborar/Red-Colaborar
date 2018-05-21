<?php
/**
 * The template used for displaying forms in the scaffolding library.
 *
 * @package Red Colaborar
 */

?>

<section class="section-scaffolding">

	<h2 class="scaffolding-heading"><?php esc_html_e( 'Forms', 'redcolaborar' ); ?></h2>

	<?php
		// Search form.
		$echo = false; // set echo to false so the search form outputs correctly.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Search Form',
			'description' => 'Display the search form.',
			'usage'       => '<?php get_search_form(); ?>',
			'output'      => get_search_form( $echo ),
		) );
	?>

	<?php
		// Textearea.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Textarea',
			'description' => 'Display a textarea.',
			'usage'       => '<textarea placeholder="Textarea"></textarea>',
			'output'      => '<textarea placeholder="Textarea"></textarea>',
		) );
	?>

	<?php
		// Input.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Input Field',
			'description' => 'Display an input field.',
			'usage'       => '<input type="text" placeholder="Input text field" />',
			'output'      => '<input type="text" placeholder="Input text field" />',
		) );
	?>

	<?php
		// Radio.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Radio Input',
			'description' => 'Display a radio input field.',
			'usage'       => '<input type="radio" />',
			'output'      => '<input type="radio" />',
		) );
	?>

	<?php
		// Checkbox.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Checkbox Input',
			'description' => 'Display a checkbox input field.',
			'usage'       => '<input type="checkbox" />',
			'output'      => '<input type="checkbox" />',
		) );
	?>

<?php // Select
	wds_redcolaborar_display_scaffolding_section( array(
		'title'       => 'Select',
		'description' => 'Display a select dropdown field.',
		'usage'       => '
			<select name="select">
				<option value="value1">Value 1</option>
				<option value="value2" selected>Value 2</option>
				<option value="value3">Value 3</option>
			</select>
		',
		'output'      => '
			<select name="select">
				<option value="value1">Value 1</option>
				<option value="value2" selected>Value 2</option>
				<option value="value3">Value 3</option>
			</select>
		',
	) ); ?>
</section>
