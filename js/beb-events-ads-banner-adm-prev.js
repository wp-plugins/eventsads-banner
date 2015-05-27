jQuery(document).ready(function($){
	/*
	var beb_eab_url_get = window.location.href;
	var beb_eab_pagina = beb_eab_url_get.split ('?');
	beb_eab_pagina = beb_eab_pagina[1].split ('=');
	beb_eab_pagina = beb_eab_pagina[1];
	*/
	var beb_eab_url_tmp = $("#beb-eab-banner-bottone").attr("src");
	beb_eab_url_tmp = beb_eab_url_tmp.split('/');
	delete beb_eab_url_tmp [0];
	delete beb_eab_url_tmp [beb_eab_url_tmp.length-1];
	var beb_eab_url = beb_eab_url_tmp.join('/');
	beb_eab_url = 'http:' + beb_eab_url;
	$('#beb-eab-contenitore-admin form#beb-eab-banner-riass-form .beb-eab-stato-banner-riassunto').each(function(index, element){
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'on') {
			$(this).css('background-image', 'url(' + beb_eab_url + 'on.png)').mouseover(function(){
				$(this).css('background-image', 'url(' + beb_eab_url + 'on_off.png)');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + beb_eab_url + 'on.png)');
			});
			$(this).click(function(){
				$(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val('off');
			});
		}
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'off') {
			$(this).css('background-image', 'url(' + beb_eab_url + 'off.png)').mouseover(function(){
				$(this).css('background-image', 'url(' + beb_eab_url + 'on_off.png)');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + beb_eab_url + 'off.png)');
			});
			$(this).click(function(){
				$(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val('on');
			});
		}
	});
	$('#beb-eab-banner-remove').click(function(){
		$('input[name="beb_eab_banner_preview[remove_banner]"]').val('si');
	});
});