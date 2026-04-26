<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class benrueeg_rue_plug_nl extends benrueeg_rue_plug_settings {
	
	function count_users() {
		global $wpdb;
		$users = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->users " );
		return $users;
	}
	
	function author_slug_structure( $user ) {
		
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
		
		if ($slug) {
			return rawurldecode( sanitize_title($slug) );
		}
		
		if ($this->author_slug_option('hash')) {
			return hash( 'sha1', $user->ID . '-' . $user->user_login );
		}
		
	}
	
	function author_slug_structure_profile( $user ) {
		
		if ( ! $user ) return;
		
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
		
		if ( $slug ) {
			return rawurldecode( sanitize_title( $slug ) );
		}
		
		if ( $this->options('author_slug') == 'hash' ) {
			return hash( 'sha1', $user->ID . '-' . $user->user_login );
		}
		
	}
	
	function benrueeg_users_table_exists() {
		global $wpdb;
		
		$table = $wpdb->get_results( $wpdb->prepare(
		"SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = %s AND TABLE_NAME = %s ",
		$wpdb->dbname, $this->benrueeg_users_table() ) );
		
		if ( ! empty( $table ) ) {
			return true;
		}
		
		return false;
	}
	
	function benrueeg_users_not_exists_or_empty() {
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return true;
		}
		
		$check = $this->benrueeg_users_count();
		if ( ! $check ) {
			return true;
		}
		
		return false;
	}
	
	function get_user_nice_name( $user_id ) {
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return false;
		}
		
		$object_id = absint( $user_id );
		if ( ! $object_id ) {
			return false;
		}
		
		$nicename = $this->benrueeg_users_var('user_nice_name', array('user_id', $object_id, 'd'));
		if ( $nicename ) {
			return $nicename;
		}
	}
	
	function add_user_nice_name( $user_id, $nicename ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return false;
		}
		
		$object_id = absint( $user_id );
		if ( ! $object_id ) {
			return false;
		}
		
		$data = array(
		'id'             => NULL,
		'user_id'        => $user_id,
		'user_nice_name' => $nicename
		);
		return $wpdb->insert( $this->benrueeg_users_table(), $data, array( '%d', '%d', '%s' ) );
	}
	
	function update_user_nice_name( $user_id, $nicename ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return false;
		}
		
		$id_exists = $this->benrueeg_users_var('id', array('user_id', $user_id, 'd'));
		
		if ( ! $id_exists ) {
			return false;
		}
		
		$object_id = absint( $user_id );
		if ( ! $object_id ) {
			return false;
		}
		
		return $wpdb->query( $wpdb->prepare( "UPDATE {$this->benrueeg_users_table()} SET user_nice_name = %s WHERE `user_id` = '%d' ", $nicename, $object_id ) );
	}
	
	function get_userid_from_user_nice_name( $name ) {
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return;
		}
		
		$userid = $this->benrueeg_users_var('user_id', array('user_nice_name', $name, 's'));
		if ( $userid ) {
			return $userid;
		}
	}
	
	function delete_user_nice_name( $user_id ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return;
		}
		
		$object_id = absint( $user_id );
		if ( ! $object_id ) {
			return false;
		}
		
		$user_id_exists = $this->benrueeg_users_var('user_id', array('user_id', $object_id, 'd'));
		if ( ! $user_id_exists ) {
			return;
		}
		
		return $wpdb->delete( $this->benrueeg_users_table(), array( 'user_id' => $object_id ) );
	}
	
	function sanitized_containts_non_latin( $name ) {
		if (preg_match ('|[^A-Za-z0-9-_]|u', sanitize_title( $name ))) {
			return true;
		}
		return false;
	}
	
	function sanitized_containts_non_latin50( $name ) {
		if ($this->sanitized_containts_non_latin($name) && (mb_strlen( sanitize_title($name) ) > 50)) { 
			return true;
		}
		return false;
	}
	
	function sanitized_containts_non_latin_1_50( $name ) {
		if ($this->sanitized_containts_non_latin($name) && (mb_strlen( sanitize_title($name) ) <= 50)) {
			return true;
		}
		return false;
	}
	
	function containts_only_latin_letters_numbers( $name ) {
		if (preg_match ('|[^A-Za-z0-9]|u', $name) || trim($name) == '') {
			return false;
		}
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
		
		if ( ! $table ) return;
		return $table[0]->CHARACTER_MAXIMUM_LENGTH;
	}
	
	function mc_varchar() {
		global $wpdb; 
		$command = apply_filters( 'command_varchar_filter_BENrueeg_RUE', 'MODIFY COLUMN' );
		$wpdb->query( "ALTER TABLE $wpdb->users $command user_nicename varchar(50) NOT NULL DEFAULT '' " );
	}
	
	function change_varchar() {
		if ($this->get_varchar_max_character() > 50) {
			$this->mc_varchar();
		}
	}
	
	function urlencode_strtolower( $title ) {
		if ( $this->benrueeg_wp_is_valid_utf8 ( $title ) ) {
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
		if ( ! $user ) return;
		
		// hash
		$hashed = $user->ID . '-' . $user->user_login;
		$user_nicename_structure = hash( 'sha1', $hashed );
		$user__nicename = apply_filters( 'benrueeg_user_nicename_updb_sha', $user_nicename_structure, $user );
		$user__nicename = $this->duplicated_usernacename('users', $user->ID, $user__nicename, true);
		
		if ( $this->sanitized_containts_non_latin( $user->user_login ) || $v ) {
			$nice = $user__nicename;
		}
		// hash
		
		// user login
		$is_only_latin = ! $this->sanitized_containts_non_latin($user->user_login) ? true : false;
		
		//if ( false && $update && $is_only_latin && $this->options('langWlatin') == 'only_lang' && $this->options('lang') != 'default_lang' ) {
		if ( $update && $is_only_latin && $v == false ) {
			
			$_user_nicename_userlogin = sanitize_title($user->user_login);
			$user_nicename_userlogin = apply_filters( 'benrueeg_user_update_nicename_updb', $_user_nicename_userlogin, $user );
			$user_nicename_userlogin = $this->duplicated_usernacename('users', $user->ID, $user_nicename_userlogin);
			
			$nice = sanitize_title($user_nicename_userlogin);
			
		}
		// user login
		
		return $nice;
	}
	
	protected function up_benrueeg_users_nicename( $user_id ) {
		global $wpdb;
		
		$varchar = $this->options('varchar') == 'enabled' ? true : false;
		$author_slug_disabled = $this->options('author_slug') == 'disable' ? true : false;
		
		$user = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->users WHERE ID = %d ", $user_id ) );
		if ( ! $user ) return;
		
		$this_opt = false;
		if ( $author_slug_disabled || ( $varchar && $this->options('author_slug') == 'hash' && $this->options('only_not_latin_up_db') == 'disable' ) ) {
			$this->delete_user_nice_name( $user_id );
			$this_opt = true;
		}
		
		// in benrueeg_users
		if ( $this_opt == false ) {
			
			$user_nicename_structure = $this->author_slug_structure_profile( $user ) ? $this->author_slug_structure_profile( $user ) : rawurldecode( sanitize_title( $user->user_login ) );
			$_user_nicename = apply_filters( 'benrueeg_user_nice_name_profile_updb', $user_nicename_structure, $user );
			$user_nicename = mb_substr( $_user_nicename, 0, 100 );
			$user_nicename = $this->duplicated_usernacename('benrueeg_users', $user->ID, $user_nicename);
			
			$author_link_check = $this->benrueeg_users_var('id', array('user_id', $user_id, 'd'));
			
			if ( $author_link_check ) {
				$this->update_user_nice_name( $user_id, $user_nicename );
			} else {
				$this->add_user_nice_name( $user_id, $user_nicename );
			}
			
		}
		// in benrueeg_users
	}
	
	// slug
	
	function _request( $query_vars ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return $query_vars;
		}
		
		$keys = array('author_name' => 'author_name','author' => 'author','bp_member' => 'bp_member');
		$key = '';
		foreach($keys as $name) {
			if (isset($query_vars[$name]) && array_key_exists($name, $query_vars)) {
				$key = $name;
			}
		}
		
		if ( $key == '' ) {
			return $query_vars;
		}
		
		$author_id = $this->get_userid_from_user_nice_name(rawurldecode($query_vars[$key]));
		$authordata = get_userdata( $author_id );
		
		if ( $author_id ) {
			$query_vars[$key] = $key == 'author' ? $author_id : $authordata->user_nicename;
		}
		
		return $query_vars;
	}
	
	function _author_link( $link, $author_id, $author_nicename ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return $link;
		}
		
		$author = $this->benrueeg_users_var('user_id', array('user_id', $author_id, 'd'));
		if ( $author ) {
			//remove_all_filters('author_link');
			$user_nicename = $this->get_user_nice_name( $author_id );
			
			if ( ! $user_nicename ) return $link;
			
			$author_nicename = ! $author_nicename ? $author_id : $author_nicename;
			$link = str_replace( $author_nicename, strtolower( rawurlencode( $user_nicename ) ), $link );
		}
		return $link;
	}
	
	function _pre_post_link( $permalink, $post, $leavename ) {
		
		if ( $permalink ) {
		
			$author = $this->get_user_nice_name( $post->post_author );
			if ( ! $author ) {
				return $permalink;
			}
		
			if ( str_contains( $permalink, '%author%' ) && ! wp_force_plain_post_permalink( $post ) ) {
				$permalink = str_replace( '%author%', $author, $permalink );
			}
		}
		
		return $permalink;
	}
	
	function _member_bp_link( $slug, $user_id ) {
		$nicename = $this->get_user_nice_name($user_id);
		if ( $nicename ) {
			$slug = strtolower( rawurlencode($nicename) );
		}
		
		return $slug;
	}
	
	function _bp_core_get_user_domain( $domain, $user_id ) {
	
		if ( empty( $user_id ) ) {
			return;
		}
		/*
		$user_id_user_nice_name = $this->get_userid_from_user_nice_name( $this->get_user_nice_name( $user_id ) );
		
		if ( ! $user_id_user_nice_name ) {
			return $domain;
		}
		*/
		$user_nice_name = $this->get_user_nice_name( $user_id );
		
		if ( ! $user_nice_name ) {
			return $domain;
		}
		
		$username = strtolower( rawurlencode( $user_nice_name ) );
		$after_domain = bp_core_enable_root_profiles() ? $username : bp_get_members_root_slug() . '/' . $username;
		$domain = trailingslashit( bp_get_root_domain() . '/' . $after_domain );
		
		return $domain;
	}
	
	/*
	* redirect after edit profile page is updated to the new nickname
	*/
	function redirect_after_edit_profile_update( $domain ) {
	
	    if ( in_array( $this->options('author_slug'), array( 'userlogin','hash','disable' ) ) ) {
			return $domain;
		}
	
		if ( ! empty( $domain ) && bp_is_user_profile_edit() && isset( $_POST['field_ids'] ) ) {
			$domain = $this->_bp_core_get_user_domain( false, bp_displayed_user_id() );
		}
		
		return $domain;
	}

	function _bp_get_the_profile_field_value( $value, $type, $field_id ) {
		
		if ( ! function_exists( 'bp_displayed_user_id' ) ) {
			return $value;
		}
		
		$user_nice_name = $this->get_user_nice_name(bp_displayed_user_id());
		
		if ( $user_nice_name && $field_id == $this->bp_xprofile_nickname_field_id() ) {
			$value = $user_nice_name;
		}
		
		return $value;
	}
	
	function _bp_core_get_userid( $userid, $username ) {
		
		$user_id = $this->get_userid_from_user_nice_name(rawurldecode($username));
		if( $user_id ){
			$userid = $user_id;
		}
		return $userid;
	}
	
	function bp_xprofile_firstname_field_id( $defalut = 1, $get_option = true ) {
		$field_id = 0;
		
		if ( ! function_exists( 'bp_get_option' ) ) {
			return;
		}
		
		if ( $this->mu() ) {
			$field_id = get_site_option( 'bp-xprofile-firstname-field-id' );
		}
		
		if ( empty( $field_id ) && $get_option ) {
			$field_id = bp_get_option( 'bp-xprofile-firstname-field-id', $defalut );
		}
		
		return $field_id;
	}
	
	function bp_xprofile_nickname_field_id( $no_fallback = false, $get_option = true ) {
		$field_id = 0;
		
		if ( ! function_exists( 'bp_get_option' ) ) {
			return;
		}
		
		if ( $this->mu() ) {
			$field_id = get_site_option( 'bp-xprofile-nickname-field-id', $no_fallback ? 0 : 0 );
		}
		
		if ( empty( $field_id ) && $get_option ) {
			$field_id = bp_get_option( 'bp-xprofile-nickname-field-id', $no_fallback ? 0 : 0 );
		}
		
		// Set nickname field id to 0(zero) if first name and nickname both are same.
		$first_name_id = $this->bp_xprofile_firstname_field_id();
		if ( $first_name_id === (int) $field_id ) {
			$field_id = 0;
		}
		
		return $field_id;
	}
	
	// slug
	
	/*
		function varchar() {
		if ( $this->options('varchar') == 'enabled' ) {
		$this->change_varchar();
		} 
		}
	*/
	function muplugins_is_empty( $path ) {
		$empty = true;
		$dir = opendir($path); 
		while($file = readdir($dir)) {
			if ( $file != '.' && $file != '..' ) {
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
		
		if ( ! file_exists( $file ) ) return;
		unlink($file);
		
		$_dir = WP_CONTENT_DIR . '/mu-plugins';
		if ( $this->muplugins_is_empty($_dir) && is_dir($_dir) ) {
			rmdir($_dir);
		}
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
		//$opts = $this->get_option( 'BENrueeg_RUE_settings' );
		//$varchar = isset($opts['varchar']) ? $opts['varchar'] : '';
		$option = $this->get_option('benrueeg_nicename_msg_only_store_all_ids');
		$user_id = isset($option['user_id']) ? $option['user_id'] : 0;
		$time = isset($option['time']) ? $option['time'] : 0;
		
		if ( /*$varchar != 'enabled' ||*/ ! $option ) {
			return;
		}
		
		if ( $user_id == get_current_user_id() && $this->can_create_users() ) {
			return;
		}
		
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
		
		$query = "CREATE TABLE {$this->benrueeg_users_table()} (
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