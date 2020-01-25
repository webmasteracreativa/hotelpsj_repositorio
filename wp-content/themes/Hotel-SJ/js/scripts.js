(function ($, root, undefined) {
	$(function () {
		if ( $("#separa").length > 0 ) {
			$(window).scroll(function() {
			    var windowHeight = $(window).scrollTop();
			    var contenido2 = $("#form").offset();
			    contenido2 = contenido2.top;

			    if (windowHeight >= contenido2) {
		    		$('#separa').addClass('mover');
			    }else{
		    		$('#separa').removeClass('mover');
			    }
			});
		}

		$('<div class="quantity-nav"><div class="quantity-button quantity-up">^</div><div class="quantity-button quantity-down">^</div></div>').insertAfter('.quantity input');
		$('.quantity').each(function() {
			var spinner = $(this),
			input = spinner.find('input[type="number"]'),
			btnUp = spinner.find('.quantity-up'),
			btnDown = spinner.find('.quantity-down'),
			min = input.attr('min'),
			max = input.attr('max');

			btnUp.click(function() {
				var oldValue = parseFloat(input.val());
				if (oldValue >= max) {
					var newVal = oldValue;
				} else {
					var newVal = oldValue + 1;
				}
				spinner.find("input").val(newVal);
				spinner.find("input").trigger("change");
			});

			btnDown.click(function() {
				var oldValue = parseFloat(input.val());
				if (oldValue <= min) {
					var newVal = oldValue;
				} else {
					var newVal = oldValue - 1;
				}
				spinner.find("input").val(newVal);
				spinner.find("input").trigger("change");
			});

		});
		
		//slick
		$('.carousel').slick({
			nextArrow: '<button type="button" class="slick-next">></button>',
			prevArrow: '<button type="button" class="slick-prev"><</button>',
			dots: true
		});
		$('.carousel-banner').slick({
			nextArrow: '<button type="button" class="slick-next">></button>',
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: 4000,
			prevArrow: '<button type="button" class="slick-prev"><</button>'
		});
		//carrusel de las habitaciones dentro de un tab

		function slickInit(){
			$('.carousel-habi').slick({
				nextArrow: '<button type="button" class="slick-next">></button>',
				prevArrow: '<button type="button" class="slick-prev"><</button>',
				dots: true
			});
		}
		slickInit();

		  $('a[data-toggle="tab"]').on("shown.bs.tab", function(e) {
		    $(".carousel-habi").slick("unslick");
		    slickInit();
		  });
		$('.footer-carousel').slick({
			loop:true,
			nextArrow: '<button type="button" class="slick-next">></button>',
			prevArrow: '<button type="button" class="slick-prev"><</button>',
			slidesToShow: 5,
			slidesToScroll: 1,
			  responsive: [
			    {
			      breakpoint: 768,
			      settings: {
			        slidesToShow: 3
			      }
			    },
			    {
			      breakpoint: 480,
			      settings: {
			        slidesToShow: 1
			      }
			    }
			  ]
		});
	    //map
	    function iniciarMap(){
		    var coord = {lat: 6.2470725 ,lng: -75.5893621};
		    var icono = 'https://www.hotelportonsj.com.co/wp-content/uploads/2019/11/icon-map.png';
		    var map = new google.maps.Map(document.getElementById('map'),{
		      zoom: 20,
		      center: coord
		    });
		    var marker = new google.maps.Marker({
		      position: coord,
		      map: map,
		      icon: icono
		    });
		}
		iniciarMap();
		
	// init Isotope
	var $grid = $('.grid').isotope({
	  itemSelector: '.grid-item',
	  percentPosition: true,
		masonry: {
	// use outer width of grid-sizer for columnWidth
		columnWidth: 350,
		gutter: 20,
			horizontalOrder: true
		}
	});

	// bind filter button click
	$('.filters').on( 'click', 'button', function() {
	  var filterValue = $( this ).attr('data-filter');
	  $grid.isotope({ filter: filterValue });
	});

	// change is-checked class on buttons
	$('.button-group').each( function( i, buttonGroup ) {
	  var $buttonGroup = $( buttonGroup );
	  $buttonGroup.on( 'click', 'button', function() {
	    $buttonGroup.find('.is-checked').removeClass('is-checked');
	    $( this ).addClass('is-checked');
	  });
	});

	$('.grid-galery').isotope({ filter: '.restaurante' });
	
	});
})(jQuery, this);

document.addEventListener("DOMContentLoaded", function() {
  let lazyImages = [].slice.call(document.querySelectorAll("img.lazy"));
  let active = false;

  const lazyLoad = function() {
    if (active === false) {
      active = true;

      setTimeout(function() {
        lazyImages.forEach(function(lazyImage) {
          if ((lazyImage.getBoundingClientRect().top <= window.innerHeight && lazyImage.getBoundingClientRect().bottom >= 0) && getComputedStyle(lazyImage).display !== "none") {
            lazyImage.src = lazyImage.dataset.src;
            lazyImage.srcset = lazyImage.dataset.srcset;
            lazyImage.classList.remove("lazy");

            lazyImages = lazyImages.filter(function(image) {
              return image !== lazyImage;
            });

            if (lazyImages.length === 0) {
              document.removeEventListener("scroll", lazyLoad);
              window.removeEventListener("resize", lazyLoad);
              window.removeEventListener("orientationchange", lazyLoad);
            }
          }
        });

        active = false;
      }, 200);
    }
  };

  document.addEventListener("scroll", lazyLoad);
  window.addEventListener("resize", lazyLoad);
  window.addEventListener("load", lazyLoad);
  window.addEventListener("orientationchange", lazyLoad);
});