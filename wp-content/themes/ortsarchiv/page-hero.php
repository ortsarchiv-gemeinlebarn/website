<?php 

	/*
	Template Name: Hero
	*/
	
	get_header();

	if (have_posts()) : while (have_posts()) : the_post();

?>

	<!-- Content -->
	<main class="content content--hero" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">
		
		<section class="section">
			<div class="section__overlay">
				<div class="section__wrapper">
					<h1><?php the_title(); ?></h1>
				</div>
				<div class="section__wrapper grid">
					<div class="grid-item grid-item--24">
						<?php the_content(); ?>
					</div>
				</div>
			</div>
		</section>

	</main>
	<!-- /Content -->

<?php 

	endwhile; endif;

	get_footer();

?>