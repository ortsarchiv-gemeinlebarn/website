<?php 
	get_header();
?>
	<div class="content-sidebar">
		<div class="content-sidebar__content">
			<section class="section">
				<div class="section__overlay">
					<div class="section__wrapper">
						<?php 
							$i=0;
							if (have_posts()) : while (have_posts()) : the_post();
								$i++;
								if($i%2){get_template_part('parts/item-image-left');}else{get_template_part('parts/item-image-right');}
							endwhile; endif;
						?>
					</div>
				</div>
			</section>
		</div>
		<div class="content-sidebar__sidebar">
			<?php get_sidebar(); ?>
		</div>
	</div>
	
	<section class="section">
		<?php get_template_part('parts/pagination'); ?>
	</section>

<?php 
	get_footer();
?>