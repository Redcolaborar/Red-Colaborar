<?php
/**
 * The template for displaying search results pages.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#search-result
 *
 * @package Red Colaborar
 */

get_header(); ?>

	<section class="primary content-area">
		<main id="main" class="site-main">

		<?php if ( have_posts() ) : ?>

			<header class="page-header">
				<h1 class="page-title"><?php /* translators: the term(s) searched */ printf( esc_html__( 'Search Results for: %s', 'redcolaborar' ), '<span>' . get_search_query() . '</span>' ); ?></h1>

				<div class="search-results-form">
					<h2 class="h3"><?php _e( 'Not what you were looking for? Try searching again below:', 'redcolaborar' ); ?></h2>
					<?php get_search_form(); ?>
				</div><!-- .search-results-form -->
			</header><!-- .page-header -->

			<?php
			/* Start the Loop */
			while ( have_posts() ) :
				the_post();

				/**
					* Run the loop for the search to output the results.
					* If you want to overload this in a child theme then include a file
					* called content-search.php and that will be used instead.
					*/
				get_template_part( 'template-parts/content', 'search' );

			endwhile;

			the_posts_navigation();

		else :

			get_template_part( 'template-parts/content', 'none' );

		endif;
		?>

		</main><!-- #main -->
	</section><!-- .primary -->

<?php get_footer(); ?>
