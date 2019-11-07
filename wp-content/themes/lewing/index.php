<?php 
	
	get_header();
	
	if (have_posts()) : while (have_posts()) : the_post();
?>

	<section class="section">
		<div class="section__overlay">
			<div class="section__wrapper">
			
				<article class="section__title gd gd--12">
					<h1><?php _e(get_the_title(), 'lewing'); ?></h1>
				</article>
				
				<article class="gd gd--12">
					<?php the_content(); ?>
				</article>
				
			</div>
		</div>
	</section>

<?php 

	endwhile; endif;

	get_footer();

?>