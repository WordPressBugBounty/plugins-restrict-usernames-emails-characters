<?php
if ( ! defined( 'ABSPATH' ) ) exit;

trait benrueeg_class_rue_plug_functions {
	
	function can_create_users() {
		$can = apply_filters( 'benrueeg_cap_can_create_users', $this->mu() ? 'manage_network_users' : 'create_users' );	
		return current_user_can( $can );
	}
	
	function can_create_users_backend() {
		$can = apply_filters( 'benrueeg_cap_can_create_users_backend', $this->mu() ? 'manage_network_users' : 'create_users' );	
		return current_user_can( $can );
	}
	
	function benrueeg_users_table() {
		global $wpdb;
		
		if ( $this->mu() ) {
			$prefix = $wpdb->base_prefix;
			} else {
			$prefix = $wpdb->prefix;
		}
		
		return "{$prefix}benrueeg_users";
	}
	
	function prefix() {
		global $wpdb;
		
		if ( $this->mu() ) {
			$prefix = $wpdb->base_prefix;
			} else {
			$prefix = $wpdb->prefix;
		}
		
		return $prefix;
	}
	
	function benrueeg_users_var( $select, $where = array() ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return false;
		}
		
		$w0 = isset($where[0]) ? $where[0] : '';
		$w1 = isset($where[1]) ? $where[1] : '';
		$w2 = isset($where[0]) ? $where[2] : '';
		
		$data = $wpdb->get_var( $wpdb->prepare( "SELECT $select FROM {$this->benrueeg_users_table()} WHERE `$w0` = '%$w2' LIMIT 1", $w1 ) );
		if ( $data ) {
			return $data;
		}
	}
	
	function benrueeg_users_row( $select, $where = array() ) {
		global $wpdb;
		
		if ( ! $this->benrueeg_users_table_exists() ) {
			return false;
		}
		
		$w0 = isset($where[0]) ? $where[0] : '';
		$w1 = isset($where[1]) ? $where[1] : '';
		$w2 = isset($where[0]) ? $where[2] : '';
		
		$data = $wpdb->get_row( $wpdb->prepare( "SELECT $select FROM {$this->benrueeg_users_table()} WHERE `$w0` = '%$w2' LIMIT 1", $w1 ) );
		if ( $data ) {
			return $data;
		}
	}
	
	function benrueeg_users_count() {
		global $wpdb;
		return $wpdb->get_var( "SELECT COUNT(*) FROM {$this->benrueeg_users_table()} " );
	}
	
	function ben_username_empty($username) {
		
		$wout_sp =  preg_replace( '/\s+/', '', $username );
		if ( empty( $wout_sp ) ) return true;
		return false;
	}
	
	function duplicated_usernacename( $table, $user_id, $usernicename, $hash = '' ) {
		global $wpdb;
		
		if (!$user_id) return;
		
		$where = '';
		
		switch ($table) {
			case 'users':
			$where = "SELECT ID FROM $wpdb->users WHERE `user_nicename` = '%s' AND `ID` != '%d' LIMIT 1"; 
			break;
			case 'benrueeg_users':
			$where = "SELECT user_id FROM {$this->benrueeg_users_table()} WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1"; 
			break;
		}
		
		if ($where == '') return;
		
		$user_nicename = $usernicename;
		$user_nicename_check = $wpdb->get_var( $wpdb->prepare( $where, $user_nicename, $user_id ) );
		
		if ( $user_nicename_check ) {
			$suffix = 2;
			while ( $user_nicename_check ) {
				// allows 100 chars. Subtract one for a hyphen, plus the length of the suffix.
				$base_length         = 99 - mb_strlen( $suffix );
				$alt_user_nicename   = mb_substr( $user_nicename, 0, $base_length ) . "-$suffix";
				$_alt_user_nicename   = $hash == true ? hash( 'sha1', $alt_user_nicename ) : $alt_user_nicename;
				$user_nicename_check = $wpdb->get_var( $wpdb->prepare( $where, $_alt_user_nicename, $user_id ) );
				++$suffix;
			}
			$user_nicename = $_alt_user_nicename;
		}
		
		return $user_nicename;
		
	}
	
	function unfinished_per_up_process() { // if the database update process is incomplete
		$store_limit = $this->get_option('benrueeg_nicename_store_all_users_id'); // option (store users id updated)
		$updb_completed = $this->get_option('benrueeg_n_store_all_completed_ids'); // option: if the update database precess is complete
		$getcounterror = $this->get_option('benrueeg_nicename_error_store_all_users_id'); // errors store
		
		if ( $store_limit && count($store_limit) < $this->count_users() && (int) $updb_completed['count_error_update'] == 0 && ! $getcounterror ) {
			return true;
		}
		
		return false;
	}
	
	function _up_process_per_method( $value ) {
		$up_process_per_method = $this->_option( 'up_process_per_method' ) ? $_POST['BENrueeg_RUE_settings']['up_process_per_method'] : $this->options( 'up_process_per_method' );
		if ( $up_process_per_method == $value ) {
			return true;
		}
		
		return false;
	}
	
	function _validate_username( $username ) {
		$sanitized = sanitize_user( $username, true );
		$valid     = ( $sanitized === $username && ! empty( $sanitized ) );
		return $valid;
	}
	
	function benrueeg_validate_username( $username ) {
		
		$valid_charts = $invalid_chars_allow = false;
		
		$list_chars = array_filter( array_unique( array_map('trim', explode( PHP_EOL, $this->options( 'disallow_spc_cars' ) ) ) ) );
		$list_chars_dis = implode( $list_chars );
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_allow_spc_cars = array_filter( array_unique( array_map( 'trim', explode( PHP_EOL, $allow_spc_cars ) ) ) );
		
		if ( ! $this->mu() ) {
			$preg_ = $this->bb() ? array( '-','_','.' ) : array( '-','_','.','@' );	
			foreach ( $preg_ as $preg ) 
			{
				if ( $this->_validate_username( $username ) && preg_match( '/['. $preg .']/', $list_chars_dis ) && preg_match( '/['. $preg .']/', $username ) && $list_chars ) {
					$valid_charts = true;
				}
			} // foreach
		}
		
		$_part = $this->chars_removed_from_allow_spc_cars();
		foreach ( $_part as $__part ) {
			if ( strpos( $username, $__part ) !== false ) {
				$invalid_chars_allow = true;
			}
		}
		
		// ++++++ restrict: "_",".",'-',"@"
		$dis_all_symbs = $this->options( 'all_symbs' );
		if ( $this->bb() ) {
			$_dis_all_symbs = true;
			$s_part = $dis_all_symbs ? array( "_",".",'-',"@" ) : array( "@" );
			} else {
			$_dis_all_symbs = $dis_all_symbs;
			$s_part = array( "_",".",'-',"@" );
		}
		
		if ( $_dis_all_symbs ) {
			$newParts = array_diff($s_part, $list_allow_spc_cars);
			foreach ( $newParts as $s__part ) 
			{
				if ( $this->_validate_username( $username ) && strpos( $username, $s__part ) !== false ) {
					$invalid_chars_allow = true;
				} 
			}
		}
		
		if ( $this->_validate_username( $username ) && $valid_charts == false && $invalid_chars_allow == false ) {
			return true;
		}
	}
	
	function benrueeg_mu_validate_username( $username ) {
		$matchCount = preg_match( $this->get_mu_lang__( $username ), $username, $match );
		$matchs = $matchCount > 0 ? $match[0] : '';
		if ( $username === $matchs ) {
			return true;
		}
	}
	
	function registration_errors_in_backend() {
		if ( $this->options( 'enable_err_backend' ) == 'enable' || ( $this->options( 'enable_err_backend' ) != 'enable' && ! $this->can_create_users_backend() && is_admin() ) || ! is_admin() ) {
			return true;
		}
	}

	function _sanitized_user_login_update( $sanitized_user_login ) {
		$glob_user_info = $this->glob_user_info;
		
		if ( $glob_user_info ) {
			return $glob_user_info;
		}
		
		return $sanitized_user_login;
	}
	
	function _sanitized_user_nicename_update( $sanitized_user_nicename ) {
		$glob_user_info = $this->glob_nicename_info;
		
		if ( $glob_user_info ) {
			return $glob_user_info;
		}
		
		return $sanitized_user_nicename;
	}
	
	function old_user_login_invalid_message( $old_user_login ) {
		$old_users = get_site_transient( 'benrueeg_old_user_login_invalid' );
		$info = $old_users ? $old_users : array();
		//if ( $old_users && isset( $old_users['user_login'] ) && ! in_array( $old_user_login, $old_users['user_login'] ) ) {
		$info['user_login'][] = $old_user_login;
		$info['user_login'] = array_unique( $info['user_login'] );
		//}
		$info['date'] = wp_date( "F j, Y g:i:s a" );
		
		return set_site_transient( 'benrueeg_old_user_login_invalid', $info, 6 * MONTH_IN_SECONDS );
	}

	function signup_username_exists( $username, $active = '' ) {
		global $wpdb;
		
		if ( 1 === $active ) {
			$atv = 'AND active = 1';
		} elseif ( 0 === $active ) {
			$atv = 'AND active = 0';
		} else {
			$atv = '';
		}
		
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->prefix()}signups WHERE user_login = %s $atv ", $username ) );
		if ( $signup ) {
			return $signup;
		}
	}
	
	function signup_email_exists( $email, $active = '' ) {
		global $wpdb, $pagenow;
		
		$_email = $email;

		if ( 'profile.php' === $pagenow ) {
			$c_email = get_user_meta( get_current_user_id(), '_new_email', true );
			if ( $c_email ) {
				$_email = $c_email['newemail'];
			}
		}
		
		if ( 1 === $active ) {
			$atv = 'AND active = 1';
		} elseif ( 0 === $active ) {
			$atv = 'AND active = 0';
		} else {
			$atv = '';
		}
		
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$this->prefix()}signups WHERE user_email = %s $atv ", $_email ) );
		if ( $signup ) {
			return $signup;
		}
	}
	
	function is_wp_signup_page() {
	    $signup_page = false;
	    if ( isset( $GLOBALS['pagenow'] ) && ( false !== strpos( $GLOBALS['pagenow'], apply_filters( 'benrueeg_rue_is_wp_signup_page_mu', 'wp-signup.php' ) ) ) ) {
			$signup_page = true;
		} elseif ( isset( $_SERVER['PHP_SELF'] ) && ( strpos( $_SERVER[ 'PHP_SELF' ], apply_filters( 'benrueeg_rue_is_wp_signup_page_mu', 'wp-signup.php' ) ) ) ) {
			$signup_page = true;
		}
		return $signup_page;
	}
	
	function is_wp_activate_page() {
	    $activate_page = false;
	    if ( isset( $GLOBALS['pagenow'] ) && ( false !== strpos( $GLOBALS['pagenow'], apply_filters( 'benrueeg_rue_is_wp_activate_page_mu', 'wp-activate.php' ) ) ) ) {
			$activate_page = true;
		} elseif ( isset( $_SERVER['PHP_SELF'] ) && ( false !== strpos( $_SERVER['PHP_SELF'], apply_filters( 'benrueeg_rue_is_wp_activate_page_mu', 'wp-activate.php' ) ) ) ) {
			$activate_page = true;
		}
		return $activate_page;
	}
	/*
	function _get_user_data_by( $field, $value ) {
		global $wpdb;

		// 'ID' is an alias of 'id'.
		if ( 'ID' === $field ) {
			$field = 'id';
		}

		if ( 'id' === $field ) {
			// Make sure the value is numeric to avoid casting objects, for example, to int 1.
			if ( ! is_numeric( $value ) ) {
				return false;
			}
			$value = (int) $value;
			if ( $value < 1 ) {
				return false;
			}
		} else {
			$value = trim( $value );
		}

		if ( ! $value ) {
			return false;
		}

		switch ( $field ) {
			case 'id':
				$user_id  = $value;
				$db_field = 'ID';
				break;
			case 'slug':
				$user_id  = wp_cache_get( $value, 'userslugs' );
				$db_field = 'user_nicename';
				break;
			case 'email':
				$user_id  = wp_cache_get( $value, 'useremail' );
				$db_field = 'user_email';
				break;
			case 'login':
				$value    = sanitize_user( $value );
				$user_id  = wp_cache_get( $value, 'userlogins' );
				$db_field = 'user_login';
				break;
			default:
				return false;
		}
		
		$user_status = '';
		
		if ( $field == 'email' ) {
			$signup = $this->signup_email_exists( $value, 0 );
		} elseif ( $field == 'login' ) {
			$signup = $this->signup_username_exists( $value, 0 );
		} else {
			$signup = '';
		}
		
		if ( $this->bb() && $signup ) {
			
			$user_status = 'AND user_status = 0';
		
		} elseif ( false !== $user_id ) {
		
			$user = wp_cache_get( $user_id, 'users' );
			if ( $user ) {
				return $user;
			}
		
		}
		
		$user = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->users WHERE $db_field = %s $user_status LIMIT 1",
				$value
			)
		);
		if ( ! $user ) {
			return false;
		}

		update_user_caches( $user );

		return $user;
	}
	
	function _username_exists( $username ) {
		$user = $this->_get_user_data_by( 'login', $username );
		if ( $user ) {
			$user_id = (int) $user->ID;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	
	function _email_exists( $email ) {
		$user = $this->_get_user_data_by( 'email', $email );
		if ( $user ) {
			$user_id = (int) $user->ID;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	*/
	
	function username_is_pendding_signup( $username ) {
	    global $wpdb;
		
		$user = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->users WHERE user_login = %s LIMIT 1",
				$username
			)
		);
		
		$user_status = false;
		if ( $user ) {
			$user_status = $user->user_status;
		}
		
		$_user = $user_status == '2' ? true : false;
		$cond_user = $this->bp() && ! $this->mu() && ( $_user == true || ! $user );
		
		if ( ( $cond_user || $this->mu() ) && $this->signup_username_exists( $username, 0 ) ) {
			$user_id = true;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	
	function email_is_pendding_signup( $email ) {
	    global $wpdb;
		
		$user = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $wpdb->users WHERE user_email = %s LIMIT 1",
				$email
			)
		);
		
		$user_status = false;
		if ( $user ) {
			$user_status = $user->user_status;
		}
		
		$_user = $user_status == '2' ? true : false;
		$cond_user = $this->bp() && ! $this->mu() && ( $_user == true || ! $user );
		
		if ( ( $cond_user || $this->mu() ) && $this->signup_email_exists( $email, 0 ) ) {
			$user_id = true;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	/*
	function inactive_signup_email( $email ) {
		if ( $this->bb() && ! $this->mu() && $this->signup_email_exists( $email, 0 ) ) {
			$user_id = true;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	
	function inactive_signup_username( $username ) {
		if ( $this->bb() && ! $this->mu() && $this->signup_username_exists( $username, 0 ) ) {
			$user_id = true;
		} else {
			$user_id = false;
		}

		return $user_id;
	}
	*/
	function nickname_exists( $value, $user_id = null ) {
		global $wpdb;
		
		//$value = preg_replace( '/\\\\/', '', $value . '/' ); // Supprimez backslash (\) et (/) du nom d'utilisateur lors de l'inscription si celui-ci en contient une, car cela provoque une erreur de base de données.
		//$value = preg_replace( '[/]', '', $value );
		$value = sanitize_user( $value, true ); // Supprimez backslash (\) et (/) du nom d'utilisateur lors de l'inscription si celui-ci en contient une, car cela provoque une erreur de base de données.
		
		$where = array(
		'meta_key = "nickname"',
		'meta_value = "' . $value . '"',
		);
		
		if ( $user_id ) {
			$where[] = 'user_id != ' . $user_id;
		}
		
		$sql = sprintf(
		'SELECT count(*) FROM %s WHERE %s',
		$wpdb->usermeta,
		implode( ' AND ', $where )
		);
		
		if ( $wpdb->get_var( $sql ) > 0 ) {
			return true;
		}
		
		return false;
	}
	
	function delete_signups_by_user_login( $username ) {
		global $wpdb;
	
		$signup_username = $this->signup_username_exists( $username );
		if ( $signup_username ) {
			$diff = time() - mysql2date( 'U', $signup_username->registered );
			if ( $this->signup_username_exists( $username, 1 ) && apply_filters( 'benrueeg_rue_delete_signup_item_activeted', true ) ) {
				$wpdb->delete( "{$this->prefix()}signups", array( 'user_login' => $username, 'active' => 1 ) );
			} elseif ( $diff > 2 * DAY_IN_SECONDS && $this->mu() ) {
				$wpdb->delete( "{$this->prefix()}signups", array( 'user_login' => $username ) );
			}
		}
	}
	
	function delete_signups_by_user_email( $email, $unset = '' ) {
		global $wpdb;
		
		$signup_email = $this->signup_email_exists( $email );
	    if ( $signup_email ) {
		    if ( ! empty( $unset ) ) {
				$unset;
			}
			$diff = time() - mysql2date( 'U', $signup_email->registered );
			if ( $this->signup_email_exists( $email, 1 ) && apply_filters( 'benrueeg_rue_delete_signup_item_activeted', true ) ) {
				$wpdb->delete( "{$this->prefix()}signups", array( 'user_email' => $email, 'active' => 1 ) );
			} elseif ( $diff > 2 * DAY_IN_SECONDS && $this->mu() ) {
				$wpdb->delete( "{$this->prefix()}signups", array( 'user_email' => $email ) );
			}
		}
	}
	
}