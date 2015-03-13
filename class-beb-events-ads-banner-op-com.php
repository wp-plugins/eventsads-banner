<?php
class Beb_Events_Ads_Banner_Op_Com {
    protected static $riassunto_banner_mappa = array(
        'stato_banner', 'nome_evento', 'priorita', 'titolo', 'sottotitolo', 'data_evento_giorno',
        'data_evento_mese', 'descrizione'
    );
    protected static $wpml_opzioni_chiavi = array(
        'wpml_id', 'wpml_nome'
    );
    protected static $wp_options_prefisso = 'beb_events_ads_banner_';
    protected static $prefisso_nome = 'beb-events-ads-banner';
    protected $mappa_mese_numero;
    protected $mappa_mese_abr_mese;
    protected $banner;
    protected $contenuto = NULL;
    protected $impostazioni = NULL;
    protected $url;
    protected static $contenuto_iniziale = array(
        0 => array(
            'banner_esempio' => true,
            'stato_banner' => 'off',
            'nome_evento' => 'Example 01',
            'priorita' => '0',
            'data_evento_giorno' => '01',
            'data_evento_mese' => '01',
            'titolo' => 'Lorem ipsum dolor sit a',
            'sottotitolo' => ' Lorem ipsum dolor sit amet, co',
            'descrizione' => ' Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                                incididunt ut labore et dolore magna aliqua. Ut enim ad minim ',
            'link_sx_titolo' => 'Info',
            'link_sx' => '#',
            'link_dx_titolo' => 'Book',
            'link_dx' => '#'
        )
    );
    protected static $impostazioni_iniziali = array(
        /*'beb_eab_banner_imp_tempo' => '10',*/
        'background-color' => array(
            'num_colori' => '2',
            'colore_1' => 'ff0000',
            'colore_2' => '000000'
        ),
        '.beb-eab-spazio-banner' => array(
            'opacity' => '0.8'
        ),
        '#beb-eab-banner-data h1' => array(
            'color' => 'ffffff',
            'text-align' => 'left',
            'font-size' => '32px'
        ),
        '#beb-eab-banner-data h2' => array(
            'color' => 'ffffff',
            'font-size' => '25px',
            'text-align' => 'center'
        ),
        '#beb-eab-banner-testo h1' => array(
            'color' => 'ffffff',
            'font-size' => '25px',
            'text-align' => 'center'
        ),
        '#beb-eab-banner-testo h2' => array(
            'color' => 'ffffff',
            'font-size' => '18px',
            'text-align' => 'center'
        ),
        '#beb-eab-banner-testo p' => array(
            'color' => 'ffffff',
            'font-size' => '12px',
            'text-align' => 'justify'
        ),
        '#beb-eab-banner-cont-prenota-sx' => array(
            'color' => 'ffffff',
            'font-size' => '14px',
            'font-weight' => 'bold',
            'text-align' => 'center'
        ),
        '#beb-eab-banner-cont-prenota-dx' => array(
            'color' => 'ffffff',
            'font-size' => '14px',
            'font-weight' => 'bold',
            'text-align' => 'center'
        )
    );
    protected $wpdb = NULL;
    
    public function __construct() {
        // INIZIALIZZARE TUTTO CON IL CONTROLLO SE IL BANNER E' VISIBILE
        //self::beb_events_ads_banner_controllo_wpml();
        global $wpdb;
        if (!has_action('beb_events_ads_banner_traduci_banner')) {
            if (is_admin()) {
                $dove = 'admin_init';
            } else {
                $dove = 'init';
            }
            add_action($dove, array($this, 'beb_events_ads_banner_traduci_banner'));
        }
        $this->mappa_mese_numero = array(
            __("January", self::$prefisso_nome) => '01',
            __("February", self::$prefisso_nome) => '02',
            __("March", self::$prefisso_nome) => '03',
            __("April", self::$prefisso_nome) => '04',
            __("May", self::$prefisso_nome) => '05',
            __("June", self::$prefisso_nome) => '06',
            __("July", self::$prefisso_nome) => '07',
            __("August", self::$prefisso_nome) => '08',
            __("September", self::$prefisso_nome) => '09',
            __("October", self::$prefisso_nome) => '10',
            __("November", self::$prefisso_nome) => '11',
            __("December", self::$prefisso_nome) => '12'
        );
        $this->mappa_mese_abr_mese = array(
            __("Jan", self::$prefisso_nome) => '01',
            __("Feb", self::$prefisso_nome) => '02',
            __("Mar", self::$prefisso_nome) => '03',
            __("Apr", self::$prefisso_nome) => '04',
            __("May", self::$prefisso_nome) => '05',
            __("Jun", self::$prefisso_nome) => '06',
            __("Jul", self::$prefisso_nome) => '07',
            __("Aug", self::$prefisso_nome) => '08',
            __("Sep", self::$prefisso_nome) => '09',
            __("Oct", self::$prefisso_nome) => '10',
            __("Nov", self::$prefisso_nome) => '11',
            __("Dec", self::$prefisso_nome) => '12'
        );
        $this->url = plugin_dir_url( __FILE__ );
        self::beb_events_ads_banner_carica_dati();
    }
    
    final public function beb_events_ads_banner_carica_dati () {
        if (is_admin()) {
            add_action('admin_menu', array($this, 'beb_events_ads_banner_carica_header'));
        } else {
            add_action('wp_head', array($this, 'beb_events_ads_banner_carica_header'));
        }
        if (isset($GLOBALS[self::$wp_options_prefisso.'contenuto']) and !empty($GLOBALS[self::$wp_options_prefisso.'contenuto'])) {
            $this->contenuto = $GLOBALS[self::$wp_options_prefisso.'contenuto'];
        } else {
            $this->contenuto = get_option(self::$wp_options_prefisso.'contenuto');
            if ($this->contenuto !== false and is_array($this->contenuto)) {
                $GLOBALS[self::$wp_options_prefisso.'contenuto'] = $this->contenuto;
            } else {
                if (is_admin()) {
                    $this->contenuto = self::$contenuto_iniziale;
                }
            }
        }
        if (isset($GLOBALS[self::$wp_options_prefisso.'impostazioni']) and !empty($GLOBALS[self::$wp_options_prefisso.'impostazioni'])) {
            $this->impostazioni = $GLOBALS[self::$wp_options_prefisso.'impostazioni'];
        } else {
            $this->impostazioni = get_option(self::$wp_options_prefisso.'impostazioni');
            if ($this->impostazioni !== false and is_array($this->impostazioni)) {
                $GLOBALS[self::$wp_options_prefisso.'impostazioni'] = $this->impostazioni;
            } else {
                if (is_admin()) {
                    $this->impostazioni = self::$impostazioni_iniziali;
                }
            }
        }
    }
    final public function beb_events_ads_banner_carica_header () {
        self::carica_header();
    }
    private function carica_header () {
        wp_register_style (self::$prefisso_nome.'-view.css', $this->url.'css/'.self::$prefisso_nome.'-view.css', array(), BEBCVBANNER_VERSION);
        wp_enqueue_style (self::$prefisso_nome.'-view.css');
    }
    final public function beb_events_ads_banner_traduci_banner () {
        self::beb_events_ads_banner_traduci_banner_operativo();
    }
    private function beb_events_ads_banner_traduci_banner_operativo () {
        load_plugin_textdomain(self::$prefisso_nome, false, dirname( plugin_basename( __FILE__ ) ).'/languages' );
    }
    final public function beb_cv_banner ($contenuto = NULL, $visualizza = NULL) {
        if (!isset($contenuto)) {
            $contenuto = $this->contenuto;
        }?>
        <style type="text/css">
            <?php if(is_admin()):?>
            #beb-eab-contenitore-admin #beb-eab-contenitore-banner {
            	background-image: url("<?php echo $this->url; ?>img/texture.jpg");
            	border: 1px solid #000000;
               	box-shadow: 5px 5px 10px #000000;
               	float: right;
               	padding: 15px;
            	position: absolute;
            	/*right: 40px;*/
               	top: 40px;
            	width: 511px;
            }
            #beb-eab-contenitore-admin[class="beb_eab_imp_add"] #beb-eab-contenitore-banner {
            	top: 150px;
            }
            <?php else:?>
            #beb-eab-contenitore-banner {
                bottom: 60px;
                height: 150px;
                position: fixed;
            	z-index: 101;
            }
            #beb-eab-contenitore-banner .beb-eab-spazio-banner {
                height: 150px;	
            
            }
            <?php endif;?>
            
            .beb-eab-spazio-banner {
            	<?php if ($this->impostazioni['background-color']['num_colori'] == 2): ?>
            	    background: -webkit-linear-gradient(left, #<?php echo $this->impostazioni['background-color']['colore_1']; ?>, #<?php echo $this->impostazioni['background-color']['colore_2']; ?>) repeat scroll 0 0 rgba(0, 0, 0, 0); /* For Safari 5.1 to 6.0 */
                    background: -o-linear-gradient(right, #<?php echo $this->impostazioni['background-color']['colore_1']; ?>, #<?php echo $this->impostazioni['background-color']['colore_2']; ?>) repeat scroll 0 0 rgba(0, 0, 0, 0); /* For Opera 11.1 to 12.0 */
                    background: -moz-linear-gradient(right, #<?php echo $this->impostazioni['background-color']['colore_1']; ?>, #<?php echo $this->impostazioni['background-color']['colore_2']; ?>) repeat scroll 0 0 rgba(0, 0, 0, 0); /* For Firefox 3.6 to 15 */
                    background: linear-gradient(to right, #<?php echo $this->impostazioni['background-color']['colore_1']; ?>, #<?php echo $this->impostazioni['background-color']['colore_2']; ?>) repeat scroll 0 0 rgba(0, 0, 0, 0); /* Standard syntax */
            	<?php else: ?>
                    background-color: #<?php echo $this->impostazioni['background-color']['colore_1']; ?>;
                <?php endif; ?>
            }
	        .beb-eab-spazio-banner2 {
                border-bottom: 75px solid transparent;
                border-right: 60px solid #<?php echo $this->impostazioni['background-color']['colore_1']; ?>;
                border-top: 75px solid transparent;
            }
            <?php foreach ($this->impostazioni as $id_class => $opzioni_arr) {
                if ($id_class != 'beb_eab_display' and 'beb_eab_banner_imp_tempo' and 'background-color') {
                    echo "$id_class {";
                    if (is_array($opzioni_arr)) {
                        foreach ($opzioni_arr as $tipo => $valore) {
                            if ($tipo === 'color') {
                                echo "$tipo: #$valore;";
                            } else {
                                echo "$tipo: $valore;";
                            }
                        }
                    }  
                    echo '}';
                }
            } ?>
        </style>
        <?php
        for ($i = 0; $i < count($contenuto); $i++) {
            if (is_admin()) {
                $mostra = true;
            } elseif ($contenuto[$i]['stato_banner'] === 'on') {
                $mostra = true;
            } elseif ($contenuto[$i]['stato_banner'] === 'off') {
                $mostra = false;
            } else {
                $mostra = false;
            }
            if ($mostra) {
                if (isset($contenuto[$i]['wpml_id'])) {
                    foreach ($contenuto as $chiave_array => $contenuto_sing) {
                        if ($chiave_array != 'wpml_id') {
                            if (!is_numeric($contenuto_sing)) {
                                $nome = explode('_', $chiave_array);
                                $nome = end($nome);
                                $contenuto[$i][$chiave_array] = icl_t($contenuto[$i]['wpml_id'], $nome, $contenuto_sing);
                            }
                        }
                    }
                }
                $larghezza_banner = 323;
                if (isset($contenuto[$i]['data_evento_giorno'])) {
                    $larghezza_banner = $larghezza_banner + 77;
                }
                if (isset($contenuto[$i]['url_img'])) {
                    $larghezza_banner = $larghezza_banner + 100;
                }
                if (!isset($contenuto[$i]['sottotitolo']) and !isset($contenuto[$i]['descrizione'])) {
                    $altezza_titolo = '25px';
                    $margin_titolo = '46px 0 0';
                    $altezza_sottotitolo = 'auto';
                    $margin_sottotitolo = '0';
                    $altezza_descrizione = '13px';
                } elseif (isset($contenuto[$i]['sottotitolo']) and !isset($contenuto[$i]['descrizione'])) {
                    $altezza_titolo = '25px';
                    $margin_titolo = '33px 0 0 0';
                    $altezza_sottotitolo = '10px';
                    $margin_sottotitolo = '15px 0 0 0';
                } elseif (isset($contenuto[$i]['descrizione']) and !isset($contenuto[$i]['sottotitolo'])) {
                    $altezza_titolo = '30px';
                    $margin_titolo = 0;
                    $altezza_descrizione = '15px';
                } else {
                    $altezza_titolo = '25px';
                    $margin_titolo = 0;
                    $altezza_sottotitolo = '18px';
                    $margin_sottotitolo = 0;
                    $altezza_descrizione = '12px';
                }
                $apertura_banner_titolo = 'beb-eab-banner-aperto';
                $apertura_banner_posizione = 'right: 40px';
                if (isset($contenuto[$i]['open']) and !is_admin()) {
                    if ($contenuto[$i]['open'] === 'home') {
                        if (is_home() or is_page('home')) {
                            $apertura_banner_titolo = 'beb-eab-banner-aperto';
                            $apertura_banner_posizione = 'right: 0';
                        } else {
                            $apertura_banner_titolo = 'beb-eab-banner-chiuso';
                            $apertura_banner_posizione = 'right: -308px';
                        }
                    } elseif ($contenuto[$i]['open'] === 'all') {
                        $apertura_banner_titolo = 'beb-eab-banner-aperto';
                        $apertura_banner_posizione = 'right: 0';
                    } elseif ($contenuto[$i]['open'] === 'never') {
                        $apertura_banner_titolo = 'beb-eab-banner-chiuso';
                        $apertura_banner_posizione = 'right: -308px';
                    }
                } ?>
                <div id="beb-eab-contenitore-banner" style="<?php echo $apertura_banner_posizione; ?>" title="<?php echo $apertura_banner_titolo; ?>">
                    <div class="beb-eab-contenuto-banner" style="width: <?php echo ($larghezza_banner + 138); ?>px; float: right;">
                    	<div class="beb-eab-cont-spazio-banner">
                    		 <div class="beb-eab-spazio-banner" style="width: <?php echo $larghezza_banner; ?>px;">
                    		    <?php if (isset($contenuto[$i]['data_evento_giorno']) or $visualizza): ?>
                    				<div id="beb-eab-banner-data" alt="beb-eab-banner-data" <?php if (!isset($contenuto[$i]['data_evento_giorno'])) {
                                            echo 'style="display: none;"';}?>>
                    					<h1><?php echo @$contenuto[$i]['data_evento_giorno']; ?></h1>
                    					<h2><?php echo @array_search($contenuto[$i]['data_evento_mese'], $this->mappa_mese_abr_mese); ?></h2>
                    				</div>
                    			<?php endif; ?>
                    			<?php if (isset($contenuto[$i]['url_img']) or $visualizza): ?>
                    		        <div id="beb-eab-banner-immagine" <?php if (!isset($contenuto[$i]['url_img'])) {
                                            echo 'style="display: none;"';}?>>
                    		            <img alt="" src="<?php echo $contenuto[$i]['url_img']; ?>">
                    		        </div>
                    	        <?php endif; ?>
                    	        <?php if (isset($this->contenuto[$i]['link_totale']) and $this->contenuto[$i]['link_totale'] === 'si'
                    	             and !is_admin()): ?>
                                    <a href="<?php echo $this->contenuto[$i]['link_totale']; ?>">
                            	<?php endif; ?>
                    			<div id="beb-eab-banner-cont-testo">
                    				<div id="beb-eab-banner-testo">
                    					<h1 style="height: <?php echo $altezza_titolo; ?>; line-height: <?php echo $altezza_titolo; ?>; margin: <?php echo $margin_titolo; ?>;">
                    					   <?php echo $contenuto[$i]['titolo']; ?>
                    					</h1>
                    					<?php if (isset($contenuto[$i]['sottotitolo']) or $visualizza): ?>
                    					    <h2 style="height: <?php echo $altezza_sottotitolo; ?>; line-height: <?php echo $altezza_sottotitolo; ?>; margin: <?php echo $margin_sottotitolo; ?>;">
                    					       <?php echo @$contenuto[$i]['sottotitolo']; ?>
                    					    </h2>
                    					<?php endif; ?>
                    					<?php if (isset($contenuto[$i]['descrizione']) or $visualizza): ?>
                    					   <p style="line-height: <?php echo $altezza_descrizione; ?>;">
                    					       <?php echo @$contenuto[$i]['descrizione']; ?>
                    					   </p>
                    					<?php endif; ?>
                    				</div>
                    				<?php if ((isset($contenuto[$i]['link_sx_titolo']) and isset($contenuto[$i]['link_dx_titolo'])) or 
                    				    (isset($contenuto[$i]['link_sx_titolo']) or isset($contenuto[$i]['link_dx_titolo'])) or $visualizza): ?>
                        				<div id="beb-eab-banner-cont-prenota">
                        				    <?php if (isset($contenuto[$i]['link_sx_titolo']) or $visualizza): ?>
                        				        <div id="beb-eab-banner-cont-prenota-sx">
                        				            <a href="<?php echo (is_admin() ? '#' : @$contenuto[$i]['link_sx']); ?> ">
                                                        <?php echo @$contenuto[$i]['link_sx_titolo']; ?>
                                                    </a>
                        				        </div>
                                                <!-- </h2> -->
                                                <!-- <h2 id="beb-eab-banner-cont-prenota-sx">
                                                    <?php if (!is_admin()): ?>
                                                    <a href="<?php echo @$contenuto[$i]['link_sx']; ?>">
                                                    <?php endif;?>
                                                        <?php echo @$contenuto[$i]['link_sx_titolo']; ?>
                                                    <?php if (!is_admin()): ?>
                                                    </a>
                                                    <?php endif;?>
                                                </h2> -->
                                            <?php endif; ?>
                                            <?php if (isset($contenuto[$i]['link_dx_titolo']) or $visualizza): ?>
                            					<div id="beb-eab-banner-cont-prenota-dx">
                            					    <a href="<?php echo (is_admin() ? '#' : @$contenuto[$i]['link_dx']); ?>">
                            					       <?php echo @$contenuto[$i]['link_dx_titolo']; ?>
                            					    </a>
                            					</div>
                            					<!-- 
                            					<h2 id="beb-eab-banner-cont-prenota-dx">
                            					    <?php if (!is_admin()): ?>
                            					    <a href="<?php echo @$contenuto[$i]['link_dx']; ?>">
                            					    <?php endif;?>
                            					       <?php echo @$contenuto[$i]['link_dx_titolo']; ?>
                            					    <?php if (!is_admin()): ?>
                            					    </a>
                            					    <?php endif;?>
                            					</h2>
                            					 -->
                        					<?php endif; ?>
                        				</div>
                    				<?php endif; ?>
                    			</div>
                    			<?php if (isset($this->contenuto[$i]['link_totale']) and
                    			    $this->contenuto[$i]['link_totale'] === 'si' and !is_admin()): ?>
                                    </a>
                            	<?php endif; ?>
                    		 </div>
                    		 <div class="beb-eab-spazio-banner2"></div>
                    		 <div class="beb-eab-banner-freccia">
                    	 		<img id="beb-eab-banner-bottone" src="<?php echo $this->url.'img/freccia_sx.png'; ?>" />
                    		 </div>
                    	</div>
                    </div>
                </div>
    <?php
            }
        }
	}
    final protected function beb_events_ads_banner_accessori_epura_chiave ($chiave) {
        $risultato = explode('_', $chiave);
        $risultato = end($risultato);
        return $risultato;
    }
}
?>