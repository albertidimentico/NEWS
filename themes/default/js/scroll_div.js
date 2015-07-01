	
	
	
	function setPageArrows() {
	contentPages = Math.ceil($('#contenido_map').height() / 150) -1;
	$(".page_up, .page_down").addClass("disabled");
		if (contentPage > 1) {
			$(".page_up").removeClass("disabled");
		}
		if (contentPage < contentPages) {
			$(".page_down").removeClass("disabled");
		}
	}

	var contentPage = 1;
	var contentPages = 1;
	$(document)
	.ready(function() {
	/* Page scroll */
	$(".page_up").click(function() {
		if (contentPage > 1) {
			contentPage--;
			$("#contenido_map").animate({
			"margin-top" : "+=150px"
			}, "slow",function() {
			setPageArrows();
			});
		}
	});
	$(".page_down").click(function() {
		if (contentPage < contentPages) {
			contentPage++;
			$("#contenido_map").animate({
			"margin-top" : "-=150px"
			}, "slow",function() {
			setPageArrows();
			});
		}
	});
	setPageArrows();
	}); 