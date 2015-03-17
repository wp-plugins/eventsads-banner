/*
 * MODIFICHE DA FARE:
 * 
 * - Fare in modo che jquery calcoli la lunghezza del banner se c'e o meno l'immagine...
 * ...va scritto anche sul CSS
 * 
 */
jQuery(document).ready(function($){
	var beb_eab_data = $('#beb-eab-banner-data').length;
	var beb_eab_immagine = $('#beb-eab-banner-immagine').length;
	var beb_eab_chiudi_banner_di = -323;
	if (beb_eab_data > 0) {
		beb_eab_chiudi_banner_di -= 2;
	}
	if (beb_eab_immagine > 0) {
		beb_eab_chiudi_banner_di -= 20;
	}
	
	var beb_eab_url_tmp = $("#beb-eab-banner-bottone").attr("src");
	beb_eab_url_tmp = beb_eab_url_tmp.split('/');
	delete beb_eab_url_tmp [0];
	delete beb_eab_url_tmp [beb_eab_url_tmp.length-1];
	var beb_eab_url = beb_eab_url_tmp.join('/');
	beb_eab_url = 'http:' + beb_eab_url; 
	var beb_eab_freccia_sx = beb_eab_url + 'freccia_sx.png';
	var beb_eab_freccia_dx = beb_eab_url + 'freccia_dx.png';
	
	if ($('#beb-eab-contenitore-banner').attr('title') == 'beb-eab-banner-aperto') {
		/* sto nella home page */
		var beb_eab_aperto = true;
		$('#beb-eab-banner-bottone').attr('src', beb_eab_freccia_dx);
		$('#beb-eab-contenitore-banner').css('right', '0');
	} else if ($('#beb-eab-contenitore-banner').attr('title') == 'beb-eab-banner-chiuso') {
		/* NON sto nella home page */
		var beb_eab_aperto = false;
		$('#beb-eab-banner-bottone').attr('src', beb_eab_freccia_sx);
		if ($(".beb-eab-spazio-banner div").is("#beb-eab-banner-data")) {
			$('#beb-eab-contenitore-banner').css('right', '-325px');
		} else {
			$('#beb-eab-contenitore-banner').css('right', '-308px');
		}
	}
	$('#beb-eab-banner-bottone').on('click', function (){
		if (beb_eab_aperto == true) {
			/* Banner aperto --> lo chiudo */
			$('#beb-eab-contenitore-banner').animate({
				right: beb_eab_chiudi_banner_di+'px'
				}, 500 );
			$('#beb-eab-banner-bottone').attr('src', beb_eab_freccia_sx);
			beb_eab_aperto = false;
		} else if (beb_eab_aperto == false) {
			/* Banner chiuso --> lo apro */
			$('#beb-eab-banner-bottone').attr('src', beb_eab_freccia_dx);
			$('#beb-eab-contenitore-banner').animate({
				right: '0'
				}, 500 );
			beb_eab_aperto = true;
		}
	});
});
