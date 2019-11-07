<?php

	get_header();

?>

	<!-- Content -->
	<main class="content" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">

		<section class="section">
			<div class="section__wrapper">
				<h1><?php $theme->e('Suche'); ?></h1>
			</div>
		</section>

		<section class="section">
			<div class="section__overlay">
				<div class="section__wrapper grid">
					<div class="grid-item grid-item--24">
						<?php get_search_form(); ?>
					</div>
                    <div class="grid-item grid-item--24">
                        <h2><?php $theme->e("Suchergebnisse für "); ?> <strong><?php echo $s ?></strong></h2>
                    </div>
				</div>
			</div>
		</section>

        <section class="section">
            <div class="section__overlay">
                <div class="section__wrapper grid post-list">

                    <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                
                        <?php get_template_part('inc/parts/post-list-item'); ?>

                    <?php endwhile; else: ?>

                        <div class="grid-item grid-item--24">
                            <h2><?php $theme->e("Leider nichts gefunden"); ?></h2>
                        </div>

                    <?php endif; ?>

                </div>
            </div>
        </section>

        <section class="section">
			<div class="section__wrapper grid">
                <?php get_template_part('inc/parts/pagination'); ?>
			</div>
		</section>
	</main>
	<!-- /Content -->

<?php

	get_footer();

?>