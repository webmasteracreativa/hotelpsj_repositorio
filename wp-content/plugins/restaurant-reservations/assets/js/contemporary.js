jQuery(document).ready(function($){
	$(function(){
		$(window).resize(function(){
			$('.rtb-booking-form form').each(function(){
				var thisForm = $(this);
				var formWidth = thisForm.width();
				if(formWidth < 900){
					thisForm.find('fieldset.reservation .rtb-select').css('left', '0');
					thisForm.find('fieldset.contact .rtb-text.phone').css('left', '0');
				}
				if(formWidth < 600){
					thisForm.find('fieldset.reservation .rtb-text.time').css('left', '0');
					thisForm.find('fieldset.contact .rtb-text.email').css('left', '0');
				}
				if(formWidth > 599){
					thisForm.find('fieldset.reservation .rtb-text.time').css('left', '-1px');
					thisForm.find('fieldset.contact .rtb-text.email').css('left', '-1px');
				}
				if(formWidth > 899){
					thisForm.find('fieldset.reservation .rtb-select').css('left', '-2px');
					thisForm.find('fieldset.contact .rtb-text.phone').css('left', '-2px');
				}
			});
		}).resize();
	}); 
});