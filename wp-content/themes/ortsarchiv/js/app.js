(($) => {

	$(document).ready(() => {

		/*** Responsive Menu - Button Calls ***/
		$('.burger').click(() => {
			if(!$('.main-menu').hasClass('responsive-open') ){ openMenu(); } 
			else { closeMenu(); }
		});

		$('.menu li a').click(() => {
			closeMenu();
		});
		/*** Responsive Menu - Button Calls ***/

		adjustFussnoten();

		var $pswp = $('.pswp')[0];
		var $pswp_index = 0;
		var $pswp_items = [];

		var $pswp_options = {
			index: 0,
			bgOpacity: 0.7,
			showHideOpacity: true
		}


		$('.wp-block-image').each(function (){

			var $pic = $(this);
			var $img = $pic.find('img');

			$img.load(function() {
				$pic.attr('data-pswp-index', $pswp_index);
				$pswp_index++;

				var lh = $img.width() / $img.height();
				var f = 1.4;

				$pswp_items.push({
					src: $img.attr('src'),
					w: ($(window).innerHeight() / f) * lh,
					h: $(window).innerHeight() / f,
					author: "",
					title: $pic.find('figcaption').html()
				});
			});
		});

		$('.wp-block-image').click(function (e){

			e.preventDefault();

			var $pic = $(this);
			$pswp_options.index = $pic.data('pswp-index');
			 
			// Initialize PhotoSwipe
			var lightBox = new PhotoSwipe($pswp, PhotoSwipeUI_Default, $pswp_items, $pswp_options);
			lightBox.init();
		});
	});

	$(window).on('resize', () => {
		adjustFussnoten();
	});	

	adjustFussnoten = () => {
		
		var offset = 20;
		var next = 0;

		$('.footnote-inline').each((i, obj) => {
			number = i+1;

			var t = $(obj).position().top;
			var h = $('.footnote-side--' + number).outerHeight();
			var mt = t - offset - next;

			if (mt < 0) mt = 0;
			$('.footnote-side--' + number).css('margin-top', mt);

			next = mt + h + next;
		});
		
		
	}


	/*** Responsive Menu - Funktionen ***/
	openMenu = () => {

		$('.main-menu').addClass('responsive-open');
		$('.x, .y, .z').addClass('collapse');
		
		setTimeout(function(){ 
			$('.burger .y').hide(); 
			$('.burger .x').addClass('rotate30'); 
			$('.burger .z').addClass('rotate150'); 
		}, 70);
		setTimeout(function(){
			$('.burger .x').addClass('rotate45'); 
			$('.burger .z').addClass('rotate135');  
		}, 120);

	}

	closeMenu = () =>{

		$('.main-menu').removeClass('responsive-open');
		
		$('.x').removeClass('rotate45').addClass('rotate30'); 
		$('.z').removeClass('rotate135').addClass('rotate150');
		
		setTimeout(function(){ 			
			$('.x').removeClass('rotate30'); 
			$('.z').removeClass('rotate150'); 			
		}, 50);

		setTimeout(function(){
			$('.y').show(); 
			$('.x, .y, .z').removeClass('collapse');
		}, 70);													
		
	}
	/*** /Responsive Menu - Funktionen ***/

})(jQuery);

