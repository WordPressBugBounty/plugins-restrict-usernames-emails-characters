<?php
	
	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_nl' ) ) :
	
	class ben_plug_restrict_usernames_emails_characters_nl extends ben_plug_restrict_usernames_emails_characters_options {
		
		public function __construct() {
			parent::__construct();
		}
		
        function count_users() {
			global $wpdb;
	        $users = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users " );
	        return $users;
	    }
		
		function author_slug_structure($user) {
			
			if (!$user) return;
			
			$slug = '';
			
		    if ($this->author_slug_option('userlogin')) {
		        $slug = $user->user_login;
		    } elseif ($this->author_slug_option('nickname')) {
		        $slug = get_user_meta( $user->ID, 'nickname', true );
		    } elseif ($this->author_slug_option('first_name')) {
				$firstname = get_user_meta( $user->ID, 'first_name', true );
		        $slug = trim($firstname) != '' ? $firstname : $user->display_name;
		    } elseif ($this->author_slug_option('last_name')) {
		        $lastname = get_user_meta( $user->ID, 'last_name', true );
				$slug = trim($lastname) != '' ? $lastname : $user->display_name;
			} elseif ($this->author_slug_option('displayname')) {
		        $slug = $user->display_name;
		    }
		
		    if ($slug)
		    return rawurldecode(sanitize_title($slug));
		
		    if ($this->author_slug_option('hash')) {
		        return hash( 'sha1', $user->ID . '-' . $user->user_login );
		    }
			
        }
		
		function author_slug_structure_profile($user) {
			
			if (!$user) return;
			
			$slug = '';
			
		    if ($this->options('author_slug') == 'userlogin') {
		        $slug = $user->user_login;
		    } elseif ($this->options('author_slug') == 'nickname') {
		        $slug = isset($_POST['nickname']) ? $_POST['nickname'] : get_user_meta( $user->ID, 'nickname', true );
		    } elseif ($this->options('author_slug') == 'first_name') {
		        $first_name_post = isset($_POST['first_name']) ? trim($_POST['first_name']) : '';
				$firstname = get_user_meta( $user->ID, 'first_name', true );
				$firstname = trim($firstname) != '' ? $firstname : $user->display_name;
				$slug = $first_name_post ? $first_name_post : $firstname;
		    } elseif ($this->options('author_slug') == 'last_name') {
		        $last_name_post = isset($_POST['last_name']) ? trim($_POST['last_name']) : '';
				$lastname = get_user_meta( $user->ID, 'last_name', true );
				$lastname = trim($lastname) != '' ? $lastname : $user->display_name;
				$slug = $last_name_post ? $last_name_post : $lastname;
			} elseif ($this->options('author_slug') == 'displayname') {	
		        $slug = isset($_POST['display_name']) ? $_POST['display_name'] : $user->display_name;
		    }
		
		    if ($slug)
		    return rawurldecode(sanitize_title($slug));
		
		    if ($this->options('author_slug') == 'hash') {
		        return hash( 'sha1', $user->ID . '-' . $user->user_login );
		    }
			
        }
		
        function check_table_exists($dbname) {
	    global $wpdb;
	
	        $dbName = $wpdb->dbname; // get database name of wordpress
	        $table_name = "{$wpdb->prefix}$dbname";
	        $table = $wpdb->get_results( $wpdb->prepare(
		    "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s ",
		    $dbName, $table_name
	        ) );
			
	        if ( ! empty( $table ) ) {
		        return true;
	        }
	    return false;
        }
		
		function benrueeg_users_not_exists_or_empty() {
	    global $wpdb;
		
		if ( ! $this->check_table_exists('benrueeg_users') )
			return true;
		
        $check = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}benrueeg_users LIMIT 1" );
		if ( ! $check )
            return true;
		
		return false;
        }
		
		function add_user_nice_name($user_id, $nicename) {
		global $wpdb;
		
			if ( ! $this->check_table_exists('benrueeg_users') )
				return false;
		
		    $object_id = absint( $user_id );
			if ( ! $object_id )
				return false;
			
				$data = array(
					'id'             => NULL,
					'user_id'        => $user_id,
					'user_nice_name' => $nicename
				);
			    return $wpdb->insert( $wpdb->prefix . 'benrueeg_users', $data, array( '%d', '%d', '%s' ) );
		}
		
		function get_user_nice_name($user_id) {
		global $wpdb;
		
			if ( ! $this->check_table_exists('benrueeg_users') )
				return false;
		
		    $object_id = absint( $user_id );
			if ( ! $object_id )
				return false;
			
			$nicename = $wpdb->get_var( $wpdb->prepare( "SELECT user_nice_name FROM {$wpdb->prefix}benrueeg_users WHERE `user_id` = '%d' LIMIT 1", $object_id ) );
            if ($nicename)
				return $nicename;
		}
		
		function get_userid_from_user_nice_name($name) {
		global $wpdb;
		
			if ( ! $this->check_table_exists('benrueeg_users') )
				return;
			
			$userid = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' LIMIT 1", $name ) );
            if ($userid)
				return $userid;
		}
		
		function update_user_nice_name($user_id, $nicename) {
		global $wpdb;
		
			if ( ! $this->check_table_exists('benrueeg_users') )
				return false;
			
			if ( ! $this->get_user_nice_name($user_id) )
				return false;
		
		    $object_id = absint( $user_id );
			if ( ! $object_id )
				return false;
		
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}benrueeg_users SET user_nice_name = %s WHERE `user_id` = '%d' ", $nicename, $object_id ) );
		}
		
		function delete_user_nice_name($user_id) {
		global $wpdb;
		
			if ( ! $this->check_table_exists('benrueeg_users') )
				return;
			
			if ( ! $this->get_user_nice_name($user_id) )
				return;
		
		    $object_id = absint( $user_id );
			if ( ! $object_id )
				return false;
		
			return $wpdb->delete( $wpdb->prefix . 'benrueeg_users', array( 'user_id' => $object_id ) );
		}
		
        function sanitized_containts_non_latin($name) {
	        if (preg_match ('|[^A-Za-z0-9-_]|u', sanitize_title( $name ))) 
				return true;
	        return false;
	    }
		
        function sanitized_containts_non_latin50($name) {
	        if ($this->sanitized_containts_non_latin($name) && (mb_strlen( sanitize_title($name) ) > 50)) 
				return true;
	        return false;
		}
		
        function sanitized_containts_non_latin_1_50($name) {
	        if ($this->sanitized_containts_non_latin($name) && (mb_strlen( sanitize_title($name) ) <= 50)) 
				return true;
	        return false;
		}
		
        function containts_only_latin_letters_numbers($name) {
	        if (preg_match ('|[^A-Za-z0-9]|u', $name) || trim($name) == '') 
				return false;
	        return true;
	    }		/*
        function sanitized_userlogin_containts_non_latin_exists() {
	    global $wpdb;
	
            $users = $wpdb->get_results( "SELECT user_login FROM $wpdb->users ORDER BY ID DESC " );
		    $exists = false;
		    foreach ($users as $user) {
			    if ($this->sanitized_containts_non_latin($user->user_login)) {
				    $exists = true;
					break;
				}
		    }
		
            if ($exists) {
				return true;
            }
	        return false;
        }
		*/
        function get_varchar_max_character() {
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
		
		function mc_varchar() {
		global $wpdb; 
		$command = apply_filters( 'command_varchar_filter_BENrueeg_RUE', 'MODIFY COLUMN' );
		$wpdb->query( "ALTER TABLE $wpdb->users $command user_nicename varchar(50) NOT NULL DEFAULT '' " );
        }
		
        function change_varchar() {
	        if ($this->get_varchar_max_character() > 50)
	        $this->mc_varchar();
        }
		
        function urlencode_strtolower($title) {
	        if ( seems_utf8( $title ) ) {
		        if ( function_exists( 'mb_strtolower' ) ) {
			        $title = mb_strtolower( $title, 'UTF-8' );
		        }
		    $title = utf8_uri_encode( $title, 200 );
	        }

	        $title = strtolower( $title );
			
			return $title;
        }
		
        protected function updb_user_nicename( $user_id, $update = '' ) {
			global $wpdb;
			
			$varchar = $this->options('varchar') == 'enabled' ? true : false;
			$v = $varchar && $this->options('only_not_latin_up_db') == 'disable' ? true : false;
			$nice = '';
			
			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID = %d ", $user_id ) );
			if (!$user) return;
				
			// hash
			$hashed = $user->ID . '-' . $user->user_login;
		    $user_nicename_structure = hash( 'sha1', $hashed );
			$user__nicename = apply_filters( 'user_nicename_register_filter_benrueeg_rue', $user_nicename_structure, $user );
			
	        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND ID != %d LIMIT 1", $user__nicename, $user_id ) );

	        if ( $user_nicename_check ) {
		        $suffix = 2;
		        while ( $user_nicename_check ) {
			        // user_nicename allows 50 chars. Subtract one for a hyphen, plus the length of the suffix.
			        $base_length         = 49 - mb_strlen( $suffix );
			        $alt_user_nicename   = mb_substr( $user__nicename, 0, $base_length ) . "-$suffix";
			        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND ID != %d LIMIT 1", $alt_user_nicename, $user_id ) );
			        ++$suffix;
		        }
		    $user__nicename = $alt_user_nicename;
	        }
			
		    if ( $this->sanitized_containts_non_latin($user->user_login) || $v ) {
				$nice = $user__nicename;
			}
			// hash
			
			// user login
			$is_only_latin = ! $this->sanitized_containts_non_latin($user->user_login) ? true : false;
			
			if ( $update && $is_only_latin && $v == false ) {
			
			$user_nicename_userlogin = rawurldecode(sanitize_title($user->user_login));
			$user_nicename_userlogin = apply_filters( 'user_nicename_register_filter_benrueeg_rue', $user_nicename_userlogin, $user );
			$user_nicename_userlogin_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND ID != %d LIMIT 1", $user_nicename_userlogin, $user_id ) );

	        if ( $user_nicename_userlogin_check ) {
		        $__suffix = 2;
		        while ( $user_nicename_userlogin_check ) {
			        // user_nicename allows 50 chars. Subtract one for a hyphen, plus the length of the __suffix.
			        $_base_length = 49 - mb_strlen( $__suffix );
			        $alt_user_nicename_userlogin = mb_substr( $user_nicename_userlogin, 0, $_base_length ) . '-' . $__suffix;
			        $user_nicename_userlogin_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND ID != %d LIMIT 1", $alt_user_nicename_userlogin, $user_id ) );
			        ++$__suffix;
		        }
		    $user_nicename_userlogin = $alt_user_nicename_userlogin;
	        }
			
				$nice = sanitize_title($user_nicename_userlogin);
			
			}
			// user login
			
			return $nice;
		}
		
        protected function up_benrueeg_users_nicename( $user_id ) {
			global $wpdb;
			
			$varchar = $this->options('varchar') == 'enabled' ? true : false;
			$dis_this_opt = $this->options('author_slug') == 'disable' ? true : false;
			
			$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID = %d ", $user_id ) );
			if (!$user) return;
			
			$this_opt = false;
			if ( $dis_this_opt || ($varchar && $this->options('author_slug') == 'hash' && $this->options('only_not_latin_up_db') == 'disable') ) {
				$this->delete_user_nice_name($user_id);
			    $this_opt = true;
			}
			
			// in benrueeg_users
			if ( $this_opt == false ) {
			
		    $user_nicename_structure = $this->author_slug_structure_profile($user) ? $this->author_slug_structure_profile($user) : $user->user_login;
			//$user_nicename = has_filter( 'user_nicename_register_filter_benrueeg_rue' ) ? apply_filters_deprecated( 'user_nicename_register_filter_benrueeg_rue', array( $user_nicename_structure, $user ), '4.0', 'user_nicename_reg_up_filter_benrueeg', 'deprecated' ) : apply_filters( 'user_nicename_reg_up_filter_benrueeg', $user_nicename_structure, $user );
			$user_nicename = apply_filters( 'user_nicename_reg_up_filter_benrueeg', $user_nicename_structure, $user );
			$user_nicename = mb_substr( $user_nicename, 0, 100 );
			
	        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $user_nicename, $user_id ) );

	        if ( $user_nicename_check ) {
		        $suffix = 2;
		        while ( $user_nicename_check ) {
			        // user_nicename allows 100 chars. Subtract one for a hyphen, plus the length of the suffix.
			        $base_length         = 99 - mb_strlen( $suffix );
			        $alt_user_nicename   = mb_substr( $user_nicename, 0, $base_length ) . "-$suffix";
			        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $alt_user_nicename, $user_id ) );
			        ++$suffix;
		        }
		    $user_nicename = $alt_user_nicename;
	        }
			
			$author_link_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_id` = '%d' LIMIT 1", $user_id ) );
			
			if ($author_link_check) {
				$this->update_user_nice_name($user_id, $user_nicename);
			} else {
			    $this->add_user_nice_name($user_id, $user_nicename);
			}
			
			}
			// in benrueeg_users
		}
		
		// slug
		
		function _request( $query_vars ) {
			global $wpdb;
			
			$dummy_query = new WP_Query();  // the query isn't run if we don't pass any query vars
            $dummy_query->parse_query( $query_vars );
			
			if ( ! $this->check_table_exists('benrueeg_users') )
				return $query_vars;
			/*
			$keys = array_fill_keys(array('author_name','bp_member'), true);
            if ((array_intersect_key($keys, $query_vars)))
			*/	
	        
			if ( isset($query_vars['author_name']) && array_key_exists( 'author_name', $query_vars ) ) {
				
		        $author_id = $this->get_userid_from_user_nice_name(rawurldecode($query_vars['author_name']));
		        $user_nicename = $wpdb->get_var( $wpdb->prepare( "SELECT user_nicename FROM $wpdb->users WHERE ID = %d", $author_id));
				
				if ( $author_id ) {
                    //$query_vars['author'] = $author_id;
                    $query_vars['author_name'] = $user_nicename;
                }
				
			}
			
			//$keys_bp = array_fill_keys(array('bp_member','author'), true);
			if ($this->bp_not_boss() && isset($query_vars['bp_member']) && array_key_exists( 'bp_member', $query_vars )) {
			//if ( $this->bp() && !$this->bb() && array_key_exists( 'bp_member', $query_vars ) ) {
				
		        $author_id = $this->get_userid_from_user_nice_name(rawurldecode($query_vars['bp_member']));
		        $user_nicename = $wpdb->get_var( $wpdb->prepare( "SELECT user_nicename FROM $wpdb->users WHERE ID = %d", $author_id));
		
		        if ( $author_id ) {
					//$query_vars['author'] = $author_id;
                    $query_vars['bp_member'] = $user_nicename;
                }
				
			}
			
            return $query_vars;
        }
		
        function _author_link( $link, $author_id, $author_nicename ) {
	    global $wpdb;
	
			if ( ! $this->check_table_exists('benrueeg_users') )
				return $link;
	
            $author = $wpdb->get_row( $wpdb->prepare("SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_id` = %d", $author_id) );
            if ( $author ) {
		        //remove_all_filters('author_link');
		        //add_filter('author_link', 'wpse5742_author_link', 10, 3);
		        $user_nicename = $this->get_user_nice_name($author_id);
		
		        if (!$user_nicename) return $link;
				
                $link = str_replace( $author_nicename, rawurlencode($user_nicename), $link );
            }
            return $link;
        }
		
        function _member_bp_link( $slug, $user_id ) {
	        $nicename = $this->get_user_nice_name($user_id);
	        if ($nicename)
                $slug = rawurlencode($nicename);

	        return $slug;
        }
		
        function _bp_core_get_user_domain($domain, $user_id, $user_nicename = false, $user_login = false) {
            if ( empty( $user_id ) )
                return;
	
	        $user_id_user_nice_name = $this->get_userid_from_user_nice_name($this->get_user_nice_name($user_id));
	
	        if (!$user_id_user_nice_name)
		        return $domain;
	
            $after_domain =  bp_get_members_root_slug() . '/' . rawurlencode($this->get_user_nice_name($user_id));
            $domain = trailingslashit( bp_get_root_domain() . '/' . $after_domain );
	
            return $domain;
        }

        function _bp_core_get_userid($userid, $username){
	
	        $user_id = $this->get_userid_from_user_nice_name(rawurldecode($username));
            if($user_id){
                $userid = $user_id;
            }
            return $userid;
        }
		
		// slug
		
		/*
        function varchar() {
		  if ( $this->options('varchar') == 'enabled' ) {
		  $this->change_varchar();
		  } 
        }
        */
        function muplugins_is_empty($path) {
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
		
	    function RemoveMuPlugin() {
			$dir  = WP_CONTENT_DIR . '/mu-plugins'; // nom du dossier
	        $file = $dir . '/restrict-username-email-character.php'; // nom du fichier .php

            if (!file_exists($file)) return;
			unlink($file);
			
			$_dir = WP_CONTENT_DIR . '/mu-plugins';
			if ($this->muplugins_is_empty($_dir) && is_dir($_dir))
				rmdir($_dir);
		}
		
		function hash_password( $password, $iteration_count_log = 8 ) {
		global $wp_hasher;

		    if ( empty( $wp_hasher ) ) {
			    require_once ABSPATH . WPINC . '/class-phpass.php';
			    // By default, use the portable hash from phpass.
			    $wp_hasher = new PasswordHash( $iteration_count_log, true );
		    }

		    return $wp_hasher->HashPassword( trim( $password ) );
	    }
		
        function maintenance_mode() {
	        //$opts = get_option( 'BENrueeg_RUE_settings' );
	        //$varchar = isset($opts['varchar']) ? $opts['varchar'] : '';
	        $option = $this->get_option('benrueeg_nicename_msg_only_store_all_ids');
			$user_id = isset($option['user_id']) ? $option['user_id'] : 0;
			$time = isset($option['time']) ? $option['time'] : 0;
	
	        if ( /*$varchar != 'enabled' ||*/ !$option )
				return;
	
	        if ( $user_id == get_current_user_id() && current_user_can(apply_filters( 'benrueeg_rue_filter_updb_cap', 'create_users' )) )
				return;
			
			if ( (time() - (int) $time) > 600 ) { // after 10 minutes auto remove the maintenance mode
		        $this->delete_option('benrueeg_nicename_msg_only_store_all_ids');
		    }
			
	        wp_die(
		        __( 'Briefly unavailable for scheduled maintenance. Check back in a minute.' ),
		        __( 'Maintenance' ),
		        503
	        );
        }
		
		function benrueeg_tables() {
	    global $wpdb;
		
        $charset_collate = $wpdb->get_charset_collate();
	
		    $query = "CREATE TABLE {$wpdb->prefix}benrueeg_users (
	        id bigint(20) unsigned NOT NULL auto_increment,
			user_id bigint(20) unsigned NOT NULL default '0',
	        user_nice_name varchar(255) NOT NULL default '',
	        PRIMARY KEY (id),
	        KEY user_id (user_id),
	        KEY user_nice_name (user_nice_name)
            ) $charset_collate;";
			
			require_once ABSPATH . 'wp-admin/includes/upgrade.php';
            dbDelta( $query );
        }
		
	}
		
	endif;