jQuery(document).ready(function($){
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