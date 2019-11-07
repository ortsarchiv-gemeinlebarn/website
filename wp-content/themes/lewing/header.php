<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
    <head>
        <title><?php echo get_bloginfo('name'); wp_title(); ?></title>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="description" content="<?php echo get_bloginfo('name'); ?>" />
        
        <?php wp_head(); ?>
        
    </head>
    
    <body <?php body_class(); ?>>

    	<header class="header">
    		<div class="header__overlay">
    			<div class="header__wrapper">
    			
					<!-- Logo -->
					<a href="<?php echo get_home_url(); ?>" class="header__logo logo" alt="<?php echo get_bloginfo('name'); ?>">
						<div class="logo__image" style="background-image:url('<?php echo get_theme_mod('header_logo'); ?>');"></div>
					</a>
					<!-- /Logo -->
    			
					<!-- Main Menu -->
					<nav class="header__menu main-menu main-menu--responsive-hidden">
						
						<div class="responsive-pull-close">
							<div class="responsive-pull-close__button responsive-pull-close__button--pull">
								<svg height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"><path d="M0 0h48v48h-48z" fill="none"/><path d="M6 36h36v-4h-36v4zm0-10h36v-4h-36v4zm0-14v4h36v-4h-36z"/></svg>
							</div>
							<div class="responsive-pull-close__button responsive-pull-close__button--close responsive-pull-close__button--hide">
								<svg height="48" viewBox="0 0 48 48" width="48" xmlns="http://www.w3.org/2000/svg"><path d="M38 12.83l-2.83-2.83-11.17 11.17-11.17-11.17-2.83 2.83 11.17 11.17-11.17 11.17 2.83 2.83 11.17-11.17 11.17 11.17 2.83-2.83-11.17-11.17z"/><path d="M0 0h48v48h-48z" fill="none"/></svg>
							</div>
						</div>
					
						<?php
							wp_nav_menu(array(
								'theme_location' 	=> 'primary',
								'container_class'	=> 'menu',
								'menu_class'		=> 'menu__items'
							));
						?>
					</nav>
					<!-- /Main Menu -->
    			
				</div>
			</div>
    	</header>
    	<!-- /Header -->
    
    	<!-- Content -->
		<main class="main">