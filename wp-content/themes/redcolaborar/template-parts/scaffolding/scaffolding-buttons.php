<?php
/**
 * The template used for displaying Buttons in the scaffolding library.
 *
 * @package Red Colaborar
 */

?>

<section class="section-scaffolding">

	<h2 class="scaffolding-heading"><?php esc_html_e( 'Buttons', 'redcolaborar' ); ?></h2>
	<?php
		// Button.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Button',
			'description' => 'Display a button.',
			'usage'       => '<button class="button" href="#">Primary Button</button>',
			'output'      => '<button class="button">Primary Button</button>',
		) );
	?>
	<?php
		// Button Small.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Button Small',
			'description' => 'Display a small button.',
			'usage'       => '<button class="button small" href="#">Primary Button</button>',
			'output'      => '<button class="button small">Primary Button</button>',
		) );
	?>
	<?php
		// Button Secondary.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Button Secondary',
			'description' => 'Display a button.',
			'usage'       => '<button class="button outline" href="#">Primary Button</button>',
			'output'      => '<button class="button outline">Primary Button</button>',
		) );
	?>
	<?php
		// Button Secondary Small.
		wds_redcolaborar_display_scaffolding_section( array(
			'title'       => 'Button Secondary Small',
			'description' => 'Display a small button.',
			'usage'       => '<button class="button outline small" href="#">Primary Button</button>',
			'output'      => '<button class="button outline small">Primary Button</button>',
		) );
	?>
</section>
