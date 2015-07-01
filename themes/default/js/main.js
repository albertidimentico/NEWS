function mobileCheck(){
	var winWidth=$(window).width();
	if ($('.gallery').length) {
		$('.gallery .pics ul').cycle(1).cycle(0);
	}
	if ($(".needmove").length) {
		if (winWidth<767) {
			$(".questions-wrapper").after($("#sidebar").addClass("moved"));
		} else {
			$("#body > .container:first-child").append($("#sidebar.moved").removeClass("moved"));
		}
	}
}
$(window).load(function() {

	mobileCheck();
	$(window).resize(function() {
		mobileCheck();
	});
	
	$('.slider').jCarouselLite({
		btnNext: '#gallery .arrow-next',
		btnPrev: '#gallery .arrow-prev',
		visible: 3
	});
	
	if ($('.gallery').length) {
		function onAfter() {
			var $ht = $(this).find("img").height();
			$(this).parent().css({height: $ht});
		}
		$('.gallery .pics ul').cycle({ 
			fx:     'fade', 
			speed:   500, 
			containerResize: 0,
			slideResize: 0,
			after: onAfter,
			timeout: 0,
			pager:  '.gallery .thumbs', 
			pagerAnchorBuilder: function(idx, slide) { 
				return '.thumbs a:eq(' + idx + ')'; 
			} 
		});
	}
	
	$('<div class="nav-overlay"/>').appendTo('body');
	
	// toggle mobile navigation
	$('.menu-trigger').on('click tap', function(){
		$('#menu-mobile').slideToggle('fast');
		$('.nav-overlay').fadeToggle('fast');
		return false;
	})
	
	$('.nav-overlay').on('click tap', function(){
		$('#menu-mobile').slideUp('fast');
		$('.nav-overlay').fadeOut('fast');
		return false;
	})

});