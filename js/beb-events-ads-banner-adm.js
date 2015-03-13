jQuery(document).ready(function($){
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ------------------------------------------------- GENERALE ----------------------------------------------------- */
	/* -------------------------------------- ANCORA TESTUALE (INDICE / PARAGRAFO) ------------------------------------ */
	/* ---------------------------------------------------------------------------------------------------------------- */
	$('.beb-eab-help-up').click(function() {
		$('html,body').animate({'scrollTop': 0},1000);
	});
	$('.beb-eab-banner-indice').click(function() {
		/*alert($(this).scrollTop());*/
		var offset = $('.' + $(this).attr('title')).offset();
		var destinazione = offset.top - 75;
		$('html,body').animate({'scrollTop': destinazione},1000);
	});
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ---------------------------------------------- PAGINA PREVIEW -------------------------------------------------- */
	/* ---------------------------------------------------------------------------------------------------------------- */
	/* ------------------------- SWITCH ACCENSIONE BANNER --------------------- */
	var url_get = window.location.href;
	var pagina = url_get.split ('?');
	pagina = pagina[1].split ('=');
	pagina = pagina[1];
	
	var url_tmp = $("#beb-eab-banner-bottone").attr("src");
	url_tmp = url_tmp.split('/');
	delete url_tmp [0];
	delete url_tmp [url_tmp.length-1];
	var url = url_tmp.join('/');
	url = 'http:' + url; 
	var immagine_on = url + 'on.png';
	var immagine_off = url + 'off.png';
	var immagine_on_off = url + 'on_off.png';
	
	if (pagina == 'beb-eab-preview') {
		var ancora_stato_banner = '.beb-eab-stato-banner-riassunto';
	} else if (pagina == 'beb-eab-add') {
		var ancora_stato_banner = '#beb-eab-stato-banner';
	}
	
	var conta = 1;
	$('.beb-eab-stato-banner-riassunto').each(function(index, element){
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'on') {
			$(this).css('background-image', 'url(' + immagine_on + ')').mouseover(function(){
				$(this).css('background-image', 'url(' + immagine_on_off + ')');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + immagine_on + ')');
			});
			$(this).click(function(){
				$(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val('off');
			});
		}
		if ($(this).children('input[name="beb_eab_banner_preview[stato_banner]"]').val() == 'off') {
			$(this).css('background-image', 'url(' + immagine_off + ')').mouseover(function(){
				$(this).css('background-image', 'url(' + immagine_on_off + ')');
			}).mouseout(function() {
				$(this).css('background-image', 'url(' + immagine_off + ')');
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
	var lunghezza_descrizione = 195;
	/* ----------TESTI: TITOLO, SOTTOTITOLO, LINK SX E LINK DX ---------------------- */
	$('#beb-eab-contenitore-admin form input[type="text"]').each(function(element) {
		var mappa_form_preview = {
				'beb-eab-titolo': '#beb-eab-banner-testo h1',
				'beb-eab-sottotitolo': '#beb-eab-banner-testo h2',
				'beb-eab-descrizione': '#beb-eab-banner-testo p',
				'beb-eab-link-sx-titolo': '#beb-eab-banner-cont-prenota-sx',
				'beb-eab-link-dx-titolo': '#beb-eab-banner-cont-prenota-dx'
		};
		var id_input = $(this).attr('id');
		
		if (id_input === 'beb-eab-nome-evento') {
			var lunghezza = 40;
			var testo_dove = ''
		} else if (id_input === 'beb-eab-titolo' || 'beb-eab-link-sx-titolo' || 'beb-eab-link-dx-titolo') {
			var lunghezza = 20;
		} else if (id_input === 'beb-eab-sottotitolo' || 'beb-eab-link-sx-titolo' || 'beb-eab-link-dx-titolo') {
			var lunghezza = 28;
		}
		$(this).keyup(function() {
			var length = $(this).val().length;
			var length = lunghezza-length;
			var testo = $(this).val();
			if (length <= 5) {
				$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#ff0000');
			} else {
				$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#0000ff');
			}
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').text(length);
			$(mappa_form_preview[id_input]).text(testo);
			/* Se impostato il sottotitolo, sistemo meglio il testo */
			if (id_input === 'beb-eab-sottotitolo') {
				if (testo === '') {
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
		var mappa_form_preview = {'beb-eab-giorno': '#beb-eab-banner-data h1', 'beb-eab-mese': '#beb-eab-banner-data h2'};
		var id_input = $(this).attr('id');
		var testo = $(this).val();
		
		var larghezza_banner_tmp = $('.beb-eab-spazio-banner').css('width');
		var larghezza_banner = larghezza_banner_tmp.split('px');
		larghezza_banner = larghezza_banner[0];
		
		var larghezza_cont_banner_tmp = $('.beb-eab-contenuto-banner').css('width');
		var larghezza_cont_banner = larghezza_cont_banner_tmp.split('px');
		larghezza_cont_banner = larghezza_cont_banner[0];
		/* Impostazioni iniziali */
		if ($('#beb-eab-banner-data h1').text() == '') {
			$('#beb-eab-banner-data').css('display', 'none');
			if (larghezza_banner == '373' || larghezza_banner == '473') {
				$('.beb-eab-spazio-banner').css('width', parseInt(larghezza_banner) - 50 + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt(larghezza_cont_banner) - 50 + 'px');
			}
		} else {
			$('#beb-eab-banner-data').css('display', 'inline');
			if (larghezza_banner == '323' || larghezza_banner == '423') {
				$('.beb-eab-spazio-banner').css('width', parseInt(larghezza_banner) + 50 + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt(larghezza_cont_banner) + 50 + 'px');
			}
		}
		/* animazione inserimento */
		$(this).change(function() {
			var mese_banner = $(this).children('option:selected').text();
			testo = mese_banner.substring(0, 3);
			$(mappa_form_preview[id_input]).text(testo);
			if (larghezza_banner == '323' || larghezza_banner == '423') {
				$('#beb-eab-banner-data').css('display', 'inline');
				/*$('.beb-eab-spazio-banner').css('width', parseInt(larghezza_banner) + 50 + 'px');*/
				$('.beb-eab-spazio-banner').animate({'width': parseInt(larghezza_banner) + 50 + 'px'}, 1000);
				$('.beb-eab-contenuto-banner').animate({'width': parseInt(larghezza_cont_banner) + 50 + 'px'}, 1000);
			}
		});
	});
	/* ------------------------- DESCRIZIONE --------------------- */
	$('#beb-eab-descrizione').keyup(function() {
		var length = $(this).val().length;
		var length = lunghezza_descrizione-length;
		var testo = $(this).val();
		if (length <= 5) {
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#ff0000');
		} else {
			$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').css('color', '#0000ff');
		}
		$(this).next().next('.beb-eab-add-banner-sugg').children('.beb-eab-add-banner-numeri').text(length);
		$('#beb-eab-banner-testo p').text(testo);
		if (testo === '') {
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
		var link_selez = $( '#beb-eab-link-totale:checked' ).length;
		if (link_selez === 0) {
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
		var valore = '#' + $(this).val();
		var associazione = $(this).attr('alt');
		/*var tipoBanner =*/
		$(this).next().css('background-color',valore);
		/*alert ('#' + associazione + ' h1');*/
		if (associazione == 'beb-eab-banner-data') {
			$('#' + associazione + ' h1').css('color', valore);
			$('#' + associazione + ' h2').css('color', valore);
		} else if (associazione == 'beb-eab-banner-cont-prenota-sx' | associazione == 'beb-eab-banner-cont-prenota-dx') {
			$('#' + associazione + ' a').css('color', valore);
		} else if (associazione == 'beb-eab-spazio-banner 1' | associazione == 'beb-eab-spazio-banner 2' | associazione == 'beb-eab-banner-imp-n-colori') {
			if ($('input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]:checked').val() == '1') {
				$('.beb-eab-spazio-banner').css('color', valore);
			} else if ($('input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]:checked').val() == '2') {
				if (associazione == 'beb-eab-spazio-banner 1') {
					var colore1 = valore;
					var colore2 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_2]"]').val();
				} else if (associazione == 'beb-eab-spazio-banner 2') {
					var colore1 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_1]"]').val();
					var colore2 = valore;
				}
				$('.beb-eab-spazio-banner').css({
				    'background-image': '-webkit-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)', 
	                'background-image': '-o-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
	                'background-image': '-moz-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
	                'background-image': 'linear-gradient(to right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
				});
				/*
				$('.beb-eab-spazio-banner').css({
	                'background': 'linear-gradient(to right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)'
				});*/
				$('.beb-eab-spazio-banner2').css('border-right', '60px solid ' + colore1);
			}
		} else if (associazione == 'beb-eab-banner-opacita') {
			$('.beb-eab-spazio-banner').css('opacity', valore);
			$('.beb-eab-spazio-banner2').css('opacity', valore);
			$('.beb-eab-banner-freccia').css('opacity', valore);
		} else {
			$('#' + associazione).css('color', valore);
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
			var testo_size_alt = $(this).attr('alt');
			var testo_size_value = $(this).val();
			$(mappa_associativa_imp_banner_size[testo_size_alt]).css('font-size', testo_size_value);
		});
	});
	/* ------------------------------------------- SCELTA 1 O 2 COLORI ------------------------------------------------- */
	$('#beb-eab-contenitore-admin input[name="beb_cv_banner_impostazioni[background-color][num_colori]"]').click(function() {
		var colore1 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_1]"]').val();
		var colore2 = $('input[name="beb_cv_banner_impostazioni[background-color][colore_2]"]').val();
		if ($(this).val() == '1') {
			$('#beb-eab-banner-background-2').hide();
			$('#beb-eab-banner-background-2').next().hide();
			$('.beb-eab-spazio-banner').css('background', colore1);
		} else if ($(this).val() == '2') {
			$('#beb-eab-banner-background-2').show();
			$('#beb-eab-banner-background-2').next().show();
			$('.beb-eab-spazio-banner').css({
			    'background': '-webkit-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)', 
                'background': '-o-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
                'background': '-moz-linear-gradient(right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
                'background': 'linear-gradient(to right, ' + colore1 + ', ' + colore2 + ') repeat scroll 0 0 rgba(0, 0, 0, 0)',
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
			var testo_align_alt = $(this).attr('alt');
			var testo_align_value = $(this).val();
			$(mappa_associativa_imp_banner_align[testo_align_alt]).css('text-align', testo_align_value);
		});
	});
});
