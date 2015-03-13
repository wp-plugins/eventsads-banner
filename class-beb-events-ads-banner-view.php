<?php
require_once 'class-beb-events-ads-banner-op-com.php';

class Beb_Events_Ads_Banner_View extends Beb_Events_Ads_Banner_Op_Com {
    private static $init_banner = false;
    
    final public function __construct() {
        parent::__construct();
        // CARICO FILE ESTERNI (CSS E JS)
        add_action('wp_footer', array($this, 'beb_eab_view_footer'));
        // BANNER !
        if (!has_action('beb_eab_view_recupera_banner')) {
            add_action ('init', array ($this, 'beb_eab_view_recupera_banner'));
        }
    }
    final public function beb_eab_view_footer () {
        self::banner_footer ();
    }
    private function banner_footer () {
        wp_register_script (parent::$prefisso_nome.'-view.js', $this->url .'js/'.parent::$prefisso_nome.'-view.js', array('jquery'),
        BEBCVBANNER_VERSION);
        wp_enqueue_script (parent::$prefisso_nome.'-view.js');
    }
    final public function beb_eab_view_recupera_banner () {
        if (!self::$init_banner and !is_admin() and !wp_is_mobile()) {
            parent::beb_cv_banner();
            self::$init_banner = true;
        } else {
            exit();
        }
    }
}
?>