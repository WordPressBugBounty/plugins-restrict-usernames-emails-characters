<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

        function benrueeg_rue__get_varchar_max_character() {
	    global $wpdb;
	
	        $dbName = $wpdb->dbname; // get database name of wordpress
	        $table_name = "{$wpdb->prefix}users";
	        $table = $wpdb->get_results( $wpdb->prepare(
		    "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s AND COLUMN_NAME = 'user_nicename' ",
		    $dbName, $table_name
	        ) );
	
	        if (!$table) return;
	        return $table[0]->CHARACTER_MAXIMUM_LENGTH;
	    }

		function benrueeg_rue__mc_varchar() {
		global $wpdb;
		if (benrueeg_rue__get_varchar_max_character() > 50)
		$wpdb->query( "ALTER TABLE $wpdb->users MODIFY COLUMN user_nicename varchar(50) NOT NULL DEFAULT '' " );
        }

        function benrueeg_rue_muplugins_is_empty($path) {
            $empty = true;
            $dir = opendir($path); 
            while($file = readdir($dir)) {
                if($file != '.' && $file != '..') {
                $empty = false;
                break;
                }
            }
            closedir($dir);
            return $empty;
        }
		
	    function benrueeg_rue_RemoveMuPlugin() {
			$dir  = WP_CONTENT_DIR . '/mu-plugins'; // nom du dossier
	        $file = $dir . '/restrict-username-email-character.php'; // nom du fichier .php

            if (!file_exists($file)) return;
			unlink($file);
			
			$_dir = WP_CONTENT_DIR . '/mu-plugins';
			if (benrueeg_rue_muplugins_is_empty($_dir) && is_dir($_dir))
				rmdir($_dir);
		}

        function benrueeg_rue_delete_plugin_uninstall() {
		global $wpdb;
		
		    delete_option('BENrueeg_RUE_settings');
		    delete_option('BENrueeg_RUE_settings_Tw');
		    delete_option('restrict_usernames_emails_characters_ver_base');
		    delete_option('benrueeg_rue_wordpress_core_nace');
			delete_option('benrueeg_nicename_msg_only_store_all_ids');
			delete_option('benrueeg_nicename_store_all_users_id');
			delete_option('benrueeg_n_store_all_completed_ids');
			delete_option('benrueeg_rue_1_7____notice');
			delete_option('benrueeg_nicename_error_store_all_users_id');
		
		    delete_site_option('BENrueeg_RUE_settings');
		    delete_site_option('BENrueeg_RUE_settings_Tw');
		    delete_site_option('restrict_usernames_emails_characters_ver_base');
		    delete_site_option('benrueeg_rue_wordpress_core_nace');
			delete_site_option('benrueeg_nicename_msg_only_store_all_ids');
			delete_site_option('benrueeg_nicename_store_all_users_id');
			delete_site_option('benrueeg_n_store_all_completed_ids');
			delete_site_option('benrueeg_rue_1_7____notice');
			delete_site_option('benrueeg_nicename_error_store_all_users_id');
		
		    $wpdb->query( "DROP table {$wpdb->prefix}benrueeg_users" );
		    benrueeg_rue_RemoveMuPlugin();
	        benrueeg_rue__mc_varchar();
        }

        if ( ! defined( 'BENRUEEG_RUE_VER_B' ) ) {
	        benrueeg_rue_delete_plugin_uninstall();
        }