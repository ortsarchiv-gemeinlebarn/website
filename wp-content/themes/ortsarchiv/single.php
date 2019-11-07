<?php 

	get_header();

	the_post();
	preg_match_all("/\[(\w+)\](.+?)\[\/\\1\]/", get_the_content(), $matches);

	$projekt = get_the_category()[0]; // = Category
	$kapiteln = get_posts(
		array(
			'category'		=> $projekt->cat_ID,
			'orderby'  		=> 'date',
			'order'			=> 'ASC',
			'numberposts'   => -1
		)
	);
?>

	<!-- Content -->
	<main class="content">

		<section class="section">
			<div class="section__wrapper">
				<p><?php _e(get_the_category(get_the_ID())[0]->name, 'aa_aufrecht'); ?></p>
				<h1><?php _e(get_the_title(), 'aa_aufrecht'); ?></h1>
			</div>
		</section>

		<section class="section">
			<div class="section__overlay">
				<div class="section__wrapper grid">
					<div class="grid-item grid-item--center grid-item--18">
						<?php the_content(); ?>
						<a href="#" class="button button--next-kapitel">zum nächsten Kapitel</a>
					</div>
					<div class="grid-item grid-item--6">
						<?php
							foreach($matches[2] as $footnote){
								$count++;
								echo "<p class='footnote-side footnote-side--$count'><sup>$count</sup> $footnote</p>";
							}
						?>
					</div>
				</div>
			</div>
		</section>


	</main>
	<!-- /Content -->

	<div class="kapitel-menu">

		<?php

			$count = 0;
			foreach($kapiteln as $kapitel){
				$count++;

		?>
			<a href="<?php _e($kapitel->guid); ?>" class="kapitel-item">
				<p><?php $theme->e("$count. Kapitel"); ?></p>
				<h4><?php $theme->e($kapitel->post_title); ?></h4>
			</a>
		<?php
			}
		?>

	</div>

<?php
	get_footer();

?>