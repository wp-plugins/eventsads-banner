jQuery(document).ready(function($){
	/* ----------------------------------- ANCORA INDICE / TESTI ---------------------------------------- */
	$('.beb-eab-help-up').click(function() {
		$('html,body').animate({'scrollTop': 0},1000);
	});
	$('.beb-eab-banner-indice').click(function() {
		var beb_eab_offset = $('.' + $(this).attr('title')).offset();
		var beb_eab_destinazione = beb_eab_offset.top - 75;
		$('html,body').animate({'scrollTop': beb_eab_destinazione},1000);
	});
	/* ----------------------------------- PULSANTE ON/OFF STATO BANNER ---------------------------------------- */
	var beb_eab_url_tmp = $("#beb-eab-banner-bottone").attr("src");
	beb_eab_url_tmp = beb_eab_url_tmp.split('/');
	delete beb_eab_url_tmp [0];
	delete beb_eab_url_tmp [beb_eab_url_tmp.length-1];
	var beb_eab_url = beb_eab_url_tmp.join('/');
	beb_eab_url = 'http:' + beb_eab_url;
	
	$('.beb-eab-stato-banner-riassunto').mouseover(function() {
		$(this).css('background-image', 'url("' + beb_eab_url + 'on_off.png")');
	}).mouseout(function() {
		$(this).css('background-image', 'url(' + beb_eab_url + $(this).children('input[name="beb_eab_banner_new[stato_banner]"]').val() + '.png)');
	});
	$('.beb-eab-stato-banner-riassunto').click(function(){
		if ($(this).children('input[name="beb_eab_banner_new[stato_banner]"]').val() === 'on') {
			$(this).children('input[name="beb_eab_banner_new[stato_banner]"]').val('off');
			$(this).css('background-image', 'url("' + beb_eab_url + 'off.png")');
		} else if ($(this).children('input[name="beb_eab_banner_new[stato_banner]"]').val() === 'off') {
			$(this).children('input[name="beb_eab_banner_new[stato_banner]"]').val('on');
			$(this).css('background-image', 'url("' + beb_eab_url + 'on.png")');
		}
	});
	/* ----------TESTI: TITOLO, SOTTOTITOLO, LINK SX E LINK DX ---------------------- */
	var beb_eab_lunghezza_descrizione = 195;
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
			if (beb_eab_id_input === 'beb-eab-link-sx-titolo' || 'beb-eab-link-dx-titolo') {
				if (beb_eab_testo === '') {
					$(this).next().next('.beb-eab-add-banner-link').hide(500);
					$(this).next().next('.beb-eab-add-banner-link').next().hide(500);
				} else {
					$(this).next().next('.beb-eab-add-banner-link').show(500);
					$(this).next().next('.beb-eab-add-banner-link').next().show(500);
				}
			}
			$(this).next('.beb-eab-add-banner-sugg').children('strong').next().text(beb_eab_lunghezza2);
			$(beb_eab_mappa_form_preview[beb_eab_id_input]).text(beb_eab_testo);
			/* Se impostato il sottotitolo, sistemo meglio il testo */
			if (beb_eab_id_input === 'beb-eab-sottotitolo') {
				if (beb_eab_testo === '') {
					$('#beb-eab-banner-testo h1').animate({'margin': '46px 0 0'}, 500);
				} else {
					$('#beb-eab-banner-testo h1').animate({'margin': '35px 0 0'}, 500);
					$('#beb-eab-banner-testo h2').css({'line-height': '20px', 'font-size': '20px'});
					$('#beb-eab-banner-testo h1').animate({'margin': '35px 0 0'}, 500);
				}
			}
		});
	});
	/* ------------------------- DATA --------------------- */
	var beb_eab_larghezza_banner = $('.beb-eab-spazio-banner').width();
	var beb_eab_larghezza_cont_banner = $('.beb-eab-contenuto-banner').width();

	$('#beb-eab-contenitore-admin form select').each(function(element) {
		var beb_eab_mappa_form_preview = {'beb-eab-giorno': '#beb-eab-banner-data h1', 'beb-eab-mese': '#beb-eab-banner-data h2'};
		var beb_eab_id_input = $(this).attr('id');
		var beb_eab_testo = $(this).val();
		var beb_eab_larg_data = 62;
		/* Impostazioni iniziali */
		if ($('#beb-eab-giorno option:selected').val() == '' && $('#beb-eab-mese option:selected').val() == '') {
			if ($('#beb-eab-banner-data').css('display') == 'inline') {
				$('#beb-eab-banner-data').css('display', 'none');
				$('.beb-eab-spazio-banner').css('width', parseInt($('.beb-eab-spazio-banner').width()) - beb_eab_larg_data + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt($('.beb-eab-contenuto-banner').width()) - beb_eab_larg_data + 'px');
			}
		} else {
			if ($('#beb-eab-banner-data').css('display') == 'none') {
				$('#beb-eab-banner-data').css('display', 'inline');
				$('.beb-eab-spazio-banner').css('width', parseInt($('.beb-eab-spazio-banner').width()) + beb_eab_larg_data + 'px');
				$('.beb-eab-contenuto-banner').css('width', parseInt($('.beb-eab-contenuto-banner').width()) + beb_eab_larg_data + 'px');
			}
		}
		/* animazione inserimento */
		$(this).change(function() {
			var beb_eab_mese_banner = $(this).children('option:selected').text();
			beb_eab_testo = beb_eab_mese_banner.substring(0, 3);
			$(beb_eab_mappa_form_preview[beb_eab_id_input]).text(beb_eab_testo);
			
			if ($('#beb-eab-banner-data').css('display') == 'none') {
				beb_eab_larghezza_banner = parseInt($('.beb-eab-spazio-banner').width()) + beb_eab_larg_data;
				beb_eab_larghezza_cont_banner = parseInt($('.beb-eab-contenuto-banner').width()) + beb_eab_larg_data;
				$('#beb-eab-banner-data').css('display', 'inline');
				$('.beb-eab-spazio-banner').animate({'width': parseInt(beb_eab_larghezza_banner) + 'px'}, 1000);
				$('.beb-eab-contenuto-banner').animate({'width': parseInt(beb_eab_larghezza_cont_banner) + 'px', 'right': 25}, 1000);
			}
			if ($('#beb-eab-giorno option:selected').val() == '' && $('#beb-eab-mese option:selected').val() == '') {
				if ($('#beb-eab-banner-data').css('display') == 'inline' || $('#beb-eab-banner-data').css('display') == 'block') {
					beb_eab_larghezza_banner = parseInt($('.beb-eab-spazio-banner').width()) - beb_eab_larg_data;
					beb_eab_larghezza_cont_banner = parseInt($('.beb-eab-contenuto-banner').width()) - beb_eab_larg_data;
					$('.beb-eab-spazio-banner').animate({'width': parseInt(beb_eab_larghezza_banner) + 'px'}, 1000);
					$('.beb-eab-contenuto-banner').animate({'width': parseInt(beb_eab_larghezza_cont_banner) + 'px', 'right': 0}, 1000);
					$('#beb-eab-banner-data').css('display', 'none');
				}
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
		$(this).next('.beb-eab-add-banner-sugg').children('strong').next().text(beb_eab_lunghezza2);
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
	/* ------------------------------------------------ IMMAGINE -------------------------------------------------- */
	var beb_eab_contatore = 1;
	var beb_eab_contatore2 = 1;
	var beb_eab_immagine_inserita = false;
	var beb_eab_check_imm = true;
	$("#beb-eab-banner-immagine").next().next('.beb-eab-add-banner-media').show();
	
	$('#beb-eab-immagine').keyup(function() {
		var beb_eab_imm_larg;
		var beb_eab_url_inserita = false;
		var beb_eab_url_imm = $(this).val();
		
		$(this).next().next('.beb-eab-add-banner-media').show(500);
		$(this).next().next('.beb-eab-add-banner-media').next().show(500);
		/* ------------------------------------------ RIPOSIZIONA BLOCCO INPUT ------------------------------------------------- */
		if (beb_eab_url_imm === '' && beb_eab_contatore2 === 1 && beb_eab_immagine_inserita === true) {
			beb_eab_larg_cont_banner = $("#beb-eab-contenitore-banner").attr('alt');
			beb_eab_larg_contenuto = $("#beb-eab-contenitore-banner .beb-eab-contenuto-banner").attr('alt');
			beb_eab_larg_banner = $("#beb-eab-contenitore-banner .beb-eab-spazio-banner").attr('alt');
			if ($('#beb-eab-banner-data').css('display') == 'inline' || $('#beb-eab-banner-data').css('display') == 'block') {
				beb_eab_larg_cont_banner = parseInt(beb_eab_larg_cont_banner) + 62;
				beb_eab_larg_contenuto = parseInt(beb_eab_larg_contenuto) + 62;
				beb_eab_larg_banner = parseInt(beb_eab_larg_banner) + 62;
			}
			$("#beb-eab-banner-immagine").hide(500);
			$('#beb-eab-contenitore-admin #beb-eab-contenitore-banner').animate({'width': beb_eab_larg_cont_banner + 'px'}, 1000);
			$('.beb-eab-contenuto-banner').animate({ 'width': beb_eab_larg_contenuto + 'px' }, 1000);
			$('.beb-eab-spazio-banner').animate({'width': beb_eab_larg_banner + 'px'}, 1000);
			$(this).next().next('.beb-eab-add-banner-media').hide(500);
			$(this).next().next('.beb-eab-add-banner-media').next().hide(500);
			// option
			$( ".contenitore-input.beb-eab-add-options-el" ).delay(500).animate({'margin-top': 0, padding: '0 3% 3%'}, 1000);
			// links
			$( ".contenitore-input.beb-eab-add-texts-el" ).animate({top: '0'}, 1000);
			// Media
			 $( ".contenitore-input.beb-eab-add-links-el" ).animate({top: '330px'}, 1000, function () {
				// Texts
				$( ".contenitore-input.beb-eab-add-texts-el" ).animate({left: '-48.5%'}, 1000);
				// links
				$( ".contenitore-input.beb-eab-add-links-el" ).animate({left: '49%'}, 1000, function () {
					 $( ".contenitore-input.beb-eab-add-links-el" ).animate({top: '-515px'}, 1000, function () {
						// Media
						 $( ".contenitore-input.beb-eab-add-media-el" ).animate({top: '-187px'}, 1000, function () {
							 // options
							 $( ".contenitore-input.beb-eab-add-options-el" ).css('float', 'none').css('margin', '1% 1% 0');
							 // texts
							 $( ".contenitore-input.beb-eab-add-texts-el" ).css('top', '0').css('left', '0').css('margin', '1% 1% 0');
							 // links
							 $( ".contenitore-input.beb-eab-add-links-el" ).css('left', '0').css('top', '0');
							 // media
							 $( ".contenitore-input.beb-eab-add-media-el" ).css('top', '0');
						});
					 });
				});
			 });
			if (beb_eab_contatore > 1) {
				beb_eab_check_imm = true;
			} else {
				beb_eab_check_imm = false;
			}
			beb_eab_contatore2++;
			beb_eab_contatore = 1;
			beb_eab_immagine_inserita = false;
		} else if (beb_eab_contatore === 1) {
			$("#beb-eab-banner-immagine img").attr('src', beb_eab_url_imm).error(function (){
				beb_eab_check_imm = false;
			});
			if (beb_eab_check_imm === true && beb_eab_url_imm != '') {
				beb_eab_immagine_inserita = true;
				var img = new Image();
				img.src = beb_eab_url_imm;
				if (parseInt(img.height) > 120) {
					beb_eab_imm_larg = ((120*parseInt(img.width))/parseInt(img.height)) + 50;
				} else {
					beb_eab_imm_larg = parseInt(img.width) + 40;
				}
				/* ------------------------------------------ SPOSTA BLOCCO INPUT ------------------------------------------------- */
				// Links
				 $( ".contenitore-input.beb-eab-add-links-el" ).animate({top: '505px'}, 1000);
				 // Media
				 $( ".contenitore-input.beb-eab-add-media-el" ).animate({top: '505px'}, 1000, function (){
					// Texts
					 $( ".contenitore-input.beb-eab-add-texts-el" ).animate({left: '49%'}, 1000, function () {
						// Options
						$( ".contenitore-input.beb-eab-add-options-el" ).animate({'margin-top': '250',padding: '3%'}, 1000);
						// Texts
						$( ".contenitore-input.beb-eab-add-texts-el" ).animate({top: '-270px'}, 1000);
						// Links
					 });
					 $( ".contenitore-input.beb-eab-add-links-el" ).animate({left: '-49%'}, 1000, function() {
						 // links
						 $( ".contenitore-input.beb-eab-add-links-el" ).animate({top: '0'}, 1000, function() {
							// links
							 $( ".contenitore-input.beb-eab-add-links-el" ).css('top', '-200px').css('left', '0px');
						});
						// Media
						 $( ".contenitore-input.beb-eab-add-media-el" ).animate({top: '-80px'}, 1000, function () {
							$("#beb-eab-banner-immagine img").attr('src', beb_eab_url_imm);
							$("#beb-eab-banner-immagine").show(500);
							$('#beb-eab-contenitore-admin #beb-eab-contenitore-banner').animate({
								'width': (parseInt($('#beb-eab-contenitore-admin #beb-eab-contenitore-banner').width()) + parseInt(beb_eab_imm_larg)) + 'px'}, 1000);
							$('.beb-eab-contenuto-banner').animate({
								'width': (parseInt($('.beb-eab-contenuto-banner').width()) + parseInt(beb_eab_imm_larg)) + 'px',
								'right': '30'
								}, 1000, function() {
									$("#beb-eab-banner-immagine").css({'margin': '0 10px', width: 'auto'});
									$("#beb-eab-banner-immagine img").css('margin', '0 10px');
							});
							$('.beb-eab-spazio-banner').animate({'width': (parseInt($('.beb-eab-spazio-banner').width()) + parseInt(beb_eab_imm_larg)) + 'px'}, 1000);
							// options
							$( ".contenitore-input.beb-eab-add-options-el" ).css('float', 'left');
							// texts
							$( ".contenitore-input.beb-eab-add-texts-el" ).css('top', '0').css('left', '0px').css('margin', '250px 0 0 1%');
							// media
							$( ".contenitore-input.beb-eab-add-media-el" ).css('top', '0');
						});
					 });
				 });
				beb_eab_contatore++;
				beb_eab_contatore2 = 1;
				beb_eab_url_inserita = true;
			} else {
				$("#beb-eab-banner-immagine").css({'margin': '0 10px', width: 'auto'});
				$("#beb-eab-banner-immagine img").css('margin', '0 10px');
				$("#beb-eab-banner-immagine img").show(500);
			}
		}
	});
});