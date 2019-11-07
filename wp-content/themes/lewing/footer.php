
			<!-- Footer -->
			<footer class="footer section" style="background-image:url('<?php echo $footer_image; ?>');">
				<div class="footer__overlay section__overlay">
					<div class="footer__wrapper section__wrapper">
						
						<article class="gd gd--12 footer-logo">
							<img src="<?php echo get_theme_mod('footer_logo'); ?>" class="footer-logo__image" alt="">
						</article>
						
						<article class="gd gd--12 footer-widget">
							<?php
								if (is_active_sidebar('footer-row-1')) {
									dynamic_sidebar('footer-row-1');
								}
							?>
						</article>
						
						<article class="gd gd--12 footer-widget">
							<?php
								if (is_active_sidebar('footer-row-2')) {
									dynamic_sidebar('footer-row-2');
								}
							?>
						</article>				
					</div>
				</div>
			</footer>
			<!-- /Footer -->
        
        </main>
        <!-- /Content -->
    		
    	</div>
        
        <?php wp_footer(); ?>

    </body>
</html>