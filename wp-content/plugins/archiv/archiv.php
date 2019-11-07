<?php
    /**
     * @package Archiv
     */

    /*
        Plugin Name: Archiv
        Plugin URI: https://www.appartig.at
        Description: Archiv-Plugin zur Verwaltung des Archivs
        Version: 0.0.1
        Author: Ing. Jakob Vesely
        Author URI: https://www.appartig.at
        License: AppArtig AGB
        Text Domain: archiv
    */

	/******************************************************
	** Installation / Aktivierung / Deinstallation
	******************************************************/

	register_activation_hook(__FILE__, 'archiv_install');
	register_deactivation_hook(__FILE__, 'archiv_uninstall');

	global $archiv_db_version;
	$archiv_db_version = '1.2';

	function archiv_install() {

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		
		global $wpdb;
		global $archiv_db_version;
		$charset_collate = $wpdb->get_charset_collate();
		$table_prefix = $wpdb->prefix . 'archiv_';

		/*

		// Table nummernkreise
		
		$table = $table_prefix . 'nummernkreise';

		$sql = "CREATE TABLE $table (
			`id` INT NOT NULL AUTO_INCREMENT,
			`prefix` VARCHAR(5) NOT NULL,
			`name` VARCHAR(55) NOT NULL,
		) $charset_collate;";
		
		dbDelta($sql);
		
		*/
		
	}

	function archiv_uninstall() {
        
	}
	
	/******************************************************
	** Styles ans Scripts
	******************************************************/

	function archiv_wp_admin_style() {
		wp_register_style('archiv_wp_admin_css', plugins_url('/style.css', __FILE__ ), false, '1.0.0' );
		wp_enqueue_style('archiv_wp_admin_css');
		
		wp_enqueue_media();
		wp_enqueue_script('jquery');
        wp_enqueue_script('archiv_wp_admin_app', plugins_url('/js/app.js', __FILE__ ), array(), '1.2.3');
	}
	add_action('admin_enqueue_scripts', 'archiv_wp_admin_style');


	/******************************************************
	** WP User Roles & Capabilities
	******************************************************/
	
	register_activation_hook(__FILE__, 'archiv_admin_roles_capabilities');

	function archiv_admin_roles_capabilities() {
        
		add_role('archiv_admin', 'Archiv Admin');
		
		$role = get_role('administrator');
        $role->add_cap('archiv_admin');
        
		$role = get_role('archiv_admin');
    	$role->add_cap('archiv_admin');
	}



	/******************************************************
	** WP Backend Menüpunkte
	******************************************************/

	add_action('admin_menu', 'archiv_admin_user_menu');

	function archiv_admin_user_menu() {
		
        add_menu_page('Objekte', 'Objekte', 'archiv_admin', 'archiv_objekte_list', 'archiv_objekte_list', 'dashicons-calendar-alt', 2);
        add_submenu_page('archiv_objekte_list', 'Neu', 'Neu', 'archiv_admin', 'archiv_objekte_deatil', 'archiv_objekte_deatil');

        add_menu_page('Kollektionen', 'Kollektionen', 'archiv_admin', 'archiv_kollektionen_list', 'archiv_kollektionen_list', 'dashicons-calendar-alt', 3);
        add_submenu_page('archiv_kollektionen_list', 'Neu', 'Neu', 'archiv_admin', 'archiv_kollektionen_deatil', 'archiv_kollektionen_deatil');

        add_menu_page('Kategorien', 'Kategorien', 'archiv_admin', 'archiv_kategorien_list', 'archiv_kategorien_list', 'dashicons-calendar-alt', 3);
        add_submenu_page('archiv_kategorien_list', 'Neu', 'Neu', 'archiv_admin', 'archiv_kategorien_deatil', 'archiv_kategorien_deatil');

        add_menu_page('Behältnisse', 'Behältnisse', 'archiv_admin', 'archiv_behaeltnisse_list', 'archiv_behaeltnisse_list', 'dashicons-calendar-alt', 3);
        add_submenu_page('archiv_behaeltnisse_list', 'Neu', 'Neu', 'archiv_admin', 'archiv_behaeltnisse_deatil', 'archiv_behaeltnisse_deatil');
        
        add_menu_page('Statistik', 'Statistik', 'archiv_admin', 'archiv_statistik', 'archiv_statistik', 'dashicons-calendar-alt', 3);
		
        add_menu_page('Sonstiges', 'Sonstiges', 'archiv_admin', 'archiv_sonstiges', 'archiv_sonstiges', 'dashicons-admin-generic', 3);
        add_submenu_page('archiv_sonstiges', 'Nummernkreise', 'Nummernkreise', 'archiv_admin', 'archiv_sonstiges_nummernkreise', 'archiv_sonstiges_nummernkreise');
        add_submenu_page('archiv_sonstiges', 'Etiketten', 'Etiketten', 'archiv_admin', 'archiv_sonstiges_etiketten', 'archiv_sonstiges_etiketten');

    }

	/******************************************************
	** WP Backend Pages/Functions
	******************************************************/

	function archiv_objekte_list(){ require_once('inc/objekte_list.php'); }
	function archiv_objekte_deatil(){ require_once('inc/objekte_detail.php'); }

	function archiv_kollektionen_list(){ require_once('inc/kollektionen_list.php'); }
	function archiv_kollektionen_deatil(){ require_once('inc/kollektionen_detail.php'); }

	function archiv_kategorien_list(){ require_once('inc/kategorien_list.php'); }
	function archiv_kategorien_deatil(){ require_once('inc/kategorien_detail.php'); }

	function archiv_behaeltnisse_list(){ require_once('inc/behaeltnisse_list.php'); }
	function archiv_behaeltnisse_deatil(){ require_once('inc/behaeltnisse_detail.php'); }

	function archiv_statistik(){ require_once('inc/statistik.php'); }

	function archiv_sonstiges(){ require_once('inc/sonstiges.php'); }
	function archiv_sonstiges_nummernkreise(){ require_once('inc/sonstiges_nummernkreise.php'); }
	function archiv_sonstiges_etiketten(){ require_once('inc/sonstiges_etiketten.php'); }

	/******************************************************
	** Admin Post Transactions
	******************************************************/
	
	require_once('inc/transactions.php');

?>