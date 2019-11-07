<?php 
	
	get_header();

    $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
?>

	<!-- Content -->
	<main class="content" style="background-image:url(<?php echo get_the_post_thumbnail_url(); ?>);">

		<section class="section">
			<div class="section__overlay">
				<h1><?php echo $curauth->nickname; ?></h1>
			</div>
		</section>

		<section class="section">
            <div class="section__wrapper">
                <h2>Der Autor</h2>
                <p>Website: <a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></p>
                <p><?php echo $curauth->user_description; ?></p>
            </div>
        </section>

        <section class="section">
            <div class="section__wrapper">

                <h2>Beiträge des Autors</h2>

                <!-- The Loop -->
                <ul>

                    <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                        <li>
                            <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
                            <?php the_title(); ?></a>,
                            <?php the_time('d M Y'); ?> in <?php the_category('&');?>
                        </li>

                    <?php endwhile; else: ?>
                    
                        <p><?php _e('No posts by this author.'); ?></p>

                    <?php endif; ?>
                </ul>
                <!-- End Loop -->

            </div>
		</section>

	</main>
	<!-- /Content -->

<?php 

	get_footer();

?>