
function setPageArrows_videos() {
contentPages = Math.ceil($('#menuvideos').height() / 150) -1;
$(".page_up_videos, .page_down_videos").addClass("disabled");
	if (contentPage > 1) {
		$(".page_up_videos").removeClass("disabled");
	}
	if (contentPage < contentPages) {
		$(".page_down_videos").removeClass("disabled");
	}
}

var contentPage = 1;
var contentPages = 1;
$(document)
.ready(function() {
/* Page scroll */
$(".page_up_videos").click(function() {
	if (contentPage > 1) {
		contentPage--;
		$("#menuvideos").animate({
		"margin-top" : "+=150px"
		}, "slow",function() {
		setPageArrows_videos();
		});
	}
});
$(".page_down_videos").click(function() {
	if (contentPage < contentPages) {
		contentPage++;
		$("#menuvideos").animate({
		"margin-top" : "-=150px"
		}, "slow",function() {
		setPageArrows_videos();
		});
	}
});
setPageArrows_videos();
}); 