jQuery(document).ready(function($){
	var beb_eab_chiudi_banner_di = $('#beb-eab-contenitore-banner').css('right');
	var beb_eab_url_tmp = $("#beb-eab-banner-bottone").attr("src");
	beb_eab_url_tmp = beb_eab_url_tmp.split('/');
	delete beb_eab_url_tmp [0];
	delete beb_eab_url_tmp [beb_eab_url_tmp.length-1];
	var beb_eab_url = beb_eab_url_tmp.join('/');
	beb_eab_url = 'http:' + beb_eab_url; 
	
	$('#beb-eab-banner-bottone').on('click', function (){
		if ($('#beb-eab-contenitore-banner').attr('title') === 'beb-eab-banner-aperto') {
			/* Banner aperto --> lo chiudo */
			$('#beb-eab-contenitore-banner').animate({
				right: beb_eab_chiudi_banner_di
				}, 500 );
			$('#beb-eab-banner-bottone').attr('src', beb_eab_url + 'freccia_sx.png');
			$('#beb-eab-contenitore-banner').attr('title', 'beb-eab-banner-chiuso');
		} else if ($('#beb-eab-contenitore-banner').attr('title') === 'beb-eab-banner-chiuso') {
			/* Banner chiuso --> lo apro */
			$('#beb-eab-contenitore-banner').animate({
				right: '0'
				}, 500 );
			$('#beb-eab-banner-bottone').attr('src', beb_eab_url + 'freccia_dx.png');
			$('#beb-eab-contenitore-banner').attr('title', 'beb-eab-banner-aperto');
		}
	});
});