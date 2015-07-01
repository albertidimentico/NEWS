$(function() {
	$(document)
		.on('focusin', '.field, textarea', function() {
			if(this.title==this.value) {
				this.value = '';
			}
			$(this).closest('fieldset').addClass('focused');
		})
		.on('focusout', '.field, textarea', function(){
			if(this.value=='') {
				this.value = this.title;
			}
			$(this).closest('fieldset').removeClass('focused');
		})
		// load more projects
		.on('click', 'a.load-more-posts', loadMoreProjects )


	// init the page
	pageInit();

	// on window load
	$(window).load(function(){
		if( !($.browser.msie && $.browser.version == 6) ){
			sliders();
		}

		// init the sidebar
		if( $('#sidebar').length ){
			var firstHeadingHeight = $('.post:eq(0) h2:eq(0)').outerHeight();
			$('#sidebar').css('margin-top', firstHeadingHeight);

			if( $('#sidebar').height() < $('.content').height() - firstHeadingHeight ){
				$('#sidebar').css('min-height', $('.content').height() - firstHeadingHeight);
			}
		}
	})

	var oldW = $(window).width();

	// on window resize
	$(window).resize(function(){
		sliders();

		var newW = $(window).width();

		// check if slider reinitalization is needed
		if( ( oldW > 768 && newW < 768 ) || ( oldW < 768 && newW > 768 ) && $('.slider').length ){
			$('.slider ul').roundabout('destroy');

			// roundabout - 3d slider in the header
			$('.slider ul').roundabout({
				minScale: 0.005,
				maxScale: 1,
				tilt: 4.4,
				minOpacity: 1,
				duration: 400,
				enableDrag: true,
				dragFactor: 7,
				responsive: true
			});

			oldW = $(window).width()
		}

	})

	
	/*
		FUNCTIONS
	============================ */
	function pageInit(){
		// fix png24 for ie6
		if( $.browser.msie && $.browser.version == 6 ){
			DD_belatedPNG.fix( '#logo span, a img, a.arr-link, .social-nav a, .feature figure img, .feature .feature-cnt span.arr, .post .meta .socials a' );
			sliders();
		}

		if( $('.slider').length ){
			// roundabout - 3d slider in the header
			$('.slider ul').roundabout({
				minScale: 0.005,
				maxScale: 1,
				tilt: 4.4,
				minOpacity: 1,
				duration: 400,
				enableDrag: true,
				dragFactor: 7,
				responsive: true
			});
		}

		resizer($('.landscape-section'), $('.landscape-section img.bg'));

		$('.responses ol li').each(function(){
			$(this).prepend('<span class="num">'+ ($(this).index() + 1) +'</span>')
		})

		// twitter posts
		if( $('#twitter-posts').length ){
			$("#twitter-posts").tweet({
				avatar_size: 0,
				count: 3,
				username: "seaofclouds",	// put your twitter name here
				template: '<span class="twitter-ico"></span><span class="time">{time}</span>{text}{join}'
			})
		}

		// call the tabs
		tabs();
		
		// call the accordion
		accordion();

		// if mobile device
		
		$('<div class="nav-overlay"/>').appendTo('body');
		$('<a href="#" class="navigation-trigger"/>').appendTo('body');
		$('#navigation').clone().addClass('mobile-nav').appendTo('body');

		// toggle mobile navigation
		$('a.navigation-trigger').on('click tap', function(){
			$('#navigation.mobile-nav').slideToggle('fast');
			$('.nav-overlay').css( 'height', $(document).height() );
			$('.nav-overlay').fadeToggle('fast');
			return false;
		})
		

		if( $(".para-part").length ){
			// mouse moving elements
			$(".para-part").each(function() {
				var leftPos = $(this).offset().left;
				
				$(this).css({
					margin: 0,
					left: leftPos
				});

				var newAlt = "";
				
				var initLeft = $(this).position().left;
				var initTop = $(this).position().top;
				
				newAlt = "l:"+ initLeft + ";t:" + initTop;
				
				$(this).data("coords", newAlt);
			});
				
			var mouse = function(){
				this.x = 0;
				this.y = 0;
			}
			
			var k1 = 0.04;
			var k2 = 0.01;
			
			$(document).mousemove(function getMouseXY(e) {
				if (document.all) {
					mouse.x = event.clientX + document.body.scrollLeft;
					mouse.y = event.clientY + document.body.scrollTop;
				} else {  
					mouse.x = e.pageX;
					mouse.y = e.pageY;
				}  
				
				if (mouse.x < 0){mouse.x = 0};
				if (mouse.y < 0){mouse.y = 0};
				
				var tree = $("body")
				var screenX = tree.offset().left + tree.width()/2;
				var screenY = tree.offset().top + tree.height()/2;
				
				$(".para-part").each(function() {
					var leafPos = $(this).data("coords");
					leafPos = {
						x: parseInt(leafPos.split(";")[0].split(":")[1]),
						y: parseInt(leafPos.split(";")[1].split(":")[1])
					};
					
					var distanceX = mouse.x - screenX;
					var distanceY = mouse.y - screenY;
					
					$(this).css({
						left: leafPos.x + k1 * distanceX * $(this).data("layer"),
						top: leafPos.y + k1 * distanceY * $(this).data("layer")
					});
				});
			
				return true;
			});
		}
	}


	// check for mobile
	function is_mobile() {
		return (
			(navigator.platform.indexOf("iPhone") != -1) ||
			(navigator.platform.indexOf("iPod") != -1) ||
			(navigator.userAgent.indexOf("ZuneWP") != -1) ||
			((navigator.userAgent.indexOf("Android") != -1))		
		);
	}

	// ride the caousels
	function sliders(){
		// posts slider
		if( $(".posts .carousel").length ){
			$(".posts .carousel").carouFredSel({
				items: 1,
				auto: false,
				pagination: ".posts-pagination",
				swipe: true,
				responsive: true
			});
		}

		// testimonials slider
		if( $(".testimonials .carousel").length ){
			$(".testimonials .carousel").carouFredSel({
				items: 1,
				auto: false,
				swipe: true,
				responsive: true,
				prev: {
					button: '.testimonials a.prev'
				},
				next: {
					button: '.testimonials a.next'
				}
			});
		}

		// posts slider
		if( $(".def-slider").length ){
			$(".def-slider").each(function(){
				var $slider = $(this),
					$prevLink = $slider.find('a.prev'),
					$nextLink = $slider.find('a.next'),
					$paging = $slider.find('.slider-nav');

				$slider.find(".carousel").carouFredSel({
					items: 1,
					auto: false,
					pagination: $paging,
					swipe: true,
					responsive: true,
					prev: {
						button: $prevLink
					},
					next: {
						button: $nextLink
					}
				});
			})
		}
	}

	// resize image and center it
	function resizer( $parent, $img ) {
		var ww = $parent.width(),
			wh = $parent.height(),
			iw = $img.width(),
			ih = $img.height(),
			rw = wh / ww,
			ri = ih / iw,
			newWidth, newHeight,
			newLeft, newTop,
			properties;

		if ( rw > ri ) {
			newWidth = wh / ri;
			newHeight = wh;
		} else {
			newWidth = ww;
			newHeight = ww * ri;
		}

		properties = {
			'width': newWidth + 'px',
			'height': newHeight + 'px',
			'top': 'auto',
			'bottom': 'auto',
			'left': '50%',
			'right': 'auto',
			'margin-left': '0'
		}

		properties[ 'top' ] = ( wh - newHeight ) / 2;
		properties[ 'margin-left' ] = -1 * newWidth / 2;

		$img.css( properties );
	}

	// loading more projects
	function loadMoreProjects(){
		var $self = $(this),
			href = $self.attr('href');

		$.ajax({
			url: href,
			data: 'POST',
			error: function(){
				alert('Error While Loading Elements');
			},
			success: function( data ){
				var newProjects = $('.project', data);
				newProjects
					.addClass('hidden')
					.appendTo('.projects-list .list');

				$('.project.hidden').fadeIn();

				var newHref = $('a.load-more-posts', data).attr('href');
				if( newHref != undefined && newHref != '' ){
					$self.attr('href')
				} else {
					$self.hide();
				}
			}
		})

		return false;
	}

	// tabs
	function tabs(){
		$('.tabs').each(function(){
			var $tabs = $(this);
			// init the tabs
			$tabs.find('.tabs-nav a:eq(0)').addClass('active');
			$tabs.find('.tab').hide().eq(0).show();

			// actions
			$tabs.find('.tabs-nav a').on('click', function(e){
				var $self = $(this),
					idx = $self.index();

				$self.addClass('active').siblings('.active').removeClass('active');

				$tabs.find('.tab').hide().eq( idx ).fadeIn();

				e.preventDefault();
			})
		})
	}

	function accordion(){
		$('.accordion h6').on('click', function(e){
			var $li = $(this).closest('li');
			
			$li
				.toggleClass('exp').find('.acc-cnt').slideToggle().end()
				.siblings('.exp').removeClass('exp').find('.acc-cnt').slideUp();

			e.preventDefault();
		})
	}
});