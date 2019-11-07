<?php 
	
	get_header();

	$author = get_queried_object();

	if ($meta = get_user_meta($author->data->ID, 'oen_user_avatar')){
		if ($tmp_img = wp_get_attachment_image_src($meta[0])){
			$img = $tmp_img[0];
		}else{
			$img = '';
		}
	}

	$name = $author->first_name . ' ' . $author->last_name;
	$bio = get_the_author_meta('description', $author->data->ID);

?>

	<section class="section">
		<div class="section__overlay">
			<div class="section__wrapper">
				<div class="gd gd--12">
					<div class="author author--page">
						<div class="author__image">
							<div class="author__image-inner" style="background-image:url(<?php echo $img; ?>);"></div>
						</div>
						<div class="author__content">
							<h1><?php _e($name, 'lewing'); ?></h1>
							<p><?php _e($bio, 'lewing'); ?></p>
						</div>
					</div>
				</div>
			</div>
			<div class="section__wrapper">
				<article class="gd gd--12">
					<h2>Artikel von <?php _e($name, 'lewing'); ?></h2>
				</article>
				
				<?php 
				
					if (have_posts()) : while (have_posts()) : the_post();
						get_template_part('parts/item-only-text');
					endwhile;
					
					else:
				?>
				
				<article class="gd gd--12">
					<p><?php _e($name, 'lewing'); ?> hat bisher noch keine Beiträge veröffentlicht.</p>
				</article>
				
				<?php 
					endif;
				?>
			</div>
		</div>
	</section>

<?php

	get_footer();

?>