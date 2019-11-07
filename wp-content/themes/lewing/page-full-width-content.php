<?php 

	/* Template Name: Full Width Content */
	
	get_header();
	
	if (have_posts()) : while (have_posts()) : the_post();
		the_content();
	endwhile; endif;
?>

<div class="section pre">
	<div class="section__overlay">
		<div class="section__wrapper">
			<div class="gd gd--12">
				Pre
			</div>
		</div>
	</div>
</div>

	<div class="list-item">

		<div class="list-item__bg"></div>

		<div class="list-item__column list-item__relevanz">
			<span class="list-item__relevanz__n">
				# 1
			</span>
			<span class="list-item__relevanz__percent">
			78,30 %
			</span>
		</div>

		<div class="list-item__column list-item__content">
			<p class="list-item__aoid">GOA000279</p>
			<h2 class="list-item__title">Gemeinderatsprotokoll 11/2/1879</h2>

			<div class="list-item__tags">
				<div class="list-item__tag tag tag--h">
					<span class="tag__type">H</span>
					<span class="tag__name">Main</span>
				</div>
				<div class="list-item__tag tag tag--n">
					<span class="tag__type">N</span>
					<span class="tag__name">Gebäude</span>
				</div>
			</div>
		</div>
	</div>
	<div class="list-item">

		<div class="list-item__bg"></div>

		<div class="list-item__column list-item__relevanz">
			<span class="list-item__relevanz__n">
				# 1
			</span>
			<span class="list-item__relevanz__percent">
			78,30 %
			</span>
		</div>

		<div class="list-item__column list-item__content">
			<p class="list-item__aoid">GOA000279</p>
			<h2 class="list-item__title">Gemeinderatsprotokoll 11/2/1879</h2>

			<div class="list-item__tags">
				<div class="list-item__tag tag tag--h">
					<span class="tag__type">H</span>
					<span class="tag__name">Main</span>
				</div>
				<div class="list-item__tag tag tag--n">
					<span class="tag__type">N</span>
					<span class="tag__name">Gebäude</span>
				</div>
			</div>
		</div>
	</div>
	<div class="list-item">

		<div class="list-item__bg"></div>

		<div class="list-item__column list-item__relevanz">
			<span class="list-item__relevanz__n">
				# 1
			</span>
			<span class="list-item__relevanz__percent">
			78,30 %
			</span>
		</div>

		<div class="list-item__column list-item__content">
			<p class="list-item__aoid">GOA000279</p>
			<h2 class="list-item__title">Gemeinderatsprotokoll 11/2/1879</h2>

			<div class="list-item__tags">
				<div class="list-item__tag tag tag--h">
					<span class="tag__type">H</span>
					<span class="tag__name">Main</span>
				</div>
				<div class="list-item__tag tag tag--n">
					<span class="tag__type">N</span>
					<span class="tag__name">Gebäude</span>
				</div>
			</div>
		</div>
	</div>

<?php
	get_footer();

?>		