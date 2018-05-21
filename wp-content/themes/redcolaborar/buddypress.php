<?php
/**
 * The template for displaying buddypress pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Red Colaborar
 * @since   Unknown
 */

get_header(); ?>

	<?php wds_redcolaborar_display_homepage_featured_posts_on_default_view_and_frontpage(); ?>

	<?php if ( is_buddypress() && ! bp_is_user() && ! bp_is_register_page() ) : ?>
		<?php get_sidebar(); ?>
	<?php endif; ?>

	<div class="primary content-area">
		<main id="main" class="site-main">
			<?php while ( have_posts() ) : the_post(); // @codingStandardsIgnoreLine: Style okay here. ?>

				<?php get_template_part( 'template-parts/content', 'buddypress' ); ?>

			<?php endwhile; // End of the loop. ?>
		</main><!-- #main -->
	</div><!-- .primary -->

<?php
get_footer();
