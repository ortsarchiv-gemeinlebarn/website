<?php 
	
	get_header();

	if (have_posts()) : while (have_posts()) : the_post();

?>

	<!-- Content -->
	<main class="content">

		<section class="section section--page-title">
			<div class="section__overlay">
				<div class="section__wrapper">
					<h1 class="title"><?php the_title(); ?></h1>
				</div>
			</div>
		</section>

		<section class="section">
			<div class="section__overlay">
				<div class="section__wrapper">
					<?php the_content(); ?>
				</div>
			</div>
		</section>

	</main>
	<!-- /Content -->

<?php 

	endwhile; endif;

	get_footer();

?>