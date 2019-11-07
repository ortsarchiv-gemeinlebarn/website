<?php global $theme; $theme = getTheme(); ?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

    <head>
        <title><?php echo get_bloginfo('name'); wp_title(); ?></title>
        <meta charset="<?php bloginfo('charset'); ?>" >
        <meta name="viewport" content="width=device-width, initial-scale=1">
        
        <?php wp_head(); ?>
        
    </head>
    
    <body <?php body_class(); ?>>
        
        <!-- Page -->
        <div class="page">
        
            <!-- Page Overlay -->
            <div class="page__overlay">

                <!-- Header -->
                <header class="header">

                    <!-- Logo -->
                    <a class="header__logo logo" href="<?php echo get_home_url(); ?>" alt="<?php echo get_bloginfo('name'); ?>">
                        <div class="logo__image" style="background-image:url('<?php echo get_theme_mod("{$theme->getDomain()}_theme_customize_setting_header_logo"); ?>');"></div>
                    </a>
                    <!-- /Logo -->
                
                    <!-- Main Menu -->
                    <nav class="header__menu main-menu">
                        
                        <div class="circle"></div>

                        <!-- Burger -->
                        <div class="burger">
                            <div class="x"></div>
                            <div class="y"></div>
                            <div class="z"></div>
                        </div>
                        <!-- /Burger -->
                        
                        <!-- Menu -->
                        <?php apply_filters('show_menu', 'primary'); ?>
                        <!-- /Menu -->

                    </nav>
                    <!-- /Main Menu -->
                    
                </header>
                <!-- /Header -->