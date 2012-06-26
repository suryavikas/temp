$(document).bind('click', function(event) {
	$(this).unbind(event);
	$('.promo-banner').hide();	
		
	if (event.target.id == 'promo_banner') {
		return true;
	} else {
		return false;
	}
});