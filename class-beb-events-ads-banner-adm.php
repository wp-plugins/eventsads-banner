<?php
require_once 'class-beb-events-ads-banner-op-com.php';

class Beb_Events_Ads_Banner_Adm extends Beb_Events_Ads_Banner_Op_Com {
    private static $form_elenco = array(
        'beb_eab_idEvento',
	    'nome_evento',
	    'priorita',
	    'titolo',
        'sottotitolo',
	    'descrizione',
	    'beb_eab_giorno_p',
	    'beb_eab_mese_p',
	    'beb_eab_giorno_r',
	    'beb_eab_mese_r'
    );
    private $add_banner_mappa_form_text_titolo_nome;
    private $add_banner_mappa_form_suggerimento_text_titolo_nome;
    private $add_banner_mappa_form_text_link;
    private $add_banner_mappa_form_suggerimento_text_link;
    private $add_banner_mappa_form_suggerimento_select_titolo_nome;
    private $set_banner_mappa_form_text_titolo_nome;
    private $set_banner_mappa_form_suggerimento_text_titolo_nome;
    private static $ancora_add_banner_traduzione = 'nome_evento';
    private static $ancora_traduzione_nome_univoco = 'beb_eab_banner - ';
    private static $prefisso_form_add = 'beb-events-ads-banner';
    private static $nome_pagina_prev = 'beb-eab-preview';
    private static $nome_pagina_add = 'beb-eab-add-banner';
    private static $nome_pagina_settings = 'beb-eab-settings';
    private static $nome_form_prev_tmp = 'beb_eab_banner_preview';
    private static $nome_form_add_tmp = 'beb_eab_banner_new';
    private static $nome_form_settings_tmp = 'beb_eab_banner_settings';
    private $nuovo_banner = NULL;
    private $banner_da_modificare;
    private $impostazioni_banner;
    private $mese_corrente;
    private $giorno_corrente;
    private $anno_corrente;
    private $num_banner;
    private static $dati_caricati = 'no';
    private $chiave_contenuto;
    private $messaggio = NULL;
    private $tipo_mess = NULL;
    private $hook_pagine;
    private $hook_pag_preview;
    private $hook_pag_add;
    private $hook_pag_settings;
    
    private $prova;
    
    private $conta = 0;
    
    final public function __construct($aggiorna_stato = NULL) {
        parent::__construct();
        if (!has_action('beb_eab_adm_add')) {
            add_action( 'admin_init', array($this, 'beb_eab_adm_add'));
            add_action( 'admin_init', array($this, 'beb_eab_adm_riassunto'));
            add_action( 'admin_init', array($this, 'beb_eab_adm_settings'));
            // MENU PANNELLO ADM
            add_action('admin_menu', array ($this, 'beb_eab_adm_carica_menu'));
            // FOGLI DI STILE E SCRIPT
            add_action('admin_init', array($this, 'beb_eab_adm_registra_stile_script'));
            add_action( 'admin_enqueue_scripts', array($this, 'beb_eab_adm_carica_stile_script') );
            //add_action( 'admin_enqueue_scripts', array($this, 'mw_enqueue_color_picker'));
            // VISUALIZZA MESSAGGI
            add_action( 'admin_notices', array ($this, 'beb_eab_adm_visualizza_messaggi'));
            //add_action( 'admin_notices', array ($this, 'beb_eab_adm_visualizza_st_messaggi'));
        }
        $this->giorno_corrente = date('d', time());
        $this->mese_corrente = date('m', time());
        $this->anno_corrente = date('Y', time());
        //self::beb_cv_carica_dati();
        /*
        if (isset($aggiorna_stato)) {
            self::beb_eab_adm_riassunto ($aggiorna_stato);
        }
        */
    }

    final static public function beb_eab_adm_attivazione_plugin() {
        // Aggiungo le impostazioni del plugin a WP
        $data_attuale = date ('Y-m-d H:i:s');
        global $wpdb;
        if (!get_option(parent::$wp_options_prefisso."plugin_attivo")) {
            update_option(parent::$wp_options_prefisso."plugin_attivo", true);
            update_option(parent::$wp_options_prefisso."data_attivazione", $data_attuale);
        }
        if (!get_option(parent::$wp_options_prefisso."data_installazione")) {
            add_option(parent::$wp_options_prefisso."versione", BEBCVBANNER_VERSION);
            add_option(parent::$wp_options_prefisso."data_installazione", $data_attuale);
            add_option(parent::$wp_options_prefisso.'impostazioni', parent::$impostazioni_iniziali, '', 'no');
        }
    }
	final static public function beb_eab_adm_disattivazione_plugin () {
	    global $wpdb;
	    if (update_option(parent::$wp_options_prefisso."plugin_attivo", false)){
	        // ins. mess. per debug
	    }
	    delete_option(parent::$wp_options_prefisso.'contenuto');
	}
	
	/*
	 public function mw_enqueue_color_picker () {
	 wp_enqueue_style( 'wp-color-picker' );
	 wp_enqueue_script( 'my-script-handle', plugins_url('my-script.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
	 }
	 */
	
	public function beb_eab_adm_carica_menu () {
	    self::carica_menu();
	}
	private function carica_menu () {
	    $this->hook_pag_preview = add_menu_page(
	       'Events/ADS Banner',     // titolo scheda 
	       'Events/ADS Banner',     // titolo menu (e prima voce link)
	       'administrator',    // permessi per poterlo vedere
	       self::$nome_pagina_prev, // ancora tra il link e la pagina (o anche nome interno della pagina)
            array($this, 'beb_eab_adm_riassunto_contenuto')    // funzione con la pagina
	    );
	    $this->hook_pag_add = add_submenu_page(
            self::$nome_pagina_prev,
            __("Create New Banner", parent::$prefisso_nome),
            __("Add Banner", parent::$prefisso_nome),
            'administrator',
            self::$nome_pagina_add,
            array($this, "beb_eab_adm_add_contenuto")
	    );
	    $this->hook_pag_settings = add_submenu_page(
    	    self::$nome_pagina_prev,
    	    __("Set the Banner", parent::$prefisso_nome),
    	    __("Banner Settings", parent::$prefisso_nome),
    	    'administrator',
    	    self::$nome_pagina_settings,
    	    array($this, 'beb_eab_adm_settings_contenuto')
	    );
	}
	
	public function beb_eab_adm_registra_stile_script () {
	    self::registra_stile_script();
	}
	private function registra_stile_script () {
       wp_register_style (parent::$prefisso_nome.'-adm.css', $this->url.'css/'.parent::$prefisso_nome.'-adm.css', array(), BEBCVBANNER_VERSION_CSS_ADM);
       wp_register_style (parent::$prefisso_nome.'-adm-add.css', $this->url.'css/'.parent::$prefisso_nome.'-adm-add.css', array(), BEBCVBANNER_VERSION_CSS_ADM_ADD);
       wp_register_style (parent::$prefisso_nome.'-adm-prev.css', $this->url.'css/'.parent::$prefisso_nome.'-adm-prev.css', array(), BEBCVBANNER_VERSION_CSS_ADM_PREV);
       wp_register_style (parent::$prefisso_nome.'-adm-set.css', $this->url.'css/'.parent::$prefisso_nome.'-adm-set.css', array(), BEBCVBANNER_VERSION_CSS_ADM_SET);
       wp_register_script (parent::$prefisso_nome.'-adm-add.js', $this->url.'js/'.parent::$prefisso_nome.'-adm-add.js', array('jquery'),
       BEBCVBANNER_VERSION_JS_ADM_ADD);
       wp_register_script (parent::$prefisso_nome.'-adm-prev.js', $this->url.'js/'.parent::$prefisso_nome.'-adm-prev.js', array('jquery'),
       BEBCVBANNER_VERSION_JS_ADM_PREV);
       wp_register_script (parent::$prefisso_nome.'-adm-set.js', $this->url.'js/'.parent::$prefisso_nome.'-adm-set.js', array('jquery'),
       BEBCVBANNER_VERSION_JS_ADM_SET);
	}
	public function beb_eab_adm_carica_stile_script ($hook) {
	    $hook_pagine [] = $this->hook_pag_add;
	    $hook_pagine [] = $this->hook_pag_preview;
	    $hook_pagine [] = $this->hook_pag_settings;
	    if (array_search($hook, $hook_pagine) === false) {
	        return;
	    }
	    wp_enqueue_style (parent::$prefisso_nome.'-adm.css', false, array(), BEBCVBANNER_VERSION_CSS_ADM);
	    $custom_css = '#beb-eab-contenitore-admin #beb-eab-contenitore-banner { ';
	    /*if ($hook === 'eventsads-banner_page_beb-eab-add-banner') {*/
	    if ($hook === $this->hook_pag_add) {
	        $custom_css .= 'top: 114px;';
	        wp_enqueue_style (parent::$prefisso_nome.'-adm-add.css', false, array(), BEBCVBANNER_VERSION_CSS_ADM_ADD);
	        wp_enqueue_script(parent::$prefisso_nome.'-adm-add.js', false, array(), BEBCVBANNER_VERSION_JS_ADM_ADD);
	    } elseif ($hook === $this->hook_pag_preview) {
	        $custom_css .= 'top: 40px;';
	        wp_enqueue_style (parent::$prefisso_nome.'-adm-prev.css', false, array(), BEBCVBANNER_VERSION_CSS_ADM_PREV);
	        wp_enqueue_script(parent::$prefisso_nome.'-adm-prev.js', false, array(), BEBCVBANNER_VERSION_JS_ADM_PREV);
	    } elseif ($hook === $this->hook_pag_settings) {
	        $custom_css .= 'top: 40px;';
	        wp_enqueue_style (parent::$prefisso_nome.'-adm-set.css', false, array(), BEBCVBANNER_VERSION_CSS_ADM_SET);
	        wp_enqueue_script(parent::$prefisso_nome.'-adm-set.js', false, array(), BEBCVBANNER_VERSION_JS_ADM_SET);
	    }
	    $custom_css .= ' }';
	    wp_add_inline_style( parent::$prefisso_nome.'-adm.css', $custom_css );
	}
	
	/*
	 * PAGINA RIASSUNTO BANNER
	 */
	public function beb_eab_adm_riassunto () {
	    register_setting (
	    self::$nome_form_prev_tmp.'_gr',   // Option group
	    self::$nome_form_prev_tmp,        // Option name
	    array($this, 'beb_eab_adm_sanitize_preview') // Sanitize
	    );
	}
	public function beb_eab_adm_riassunto_contenuto () {
	    $contenuto = get_option(parent::$wp_options_prefisso.'contenuto');
	    if ($contenuto == false) {
	        $contenuto = $this->contenuto;
	    }
	    $nome_arr_form = self::$nome_form_prev_tmp; ?>
        <style type="text/css">
    	    #beb-eab-banner-riass-form .beb-eab-stato-banner-riassunto:HOVER {
    	    	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/on_off.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0) !important;
    	    }
    	    	
            #beb-eab-banner-remove {
    	    	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/delete.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
    	    }
	    	
            #beb-eab-banner-remove:hover {
            	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/delete_hover.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
            }
            
            #beb-eab-banner-remove:active {
            	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/delete_click.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
                height: 25px;
    	    	width: 25px;
            }
 	
            #beb-eab-banner-edit {
    	    	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/edit.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
    	    }
	    	
            #beb-eab-banner-edit:hover {
            	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/edit_hover.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
            }
            
            #beb-eab-banner-edit:active {
            	background: url('<?php echo BEBCVBANNER__PLUGIN_URL; ?>img/edit_click.png') no-repeat scroll 0 0 rgba(0, 0, 0, 0);
                height: 25px;
    	    	width: 25px;
            }            	
	    </style>
	    <?php if (isset($this->contenuto[0]['banner_esempio'])): ?>
	       <h1 class="beb-eab-riass-add">
               <a href="<?php echo site_url();?>/wp-admin/admin.php?page=<?php echo self::$nome_pagina_add; ?>"><?php echo __("Create first banner", parent::$prefisso_nome); ?></a>
           </h1>
	    <?php //else: ?>
	    <!-- 
	       <h1 class="beb-eab-riass-add">
               <a href="<?php echo site_url();?>/wp-admin/admin.php?page=beb-cv-admin-banner-pro"><?php echo __("Add more", parent::$prefisso_nome); ?></a>
           </h1>
         -->
	    <?php endif; ?>
	    <div id="beb-eab-contenitore-admin" style="min-height: 250px;">
	       <form method="post" action="options.php" id="beb-eab-banner-riass-form">
            <?php
                $n_contenuti = count($contenuto);
                for ($i = 0; $i < $n_contenuti; $i++): ?>
                <?php settings_fields(self::$nome_form_prev_tmp.'_gr'); ?>
                <h1 style="text-align: center; margin: 0 0 7px;">
                    <?php echo __("Event Name: ", parent::$prefisso_nome).' <i>"'.$contenuto[$i]['nome_evento'].'"</i>'; ?>
                </h1>
                <div style="padding: 0; border: none;">
                    <?php if (!isset($contenuto[$i]['banner_esempio'])) :?>
                        <div class="beb-eab-stato-banner-riassunto" style="background: url('<?php echo BEBCVBANNER__PLUGIN_URL.'img/'.
                                    $contenuto[$i]['stato_banner'].'.png'; ?>') no-repeat scroll 0 0 rgba(0, 0, 0, 0);">
                            <input type="hidden" value="<?php echo $contenuto[$i]['stato_banner']; ?>" name="<?php echo $nome_arr_form; ?>[stato_banner]" />
                            <input type="hidden" value="<?php echo $i; ?>" name="<?php echo $nome_arr_form; ?>[banner_id_array]" />
                            <p class="submit">
                            	<input id="submit" type="submit" class="button button-primary" value="" name="submit" />
                            </p>
                        </div>
                        <div class="beb-eab-proprieta-banner" style="padding: 0; width: 40%; <?php if (isset($contenuto[$i]['immagine'])) {
                            echo 'margin-top: ';
                        }?>">
                            <div id="beb-eab-banner-operazioni">
                                <a href="<?php echo site_url();?>/wp-admin/admin.php?page=beb-eab-add-banner&beb_eab_banner_modifica=<?php echo $i;?>">
                                    <div id="beb-eab-banner-edit"></div>
                                </a>
                                <div id="beb-eab-banner-remove">
                                    <input type="hidden" value="no" name="<?php echo $nome_arr_form; ?>[remove_banner]" />
                                	<input type="submit" name="submit" value="&nbsp;" />
                                </div>
                            </div>
                            <p>
                                <strong><?php echo __("Open the Banner", parent::$prefisso_nome); ?>:</strong>
                                <select name="<?php echo $nome_arr_form; ?>[open]" style="margin-left: 20px;">
                                    <option value="home" <?php if ('home' === $contenuto[$i]['open']) { echo 'selected="selected"' ;}?>>
                                        <?php echo __("Open only in Home Page", parent::$prefisso_nome); ?>
                                    </option>
                                    <option value="all" <?php if ('all' === $contenuto[$i]['open']) { echo 'selected="selected"' ;}?>>
                                        <?php echo __("Open in all site", parent::$prefisso_nome); ?>
                                    </option>
                                    <option value="never" <?php if ('never' === $contenuto[$i]['open']) { echo 'selected="selected"' ;}?>>
                                        <?php echo __("Never open", parent::$prefisso_nome); ?>
                                    </option>
                                </select>
                            </p>
                            <p class="beb-eab-add-banner-sugg">
                    	       <?php echo __("(Open the banner only in the home page, or in all site, or never)", parent::$prefisso_nome); ?>
                    	    </p>   
                    	    <?php if (isset($contenuto[$i]['immagine']) and !empty($contenuto[$i]['immagine'])):?>
                                <p>
                                    <strong><?php echo __("Show Image in Closed Mode", parent::$prefisso_nome); ?>:</strong><br />
                                    <input type="radio" name="<?php echo $nome_arr_form; ?>[immagine_show]" value="show" style="margin-left: 25%;"
                                        <?php if ('show' === $contenuto[$i]['immagine_show']) { echo 'checked="checked"'; } ?>>
                                    <?php echo __("Show", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
                            	    <input type="radio" name="<?php echo $nome_arr_form; ?>[immagine_show]" value="hide" 
                            	       <?php if ('hide' === $contenuto[$i]['immagine_show']) { echo 'checked="checked"'; } ?>>
                            	    <?php echo __("Hide", parent::$prefisso_nome); ?>
                                </p>
                                <p class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(When the banner is closed you prefer show the image?)", parent::$prefisso_nome); ?>
                        	    </p>
                            <?php endif;?>
                            <p>&nbsp;</p>
                            <p class="submit">
                            	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", parent::$prefisso_nome); ?>"
                            	   name="<?php echo $nome_arr_form; ?>[submit]" style="margin-left: 45%;" />
                            </p>
                        </div>
                    <?php endif; ?>
                    <?php parent::beb_cv_banner(); ?>
                </div>
            <?php endfor; ?>
            </form>
	    </div>
	    <!-- 
	    <div id="beb-eab-contenitore-admin" style="min-height: 190px;">
	    <?php
	    print_r($this->hook_pagine);
	    echo '<br><br><br>';
	    echo '<pre>';
	    print_r($contenuto);
	    echo '</pre>';
	    ?>
	    </div>
	     -->
    	<?php
	}	
	
    private function beb_cv_popup($testo) { ?>
        <div id="beb-eab-banner-popup-body"></div>
        <div id="beb-eab-banner-popup-contenitore">
            <h1 style="text-align: center; color: #ff0000;"><?php echo $testo; ?></h1>
            <p class="submit" style="margin-left: 10%;">
                <input id="beb-eab-banner-popup-annulla" class="button delete" type="submit" value="<?php echo __("Cancel", parent::$prefisso_nome); ?>" name="submit">
            	<input id="submit" class="button button-primary" type="submit" value="<?php echo __("Yes", parent::$prefisso_nome); ?>" name="submit" />
            </p>
        </div>
        <?php
    }
	
	/*
	 * PAGINA PER L'AGGIUNTA DI UN BANNER
	 */
	public function beb_eab_adm_add () {
	    register_setting (
	    self::$nome_form_add_tmp.'_gr',   // Option group
	    self::$nome_form_add_tmp,        // Option name
	    array($this, 'beb_eab_adm_sanitize_add') // Sanitize
	    );
	}
	public function beb_eab_adm_add_contenuto ($contenuto=NULL) {
	    $stato_banner = 'off';
	    $nome_evento = 'placeholder="'.__("Insert Banner's Name here.", parent::$prefisso_nome).'"';
	    
	    if (isset($_GET['beb_eab_banner_modifica'])) {
	        $arr_tmp = array('nome_evento', 'titolo', 'sottotitolo', 'link_sx_titolo', 'link_sx', 'link_dx_titolo', 'link_dx', 'immagine');
	        $contenuto_prev [0] = $this->contenuto[$_GET['beb_eab_banner_modifica']];
	        $contenuto [0] = $this->contenuto[$_GET['beb_eab_banner_modifica']];
	        $contenuto [0]['titolo_pagina'] = __('CHANGE', parent::$prefisso_nome).' "'.$contenuto [0]['nome_evento'].'"';
	        $contenuto [0]['sottotitolo_pagina'] = NULL;
	        foreach ($contenuto[0] as $chiave => $valore) {
	            if (false !== array_search($chiave, $arr_tmp) and !empty($valore)) {
                   $contenuto [0][$chiave] = 'value="'.$valore.'"';
	            }
	        }
	        $contenuto [0]['link_sx_titolo_check'] = true;
	        $contenuto [0]['link_dx_titolo_check'] = true;
	        $contenuto [0]['immagine_check'] = true;
	        if (empty($contenuto [0]['nome_evento'])) {
	            $contenuto [0]['nome_evento'] = 'placeholder="'.__("Insert Banner's Name here.", parent::$prefisso_nome).'"';
	        }
	        if (empty($contenuto [0]['titolo'])) {
	            $contenuto [0]['titolo'] = 'placeholder="'.__("Insert Title here", parent::$prefisso_nome).'"';
	        }
	        if (empty($contenuto [0]['sottotitolo'])) {
	            $contenuto [0]['sottotitolo'] = 'placeholder="'.__("Insert Subtitle here", parent::$prefisso_nome).'"';
	        }
	        if (empty($contenuto [0]['descrizione'])) {
	            $contenuto [0]['descrizione'] = NULL;
	        } else {
	            $contenuto [0]['descrizione'] = '>'.$contenuto [0]['descrizione'];
	        }
	        if (empty($contenuto [0]['link_sx_titolo'])) {
	            $contenuto [0]['link_sx_titolo'] = 'placeholder="'.__("Insert left title link here", parent::$prefisso_nome).'"';
	            $contenuto [0]['link_sx_titolo_check'] = false;
	        }
	        if (empty($contenuto [0]['link_sx'])) {
	            $contenuto [0]['link_sx'] = 'placeholder="'.__("http://www.yourlink.link", parent::$prefisso_nome).'"';
	        }
	        if (empty($contenuto [0]['link_dx_titolo'])) {
	            $contenuto [0]['link_dx_titolo'] = 'placeholder="'.__("Insert right title link here", parent::$prefisso_nome).'"';
	            $contenuto [0]['link_dx_titolo_check'] = false;
	        }
	        if (empty($contenuto [0]['link_dx'])) {
	            $contenuto [0]['link_dx'] = 'placeholder="'.__("http://www.yourlink.link", parent::$prefisso_nome).'"';
	        }
	        if (empty($contenuto [0]['immagine'])) {
	            $contenuto [0]['immagine'] = 'placeholder="'.__("Insert Image link here", parent::$prefisso_nome).'"';
	            $contenuto [0]['immagine_check'] = false;
	        }
	    } else {
	        $contenuto_prev [0] = array('nome_evento' => '', 'titolo' => '');
	        $contenuto = array(
	            0 => array(
	                'titolo_pagina' => __('INSERT NEW EVENT', parent::$prefisso_nome).' - '.$this->giorno_corrente.'/'.
	                       $this->mese_corrente.'/'.$this->anno_corrente,
	                'sottotitolo_pagina' => '*'.__("unfortunately for now you can only create one banner", parent::$prefisso_nome),
	                'stato_banner' => 'off',
	                'nome_evento' => 'placeholder="'.__("Insert Banner's Name here.", parent::$prefisso_nome).'"',
	                'titolo' => '',
	                'open' => 'home',
	                'data_evento_giorno' => NULL,
	                'data_evento_mese' => NULL,
	                'titolo' => 'placeholder="'.__("Insert Title here", parent::$prefisso_nome).'"',
	                'sottotitolo' => 'placeholder="'.__("Insert Subtitle here", parent::$prefisso_nome).'"',
	                'descrizione' => NULL,
	                'link_sx_titolo_check' => false,
	                'link_sx_titolo' => 'placeholder="'.__("Insert left title link here", parent::$prefisso_nome).'"',
	                'link_sx' => 'placeholder="'.__("http://www.yourlink.link", parent::$prefisso_nome).'"',
	                'link_dx_titolo_check' => false,
	                'link_dx_titolo' => 'placeholder="'.__("Insert right title link here", parent::$prefisso_nome).'"',
	                'link_dx' => 'placeholder="'.__("http://www.yourlink.link", parent::$prefisso_nome).'"',
	                'immagine_check' => false,
	                'immagine' => 'placeholder="'.__("Insert Image link here", parent::$prefisso_nome).'"',
	                'immagine_show' => 'hide'
	            )
	        );
	    }
	    
	    $nome_arr_form = self::$nome_form_add_tmp;
	    $visualizza = true;
	    ?>
	    <style>
            #beb-eab-contenitore-admin #beb-eab-contenitore-banner {
                right: 75px !important;
            }
            <?php if (!$contenuto [0]['link_sx_titolo_check']): ?>
                .beb-eab-add-banner-link.beb-eab-add-sx, .beb-eab-add-banner-sugg.beb-eab-add-sx {
                    display:none;
                }
            <?php endif;?>
            <?php if (!$contenuto [0]['link_dx_titolo_check']): ?>
                .beb-eab-add-banner-link.beb-eab-add-dx, .beb-eab-add-banner-sugg.beb-eab-add-dx {
                    display:none;
                }
            <?php endif;?>
            <?php if (!$contenuto [0]['immagine_check']): ?>
                .beb-eab-add-banner-media {
                    display:none;
                }
            <?php endif;?>
            
        </style>
	    <div id="beb-eab-contenitore-admin" class="beb-eab-imp-add" style="margin: 1% 3% 3%;">
	       <h1 style="line-height: 26px; margin: 0; text-align: center; width: 100%;">
                <?php echo $contenuto[0]['titolo_pagina']; ?>
            </h1>
            <h4 style="color: #ff0000; text-align: center; width: 100%;">
                <?php echo $contenuto[0]['sottotitolo_pagina']; ?>
    	    </h4>
            <?php parent::beb_cv_banner($contenuto_prev, $visualizza);?>
	       <form method="post" action="options.php">
	           <?php settings_fields($nome_arr_form.'_gr'); ?>
               <fieldset class="contenitore-input beb-eab-add-options-el" style="float: none; padding: 0 3% 3%;">
                    <legend><?php echo __("Options", parent::$prefisso_nome); ?></legend>
                    <div class="beb-eab-stato-banner-riassunto"
                            style="background: url('<?php echo BEBCVBANNER__PLUGIN_URL.'img/off.png'; ?>') no-repeat scroll 0 0 rgba(0, 0, 0, 0);">
                        <input type="hidden" name="<?php echo $nome_arr_form; ?>[stato_banner]" value="<?php echo $contenuto[0]['stato_banner']; ?>" />
                        &nbsp;
                    </div>
                    <strong><?php echo __("Banner ID Name", parent::$prefisso_nome); ?><span style="color: #ff0000;">*</span> :</strong><br />
                    <input type="text" id="beb-eab-nome-evento" name="<?php echo $nome_arr_form; ?>[nome_evento]" <?php echo $contenuto[0]['nome_evento']; ?> />
                    <p class="beb-eab-add-banner-sugg">
            	       <?php echo '(<strong style="color: #ff0000;">'.__("Required - Unique Key ID</strong> - Max ", parent::$prefisso_nome).
                        ' <strong>40</strong> '.__(" characters)", parent::$prefisso_nome); ?>
            	    </p>
            	    <strong><?php echo __("Open the Banner", parent::$prefisso_nome); ?>:</strong><br />
                    <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="home" style="margin-left: 15px;"
                            <?php if ($contenuto[0]['open'] === 'home') {echo 'checked="checked"'; }?>>
            	       <?php echo __("Open only in Home Page", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
            	    <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="all" <?php if ($contenuto[0]['open'] === 'all') {echo 'checked="checked"'; }?>>
            	       <?php echo __("Open in all site", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
            	    <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="never" <?php if ($contenuto[0]['open'] === 'never') {echo 'checked="checked"'; }?>>
            	       <?php echo __("Never open", parent::$prefisso_nome); ?><br />
            	    <p class="beb-eab-add-banner-sugg" style="margin: 0;">
            	       <?php echo __("(Open the banner only in the home page, or in all site, or never)", parent::$prefisso_nome); ?>
            	    </p>
               </fieldset>
               
               <fieldset class="contenitore-input beb-eab-add-texts-el" style="">
                    <legend><?php echo __("Texts", parent::$prefisso_nome); ?></legend>
                    <strong><?php echo __("Date", parent::$prefisso_nome); ?>:</strong><br />
                    <select name="<?php echo $nome_arr_form; ?>[data_evento_giorno]" id="beb-eab-giorno">
                        <option selected="selected" <?php if (!empty($contenuto[0]['data_evento_giorno'])){ echo 'value="'.$contenuto[0]['data_evento_giorno'].'"'; }?>>
                            <?php if (!empty($contenuto[0]['data_evento_giorno'])){ echo $contenuto[0]['data_evento_giorno']; } ?>
                        </option>
                        <?php
                        for ($i = 1; $i <= 31; $i++) {
                           if ($i < 10) {
                               $i = '0'.$i;
                           }
                           echo '<option value="'.$i.'">'.$i.'</option>';
                        } ?>
                    </select>
                    <select name="<?php echo $nome_arr_form; ?>[data_evento_mese]" id="beb-eab-mese">
                        <option selected="selected" <?php if (!empty($contenuto[0]['data_evento_mese'])){ echo 'value="'.$contenuto[0]['data_evento_mese'].'"'; }?>>
                            <?php if (!empty($contenuto[0]['data_evento_mese'])){ echo $contenuto[0]['data_evento_mese']; } ?>
                        </option>
                        <?php foreach ($this->mappa_mese_numero as $mese => $numero) {
                        	echo '<option value="'.$numero.'">'.$mese.'</option>';
                        } ?>
                    </select>
                    <p class="beb-eab-add-banner-sugg"><?php echo __("(<strong>Optional</strong> - The date that appears in the Banner)", parent::$prefisso_nome); ?></p>
            	    <strong><?php echo __("Title", parent::$prefisso_nome); ?><span style="color: #ff0000;">*</span> :</strong><br />
                    <input type="text" id="beb-eab-titolo" name="<?php echo $nome_arr_form; ?>[titolo]" <?php echo $contenuto[0]['titolo']; ?> />
                    <p class="beb-eab-add-banner-sugg">
            	       <?php echo '(<strong style="color: #ff0000;">'.__("Required</strong> - Max ", parent::$prefisso_nome).
                	    ' <strong>20</strong> '.__(" characters)", parent::$prefisso_nome); ?>
            	    </p>
            	    <strong><?php echo __("Subtitle", parent::$prefisso_nome); ?>:</strong><br />
                    <input type="text" id="beb-eab-sottotitolo" name="<?php echo $nome_arr_form; ?>[sottotitolo]" <?php echo $contenuto[0]['sottotitolo']; ?> />
                    <p class="beb-eab-add-banner-sugg">
            	       <?php echo __("(<strong>Optional</strong> - Max ", parent::$prefisso_nome).' <strong>28</strong> '.__(" characters)", parent::$prefisso_nome); ?>
            	    </p>
                    <strong><?php echo __("Description", parent::$prefisso_nome); ?>:</strong><br />
                    <textarea id="beb-eab-descrizione" name="<?php echo $nome_arr_form; ?>[descrizione]" <?php if(empty($contenuto[0]['descrizione'])){
                        echo 'placeholder="'.__("Enter the Event's description here", parent::$prefisso_nome).'"';}?>><?php
                    if(!empty($contenuto[0]['descrizione'])){ echo $contenuto[0]['descrizione']; } ?></textarea>
                    <p class="beb-eab-add-banner-sugg"><?php echo __("(<strong>Optional</strong> - Max ", parent::$prefisso_nome); ?> 
    	               <span class="beb-eab-add-banner-numeri">195</span> <?php echo __(" characters)", parent::$prefisso_nome); ?>
    	            </p>
               </fieldset>
               
                <fieldset class="contenitore-input beb-eab-add-links-el" style="">
                    <legend><?php echo __("Links", parent::$prefisso_nome); ?></legend>
                    <strong><?php echo __("All Banner is a link ?", parent::$prefisso_nome); ?></strong>
                    <input type="checkbox" id="beb-eab-link-totale" name="<?php echo $nome_arr_form; ?>[link_totale]" value="si"
                    <?php if (isset($contenuto[0]['link_totale'])) {
            	        echo 'checked="checked"';
            	    } ?> />&nbsp;&nbsp;&nbsp;
            	    <p class="beb-eab-add-banner-sugg">
            	       <?php echo __("(<strong>Optional</strong> - If checked all banner will be linkable)", parent::$prefisso_nome); ?>
            	    </p>
            	    <strong><?php echo __("Left link Title", parent::$prefisso_nome); ?>:</strong><br />
                    <input type="text" id="beb-eab-link-sx-titolo" name="<?php echo $nome_arr_form; ?>[link_sx_titolo]" <?php echo $contenuto[0]['link_sx_titolo'];?> />
            	    <p class="beb-eab-add-banner-sugg">
            	       <?php echo __("(<strong>Optional</strong> - This will be the left link title - Max ", parent::$prefisso_nome).
            	       ' <strong>20</strong> '.__(" characters)", parent::$prefisso_nome); ?>
            	    </p>
            	    <p class="beb-eab-add-banner-link beb-eab-add-sx">
                	    <strong><?php echo __("Left link URL", parent::$prefisso_nome); ?>:</strong><br />
                        <input type="text" id="beb-eab-link-sx" name="<?php echo $nome_arr_form; ?>[link_sx]" <?php echo $contenuto[0]['link_sx'];?> />
                    </p>
            	    <p class="beb-eab-add-banner-sugg beb-eab-add-sx">
            	       <?php echo __("(<strong>Optional</strong> - Left link)", parent::$prefisso_nome); ?>
            	    </p>
            	    <strong><?php echo __("Right link Title", parent::$prefisso_nome); ?>:</strong><br />
                    <input type="text" id="beb-eab-link-dx-titolo" name="<?php echo $nome_arr_form; ?>[link_dx_titolo]" <?php echo $contenuto[0]['link_dx_titolo'];?> />
            	    <p class="beb-eab-add-banner-sugg">
            	       <?php echo __("(<strong>Optional</strong> - This will be the right link title - Max ", parent::$prefisso_nome).
            	       ' <strong>20</strong> '.__(" characters)", parent::$prefisso_nome); ?>
            	    </p>
            	    <p class="beb-eab-add-banner-link beb-eab-add-dx">
                	    <strong><?php echo __("Right link URL", parent::$prefisso_nome); ?>:</strong><br />
                        <input type="text" id="beb-eab-link-dx" name="<?php echo $nome_arr_form; ?>[link_dx]" <?php echo $contenuto[0]['link_dx'];?> />
                    </p>
            	    <p class="beb-eab-add-banner-sugg beb-eab-add-dx">
            	       <?php echo __("(<strong>Optional</strong> - Right link)", parent::$prefisso_nome); ?>
            	    </p>
                </fieldset>
                
                <fieldset class="contenitore-input beb-eab-add-media-el" style="">
                    <legend><?php echo __("Media", parent::$prefisso_nome); ?></legend>
                    <strong><?php echo __("Image", parent::$prefisso_nome); ?>:</strong><br />
                    <input type="text" id="beb-eab-immagine" name="<?php echo $nome_arr_form; ?>[immagine]" <?php echo $contenuto[0]['immagine'];?> />
                    <p class="beb-eab-add-banner-sugg"><?php echo __("(<strong>Optional - RECOMMENDED height 120 px</strong>)", parent::$prefisso_nome); ?></p>
            	    <p class="beb-eab-add-banner-media">
                	    <strong><?php echo __("Show Image in Closed Mode", parent::$prefisso_nome); ?>:</strong><br />
                        <input type="radio" name="<?php echo $nome_arr_form; ?>[immagine_show]" value="show" 
                        <?php if ('show' === $contenuto[0]['immagine_show']) {echo 'checked="checked"';} ?> style="margin-left: 25%;">
                        <?php echo __("Show", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
                	    <input type="radio" name="<?php echo $nome_arr_form; ?>[immagine_show]" value="hide" 
                	    <?php if ('hide' === $contenuto[0]['immagine_show']) {echo 'checked="checked"';}?>>
                	    <?php echo __("Hide", parent::$prefisso_nome); ?>
            	    </p>
            	    <p class="beb-eab-add-banner-sugg beb-eab-add-banner-media-sugg" style="margin: 1% 0 0;">
            	       <?php echo __("(When the banner is closed you prefer show the image?)", parent::$prefisso_nome); ?>
            	    </p>
                </fieldset>
                
                <fieldset style="float: left; padding: 50px 0 0 47%; width: 100%;">
                    <p class="submit">
                    	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", parent::$prefisso_nome); ?>" name="<?php echo $nome_arr_form; ?>[submit]" />
                    </p>
                </fieldset>
    		</form>
        </div>
        <div id="beb-eab-contenitore-admin">
            <a href="#" style="text-decoration: none;">
                <table id="beb-eab-add-banner-pro">
                    <thead>
                        <tr>
                            <th colspan="3">
                                <h1 style="color: #0000ff; text-align: center;"><?php echo __("Pro Version", parent::$prefisso_nome); ?></h1>
                            </th>
                        </tr>
                        <tr>
                            <td colspan="3"><h2 style="color: #00008b; text-align: center;">
                                <?php echo __("In the Pro Version is possible to enable this feature and create multiple banners", parent::$prefisso_nome); ?></h2></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo __("Video:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input" style="height: 100px; vertical-align: middle;">
                                <i><?php echo __("Drop here", parent::$prefisso_nome); ?></i><br />&#8595;
                            </td>
                            <td><?php echo __("(<strong>Optional</strong> - Insert a video)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Display it on the:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input"><?php echo date('Y / m / d'); ?></td>
                            <td><?php echo __("(<strong>Optional</strong> - When publishing the Banner)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Remove it on the:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input"><?php echo date('Y / m / d'); ?></td>
                            <td><?php echo __("(<strong>Optional</strong> - When remove the Banner)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Refresh:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input">300</td>
                            <td><?php echo __("(Banner refreshing Time  - in seconds)", parent::$prefisso_nome); ?></td>
                        </tr>
                    </tbody>
                </table>
            </a>
        </div>
        <?php
	}
	
	
	/*
	 * PAGINA DELLE IMPOSTAZIONI DEI BANNER
	 */
	public function beb_eab_adm_settings () {
	    $nome = parent::$wp_options_prefisso.'impostazioni';
	    register_setting (
	    self::$nome_form_settings_tmp.'_gr',      // Option group
	    self::$nome_form_settings_tmp,      // Option name
	    array ($this, 'beb_eab_adm_sanitize_impostazioni') // Sanitize
	    );
	}
	public function beb_eab_adm_settings_contenuto () {
        $nome = parent::$wp_options_prefisso.'impostazioni';
        $nome_arr_form = self::$nome_form_settings_tmp;?>
        <div id="beb-eab-contenitore-admin">
            <h1 style="color: #0000ff;"><?php echo __("Index", parent::$prefisso_nome); ?></h1>
            <ol>
                <li><span class="beb-eab-banner-indice" title="beb-eab-imp-colori"><?php echo __("Banner colors", parent::$prefisso_nome); ?></span></li>
                <li><span class="beb-eab-banner-indice" title="beb-eab-imp-formattazione"><?php echo __("Formatting Text and Paragraph", parent::$prefisso_nome); ?></span></li>
                <li class="beb-eab-banner-indice-pro"><span class="beb-eab-banner-indice-pro">
                    <?php echo __("Shape and position of the banner", parent::$prefisso_nome); ?></span>
                </li>
            </ol>
        </div>
        <form method="post" action="options.php" id="beb-eab-banner-impostazioni">
            <?php settings_fields($nome_arr_form.'_gr'); ?>
            <div id="beb-eab-contenitore-admin" class="beb-eab-imp-colori">
                <h1><?php echo __("Banner colors", parent::$prefisso_nome); ?></h1>
                <?php parent::beb_cv_banner(parent::$contenuto_iniziale); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                        <!-- 
                            <th scope="row">
                                <?php //echo __("Interval between a banner and the other:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php //echo $this->impostazioni['beb_eab_banner_imp_tempo']; ?>" alt="beb-eab-banner-tempo"
                                    name="<?php echo $nome_arr_form; ?>[beb_eab_banner_imp_tempo]" />
                                <span class="beb-eab-add-banner-sugg"><?php //echo __("(in second)", parent::$prefisso_nome); ?></span>
                            </td>
                             -->
                            <th scope="row">
                                <?php echo __("Opacity:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['.beb-eab-spazio-banner']['opacity']; ?>" alt="beb-eab-banner-opacita"
                                    name="<?php echo $nome_arr_form; ?>[.beb-eab-spazio-banner][opacity]" /><br />
                                <span class="beb-eab-add-banner-sugg">(0 --&gt; 1)</span>
                            </td>
                            <th scope="row">
                                <?php echo __("Date color:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h1']['color']; ?>" alt="beb-eab-banner-data"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][color]" class="my-color-field" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-data h1']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Title color:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['color']; ?>" alt="beb-eab-banner-testo h1"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Subtitle color:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['color']; ?>" alt="beb-eab-banner-testo h2"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Content color:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo p']['color']; ?>" alt="beb-eab-banner-testo p"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo p']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Left link:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['color']; ?>" alt="beb-eab-banner-cont-prenota-sx"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Right link:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['color']; ?>" alt="beb-eab-banner-cont-prenota-dx"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Colors:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="radio" value="1" name="<?php echo $nome_arr_form; ?>[background-color][num_colori]"
                                    <?php if ($this->impostazioni['background-color']['num_colori'] == 1) {
                                        echo 'checked="checked"';
                                    }?> alt="beb-eab-banner-imp-n-colori" />1
                                <input type="radio" value="2" name="<?php echo $nome_arr_form; ?>[background-color][num_colori]"
                                    <?php if ($this->impostazioni['background-color']['num_colori'] == 2) {
                                        echo 'checked="checked"';
                                    } ?> alt="beb-eab-banner-imp-n-colori" />2
                            </td>
                            <th scope="row">
                                <?php echo __("Background color 1:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['background-color']['colore_1']; ?>" alt="beb-eab-spazio-banner 1" 
                                    name="<?php echo $nome_arr_form; ?>[background-color][colore_1]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['background-color']['colore_1'] ;?>;"></div>
                            </td>
                            <th scope="row" id="beb-eab-banner-background-2">
                                <?php echo __("Background color 2:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['background-color']['colore_2']; ?>" alt="beb-eab-spazio-banner 2"
                                    name="<?php echo $nome_arr_form; ?>[background-color][colore_2]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['background-color']['colore_2'] ;?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="6" style="padding: 30px 0 20px; width: 100%;">
                                <p class="submit">
                                	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", parent::$prefisso_nome); ?>"
                                	name="<?php echo $nome_arr_form; ?>[submit]" />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <img class="beb-eab-help-up" src="<?php echo BEBCVBANNER__PLUGIN_URL.'img/up.png';?>" />
            </div>            
            <div id="beb-eab-contenitore-admin" class="beb-eab-imp-formattazione">
                <h1 style="margin-bottom: 50px;"><?php echo __("Formatting Text and Paragraph", parent::$prefisso_nome); ?></h1>
                <?php parent::beb_cv_banner(parent::$contenuto_iniziale); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php echo __("Event Date - Day: ", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h1']['font-size']; ?>"
                                    alt="beb-eab-banner-data-giorno-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'center' ? ' checked="checked"' : '');?>/>
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Event Date - Month: ", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h2']['font-size']; ?>"
                                    alt="beb-eab-banner-data-mese-size" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Title:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['font-size']; ?>"
                                    alt="beb-eab-banner-titolo-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Subtitle:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['font-size']; ?>"
                                    alt="beb-eab-banner-sottotitolo-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'center' ? ' checked="checked"' : '');?>/>
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                            <th scope="row">
                                <?php echo __("Description:", parent::$prefisso_nome); ?>
                            </th>
                            <td style="width: 35%;">
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo p']['font-size']; ?>"
                                    alt="beb-eab-banner-description-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                                <input type="radio" value="justify" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'justify' ? ' checked="checked"' : '');?> />
                                <?php echo __("justify", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Left link:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['font-size']; ?>"
                                    alt="beb-eab-banner-left-link-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                            <th scope="row">
                                <?php echo __("Right link:", parent::$prefisso_nome); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['font-size']; ?>"
                                    alt="beb-eab-banner-right-link-size" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", parent::$prefisso_nome); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", parent::$prefisso_nome); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 30px 0 20px; width: 100%;">
                                <p class="submit">
                                	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", parent::$prefisso_nome); ?>"
                                	name="<?php echo $nome_arr_form; ?>[submit]" />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <img class="beb-eab-help-up" src="<?php echo BEBCVBANNER__PLUGIN_URL.'img/up.png';?>" />
            </div>
        </form>
	    <?php
	}
	
	/**
	 * 
	 * @param string $tipo (preview, add, settings)
	 * 
	 * @return string/array (array nel caso di settings)
	 */
	private function campo_form_array_output ($tipo) {
	    $risultato = false;
	    if ($tipo === 'preview') {
	        ;
	    } elseif ($tipo === 'add') {
	        
	    } elseif ($tipo === 'settings') {
	        $risultato = array(
	            
	            
	        );
	    }
	    return $risultato;
	}
	
	public function beb_eab_adm_sanitize_add ($input) {
	    $new_input_b = NULL;
	    if (!isset($this->contenuto)) {
	        $new_input['ho caricato contenuto'] = true;
	        self::beb_cv_carica_dati();
	    }
	    if ((empty($input['titolo']) and empty($input['nome_evento'])) or (empty($input['titolo']) or empty($input['nome_evento']))) {
    	    if (empty($input['titolo']) and empty($input['nome_evento'])) {
    	        //beb_events_ads_banner_contenuto[nome_evento]
    	        $tipo = 'error';
    	        $messaggio = __('"Title" and "Banner ID Name" can not be empty, please retry.', parent::$prefisso_nome);
    	    } elseif (empty($input['titolo'])) {
    	        //beb_events_ads_banner_contenuto[nome_evento]
    	        $tipo = 'error';
    	        $messaggio = __('"Title" can not be empty, please retry.', parent::$prefisso_nome);
    	    } elseif (empty($input['nome_evento'])) {
    	        //beb_events_ads_banner_contenuto[nome_evento]
    	        $tipo = 'error';
    	        $messaggio = __('"Banner ID Name" can not be empty, please retry.', parent::$prefisso_nome);
    	    }
    	    
    	    if (!isset($this->contenuto[0]['banner_esempio'])) {
    	        $new_input_b = get_option(parent::$wp_options_prefisso.'contenuto');
    	    }
	    } else {
    	    $wpml = false;
    	    if (is_plugin_active('sitepress-multilingual-cms/sitepress.php')) {
    	        $wpml = true;
    	    }
    	    $new_input = array();
    	    
    	    //print_r($input);
    	    
    	    
    	    foreach ($input as $chiave_form => $contenuto) {
    	        if (!empty($contenuto)) {
    	            if (is_numeric ($contenuto)) {
    	                $new_input [$chiave_form] = absint ($contenuto);
    	            } else {
    	                $new_input [$chiave_form] = sanitize_text_field ($contenuto);
    	                if ($wpml) {
    	                    if ($chiave_form === self::$ancora_add_banner_traduzione) {
    	                        $wpml_id = parent::$prefisso_nome.$contenuto;
    	                    }
    	                    icl_register_string($wpml_id, parent::beb_events_ads_banner_accessori_epura_chiave($chiave_form), $contenuto);
    	                }
    	            }
    	        }
    	    }
    	    // Controllo se è installato ed attivo WPML
    	    if ($wpml) {
    	        $new_input['wpml_id'] = $wpml_id;
    	        $new_input['beb_eab_form_id'] = parent::$wp_options_prefisso.'contenuto';
    	    }
            $new_input_b[0] = $new_input;
    	   $messaggio = null;
    	   $tipo = null;
    	   global $wpdb;
    	   if (false == get_option(parent::$wp_options_prefisso.'contenuto')) {
    	       if (add_option(parent::$wp_options_prefisso.'contenuto', $new_input_b)) {
    	           $tipo = 'updated';
    	           $messaggio = __('Successfully saved !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_prev.'">'.
    	               __('go to', parent::$prefisso_nome).'</a>';
    	       } else {
    	           $tipo = 'error';
    	           $messaggio = __('Oops there was some error updating, please try again.', parent::$prefisso_nome);
    	       }
    	   } else {
    	       if (update_option(parent::$wp_options_prefisso.'contenuto', $new_input_b)) {
    	           $tipo = 'updated';
    	           $messaggio = __('Successfully saved !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_prev.'">'.
    	               __('go to', parent::$prefisso_nome).'</a>';
    	       } else {
    	           $tipo = 'error';
    	           $messaggio = __('Oops there was some error updating, please try again.', parent::$prefisso_nome);
    	       }
    	   }
	   }
	   self::beb_eab_adm_aggiungi_mess($tipo, $messaggio);
	}
	public function beb_eab_adm_sanitize_preview ($input2) {
	    foreach ($input2 as $chiave => $value) {
	        $input2[$chiave] = sanitize_text_field($value);
	    }
	    global $wpdb;
	    if (isset($input2['remove_banner']) and $input2['remove_banner'] === 'si') {
	        if (count($this->contenuto) === 1) {
	            $this->contenuto[0] = parent::$contenuto_iniziale;
	            if (delete_option(parent::$wp_options_prefisso.'contenuto')) {
	                $tipo = 'updated';
	                $messaggio = __('Successfully deleted !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error deleting, please try again.', parent::$prefisso_nome);
	            }
	        } else {
	            unset($this->contenuto[$input2['banner_id_array']]);
	            if (update_option(parent::$wp_options_prefisso.'contenuto', $this->contenuto)) {
	                $tipo = 'updated';
	                $messaggio = __('Successfully deleted !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error deleting, please try again.', parent::$prefisso_nome);
	            }
	        }
	    } else {
	        $this->contenuto[$input2['banner_id_array']]['stato_banner'] = $input2['stato_banner'];
	        $this->contenuto[$input2['banner_id_array']]['open'] = $input2['open'];
	        $this->contenuto[$input2['banner_id_array']]['immagine_show'] = $input2['immagine_show'];
	        if (update_option(parent::$wp_options_prefisso.'contenuto', $this->contenuto)) {
	            $tipo = 'updated';
	            $messaggio = __('Successfully updated !', parent::$prefisso_nome);
	        } else {
	            $tipo = 'error';
	            $messaggio = __('Oops there was some error updating, please try again.', parent::$prefisso_nome);
	        }
	    }
	    self::beb_eab_adm_aggiungi_mess($tipo, $messaggio);	    
	}
	public function beb_eab_adm_sanitize_impostazioni ($input3) {
	    $errore = NULL;
	    $tipo = NULL;
	    $messaggio = NULL;
	    foreach ($input3 as $id_class => $impostaz_css) {
	        if (is_array($impostaz_css)) {
	            foreach ($impostaz_css as $tipo => $valore) {
	                if ($tipo == 'color' or $tipo == 'colore_1' or $tipo == 'colore_2') {
	                    if (strlen($valore) !== 6) {
	                        $errore ['colori'] = true;
	                    }
	                } elseif ($tipo == 'num_colori') {
	                    if (!is_numeric($valore)) {
	                        $errore ['numeri'] = true;
	                    }
	                } elseif ($tipo == 'opacity') {
	                    if ($valore > 1 or $valore < 0) {
	                        $errore ['opacita'] = true;
	                    }
	                }
	                $input3 [$id_class][$tipo] = sanitize_text_field($valore);
	            }
	        } else {
	            $input3 [$id_class] = sanitize_text_field($impostaz_css);
	        }
	    }
	    
	    if (isset($errore)) {
	        $tipo = 'error';
	        if (isset($errore['colori']) and isset($errore['numeri']) and isset($errore['opacita'])) {
	            $messaggio = __('Error - Colors must be in hexadecimal code; the value must be a number and the value "opacity" must be between 0 and 1 ', parent::$prefisso_nome);
	        } elseif (isset($errore['colori'])) {
	            $messaggio = __('Error - The colors must be in hexadecimal code', parent::$prefisso_nome);
	        } elseif (isset($errore['numeri'])) {
	            $messaggio = __('Error - The value must be a number', parent::$prefisso_nome);
	        } elseif (isset($errore['opacita'])) {
	            $messaggio = __('Error - The value "opacity" must be between 0 and 1', parent::$prefisso_nome);
	        }
	    } else {
	        $impostazioni_originali = get_option(parent::$wp_options_prefisso.'impostazioni'); 
	        if ($impostazioni_originali != false) {
	            $impostazioni_modifcate = self::beb_eab_adm_accessori_aggiorna_array_impostazioni($impostazioni_originali, $input3);
	            if (update_option(parent::$wp_options_prefisso.'impostazioni', $impostazioni_modifcate)) {
	                $tipo = 'updated';
	                $messaggio = __('Settings are successfully updated !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error updating (01), please try again.', parent::$prefisso_nome);
	            }
	        } else {
	            if (add_option(parent::$wp_options_prefisso.'impostazioni', $input3)) {
	                $tipo = 'updated';
	                $messaggio = __('Settings are successfully updated !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error updating (02), please try again.', parent::$prefisso_nome);
	            }
	        }
	    }
	    self::beb_eab_adm_aggiungi_mess($tipo, $messaggio);
	    unset($input3);
	}
	
	public function beb_eab_adm_aggiungi_mess ($tipo = NULL, $messaggio = NULL) {
	    add_settings_error(
	    parent::$wp_options_prefisso.'messaggi',
	    esc_attr('settings_updated'),
	    $messaggio,
	    $tipo
	    );
	}
	public function beb_eab_adm_visualizza_messaggi () {
	    settings_errors(parent::$wp_options_prefisso.'messaggi');
	}
	
	private function formBannerNuovoEv () {
	    echo '
	<!--  style="display: none;" -->
		<div class="beb-eab-riga" id="beb_eab_AdNuovoEvento">
			<div class="beb-eab-drag-drop" style="background-color: #D0D0D0; margin-top: 50px;">
				<h1>Drag and Drop Image<br /> Here</h1>
				<h2>
					Fare controllo che se l\'immagine non viene inserita, riduce la lunghezza del Banner
					sia sul CSS che sul JS
				</h2>
			</div>
		</div>';
	}
	
	private function beb_eab_adm_accessori_aggiorna_array_impostazioni ($imp_iniziali, $imp_modificate) {
	    $risultato = array();
	    foreach ($imp_iniziali as $chiave => $valore) {
	        if (is_array($valore)) {
	            if (isset($imp_modificate[$chiave])) {
	                if (is_array($imp_modificate[$chiave])) {
	                    $risultato[$chiave] = self::beb_eab_adm_accessori_aggiorna_array_impostazioni($valore, $imp_modificate[$chiave]);
	                } else {
	                    $risultato[$chiave] = $valore;
	                }
	            } else {
	                $risultato[$chiave] = $valore;
	            }
	        } else {
	            if (isset($imp_modificate[$chiave])) {
	                $risultato[$chiave] = $imp_modificate[$chiave];
	            } else {
	                $risultato[$chiave] = $valore;
	            }
	        }
	    }
	    return $risultato;
	}
}
?>