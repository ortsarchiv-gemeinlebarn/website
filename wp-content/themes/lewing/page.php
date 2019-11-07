<?php 
	
	get_header();
	
	if (have_posts()) : while (have_posts()) : the_post();
?>

	<section class="section">
		<div class="section__overlay">
			<div class="section__wrapper">
				<article class="single-item gd gd--12">

					<h2 class="item__title"><?php _e(get_the_title(), 'lewing'); ?></h2>
					
					<?php the_content(); ?>
				</article>
			</div>
		</div>
	</section>

<?php 

	endwhile; endif;

	get_footer();

?>