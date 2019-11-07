<?php 

	/*
		Template Name: Archive
	*/

	get_header();

?>

	<!-- Content -->
	<main class="content" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">

		<section class="section">
			<div class="section__wrapper">
				<h1><?php the_title(); ?></h1>
			</div>
		</section>

		<section class="section">
			<div class="section__wrapper grid">

				<div class="grid-item grid-item--24">
					<?php get_search_form(); ?>
					
					<h2><?php getTheme()->e('Monate'); ?></h2>
					<ul>
						<?php wp_get_archives('type=monthly'); ?>
					</ul>
				
					<h2><?php getTheme()->e('Kategorien'); ?></h2>
					<ul>
						<?php wp_list_categories(); ?>
					</ul>

				</div>

			</div>
		</section>

	</main>
	<!-- /Content -->

<?php

	get_footer();

?>