<?php 
	
	get_header();
	
	if (have_posts()) : while (have_posts()) : the_post();
?>

	<div class="content-sidebar">
		<div class="content-sidebar__content">
			<section class="section">
				<div class="section__overlay">
					<div class="section__wrapper">
						<article class="single-item gd gd--12">
							<div class="item__meta"><?php _e(get_the_date('d. F Y'), 'lewing'); ?> <span>&bull;</span> geschrieben von <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>"><?php _e(get_the_author(), 'lewing'); ?></a> <span>&bull;</span> <?php echo get_the_category_list(" <span>&bull;</span> ", get_the_ID()); ?></div>

							<h2 class="item__title"><?php _e(get_the_title(), 'lewing'); ?></h2>

							<?php the_content(); ?>
						</article>
					</div>
				</div>
			</section>
		</div>
		<div class="content-sidebar__sidebar">
			<?php get_sidebar(); ?>
		</div>
	</div>
	
	<section class="section single-author">
		<div class="section__overlay">
			<div class="section__wrapper">
				<h3>Geschrieben von</h3>
				<?php get_template_part('parts/author-short'); ?>
			</div>
		</div>
	</section>

<?php 

	endwhile; endif;

	get_footer();

?>