jQuery(document).ready(function($){
	$(function(){
		$(window).resize(function(){
			$('.rtb-booking-form form button').each(function(){
				var thisButton = $(this);
				var buttonHalfWidthBig = ( thisButton.width() / 2 ) + 56;
				var buttonHalfWidthSmall = ( thisButton.width() / 2 ) + 28;
				if( $(window).width() > 768 ){
					thisButton.css('margin-left', 'calc(50% - '+buttonHalfWidthBig+'px');
				}
				else{
					thisButton.css('margin-left', 'calc(50% - '+buttonHalfWidthSmall+'px');
				}
			});
		}).resize();
	}); 
});