function initAppArtigContactMap(){var e;if(e=document.getElementById("lewing_contact__map")){var t={lat:48.323917,lng:15.807997},n=new google.maps.Map(e,{zoom:12,center:t,scrollwheel:!1});new google.maps.Marker({position:t,map:n,title:"Ö-News"})}}jQuery(document).ready(function(){jQuery(".responsive-pull-close__button").click(function(){jQuery(".main-menu").toggleClass("main-menu--responsive-hidden")}),jQuery(".gallery .gallery-item a").simpleLightbox({captions:!0,captionSelector:"img",captionType:"attr",captionsData:"title",showCounter:!0}),initAppArtigContactMap()}),jQuery(window).on("resize",function(){});