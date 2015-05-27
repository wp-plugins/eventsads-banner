jQuery(document).ready(function($){
	$('.beb-eab-help-up').click(function() {
		$('html,body').animate({'scrollTop': 0},1000);
	});
	$('.beb-eab-banner-indice').click(function() {
		var beb_eab_offset = $('.' + $(this).attr('title')).offset();
		var beb_eab_destinazione = beb_eab_offset.top - 35;
		$('html,body').animate({'scrollTop': beb_eab_destinazione},1000);
	});
	
});