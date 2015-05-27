<?php
require_once (ABSPATH.'wp-admin/includes/upgrade.php');
require_once 'class-beb-events-ads-banner-op-com.php';

class Beb_Events_Ads_Banner_Uninstall extends Beb_Events_Ads_Banner_Op_Com {
    final public function beb_events_ads_banner_get_prefisso () {
        return parent::$wp_options_prefisso;
    }
}

if (! defined('WP_UNINSTALL_PLUGIN')) {
    exit();
}

$uninstall_banner = new Beb_Events_Ads_Banner_Uninstall();
$prefisso = $uninstall_banner->beb_events_ads_banner_get_prefisso();

global $wpdb;
delete_option($prefisso.'data_installazione');
delete_option($prefisso.'plugin_attivo');
delete_option($prefisso.'versione');
delete_option($prefisso.'data_attivazione');
delete_option($prefisso.'data_installazione');
delete_option($prefisso.'impostazioni');
delete_option($prefisso.'new');
delete_option($prefisso.'preview');
delete_option($prefisso.'impostazioni_nm');
?>