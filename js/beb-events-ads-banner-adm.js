jQuery(document).ready(function($){
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ------------------------------------------------- GENERALE ----------------------------------------------------- */
	/* -------------------------------------- ANCORA TESTUALE (INDICE / PARAGRAFO) ------------------------------------ */
	/* ---------------------------------------------------------------------------------------------------------------- */
	$('.beb-eab-help-up').click(function() {
		$('html,body').animate({'scrollTop': 0},1000);
	});
	$('.beb-eab-banner-indice').click(function() {
		var beb_eab_offset = $('.' + $(this).attr('title')).offset();
		var beb_eab_destinazione = beb_eab_offset.top - 75;
		$('html,body').animate({'scrollTop': beb_eab_destinazione},1000);
	});
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------- PAGINA PREVIEW -------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ------------------------- SWITCH ACCENSIONE BANNER --------------------- */
	var beb_eab_url_get = window.location.href;
	var beb_eab_pagina = beb_eab_url_get.split ('?');
	beb_eab_pagina = beb_eab_pagina[1].split ('=');
	beb_eab_pagina = beb_eab_pagina[1];
	
	var beb_eab_url_tmp = $("#beb-eab-banner-bottone").attr("src");
	beb_eab_url_tmp = beb_eab_url_tmp.split('/');
	delete beb_eab_url_tmp [0];
	delete beb_eab_url_tmp [beb_eab_url_tmp.length-1];
	var beb_eab_url = beb_eab_url_tmp.join('/');
	beb_eab_url = 'http:' + beb_eab_url; 
	var beb_eab_immagine_on = beb_eab_url + 'on.png';
	var beb_eab_immagine_off = beb_eab_url + 'off.png';
	var beb_eab_immagine_on_off = beb_eab_url + 'on_off.png';
	/*
	if (beb_eab_pagina == 'beb-eab-preview') {
		var beb_eab_ancora_stato_banner = '.beb-eab-stato-banner-riassunto';
	} else if (beb_eab_pagina == 'beb-eab-add') {
		var beb_eab_ancora_stato_banner = '#beb-eab-stato-banner';
	}
	*/
	/*var beb_eab_conta = 1;*/
	$('.beb-eab-stato-banner-riassunto').each(function(index, element){
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'on') {
			$(this).css('background-image', 'url(' + beb_eab_immagine_on + ')').mouseover(function(){
				$(this).css('background-image', 'url(' + beb_eab_immagine_on_off + ')');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + beb_eab_immagine_on + ')');
			});
			$(this).click(function(){
				$(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val('off');
			});
		}
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'off') {
			$(this).css('background-image', 'url(' + beb_eab_immagine_off + ')').mouseover(function(){
				$(this).css('background-image', 'url(' + beb_eab_immagine_on_off + ')');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + beb_eab_immagine_off + ')');
			});
			$(this).click(function(){
				$(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val('on');
			});
		}
	});
	$('#beb-eab-banner-remove').click(function(){
		$('input[name="beb_eab_banner_preview[remove_banner]"]').val('si');
	});
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------- PAGINA ADD BANNER ----------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ----------------------------------- CONTA CARATTERI ED IMMISSIONE TESTO ---------------------------------------- */
	var beb_eab_lunghezza_descrizione = 195;
	/* ----------TESTI: TITOLO, SOTTOTITOLO, LINK SX E LINK DX ---------------------- */
	$('#beb-eab-contenitore-admin form input[type="text"]').each(function(element) {
		var beb_eab_mappa_form_preview = {
				'beb-eab-titolo': '#beb-eab-banner-testo h1',
				'beb-eab-sottotitolo': '#beb-eab-banner-testo h2',
				'beb-eab-descrizione': '#beb-eab-banner-testo p',
				'beb-eab-link-sx-titolo': '#beb-eab-banner-cont-prenota-sx',
				'beb-eab-link-dx-titolo': '#beb-eab-banner-cont-prenota-dx'
		};
		var beb_eab_id_input = $(this).attr('id');
		
		if (beb_eab_id_input === 'beb-eab-nome-evento') {
			var beb_eab_lunghezza = 40;
			var beb_eab_testo_dove = ''
		} else if (beb_eab_id_input === 'beb-eab-titolo' || 'beb-eab-link-sx-titolo' || 'beb-eab-link-dx-titolo') {
			var beb_eab_lunghezza = 20;
		} else if (beb_eab_id_input === 'beb-eab-sottotitolo' || 'beb-eab-link-sx-titolo' || 'beb-eab-link-dx-titolo') {
			var beb_eab_lunghezza = 28;
		}
		$(this).keyup(function() {
			var beb_eab_lunghezza2 = $(this).val().length;
			beb_eab_lunghezza2 = beb_eab_lunghezza-beb_eab_lunghezza2;
			var beb_eab_testo = $(this).val();
			if (beb_eab_lunghezza2 <= 5) {
				$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#ff0000');
			} else {
				$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#0000ff');
			}
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').text(beb_eab_lunghezza2);
			$(beb_eab_mappa_form_preview[beb_eab_id_input]).text(beb_eab_testo);
			/* Se impostato il sottotitolo, sistemo meglio il testo */
			if (beb_eab_id_input === 'beb-eab-sottotitolo') {
				if (beb_eab_testo === '') {
					$('#beb-eab-banner-testo h1').animate({'margin': '46px 0 0'}, 500);
				} else {
					$('#beb-eab-banner-testo h1').animate({'margin': '35px 0 0'}, 500);
					$('#beb-eab-banner-testo h2').css({'line-height': '20px', 'font-size': '20px'});
				}
			}
		});
	});
	/* ------------------------- DATA --------------------- */
	$('#beb-eab-contenitore-admin form select').each(function(element) {
		var beb_eab_mappa_form_preview = {'beb-eab-giorno': '#beb-eab-banner-data h1', 'beb-eab-mese': '#beb-eab-banner-data h2'};
		var beb_eab_id_input = $(this).attr('id');
		var beb_eab_testo = $(this).val();
		
		var beb_eab_larghezza_banner_tmp = $('.beb-eab-spazio-banner').css('width');
		var beb_eab_larghezza_banner = beb_eab_larghezza_banner_tmp.split('px');
		beb_eab_larghezza_banner = beb_eab_larghezza_banner[0];
		
		var beb_eab_larghezza_cont_banner_tmp = $('.beb-eab-contenuto-banner').css('width');
		var beb_eab_larghezza_cont_banner = beb_eab_larghezza_cont_banner_tmp.split('px');
		beb_eab_larghezza_cont_banner = beb_eab_larghezza_cont_banner[0];
		/* Impostazioni iniziali */
		if ($('#beb-eab-banner-data h1').text() == '') {
			$('#beb-eab-banner-data').css('display', 'none');
			if (beb_eab_larghezza_banner == '385' || beb_eab_larghezza_banner == '485') {
				$('.beb-eab-spazio-banner').css('width', parseInt(beb_eab_larghezza_banner) - 62 + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt(beb_eab_larghezza_cont_banner) - 62 + 'px');
			}
		} else {
			$('#beb-eab-banner-data').css('display', 'inline');
			if (beb_eab_larghezza_banner == '323' || beb_eab_larghezza_banner == '423') {
				$('.beb-eab-spazio-banner').css('width', parseInt(beb_eab_larghezza_banner) + 62 + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt(beb_eab_larghezza_cont_banner) + 62 + 'px');
			}
		}
		/* animazione inserimento */
		$(this).change(function() {
			var beb_eab_mese_banner = $(this).children('option:selected').text();
			beb_eab_testo = beb_eab_mese_banner.substring(0, 3);
			$(beb_eab_mappa_form_preview[beb_eab_id_input]).text(beb_eab_testo);
			if (beb_eab_larghezza_banner == '323' || beb_eab_larghezza_banner == '423') {
				$('#beb-eab-banner-data').css('display', 'inline');
				$('.beb-eab-spazio-banner').animate({'width': parseInt(beb_eab_larghezza_banner) + 62 + 'px'}, 1000);
				$('.beb-eab-contenuto-banner').animate({'width': parseInt(beb_eab_larghezza_cont_banner) + 62 + 'px'}, 1000);
			}
		});
	});
	/* ------------------------- DESCRIZIONE --------------------- */
	$('#beb-eab-descrizione').keyup(function() {
		var beb_eab_lunghezza2 = $(this).val().length;
		var beb_eab_lunghezza2 = beb_eab_lunghezza_descrizione-beb_eab_lunghezza2;
		var beb_eab_testo = $(this).val();
		if (beb_eab_lunghezza2 <= 5) {
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#ff0000');
		} else {
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#0000ff');
		}
		$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').text(beb_eab_lunghezza2);
		$('#beb-eab-banner-testo p').text(beb_eab_testo);
		if (beb_eab_testo === '') {
			if ($('#beb-eab-banner-testo h2').text() != '') {
				$('#beb-eab-banner-testo h1').animate({'margin': '35px 0 0'}, 500);
			} else {
				$('#beb-eab-banner-testo h1').animate({'margin': '46px 0 0'}, 500);
			}
		} else {
			if ($('#beb-eab-banner-testo h2').text() != '') {
				$('#beb-eab-banner-testo h1').animate({'margin': '0'}, 500);
			} else {
				$('#beb-eab-banner-testo h1').animate({'margin': '11px 0 0'}, 500);
			}
		}
	});
	/* ------------------------------------------ SELEZIONE/ESCLUSIONE DEI LINK ------------------------------------------- */
	$('#beb-eab-link-totale').click(function (){
		var beb_eab_link_selez = $( '#beb-eab-link-totale:checked' ).length;
		if (beb_eab_link_selez === 0) {
			$('#beb-eab-link-sx-titolo').css('opacity', '1');
			$('#beb-eab-link-sx-titolo' ).prop( 'disabled', false );
			$('#beb-eab-link-sx-titolo').next().css('opacity', '1');
			$('#beb-eab-link-sx-titolo').parent().prev().css('opacity', '1');
			
			$('#beb-eab-link-dx-titolo').css('opacity', '1');
			$('#beb-eab-link-dx-titolo' ).prop( 'disabled', false );
			$('#beb-eab-link-dx-titolo').next().css('opacity', '1');
			$('#beb-eab-link-dx-titolo').parent().prev().css('opacity', '1');
			
			$('#beb-eab-link-sx').css('opacity', '1');
			$('#beb-eab-link-sx' ).prop( 'disabled', false );
			$('#beb-eab-link-sx').next().css('opacity', '1');
			$('#beb-eab-link-sx').parent().prev().css('opacity', '1');
			
			$('#beb-eab-link-dx').css('opacity', '1');
			$('#beb-eab-link-dx' ).prop( 'disabled', false );
			$('#beb-eab-link-dx').next().css('opacity', '1');
			$('#beb-eab-link-dx').parent().prev().css('opacity', '1');
		} else {
			$('#beb-eab-link-sx').css('opacity', '0.5');
			$('#beb-eab-link-sx' ).prop( 'disabled', true );
			$('#beb-eab-link-sx').next().css('opacity', '0.5');
			$('#beb-eab-link-sx').parent().prev().css('opacity', '0.5');
			
			$('#beb-eab-link-dx').css('opacity', '0.5');
			$('#beb-eab-link-dx' ).prop( 'disabled', true );
			$('#beb-eab-link-dx').next().css('opacity', '0.5');
			$('#beb-eab-link-dx').parent().prev().css('opacity', '0.5');
			
			$('#beb-eab-link-sx-titolo').focus(function (){
				$('#beb-eab-link-dx-titolo').css('opacity', '0.5');
				$('#beb-eab-link-dx-titolo' ).prop( 'disabled', true );
				$('#beb-eab-link-dx-titolo').next().css('opacity', '0.5');
				$('#beb-eab-link-dx-titolo').parent().prev().css('opacity', '0.5');
			});
			
			$('#beb-eab-link-dx-titolo').focus(function (){
				$('#beb-eab-link-sx-titolo').css('opacity', '0.5');
				$('#beb-eab-link-sx-titolo' ).prop( 'disabled', true );
				$('#beb-eab-link-sx-titolo').next().css('opacity', '0.5');
				$('#beb-eab-link-sx-titolo').parent().prev().css('opacity', '0.5');
			});
		}
	});
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* -------------------------------------------- PAGINA IMPOSTAZIONI ----------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------- */
	$('#beb-eab-contenitore-admin input').keyup(function(){
		var beb_eab_valore = '#' + $(this).val();
		var beb_eab_associazione = $(this).attr('alt');
		/*var tipoBanner =*/
		$(this).next().css('background-color',beb_eab_valore);
		if (beb_eab_associazione == 'beb-eab-banner-data') {
			$('#' + beb_eab_associazione + ' h1').css('color', beb_eab_valore);
			$('#' + beb_eab_associazione + ' h2').css('color', beb_eab_valore);
		} else if (beb_eab_associazione == 'beb-eab-banner-cont-prenota-sx' | beb_eab_associazione == 'beb-eab-banner-cont-prenota-dx') {
			$('#' + beb_eab_associazione + ' a').css('color', beb_eab_valore);
		} else if (beb_eab_associazione == 'beb-eab-spazio-banner 1' | beb_eab_associazione == 'beb-eab-spazio-banner 2' | beb_eab_associazione == 'beb-eab-banner-imp-n-colori') {
			if ($('input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]:checked').val() == '1') {
				$('.beb-eab-spazio-banner').css('color', beb_eab_valore);
			} else if ($('input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]:checked').val() == '2') {
				if (beb_eab_associazione == 'beb-eab-spazio-banner 1') {
					var beb_eab_colore1 = beb_eab_valore;
					var beb_eab_colore2 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_2]"]').val();
				} else if (beb_eab_associazione == 'beb-eab-spazio-banner 2') {
					var beb_eab_colore1 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_1]"]').val();
					var beb_eab_colore2 = beb_eab_valore;
				}
				$('.beb-eab-spazio-banner').css({
				    'background-image': '-webkit-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)', 
	                'background-image': '-o-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
	                'background-image': '-moz-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
	                'background-image': 'linear-gradient(to right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
				});
				$('.beb-eab-spazio-banner2').css('border-right', '60px solid ' + beb_eab_colore1);
			}
		} else if (beb_eab_associazione == 'beb-eab-banner-opacita') {
			$('.beb-eab-spazio-banner').css('opacity', beb_eab_valore);
			$('.beb-eab-spazio-banner2').css('opacity', beb_eab_valore);
			$('.beb-eab-banner-freccia').css('opacity', beb_eab_valore);
		} else {
			$('#' + beb_eab_associazione).css('color', beb_eab_valore);
		}
	/* ------------------------------------------- DIMENSIONE FONT ------------------------------------------------- */	
		var mappa_associativa_imp_banner_size = {
				'beb-eab-banner-data-giorno-size': '#beb-eab-banner-data h1',
				'beb-eab-banner-data-mese-size': '#beb-eab-banner-data h2',
				'beb-eab-banner-titolo-size': '#beb-eab-banner-testo h1',
				'beb-eab-banner-sottotitolo-size': '#beb-eab-banner-testo h2',
				'beb-eab-banner-description-size': '#beb-eab-banner-testo p',
				'beb-eab-banner-left-link-size': '#beb-eab-banner-cont-prenota-sx a',
				'beb-eab-banner-right-link-size': '#beb-eab-banner-cont-prenota-dx a'
		};
		$(this).keyup(function(){
			var beb_eab_testo_size_alt = $(this).attr('alt');
			var beb_eab_testo_size_value = $(this).val();
			$(mappa_associativa_imp_banner_size[beb_eab_testo_size_alt]).css('font-size', beb_eab_testo_size_value);
		});
	});
	/* ------------------------------------------- SCELTA 1 O 2 COLORI ------------------------------------------------- */
	$('#beb-eab-contenitore-admin input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]').click(function() {
		var beb_eab_colore1 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_1]"]').val();
		var beb_eab_colore2 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_2]"]').val();
		if ($(this).val() == '1') {
			$('#beb-eab-banner-background-2').hide();
			$('#beb-eab-banner-background-2').next().hide();
			$('.beb-eab-spazio-banner').css('background', beb_eab_colore1);
		} else if ($(this).val() == '2') {
			$('#beb-eab-banner-background-2').show();
			$('#beb-eab-banner-background-2').next().show();
			$('.beb-eab-spazio-banner').css({
			    'background': '-webkit-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)', 
                'background': '-o-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
                'background': '-moz-linear-gradient(right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
                'background': 'linear-gradient(to right, ' + beb_eab_colore1 + ', ' + beb_eab_colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
			});
		}
	});
	/* ------------------------------------------- Formatting Text and Paragraph ------------------------------------------------- */
	$('#beb-eab-contenitore-admin input[type="radio"]').each(function() {
		var mappa_associativa_imp_banner_align = {
				'beb-eab-banner-data-giorno-align': '#beb-eab-banner-data h1',
				'beb-eab-banner-data-mese-align': '#beb-eab-banner-data h2',
				'beb-eab-banner-titolo-align': '#beb-eab-banner-testo h1',
				'beb-eab-banner-sottotitolo-align': '#beb-eab-banner-testo h2',
				'beb-eab-banner-description-align': '#beb-eab-banner-testo p',
				'beb-eab-banner-left-link-align': '#beb-eab-banner-cont-prenota #beb-eab-banner-cont-prenota-sx',
				'beb-eab-banner-right-link-align': '#beb-eab-banner-cont-prenota #beb-eab-banner-cont-prenota-dx'
		};
		$(this).click(function(){
			var beb_eab_testo_align_alt = $(this).attr('alt');
			var beb_eab_testo_align_value = $(this).val();
			$(mappa_associativa_imp_banner_align[beb_eab_testo_align_alt]).css('text-align', beb_eab_testo_align_value);
		});
	});
});
