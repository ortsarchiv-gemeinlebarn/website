<?php

    /*********************************************************
    *** Adding Sytelsheets & Scripts
    *********************************************************/

    add_theme_support('post-thumbnails');


    /*********************************************************
    *** Image Sizes
    *********************************************************/
    
    // Filter out 'medium_large'
    add_filter( 'intermediate_image_sizes', function($sizes){
        return array_filter( $sizes, function($val) {
            return 'medium_large' !== $val; 
        });
    });


    /*********************************************************
    *** AppArtig Login Style
    *********************************************************/

	function lewing_login_logo_image() {

?>
		<style type="text/css">
			#login h1 a, .login h1 a {
				background-image: url(<?php echo get_stylesheet_directory_uri(); ?>/img/appartig_logo.png);
				height: 46px;
    			width: 220px;
				background-size: contain;
				background-repeat: no-repeat;
				margin-bottom: 30px;
			}
		</style>
<?php
	
	}

	add_action('login_enqueue_scripts', 'lewing_login_logo_image');


	function lewing_login_logo_url() {
		return home_url();
	}

	add_filter('login_headerurl', 'lewing_login_logo_url');


	function lewing_login_logo_url_title() {
		return get_bloginfo() . ' by AppArtig e.U.';
	}

	add_filter('login_headertitle', 'lewing_login_logo_url_title' );
    

    /*********************************************************
    *** Adding Sytelsheets & Scripts
    *********************************************************/

    function lewing_enqueue_scripts_styles() {
		
		// CSS
		wp_enqueue_style('lewing__styles--simplelightbox', "//cdnjs.cloudflare.com/ajax/libs/simplelightbox/1.12.1/simplelightbox.min.css", '', '1.12.1');
		wp_enqueue_style('lewing__styles--theme-style', get_stylesheet_uri(), '', '1.0.8');
		
		// JS
        wp_enqueue_script('jquery');
        wp_enqueue_script('lewing__scripts--theme-app', get_template_directory_uri() . '/js/app.js', array(), '1.0.1', true);
        wp_enqueue_script('lewing__scripts--simplelightbox', '//cdnjs.cloudflare.com/ajax/libs/simplelightbox/1.12.1/simple-lightbox.min.js', array(), '1.12.1', true);
		wp_enqueue_script('lewing__scripts--maps', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyBLlcysCV9jci8o4AdOfi5AqE7A6CG7Tq8', array(), '1.0.0', true);
    }

    add_action('wp_enqueue_scripts', 'lewing_enqueue_scripts_styles');


    /*********************************************************
    *** Opengraph Tags
    *********************************************************/

	function lewing_fb_opengraph() {
		
		global $post;

		// Startseite
		if(is_front_page()) {
			$title   = get_bloginfo();
			$excerpt = get_bloginfo('description');
			$img_src = get_stylesheet_directory_uri() . '/img/opengraph_image.jpg';
			$url 	 = get_the_permalink();
			
		// Post
		} elseif(is_single()) {
			
			$title   = get_the_title();
			$url 	 = get_the_permalink();
			
			if(has_post_thumbnail($post->ID)) {
				if ($tmp_img = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large')){
					$img_src = $tmp_img[0];
				}else{
					$img_src = get_stylesheet_directory_uri() . '/img/opengraph_image.jpg';
				}
			} else {
				$img_src = get_stylesheet_directory_uri() . '/img/opengraph_image.jpg';
			}
			
			if($excerpt = $post->post_excerpt) {
				$excerpt = strip_tags($post->post_excerpt);
				$excerpt = str_replace("", "'", $excerpt);
			} else {
				$excerpt = get_bloginfo('description');
			}
		
		// Fallback
		} else {
					
			$title   = get_bloginfo();
			$excerpt = get_bloginfo('description');
			$img_src = get_stylesheet_directory_uri() . '/img/opengraph_image.jpg';
			$url 	 = get_the_permalink();
			
		}
	
	?>

		<meta property="og:title" content="<?php echo $title; ?>"/>
		<meta property="og:description" content="<?php echo $excerpt; ?>"/>
		<meta property="og:type" content="article"/>
		<meta property="og:url" content="<?php echo $url; ?>"/>
		<meta property="og:site_name" content="<?php echo get_bloginfo(); ?>"/>
		<meta property="og:image" content="<?php echo $img_src; ?>"/>
		<meta property="article:publisher" content="https://www.facebook.com/122226564504071/"/>

	<?php
		
	}
    add_action('wp_head', 'lewing_fb_opengraph', 5);
  

    /*********************************************************
    *** Google Structured Data
    *********************************************************/

	function lewing_google_structured_data() {

        if (is_front_page()){

            $posts = wp_get_recent_posts(array(
                'numberposts' => 5,
                'orderby' => 'post_date',
                'post_status' => 'publish'
            ), OBJECT);
?>
            <script type="application/ld+json">
                {
                    "@context": "http://schema.org",
                    "@type": "BreadcrumbList",
                    "itemListElement": [{
                        "@type": "ListItem",
                        "position": 1,
                        "item": {
                            "@id": "<?php echo get_home_url(); ?>",
                            "name": "Aktuelle News"
                        }
                    }]
                }
            </script>

            <script type="application/ld+json">
                {
                    "@context":"http://schema.org",
                    "@type":"ItemList",
                    "itemListElement":[
<?php       
            $i=0;
            foreach($posts as $post){

                $i++;

                $title   = $post->post_title;
                $url 	 = get_the_permalink($post->ID);

                $img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large')[0];

                $excerpt = strip_tags($post->post_excerpt);
                $excerpt = str_replace("", "'", $excerpt);
                $excerpt = preg_replace( "/\r|\n/", "", $excerpt);

?>
                    {
                        "@type":"ListItem",
                        "position":<?php echo $i; ?>,
                        "item":{
                            "@type": "NewsArticle",
                            "url": "<?php echo $url; ?>",
                            "mainEntityOfPage": {
                                "@type": "WebPage",
                                "@id": "<?php echo $url; ?>"
                            },
                            "headline": "<?php echo $title; ?>",
                            "image": [
                                "<?php echo $img_src; ?>"
                            ],
                            "datePublished": "<?php _e($post->post_date, 'lewing'); ?>",
                            "dateModified": "<?php _e($post->post_modified, 'lewing'); ?>",
                            "author": {
                                "@type": "Person",
                                "name": "<?php _e(get_the_author_meta('first_name', $post->post_author).' '.get_the_author_meta('last_name', $post->post_author), 'lewing'); ?>"
                            },
                            "publisher": {
                                "@type": "Organization",
                                "name": "<?php echo get_bloginfo(); ?>",
                                "logo": {
                                "@type": "ImageObject",
                                "url": "http://www.oe-news.at/wp-content/uploads/2018/02/logo_header.png"
                                }
                            },
                            "description": "<?php echo $excerpt; ?>"
                        }
                    } <?php if ($i<5) echo ','; ?>
<?php
            }
?>
                    ]
                }
            </script>
<?php
        }else if(is_singular('post')){
		
            global $post;

            $title   = $post->post_title;
			$url 	 = get_the_permalink();
			
			$img_src = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'large')[0];
			
            $excerpt = strip_tags($post->post_excerpt);
            $excerpt = str_replace("", "'", $excerpt);
            $excerpt = preg_replace( "/\r|\n/", "", $excerpt);

            $cat = get_the_category()[0];
            $cat_link = str_replace("./", "", get_category_link($cat));
            $cat_name = $cat->name;

?>

<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "item": {
                "@id": "<?php _e($cat_link, 'lewing'); ?>",
                "name": "<?php _e($cat_name, 'lewing'); ?>"
            }
        },{
            "@type": "ListItem",
            "position": 2,
            "item": {
                "@id": "<?php _e($url, 'lewing'); ?>",
                "name": "<?php _e($title, 'lewing'); ?>"
            }
        }]
    }
</script>
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "NewsArticle",
  "url": "<?php echo $url; ?>",
  "mainEntityOfPage": {
    "@type": "WebPage",
    "@id": "<?php echo $url; ?>"
  },
  "headline": "<?php echo $title; ?>",
  "image": [
    "<?php echo $img_src; ?>"
   ],
  "datePublished": "<?php _e($post->post_date, 'lewing'); ?>",
  "dateModified": "<?php _e($post->post_modified, 'lewing'); ?>",
  "author": {
    "@type": "Person",
    "name": "<?php _e(get_the_author_meta('first_name', $post->post_author).' '.get_the_author_meta('last_name', $post->post_author), 'lewing'); ?>"
  },
   "publisher": {
    "@type": "Organization",
    "name": "<?php echo get_bloginfo(); ?>",
    "logo": {
      "@type": "ImageObject",
      "url": "http://www.oe-news.at/wp-content/uploads/2018/02/logo_header.png"
    }
  },
  "description": "<?php echo $excerpt; ?>"
}
</script>

<?php
        } else if (is_category()){

            $cat = get_the_category()[0];
            $cat_link = str_replace("./", "", get_category_link($cat));
            $cat_name = $cat->name;

?>

<script type="application/ld+json">
    {
        "@context": "http://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [{
            "@type": "ListItem",
            "position": 1,
            "item": {
                "@id": "<?php _e($cat_link, 'lewing'); ?>",
                "name": "<?php _e($cat_name, 'lewing'); ?>"
            }
        }]
    }
</script>
<?php
        }
		
    }
    
    add_action('wp_head', 'lewing_google_structured_data', 5);  


    /*********************************************************
    *** Widgets
    *********************************************************/

    function lewing_init_widgets() {
		
        register_sidebar(array(
            'name'          => 'Footer Row 1',
            'id'            => 'footer-row-1',
			'description' 	=> '',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => ''
        ));
		
        register_sidebar(array(
            'name'          => 'Footer Row 2',
            'id'            => 'footer-row-2',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => ''
        ));
    }

    add_action('widgets_init', 'lewing_init_widgets');


    /*********************************************************
    *** Gallery Target Hook
    *********************************************************/

	function lewing_gallery_shortcode_link($out) {
		$out['link'] = 'file';
		return $out;
	}

    add_filter('shortcode_atts_gallery', 'lewing_gallery_shortcode_link');


    /*********************************************************
    *** Gallery Link to 'large' Image Size
    *********************************************************/

    function lewing_attachment_link_filter($content, $post_id, $size, $permalink) {
        if (! $permalink) {
            $image = wp_get_attachment_image_src( $post_id, 'large' );
            $new_content = preg_replace('/href=\'(.*?)\'/', 'href=\'' . $image[0] . '\'', $content );
            return $new_content;
        } else {
            return $content;
        }
    }

    add_filter('wp_get_attachment_link', 'lewing_attachment_link_filter', 10, 4);


    /*********************************************************
    *** Gallery Add Caption to Images as Attribute
    *********************************************************/

	function lewing_gallery_add_caption_as_attr($atts, $attachment) {
		$atts['title'] = get_post_field('post_excerpt', $attachment->ID);
		return $atts;
	}

	add_filter('wp_get_attachment_image_attributes', 'lewing_gallery_add_caption_as_attr', 10, 2);


    /*********************************************************
    *** Navigation
    *********************************************************/
    
    function lewing_register_menus() {
        register_nav_menu('primary', __('Hauptmenü', 'lewing'));
    }

    add_action('after_setup_theme', 'lewing_register_menus');


    /*********************************************************
    *** Customizer
    *********************************************************/

	function lewing_customize_register($wp_customize) {
		
		$wp_customize->add_section('oenews' , array(
			'title'      => __('Lewing', 'lewing'),
			'priority'   => 10,
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
					'label'      => __('Logo für Header', 'lewing'),
					'section'    => 'oenews',
					'settings'   => 'header_logo',
					'context'    => 'header_logo_context' 
				)
			)
		);
		
		$wp_customize->add_setting('footer_logo' , array(
			'default'   => '',
			'transport' => 'refresh',
		));
		
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'footer_logo',
				array(
					'label'      => __('Logo für Footer', 'lewing'),
					'section'    => 'oenews',
					'settings'   => 'footer_logo',
					'context'    => 'footer_logo_context' 
				)
			)
		);
	}

	add_action('customize_register', 'lewing_customize_register');

	
?>