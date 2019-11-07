<?php 

	get_header();

?>

	<!-- Content -->
	<main class="content" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">

		<?php 
			$cat_id = get_category(get_query_var('cat'))->cat_ID;
			$categories = get_categories(array('child_of' => $cat_id));

			foreach($categories as $category){
		?>

		<section class="section">
			<div class="section__wrapper">
				<h1><?php $theme->e($category->name); ?></h1>
				<p><?php $theme->e($category->category_description); ?></p>
			</div>
		</section>

		<section class="section">
			<div class="section__wrapper grid post-list">

				<?php
					$posts = get_posts(
						array(
							'category' 		=> $category->cat_ID,
							'orderby'      	=> 'date',
        					'order'         => 'ASC',
							'numberposts'   => -1
						)
					);

					foreach($posts as $post){
						setup_postdata($post);
						$count++;
						get_template_part('inc/parts/kapitel-list-item');
					}
				
				?>

			</div>
        </section>

        <section class="section">
			<div class="section__wrapper grid">
                <?php get_template_part('inc/parts/pagination'); ?>
			</div>
		</section>

		<?php 
			}
		?>

	</main>
	<!-- /Content -->

<?php

	get_footer();

?>