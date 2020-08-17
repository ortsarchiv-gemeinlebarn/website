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

        <!-- Header -->
        <header class="header">

            <div class="header__quicklinks">
                <div class="quicklinks__titel">Digitale Services des Ortsarchivs Gemeinlebarn</div>
                <nav class="quicklinks__links">
                    <a href="https://suche.ortsarchiv-gemeinlebarn.org" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><title>ionicons-v5-f</title><path d="M221.09,64A157.09,157.09,0,1,0,378.18,221.09,157.1,157.1,0,0,0,221.09,64Z" style="fill:none;stroke:#000;stroke-miterlimit:10;stroke-width:32px"/><line x1="338.29" y1="338.29" x2="448" y2="448" style="fill:none;stroke:#000;stroke-linecap:round;stroke-miterlimit:10;stroke-width:32px"/></svg>
                        <span>Öffentliche Suchmaschine</span>
                    </a>
                    <a href="https://findbuch.ortsarchiv-gemeinlebarn.org" class="active" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><title>ionicons-v5-h</title><path d="M256,160c16-63.16,76.43-95.41,208-96a15.94,15.94,0,0,1,16,16V368a16,16,0,0,1-16,16c-128,0-177.45,25.81-208,64-30.37-38-80-64-208-64-9.88,0-16-8.05-16-17.93V80A15.94,15.94,0,0,1,48,64C179.57,64.59,240,96.84,256,160Z" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><line x1="256" y1="160" x2="256" y2="448" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/></svg>
                        <span>Findbuch</span>
                    </a>
                    <a href="https://www.ortsarchiv-gemeinlebarn.org" target="_blank">
                        <svg xmlns="http://www.w3.org/2000/svg" width="512" height="512" viewBox="0 0 512 512"><title>ionicons-v5-l</title><rect x="32" y="96" width="64" height="368" rx="16" ry="16" style="fill:none;stroke:#000;stroke-linejoin:round;stroke-width:32px"/><line x1="112" y1="224" x2="240" y2="224" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><line x1="112" y1="400" x2="240" y2="400" style="fill:none;stroke:#000;stroke-linecap:round;stroke-linejoin:round;stroke-width:32px"/><rect x="112" y="160" width="128" height="304" rx="16" ry="16" style="fill:none;stroke:#000;stroke-linejoin:round;stroke-width:32px"/><rect x="256" y="48" width="96" height="416" rx="16" ry="16" style="fill:none;stroke:#000;stroke-linejoin:round;stroke-width:32px"/><path d="M422.46,96.11l-40.4,4.25c-11.12,1.17-19.18,11.57-17.93,23.1l34.92,321.59c1.26,11.53,11.37,20,22.49,18.84l40.4-4.25c11.12-1.17,19.18-11.57,17.93-23.1L445,115C443.69,103.42,433.58,94.94,422.46,96.11Z" style="fill:none;stroke:#000;stroke-linejoin:round;stroke-width:32px"/></svg>
                        <span>Informationen, Termine & Projekte</span>
                    </a>                
                </nav>
            </div>

            <div class="header__main">

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
        </div>
            