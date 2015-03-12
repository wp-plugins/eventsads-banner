/*
 * MODIFICHE DA FARE:
 * 
 * - Fare in modo che jquery calcoli la lunghezza del banner se c'e o meno l'immagine...
 * ...va scritto anche sul CSS
 * 
 */
jQuery(document).ready(function($){
	var data = $('#beb-eab-banner-data').length;
	var immagine = $('#beb-eab-banner-immagine').length;
	var chiudi_banner_di = -323;
	if (data > 0) {
		chiudi_banner_di -= 2;
	}
	if (immagine > 0) {
		chiudi_banner_di -= 20;
	}
	
	var url_tmp = $("#beb-eab-banner-bottone").attr("src");
	url_tmp = url_tmp.split('/');
	delete url_tmp [0];
	delete url_tmp [url_tmp.length-1];
	var url = url_tmp.join('/');
	url = 'http:' + url; 
	var freccia_sx = url + 'freccia_sx.png';
	var freccia_dx = url + 'freccia_dx.png';
	
	if ($('#beb-eab-contenitore-banner').attr('title') == 'beb-eab-banner-aperto') {
		/* sto nella home page */
		var aperto = true;
		$('#beb-eab-banner-bottone').attr('src', freccia_dx);
		$('#beb-eab-contenitore-banner').css('right', '0');
	} else if ($('#beb-eab-contenitore-banner').attr('title') == 'beb-eab-banner-chiuso') {
		/* NON sto nella home page */
		var aperto = false;
		$('#beb-eab-banner-bottone').attr('src', freccia_sx);
		$('#beb-eab-contenitore-banner').css('right', '-308px');
	}
	$('#beb-eab-banner-bottone').on('click', function (){
		if (aperto == true) {
			/* Banner aperto --> lo chiudo */
			$('#beb-eab-contenitore-banner').animate({
				right: chiudi_banner_di+'px'
				}, 500 );
			$('#beb-eab-banner-bottone').attr('src', freccia_sx);
			aperto = false;
		} else if (aperto == false) {
			/* Banner chiuso --> lo apro */
			$('#beb-eab-banner-bottone').attr('src', freccia_dx);
			$('#beb-eab-contenitore-banner').animate({
				right: '0'
				}, 500 );
			aperto = true;
		}
	});
});
