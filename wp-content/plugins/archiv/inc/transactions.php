<?php

	/******************************************************
	** Personen
	******************************************************/

	add_action('admin_post_asbd_personen__create', 'action__asbd_personen__create');
	add_action('admin_post_asbd_personen__update', 'action__asbd_personen__update');
	add_action('admin_post_asbd_personen__delete', 'action__asbd_personen__delete');

	function action__asbd_personen__create(){

		global $wpdb;
		
		$email = $_GET['email'];
		
		if (!username_exists($email) && !email_exists($email)) {
			
			$passwort = $_GET['passwort'];
			
            $insert_user_result = wp_insert_user(array(
                "user_pass"     => $passwort,
                "user_login"    => $email . '__' . time(),
                "user_nicename" => "",
                "user_email"    => $email,
                "display_name"  => $_GET['vorname'] . ' ' . $_GET['nachname'],
                "first_name"    => $_GET['vorname'],
                "last_name"     => $_GET['nachname'],
                "role"          => $_GET['role']
            ));
			
			if($insert_user_result){
				
				wp_new_user_notification($insert_user_result, null,  'user');
				
				$wpdb->insert( 
					$wpdb->prefix . 'asbd_personen',
					array( 
						'vorname' => $_GET['vorname'], 
						'nachname' => $_GET['nachname'],
						'telefon' => $_GET['telefon'], 
						'email' => $email, 
						'email_benachrichtigung' => $_GET['email_benachrichtigung'], 
						'aktiv' => $_GET['aktiv'],
						'image_attachment_id' => $_GET['image_attachment_id'],
					), 
					array( 
						'%s',
						'%s',
						'%s',
						'%s', 
						'%d', 
						'%d', 
						'%d' 
					) 
				);
			}

		}
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_personen');
		exit;
	}

	function action__asbd_personen__update(){
		
		global $wpdb;
		
		$person = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_personen WHERE id={$_GET['id']}", OBJECT)[0];
		
		$user = get_user_by('email', $person->email);
		
		$update_user_result = wp_update_user(array(
			"ID"			=> $user->data->ID,
			"user_email"    => $_GET['email'],
			"display_name"  => $_GET['vorname'] . ' ' . $_GET['nachname'],
			"first_name"    => $_GET['vorname'],
			"last_name"     => $_GET['nachname'],
			"role"          => $_GET['role']
		));
			
		if (isset($_GET['passwort']) && $_GET['passwort'] != '') wp_set_password ($_GET['passwort'], $user->data->ID);
		
		if ($update_user_result){
			$wpdb->update( 
				$wpdb->prefix . 'asbd_personen',
				array( 
					'vorname' => $_GET['vorname'], 
					'nachname' => $_GET['nachname'],
					'telefon' => $_GET['telefon'], 
					'email' => $_GET['email'], 
					'email_benachrichtigung' => $_GET['email_benachrichtigung'], 
					'aktiv' => $_GET['aktiv'], 
					'image_attachment_id' => $_GET['image_attachment_id'],
				), 
				array('id' => $_GET['id']), 
				array( 
					'%s',
					'%s',
					'%s',
					'%s', 
					'%d', 
					'%d', 
					'%d' 
				), 
				array('%d') 
			);
		}
		if (isset($_GET['redirect_to'])){
			wp_redirect($_GET['redirect_to']);
		}else{
			wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_personen');
		}
		
		exit;
	}

	function action__asbd_personen__delete(){
		
		global $wpdb;
		
		$person = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}asbd_personen WHERE id={$_GET['id']}", OBJECT)[0];
		
		$user = get_user_by('email', $person->email);
		wp_delete_user($user->data->ID);
		
		$wpdb->delete( 
			$wpdb->prefix . 'asbd_personen',
			array('id' => $_GET['id']), 
			array('%d')
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_personen');
		exit;
	}


	/******************************************************
	** Fahrzeuge
	******************************************************/

	add_action('admin_post_asbd_fahrzeuge__create', 'action__asbd_fahrzeuge__create');
	add_action('admin_post_asbd_fahrzeuge__update', 'action__asbd_fahrzeuge__update');
	add_action('admin_post_asbd_fahrzeuge__delete', 'action__asbd_fahrzeuge__delete');

	function action__asbd_fahrzeuge__create(){

		global $wpdb;
		
		$wpdb->insert( 
			$wpdb->prefix . 'asbd_fahrzeuge',
			array( 
				'name' => $_GET['name'], 
				'default_tagdienst_beginn' => $_GET['default_tagdienst_beginn'],
				'default_tagdienst_ende' => $_GET['default_tagdienst_ende'], 
				'default_nachtdienst_beginn' => $_GET['default_nachtdienst_beginn'], 
				'default_nachtdienst_ende' => $_GET['default_nachtdienst_ende'], 
				'kennzeichen' => $_GET['kennzeichen'], 
				'notiz' => $_GET['notiz'], 
			), 
			array( 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s', 
				'%s', 
				'%s' 
			) 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_fahrzeuge');
		exit;
	}

	function action__asbd_fahrzeuge__update(){
		
		global $wpdb;
		
		$wpdb->update( 
			$wpdb->prefix . 'asbd_fahrzeuge',
			array( 
				'name' => $_GET['name'], 
				'default_tagdienst_beginn' => $_GET['default_tagdienst_beginn'],
				'default_tagdienst_ende' => $_GET['default_tagdienst_ende'], 
				'default_nachtdienst_beginn' => $_GET['default_nachtdienst_beginn'], 
				'default_nachtdienst_ende' => $_GET['default_nachtdienst_ende'], 
				'kennzeichen' => $_GET['kennzeichen'], 
				'notiz' => $_GET['notiz'], 
			), 
			array('id' => $_GET['id']), 
			array( 
				'%s',
				'%s',
				'%s',
				'%s',
				'%s', 
				'%s', 
				'%s' 
			), 
			array('%d') 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_fahrzeuge');
		exit;
	}

	function action__asbd_fahrzeuge__delete(){
		
		global $wpdb;
		
		$wpdb->delete( 
			$wpdb->prefix . 'asbd_fahrzeuge',
			array('id' => $_GET['id']), 
			array('%d') 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_fahrzeuge');
		exit;
	}


	/******************************************************
	** Sitzplätze
	******************************************************/

	add_action('admin_post_asbd_sitzplaetze__create', 'action__asbd_sitzplaetze__create');
	add_action('admin_post_asbd_sitzplaetze__update', 'action__asbd_sitzplaetze__update');
	add_action('admin_post_asbd_sitzplaetze__delete', 'action__asbd_sitzplaetze__delete');

	function action__asbd_sitzplaetze__create(){

		global $wpdb;
		
		$wpdb->insert( 
			$wpdb->prefix . 'asbd_sitzplaetze',
			array( 
				'name' => $_GET['name'], 
				'fahrzeuge_id' => $_GET['fahrzeuge_id'],
			), 
			array( 
				'%s',
				'%d',
			) 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_sitzplaetze');
		exit;
	}

	function action__asbd_sitzplaetze__update(){
		
		global $wpdb;
		
		$wpdb->update( 
			$wpdb->prefix . 'asbd_sitzplaetze',
			array( 
				'name' => $_GET['name'], 
				'fahrzeuge_id' => $_GET['fahrzeuge_id'],
			), 
			array('id' => $_GET['id']), 
			array( 
				'%s',
				'%d'
			), 
			array('%d') 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_sitzplaetze');
		exit;
	}

	function action__asbd_sitzplaetze__delete(){
		
		global $wpdb;
		
		$wpdb->delete( 
			$wpdb->prefix . 'asbd_sitzplaetze',
			array('id' => $_GET['id']), 
			array('%d') 
		);
		
		wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan_sitzplaetze');
		exit;
	}


	/******************************************************
	** Besetzungen
	******************************************************/

	add_action('admin_post_asbd_besetzungen__create', 'action__asbd_besetzungen__create');
	add_action('admin_post_asbd_besetzungen__update', 'action__asbd_besetzungen__update');
	add_action('admin_post_asbd_besetzungen__delete', 'action__asbd_besetzungen__delete');

	function action__asbd_besetzungen__create(){

		global $wpdb;
		
		$wpdb->insert( 
			$wpdb->prefix . 'asbd_besetzungen',
			array( 
				'personen_id' => $_GET['personen_id'], 
				'sitzplaetze_id' => $_GET['sitzplaetze_id'],
				'datum' => $_GET['datum'], 
				'beginn' => $_GET['beginn'], 
				'ende' => $_GET['ende'], 
				'dienst' => $_GET['dienst']
			), 
			array( 
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
			) 
		);
		
		if (isset($_GET['from'])){
			wp_redirect($_GET['from']);
		}else{
			wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan');
		}
		
		exit;
	}

	function action__asbd_besetzungen__update(){
		
		global $wpdb;
		
		$wpdb->update( 
			$wpdb->prefix . 'asbd_besetzungen',
			array( 
				'personen_id' => $_GET['personen_id'], 
				'sitzplaetze_id' => $_GET['sitzplaetze_id'],
				'datum' => $_GET['datum'], 
				'beginn' => $_GET['beginn'], 
				'ende' => $_GET['ende'], 
				'dienst' => $_GET['dienst']
			), 
			array('id' => $_GET['id']), 
			array( 
				'%d',
				'%d',
				'%s',
				'%s',
				'%s', 
				'%s', 
			), 
			array('%d') 
		);
		
		if (isset($_GET['from'])){
			wp_redirect($_GET['from']);
		}else{
			wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan');
		}
		
		exit;
	}

	function action__asbd_besetzungen__delete(){
		
		global $wpdb;
		
		$wpdb->delete( 
			$wpdb->prefix . 'asbd_besetzungen',
			array('id' => $_GET['id']), 
			array('%d') 
		);
		
		if (isset($_GET['from'])){
			wp_redirect($_GET['from']);
		}else{
			wp_redirect('/wp-admin/admin.php?page=asbd_dienstplan');
		}
		
		exit;
	}


	/******************************************************
	** Einstellungen
	******************************************************/

	add_action('admin_post_asbd_einstellungen__update', 'action__asbd_einstellungen__update');

	function action__asbd_einstellungen__update(){
		
		$fields = array(
			'asbd_smtp_host',
			'asbd_smtp_port',
			'asbd_smtp_email',
			'asbd_smtp_user',
			'asbd_smtp_passwort',
			'asbd_smtp_auth',
			'asbd_smtp_secure',
			'asbd_smtp_absender'
		);
		
		foreach($fields as $field){
			if (get_option($field) !== false) {
				update_option($field, $_GET[$field]);
			} else {
				add_option($field, $_GET[$field], null, 'no');			
			}
		}
		
		wp_redirect('/wp-admin/admin.php?page=asbd_einstellungen');

	}

?>