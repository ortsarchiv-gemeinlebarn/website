jQuery(document).ready(function(){
	
	jQuery('.responsive-pull-close__button').click(function(){
		jQuery('.main-menu').toggleClass('main-menu--responsive-hidden');
	});

	jQuery('.gallery .gallery-item a').simpleLightbox({
		captions: true,
		captionSelector: 'img',
		captionType: 'attr',
        captionsData: 'title',
        showCounter: true
	});
	
	initAppArtigContactMap();
	
});

jQuery(window).on('resize', function(){
		
});

function initAppArtigContactMap(){
	
	var dom;
	if (dom = document.getElementById("lewing_contact__map")){

		var lewing_contact__latlng = {lat: 48.323917, lng: 15.807997};

		var lewing_contact__map = new google.maps.Map(dom, {
			zoom: 12,
			center: lewing_contact__latlng,
			scrollwheel: false,
			//styles: [ { "featureType": "water", "elementType": "geometry", "stylers": [ { "color": "#e9e9e9" }, { "lightness": 17 } ] }, { "featureType": "landscape", "elementType": "geometry", "stylers": [ { "color": "#f5f5f5" }, { "lightness": 20 } ] }, { "featureType": "road.highway", "elementType": "geometry.fill", "stylers": [ { "color": "#ffffff" }, { "lightness": 17 } ] }, { "featureType": "road.highway", "elementType": "geometry.stroke", "stylers": [ { "color": "#ffffff" }, { "lightness": 29 }, { "weight": 0.2 } ] }, { "featureType": "road.arterial", "elementType": "geometry", "stylers": [ { "color": "#ffffff" }, { "lightness": 18 } ] }, { "featureType": "road.local", "elementType": "geometry", "stylers": [ { "color": "#ffffff" }, { "lightness": 16 } ] }, { "featureType": "poi", "elementType": "geometry", "stylers": [ { "color": "#f5f5f5" }, { "lightness": 21 } ] }, { "featureType": "poi.park", "elementType": "geometry", "stylers": [ { "color": "#dedede" }, { "lightness": 21 } ] }, { "elementType": "labels.text.stroke", "stylers": [ { "visibility": "on" }, { "color": "#ffffff" }, { "lightness": 16 } ] }, { "elementType": "labels.text.fill", "stylers": [ { "saturation": 36 }, { "color": "#333333" }, { "lightness": 40 } ] }, { "elementType": "labels.icon", "stylers": [ { "visibility": "off" } ] }, { "featureType": "transit", "elementType": "geometry", "stylers": [ { "color": "#f2f2f2" }, { "lightness": 19 } ] }, { "featureType": "administrative", "elementType": "geometry.fill", "stylers": [ { "color": "#fefefe" }, { "lightness": 20 } ] }, { "featureType": "administrative", "elementType": "geometry.stroke", "stylers": [ { "color": "#fefefe" }, { "lightness": 17 }, { "weight": 1.2 } ] } ]
		});

		var lewing_contact__marker = new google.maps.Marker({
			position: lewing_contact__latlng,
			map: lewing_contact__map,
			title: "Ö-News"
		});
		
	}
}