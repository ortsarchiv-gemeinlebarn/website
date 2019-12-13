<?php

    class AppArtigTheme {

        /*********************************************************
        *** Theme Settings
        *********************************************************/

        public $prefix = 'appartig';
        public $name = '';
        public $folder = '';

        /*********************************************************
        *** Konstruktor
        *********************************************************/

        function __construct($name){

            $this->name = $name;
            
            $this->addSupport('post-thumbnails');

            
            /*** Styles und Scripts ***/

            $this->addAction('wp_enqueue_scripts', function() {
                $this->addStyle('theme-stylesheet', get_stylesheet_uri(), '1.0.1');
                $this->addScript('jquery');
                $this->addScript('app', get_template_directory_uri() . '/js/app.min.js', '1.0.1');
                $this->addScript('simplemodal', get_template_directory_uri() . '/js/jquery.simplemodal.1.4.4.min.js', '1.0.0');
            });


            /*********************************************************
            *** Show/Use in Template Files
            *********************************************************/

            $this->actionAfterSetup(function() {
                add_filter('show_menu', function($slug){
                    $this->showMenu($slug);
                });
            });
            
            $this->actionAfterSetup(function() {
                add_filter('show_widget', function($slug){
                    $this->showWidget($slug);
                });
            });

        }

        public function __($text){
            return __($text, $this->getDomain());
        }
        
        public function e($text){
            echo $this->__($text);
        }

        public function getPostType($slug){
            if ($slug == 'post') return $this->__("Beitrag");
            if ($slug == 'page') return $this->__("Seite");
            if ($slug == 'attachments') return $this->__("Anhang");
            return $this->__("Unbekannter Typ");
        }

        public function getDomain(){
            return "{$this->prefix}_{$this->name}";
        }

        public function actionAfterSetup($function) {
            $this->addAction('after_setup_theme', $function);
        }


        /*********************************************************
        *** Wordpress Functions - Styles / Scripts
        *********************************************************/

        public function addStyle($name, $path, $version) {
            wp_enqueue_style("{$this->getDomain()}_$name", $path, null, $version);
        }

        public function addScript($name, $path = null, $version = null) {
            if ($path && $version){
                wp_enqueue_script("{$this->getDomain()}_$name",$path, null, $version);
            }else{
                wp_enqueue_script($name);
            }
        }


        /*********************************************************
        *** Wordpress Functions - Action Hooks / Theme Support 
        *********************************************************/

        /*** Add Action ***/

        private function addAction($name, $function) {
            add_action($name, function() use ($function) {
                $function();
            });
        }

        /*** Add Support ***/

        public function addSupport($feature, $options = null) {
            $this->actionAfterSetup(function() use ($feature, $options) {
                if ($options){
                    add_theme_support($feature, $options);
                } else {
                    add_theme_support($feature);
                }
            });
        }


        /*********************************************************
        *** Menus
        *********************************************************/

        public function addMenuLocation($slug, $name) {
            $this->actionAfterSetup(function() use ($slug, $name){
                register_nav_menu("{$this->getDomain()}_$slug", $this->__($name));
            });
        }

        public function showMenu($slug) {
            wp_nav_menu(array(
                'theme_location' 	=> "{$this->getDomain()}_$slug",
                'container_class'	=> 'menu',
                'menu_class'		=> 'menu__items'
            ));
        }

        

        /*********************************************************
        *** Widgets
        *********************************************************/

        public function addWidget($widget = array()) {
            $this->addAction('widgets_init', function() use ($widget){
                $widget['id'] = "{$this->getDomain()}_{$widget['id']}";
                register_sidebar($widget);
            });
        }

        public function showWidget($slug) {
            if (is_active_sidebar("{$this->getDomain()}_$slug")) {
                dynamic_sidebar("{$this->getDomain()}_$slug");
            }
        }
        
        
        
        /*********************************************************
        *** Customizer
        *********************************************************/

        public function addCustomizerSetting($label, $slug, $type, $defaultValue = '') {
            add_action('customize_register', function($wp_customize) use ($label, $slug, $type, $defaultValue) {
                
                $wp_customize->add_section("{$this->getDomain()}_theme_customize_section" , array(
                    'title'      => $this->__("{$this->name} Benutzer-Einstellungen"),
                    'priority'   => 10
                ) );

                $wp_customize->add_setting("{$this->getDomain()}_theme_customize_setting_{$slug}" , array(
                    'default'   => $defaultValue
                ) );

                if ($type == 'image'){
                    $wp_customize->add_control(new WP_Customize_Image_Control($wp_customize, "{$this->getDomain()}_theme_customize_setting_{$slug}", array(
                            'label'      => $this->__($label),
                            'section'    => "{$this->getDomain()}_theme_customize_section",
                            'settings'   => "{$this->getDomain()}_theme_customize_setting_{$slug}",
                        )));
                }else{
                    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $type, array(
                        'label'      => $this->__($label),
                        'section'    => "{$this->getDomain()}_theme_customize_section",
                        'settings'   => "{$this->getDomain()}_theme_customize_setting_{$slug}",
                    )));
                }

                
            });
        }
    }

?>