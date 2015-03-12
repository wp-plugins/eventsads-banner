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
            add_action('admin_menu', array($this, 'beb_eab_adm_carica_header'));
            add_action('admin_menu', array($this, 'beb_eab_adm_carica_footer'));
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
	    add_menu_page(
	       'Events/ADS Banner',     // titolo scheda 
	       'Events/ADS Banner',     // titolo menu (e prima voce link)
	       'administrator',    // permessi per poterlo vedere
	       self::$nome_pagina_prev, // ancora tra il link e la pagina (o anche nome interno della pagina)
            array($this, 'beb_eab_adm_riassunto_contenuto')    // funzione con la pagina
	    );
	    add_submenu_page(
            self::$nome_pagina_prev,
            __("Create New Banner", parent::$prefisso_nome),
            __("Add Banner", parent::$prefisso_nome),
            'administrator',
            self::$nome_pagina_add,
            array($this, "beb_eab_adm_add_contenuto")
	    );
	    add_submenu_page(
    	    self::$nome_pagina_prev,
    	    __("Sets the Banner", parent::$prefisso_nome),
    	    __("Settings Banner", parent::$prefisso_nome),
    	    'administrator',
    	    self::$nome_pagina_settings,
    	    array($this, 'beb_eab_adm_settings_contenuto')
	    );
	}
	
	public function beb_eab_adm_carica_header () {
	    self::carica_header();
	}
	private function carica_header () {
	    wp_register_style (parent::$prefisso_nome.'-adm.css', $this->url.'css/'.parent::$prefisso_nome.'-adm.css', array());
	    wp_enqueue_style (parent::$prefisso_nome.'-adm.css');
	}
	
	public function beb_eab_adm_carica_footer () {
	    self::carica_footer();
	}
	private function carica_footer () {
	    wp_register_script (parent::$prefisso_nome.'-adm.js', $this->url.'js/'.parent::$prefisso_nome.'-adm.js', array('jquery'),
	       BEBCVBANNER_VERSION);
	    wp_enqueue_script(parent::$prefisso_nome.'-adm.js');
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
	    </style>
	    <?php if (isset($this->contenuto[0]['banner_esempio'])): ?>
	       <h1 class="beb-eab-riass-add">
               <a href="<?php echo site_url();?>/wp-admin/admin.php?page=<?php echo self::$nome_pagina_add; ?>"><?php echo __("Create first banner", parent::$prefisso_nome); ?></a>
           </h1>
	    <?php else: ?>
	       <h1 class="beb-eab-riass-add">
               <a href="<?php echo site_url();?>/wp-admin/admin.php?page=beb-cv-admin-banner-pro"><?php echo __("Add more", parent::$prefisso_nome); ?></a>
           </h1>
	    <?php endif; ?>
	    <div id="beb-eab-contenitore-admin" style="min-height: 190px;">
	       <!--  <form method="post" action="?page=<?php echo self::$nome_pagina_prev; ?>" id="beb-eab-banner-riass-form"> -->
	       <form method="post" action="options.php" id="beb-eab-banner-riass-form">
            <?php for ($i = 0; $i < count($contenuto); $i++): ?>
                <?php settings_fields(self::$nome_form_prev_tmp.'_gr'); ?>
                <div style="padding: 0; border: none;">
                    <div class="beb-eab-proprieta-banner" style="padding: 0; width: 40%;">
                       <div class="beb-eab-stato-banner-riassunto" style="background: url('<?php echo BEBCVBANNER__PLUGIN_URL.'img/'.$contenuto[$i]['stato_banner'].'.png'; ?>') no-repeat scroll 0 0 rgba(0, 0, 0, 0);">
                            <?php if (!isset($contenuto[$i]['banner_esempio'])) :?>
                            <input type="hidden" value="<?php echo $contenuto[$i]['stato_banner']; ?>" name="<?php echo $nome_arr_form; ?>[stato_banner]" />
                            <input type="hidden" value="<?php echo $i; ?>" name="<?php echo $nome_arr_form; ?>[banner_id_array]" />
                            <p class="submit">
                            	<input id="submit" type="submit" class="button button-primary" value="" name="submit" />
                            </p>
                        <?php endif; ?>
                        </div>
                        <h1 style="text-align: left; margin: 0 0 7px;">
                            <?php echo __("Event Name: ", parent::$prefisso_nome).' <i>"'.$contenuto[$i]['nome_evento'].'"</i>'; ?>
                        </h1>
                        <br />
                        <?php if (!isset($contenuto[0]['banner_esempio'])): ?>
                        <p class="submit">
                            <input type="hidden" value="no" name="<?php echo $nome_arr_form; ?>[remove_banner]" />
                        	<input id="beb-eab-banner-remove" type="submit" class="beb-eab-banner-button beb-eab-banner-button-red"
                        	       value="<?php echo __("Remove", parent::$prefisso_nome); ?>" name="submit" />
                        </p>
                        <?php endif; ?>
                        <!-- 
                        <h1 style="text-align: left; margin-top: 50px;">
                            <a class="beb-eab-banner-button beb-eab-banner-button-red" href="#"><strong><?php echo __("Remove", parent::$prefisso_nome); ?></strong></a>
                        </h1>
                         -->
                    </div>
                    <?php parent::beb_cv_banner(); ?>
                </div>
            <?php endfor; ?>
            </form>
	    </div>
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
	public function beb_eab_adm_add_contenuto () {
	    $contenuto = array(
	        0 => array(
                'nome_evento' => '',
                'titolo' => ''
	        )
	    );
	    $nome_arr_form = self::$nome_form_add_tmp;
	    $visualizza = true;
	    ?>
	    <div id="beb-eab-contenitore-admin" class="beb-eab-imp-add">
	       <h1 style="line-height: 26px; margin: 0; text-align: center;">
                <?php echo __('INSERT NEW EVENT', parent::$prefisso_nome); ?> - <?php echo $this->giorno_corrente.'/'.$this->mese_corrente.'/'.$this->anno_corrente; ?>
            </h1>
            <h4 style="color: #ff0000; text-align: center;">
                *<?php echo __("unfortunately for now you can create a single banner for time", "beb-cv-banner"); ?>
    	    </h4>
            <?php parent::beb_cv_banner($contenuto, $visualizza);?>
	       <form method="post" action="options.php">
	           <?php settings_fields($nome_arr_form.'_gr'); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                            <td style="padding-bottom: 15px;">
                                <strong><?php echo __("Display:", "beb-cv-banner"); ?>&nbsp;&nbsp;&nbsp;</strong>
                                <input type="radio" name="<?php echo $nome_arr_form; ?>[stato_banner]" value="on"> <?php echo __("On", parent::$prefisso_nome); ?>
                        	    &nbsp;&nbsp;&nbsp;
                        	    <input type="radio" name="<?php echo $nome_arr_form; ?>[stato_banner]" value="off" checked="checked"> <?php echo __("Off", parent::$prefisso_nome); ?>
                        	    &nbsp;&nbsp;&nbsp;
                        	    <span class="beb-eab-add-banner-sugg"><?php echo __("(Show the banner (On) or not (Off))", parent::$prefisso_nome); ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong><?php echo __("Open the Banner", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="home" checked="checked" style="margin-left: 15px;">
                        	       <?php echo __("Open only in Home Page", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
                        	    <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="all">
                        	       <?php echo __("Open in all site", parent::$prefisso_nome); ?>&nbsp;&nbsp;&nbsp;
                        	    <input type="radio" name="<?php echo $nome_arr_form; ?>[open]" value="never">
                        	       <?php echo __("Don't open never", parent::$prefisso_nome); ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="beb-eab-add-sugg" style="padding-bottom: 50px;">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(Open the banner only in the home page, or in all site, or never)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo __("Banner Name ID", parent::$prefisso_nome); ?><span style="color: #ff0000;">*</span> :</strong><br />
                                <input type="text" id="beb-eab-nome-evento" name="<?php echo $nome_arr_form; ?>[nome_evento]" <?php
                                    if (isset($this->banner_da_modificare['nome_evento'])) {
                            	        echo 'value="'.esc_attr( $this->banner_da_modificare['nome_evento']).'"';
                            	    } else {
                                        echo 'placeholder="'.__("Insert Banner's Name here.", parent::$prefisso_nome).'"';
                                    } ?> />
                            </td>
                            <td>
                                <strong><?php echo __("Date:", "beb-cv-banner"); ?></strong><br />
                                <select name="<?php echo $nome_arr_form; ?>[data_evento_giorno]" id="beb-eab-giorno">
                                    <option selected="selected"></option>
                                    <?php $giorni = 31;
                                    for ($i = 1; $i <= $giorni; $i++) {
                                       if ($i < 10) {
                                           $i = '0'.$i;
                                       }
                                       echo '<option value="'.$i.'">'.$i.'</option>';
                                    } ?>
                                </select>
                                <select name="<?php echo $nome_arr_form; ?>[data_evento_mese]" id="beb-eab-mese">
                                    <option selected="selected"></option>
                                    <?php foreach ($this->mappa_mese_numero as $mese => $numero) {
                                    	echo '<option value="'.$numero.'">'.$mese.'</option>';
                                    } ?>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo '(<strong style="color: #ff0000;">'.__("Required - Unique Key ID</strong> - Max ", parent::$prefisso_nome).
                                    '<strong>40</strong>'.__(" characters)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                            <td class="beb-eab-add-sugg" style="padding-bottom: 0;">
                                <span class="beb-eab-add-banner-sugg">
                                    <?php echo __("(<strong>Optional</strong> - The date that appears in the Banner)", parent::$prefisso_nome); ?>
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo __("Title", parent::$prefisso_nome); ?><span style="color: #ff0000;">*</span> :</strong><br />
                                <input type="text" id="beb-eab-titolo" name="<?php echo $nome_arr_form; ?>[titolo]" <?php
                                    if (isset($this->banner_da_modificare['titolo'])) {
                            	        echo 'value="'.esc_attr( $this->banner_da_modificare['titolo']).'"';
                            	    } else {
                            	       echo 'placeholder="'.__("Insert Title here", "beb-cv-banner").'"';
                                    }?> />
                            </td>
                            <td>
                                <strong><?php echo __("Subtitle", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="text" id="beb-eab-sottotitolo" name="<?php echo $nome_arr_form; ?>[sottotitolo]" <?php
                                    if (isset($this->banner_da_modificare['sottotitolo'])) {
                            	        echo 'value="'.esc_attr( $this->banner_da_modificare['sottotitolo']).'"';
                            	    } else {
                            	       echo 'placeholder="'.__("Insert Subtitle here", parent::$prefisso_nome).'"';
	                                }?> />
                            </td>
                        </tr>
                        <tr>
                            <td class="beb-eab-add-sugg" style="padding-bottom: 0;">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo '(<strong style="color: #ff0000;">'.__("Required</strong> - Max ", parent::$prefisso_nome).
                            	    '<strong>20</strong>'.__(" characters)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - Max ", parent::$prefisso_nome).'<strong>28</strong>'.__(" characters)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <strong><?php echo __("Description", parent::$prefisso_nome); ?>:</strong><br />
                                <textarea id="beb-eab-descrizione" name="<?php echo $nome_arr_form; ?>[descrizione]" <?php 
                                if (!isset($this->banner_da_modificare['descrizione']) or empty($this->banner_da_modificare)) {
                                    echo 'placeholder="'.__("Enter the Event's descriptionhere", parent::$prefisso_nome).'"';
                                } ?>><?php if (isset($this->banner_da_modificare['descrizione'])) {
                                        echo esc_attr( $this->banner_da_modificare['descrizione']);
                                    }?></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg"><?php echo __("(<strong>Optional</strong> - Max ", parent::$prefisso_nome); ?>
                	               <span class="beb-eab-add-banner-numeri">195</span><?php echo __(" characters)", parent::$prefisso_nome); ?>
                	            </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 10px 0 15px; text-align: center;">
                                <strong><?php echo __("All Banner is a link ?", parent::$prefisso_nome); ?></strong>
                                <input type="checkbox" id="beb-eab-link-totale" name="<?php echo $nome_arr_form; ?>[link_totale]" value="si"
                                <?php if (isset($this->banner_da_modificare['link_totale'])) {
                        	        echo 'checked="checked"';
                        	    } ?> />&nbsp;&nbsp;&nbsp;
                        	    <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - If checked all banner will be linkable)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo __("Left link Title", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="text" id="beb-eab-link-sx-titolo" name="<?php echo $nome_arr_form; ?>[link_sx_titolo]" <?php
                                if (isset($this->banner_da_modificare['link_sx_titolo'])) {
                        	        echo 'value="'.esc_attr( $this->banner_da_modificare['link_sx_titolo']).'"';
                        	    } else {
                                    echo 'placeholder="'.__("Insert left title link here", "beb-cv-banner").'"';
                                }?> />
                            </td>
                            <td>
                                <strong><?php echo __("Left link URL", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="text" id="beb-eab-link-sx" name="<?php echo $nome_arr_form; ?>[link_sx]" <?php
                                if (isset($this->banner_da_modificare['link_sx'])) {
                        	        echo 'value="'.esc_attr( $this->banner_da_modificare['link_sx']).'"';
                        	    } else {
                                    echo 'placeholder="'.__("http://www.yourlink.link", "beb-cv-banner").'"';
                                } ?> style="width: 100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - The title will be appare for the left link - Max ", parent::$prefisso_nome).
                        	       '<strong>20</strong>'.__(" characters)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - The left link)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <strong><?php echo __("Right link Title", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="text" id="beb-eab-link-dx-titolo" name="<?php echo $nome_arr_form; ?>[link_dx_titolo]" <?php
                                if (isset($this->banner_da_modificare['link_dx_titolo'])) {
                        	        echo 'value="'.esc_attr( $this->banner_da_modificare['link_dx_titolo']).'"';
                        	    } else {
                                    echo 'placeholder="'.__("Insert right title link here", "beb-cv-banner").'"';
                                } ?> />
                            </td>
                            <td>
                                <strong><?php echo __("Right link URL", parent::$prefisso_nome); ?>:</strong><br />
                                <input type="text" id="beb-eab-link-dx" name="<?php echo $nome_arr_form; ?>[link_dx]" <?php
                                if (isset($this->banner_da_modificare['link_dx'])) {
                        	        echo 'value="'.esc_attr( $this->banner_da_modificare['link_dx']).'"';
                        	    } else {
                                    echo 'placeholder="'.__("http://www.yourlink.link", "beb-cv-banner").'"';
                                } ?> style="width: 100%;" />
                            </td>
                        </tr>
                        <tr>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - The title will be appare for the right link - Max ", parent::$prefisso_nome).
                        	       '<strong>20</strong>'.__(" characters)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                            <td class="beb-eab-add-sugg">
                                <span class="beb-eab-add-banner-sugg">
                        	       <?php echo __("(<strong>Optional</strong> - The right link)", parent::$prefisso_nome); ?>
                        	    </span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" style="padding: 30px 0 20px;">
                                <p class="submit">
                                	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", "beb-cv-banner"); ?>" name="<?php echo $nome_arr_form; ?>[submit]" />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
                                <?php echo __("In the Pro Version is possible to able this feature and create multiple banners", parent::$prefisso_nome); ?></h2></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr><td>&nbsp;</td></tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo __("Image:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input" style="height: 100px; vertical-align: middle;">
                                <i><?php echo __("Drop here", parent::$prefisso_nome); ?></i><br />&#8595;
                            </td>
                            <td><?php echo __("(<strong>Optional</strong> - Insert an image)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Video:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input" style="height: 100px; vertical-align: middle;">
                                <i><?php echo __("Drop here", parent::$prefisso_nome); ?></i><br />&#8595;
                            </td>
                            <td><?php echo __("(<strong>Optional</strong> - Insert a video)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Display it on:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input"><?php echo date('Y / m / d'); ?></td>
                            <td><?php echo __("(<strong>Optional</strong> - When publishing the Banner)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Remove it on:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input"><?php echo date('Y / m / d'); ?></td>
                            <td><?php echo __("(<strong>Optional</strong> - When remove the Banner)", parent::$prefisso_nome); ?></td>
                        </tr>
                        <tr><td>&nbsp;</td></tr>
                        <tr>
                            <td><?php echo __("Refresh:", parent::$prefisso_nome); ?></td>
                            <td class="beb-eab-add-banner-pro-input">300</td>
                            <td><?php echo __("(Time of banner's refreshing - in seconds)", parent::$prefisso_nome); ?></td>
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
            <h1 style="color: #0000ff;"><?php echo __("Index", "beb-cv-banner"); ?></h1>
            <ol>
                <li><span class="beb-eab-banner-indice" title="beb-eab-imp-colori"><?php echo __("Banner's colors", "beb-cv-banner"); ?></span></li>
                <li><span class="beb-eab-banner-indice" title="beb-eab-imp-formattazione"><?php echo __("Formatting Text and Paragraph", "beb-cv-banner"); ?></span></li>
                <li class="beb-eab-banner-indice-pro"><span class="beb-eab-banner-indice-pro">
                    <?php echo __("Shape and position of the banner", "beb-cv-banner"); ?></span>
                </li>
            </ol>
        </div>
        <form method="post" action="options.php" id="beb-eab-banner-impostazioni">
            <?php settings_fields($nome_arr_form.'_gr'); ?>
            <div id="beb-eab-contenitore-admin" class="beb-eab-imp-colori">
                <h1><?php echo __("Banner's colors", "beb-cv-banner"); ?></h1>
                <?php parent::beb_cv_banner(parent::$contenuto_iniziale); ?>
                <table class="form-table">
                    <tbody>
                        <tr>
                        <!-- 
                            <th scope="row">
                                <?php //echo __("Interval between a banner and the other:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php //echo $this->impostazioni['beb_eab_banner_imp_tempo']; ?>" alt="beb-eab-banner-tempo"
                                    name="<?php echo $nome_arr_form; ?>[beb_eab_banner_imp_tempo]" />
                                <span class="beb-eab-add-banner-sugg"><?php //echo __("(in second)", "beb-cv-banner"); ?></span>
                            </td>
                             -->
                            <th scope="row">
                                <?php echo __("Opacity:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['.beb-eab-spazio-banner']['opacity']; ?>" alt="beb-eab-banner-opacita"
                                    name="<?php echo $nome_arr_form; ?>[.beb-eab-spazio-banner][opacity]" />
                                <span class="beb-eab-add-banner-sugg">(0 --> 1)</span>
                            </td>
                            <th scope="row">
                                <?php echo __("Date color:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h1']['color']; ?>" alt="beb-eab-banner-data"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][color]" class="my-color-field" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-data h1']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Title color:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['color']; ?>" alt="beb-eab-banner-testo h1"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Subtitle color:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['color']; ?>" alt="beb-eab-banner-testo h2"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Content color:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo p']['color']; ?>" alt="beb-eab-banner-testo p"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-testo p']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Left's link:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['color']; ?>" alt="beb-eab-banner-cont-prenota-sx"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['color']; ?>;"></div>
                            </td>
                            <th scope="row">
                                <?php echo __("Right's link:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['color']; ?>" alt="beb-eab-banner-cont-prenota-dx"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][color]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['color']; ?>;"></div>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Colors:", "beb-cv-banner"); ?>
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
                                <?php echo __("Background color 1:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                #<input type="text" value="<?php echo $this->impostazioni['background-color']['colore_1']; ?>" alt="beb-eab-spazio-banner 1" 
                                    name="<?php echo $nome_arr_form; ?>[background-color][colore_1]" />
                                <div class="beb-eab-add-banner-colore" style="background-color: #<?php echo $this->impostazioni['background-color']['colore_1'] ;?>;"></div>
                            </td>
                            <th scope="row" id="beb-eab-banner-background-2">
                                <?php echo __("Background color 2:", "beb-cv-banner"); ?>
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
                                	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", "beb-cv-banner"); ?>"
                                	name="<?php echo $nome_arr_form; ?>[submit]" />
                                </p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <img class="beb-eab-help-up" src="<?php echo BEBCVBANNER__PLUGIN_URL.'img/up.png';?>" />
            </div>
            <div id="beb-eab-contenitore-admin" class="beb-eab-imp-formattazione">
                <h1 style="margin-bottom: 50px;"><?php echo __("Formatting Text and Paragraph", "beb-cv-banner"); ?></h1>
                <?php parent::beb_cv_banner(parent::$contenuto_iniziale); ?>
                <table class="form-table" style="margin-top: 20px;">
                    <tbody>
                        <tr>
                            <th scope="row">
                                <?php echo __("Date of Event - Day:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h1']['font-size']; ?>"
                                    alt="beb-eab-banner-data-giorno-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'center' ? ' checked="checked"' : '');?>/>
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h1][text-align]"
                                    alt="beb-eab-banner-data-giorno-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h1']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Date of Event - Month:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-data h2']['font-size']; ?>"
                                    alt="beb-eab-banner-data-mese-size" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-data h2][text-align]"
                                    alt="beb-eab-banner-data-mese-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-data h2']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Title:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h1']['font-size']; ?>"
                                    alt="beb-eab-banner-titolo-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h1][text-align]"
                                    alt="beb-eab-banner-titolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h1']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Subtitle:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo h2']['font-size']; ?>"
                                    alt="beb-eab-banner-sottotitolo-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'center' ? ' checked="checked"' : '');?>/>
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo h2][text-align]"
                                    alt="beb-eab-banner-sottotitolo-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo h2']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                            <th scope="row">
                                <?php echo __("Description:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-testo p']['font-size']; ?>"
                                    alt="beb-eab-banner-description-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                                <input type="radio" value="justify" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-testo p][text-align]"
                                    alt="beb-eab-banner-description-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-testo p']['text-align'] == 'justify' ? ' checked="checked"' : '');?> />
                                <?php echo __("justify", "beb-cv-banner"); ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">
                                <?php echo __("Left's link:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-sx']['font-size']; ?>"
                                    alt="beb-eab-banner-left-link-size"
                                    name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-sx][text-align]"
                                    alt="beb-eab-banner-left-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-sx']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                            <th scope="row">
                                <?php echo __("Right's link:", "beb-cv-banner"); ?>
                            </th>
                            <td>
                                <input type="text" value="<?php echo $this->impostazioni['#beb-eab-banner-cont-prenota-dx']['font-size']; ?>"
                                    alt="beb-eab-banner-right-link-size" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][font-size]" />
                                <input type="radio" value="left" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'left' ? ' checked="checked"' : '');?> />
                                <?php echo __("left", "beb-cv-banner"); ?>
                                <input type="radio" value="center" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'center' ? ' checked="checked"' : '');?> />
                                <?php echo __("center", "beb-cv-banner"); ?>
                                <input type="radio" value="right" name="<?php echo $nome_arr_form; ?>[#beb-eab-banner-cont-prenota-dx][text-align]"
                                    alt="beb-eab-banner-right-link-align"
                                    <?php echo ($this->impostazioni['#beb-eab-banner-cont-prenota-dx']['text-align'] == 'right' ? ' checked="checked"' : '');?> />
                                <?php echo __("right", "beb-cv-banner"); ?>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="padding: 30px 0 20px; width: 100%;">
                                <p class="submit">
                                	<input id="submit" type="submit" class="button button-primary" value="<?php echo __("Save", "beb-cv-banner"); ?>"
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
    	        $messaggio = __('"Title" and "Banner Name ID" can not be empty, please retry.', parent::$prefisso_nome);
    	    } elseif (empty($input['titolo'])) {
    	        //beb_events_ads_banner_contenuto[nome_evento]
    	        $tipo = 'error';
    	        $messaggio = __('"Title" can not be empty, please retry.', parent::$prefisso_nome);
    	    } elseif (empty($input['nome_evento'])) {
    	        //beb_events_ads_banner_contenuto[nome_evento]
    	        $tipo = 'error';
    	        $messaggio = __('"Banner Name ID" can not be empty, please retry.', parent::$prefisso_nome);
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
    	    foreach ($input as $chiave_form => $contenuto) {
    	        if (!empty($contenuto)) {
    	            if (is_numeric ($contenuto)) {
    	                $new_input [$chiave_form] = absint ($contenuto);
    	            } else {
    	                $new_input [$chiave_form] = sanitize_text_field ($contenuto);
    	                if ($wpml) {
    	                    if ($chiave_form === self::$ancora_add_banner_traduzione) {
    	                        $wpml_id = parent::$prefisso_nome_nome_univoco.$contenuto;
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
    	           $messaggio = __('Oops there was some error on updating, please try again.', parent::$prefisso_nome);
    	       }
    	   } else {
    	       if (update_option(parent::$wp_options_prefisso.'contenuto', $new_input_b)) {
    	           $tipo = 'updated';
    	           $messaggio = __('Successfully saved !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_prev.'">'.
    	               __('go to', parent::$prefisso_nome).'</a>';
    	       } else {
    	           $tipo = 'error';
    	           $messaggio = __('Oops there was some error on updating, please try again.', parent::$prefisso_nome);
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
	                $messaggio = __('Oops there was some error in deleting, please try again.', parent::$prefisso_nome);
	            }
	        } else {
	            unset($this->contenuto[$input2['banner_id_array']]);
	            if (update_option(parent::$wp_options_prefisso.'contenuto', $this->contenuto)) {
	                $tipo = 'updated';
	                $messaggio = __('Successfully deleted !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error in deleting, please try again.', parent::$prefisso_nome);
	            }
	        }
	    } else {
	        $this->contenuto[$input2['banner_id_array']]['stato_banner'] = $input2['stato_banner'];
	        if (update_option(parent::$wp_options_prefisso.'contenuto', $this->contenuto)) {
	            $tipo = 'updated';
	            $messaggio = __('Successfully updated !', parent::$prefisso_nome);
	        } else {
	            $tipo = 'error';
	            $messaggio = __('Oops there was some error in updating, please try again.', parent::$prefisso_nome);
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
	            $messaggio = __('Error - The colors must be in hexadecimal code; the value must be a number and
	                the value "opacity" must be between 0 and 1', parent::$prefisso_nome);
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
	                $messaggio = __('Oops there was some error in updating (01), please try again.', parent::$prefisso_nome);
	            }
	        } else {
	            if (add_option(parent::$wp_options_prefisso.'impostazioni', $input3)) {
	                $tipo = 'updated';
	                $messaggio = __('Settings are successfully updated !', parent::$prefisso_nome).' - <a href="?page='.self::$nome_pagina_add.'">'.
	                    __('Create one', parent::$prefisso_nome).'</a>';
	            } else {
	                $tipo = 'error';
	                $messaggio = __('Oops there was some error in updating (02), please try again.', parent::$prefisso_nome);
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
	                    $risultato[$chiave] = aggiorna_array($valore, $imp_modificate[$chiave]);
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