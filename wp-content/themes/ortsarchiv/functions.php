<?php

	require_once 'inc/classes/theme/AppArtigThemeClass.php';

	class Theme extends AppArtigTheme {
		
		public function __construct($name){
			parent::__construct($name);

			$domain = parent::getDomain();

			// Fonts
			parent::addStyle('google-fonts', 'https://fonts.googleapis.com/css?family=Cardo|Josefin+Sans&display=swap', '1.0.0');

			// Photoswipe

			parent::addStyle('pswp', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.1/photoswipe.min.css', '4.1.1');
			parent::addStyle('pswp-ui', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.1/default-skin/default-skin.min.css', '4.1.1');
			parent::addScript('pswp', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.1/photoswipe.min.js', '4.1.1');
			parent::addScript('pswp-ui', 'https://cdnjs.cloudflare.com/ajax/libs/photoswipe/4.1.1/photoswipe-ui-default.min.js', '4.1.1');
			
			// Menu
			parent::addMenuLocation('primary', 'Hauptmenü');
	
			// Footer
			parent::addWidget(array(
				'name'          => "Footer",
				'id'            => "footer",
				'description' 	=> "",
				'before_widget' => "",
				'after_widget'  => "",
				'before_title'  => "",
				'after_title'   => ""
			));


			// Customizer
			parent::addCustomizerSetting('Logo', 'header_logo', 'image');


			// Fußnoten

			$footnote_number = 0;

			parent::actionAfterSetup(function() {
				add_shortcode('footnote', function($atts, $content = null) {
					global $footnote_number;
					$footnote_number++;
					return "<span class='footnote-inline footnote-inline--$footnote_number'><sup>$footnote_number</sup></span>";
				});
            });
		}
		

	}
	
	function getTheme(){ return new Theme(APPARTIG_THEME_NAME); }
	$theme = getTheme();


    

    
    
    /*********************************************************
    *** Customizer
    *********************************************************

	function aa_example_customize_register($wp_customize) {
		
		$wp_customize->add_section('header' , array(
			'title'      => __('Header', 'aa_aufrecht'),
			'priority'   => 30,
		));
		
		$wp_customize->add_setting('header_logo' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'header_logo',
				array(
					'label'      => __('Logo für Header', 'aa_aufrecht'),
					'section'    => 'header',
					'settings'   => 'header_logo',
					'context'    => 'header_logo_context' 
				)
			)
		);
		
		$wp_customize->add_section('home' , array(
			'title'      => __('Startseite', 'aa_aufrecht'),
			'priority'   => 30,
		));
		
		$wp_customize->add_setting('home_bg_image_cantienica' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_bg_image_cantienica',
				array(
					'label'      => __('Hintergrundbild Cantienica', 'aa_aufrecht'),
					'section'    => 'home',
					'settings'   => 'home_bg_image_cantienica',
					'context'    => 'home_bg_image_cantienica_context' 
				)
			)
		);
		
		$wp_customize->add_setting('home_bg_image_cranio' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_bg_image_cranio',
				array(
					'label'      => __('Hintergrundbild Cranio', 'aa_aufrecht'),
					'section'    => 'home',
					'settings'   => 'home_bg_image_cranio',
					'context'    => 'home_bg_image_cranio_context' 
				)
			)
		);
		
		$wp_customize->add_setting('home_bg_image_stundenplan' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_bg_image_stundenplan',
				array(
					'label'      => __('Hintergrundbild Stundenplan', 'aa_aufrecht'),
					'section'    => 'home',
					'settings'   => 'home_bg_image_stundenplan',
					'context'    => 'home_bg_image_stundenplan' 
				)
			)
		);
		
		$wp_customize->add_setting('home_bg_image_hello' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'home_bg_image_hello',
				array(
					'label'      => __('Hintergrundbild Willkommen', 'aa_aufrecht'),
					'section'    => 'home',
					'settings'   => 'home_bg_image_hello',
					'context'    => 'home_bg_image_hello_context' 
				)
			)
		);
	}

	add_action('customize_register', 'aa_example_customize_register');
*/

?>