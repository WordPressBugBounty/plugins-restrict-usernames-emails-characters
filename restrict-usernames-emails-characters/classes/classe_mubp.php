<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class benrueeg_rue_plug_mu_bp extends benrueeg_rue_plug_chars {
	
	function benrueeg_error( $new_error, $code, $message, $fieldId = '' ) {
		
		if ( $this->bb() && ! is_admin() && ! ( $this->mu() && $this->is_wp_signup_page()) ) {
			$field_id = $fieldId ? $fieldId : bp_xprofile_nickname_field_id();
			buddypress()->signup->errors[ 'field_' . $field_id ] = sprintf(
			'<div class="bp-messages bp-feedback error">
			<span class="bp-icon" aria-hidden="true"></span>
			<p>%s</p>
			</div>',
			$message
			);
			} else {
			$new_error->add( $code, $message );
		}
		
	}
	
	function benrueeg_bp_signup_validate() {
		
		$bb = $this->bb();
		$bp = buddypress();
		
		$email_opt    = function_exists( 'bp_register_confirm_email' ) && true === bp_register_confirm_email() ? true : false;
		$password_opt = function_exists( 'bp_register_confirm_password' ) && true === bp_register_confirm_password() ? true : false;
		
		// Check that both password fields are filled in.
		if ( $bb && isset( $_POST['signup_password'] ) && empty( $_POST['signup_password'] ) ) {
			$bp->signup->errors['signup_password'] = $this->error_message('err_bb_password');
		}
		
		// if email opt enabled.
		if ( true === $email_opt ) {
			
			// Check that both password fields are filled in.
			if ( empty( $_POST['signup_email'] ) || empty( $_POST['signup_email_confirm'] ) ) {
				$bp->signup->errors['signup_email'] = $this->error_message('err_bb_email_twice');
			}
			
			// Check that the passwords match.
			if ( ( ! empty( $_POST['signup_email'] ) && ! empty( $_POST['signup_email_confirm'] ) ) && strcasecmp( $_POST['signup_email'], $_POST['signup_email_confirm'] ) !== 0 ) {
				$bp->signup->errors['signup_email'] = $this->error_message('err_bb_email_not_match');
			}
		}
		
		// if password opt enabled.
		if ( true === $password_opt ) {
			
			// Check that both password fields are filled in.
			if ( empty( $_POST['signup_password'] ) || empty( $_POST['signup_password_confirm'] ) ) {
				$bp->signup->errors['signup_password'] = $this->error_message('err_bb_password_twice');
			}
			
			// Check that the passwords match.
			if ( ( ! empty( $_POST['signup_password'] ) && ! empty( $_POST['signup_password_confirm'] ) ) && $_POST['signup_password'] != $_POST['signup_password_confirm'] ) {
				$bp->signup->errors['signup_password'] = $this->error_message('err_bb_password_not_match');
			}
		}
		
		if ( $this->bp_not_boss() ) {
			
			// Password strength check.
			$required_password_strength = bp_members_user_pass_required_strength();
			$current_password_strength  = null;
			if ( isset( $_POST['_password_strength_score'] ) ) {
				$current_password_strength = (int) $_POST['_password_strength_score'];
			}
			
			if ( $required_password_strength && ! is_null( $current_password_strength ) && $required_password_strength > $current_password_strength ) {
				$account_password = new WP_Error(
				'not_strong_enough_password',
				$this->error_message('err_only_bp_pass_not_strong')
				);
				} else {
				$signup_pass = '';
				if ( isset( $_POST['signup_password'] ) ) {
					$signup_pass = wp_unslash( $_POST['signup_password'] );
				}
				
				$signup_pass_confirm = '';
				if ( isset( $_POST['signup_password_confirm'] ) ) {
					$signup_pass_confirm = wp_unslash( $_POST['signup_password_confirm'] );
				}
				
				// Check the account password for problems.
				$account_password = bp_members_validate_user_password( $signup_pass, $signup_pass_confirm );
			}
			
			$password_error = $account_password->get_error_message();
			
			if ( $password_error ) {
				$bp->signup->errors['signup_password'] = $password_error;
			}
			
			if ( bp_signup_requires_privacy_policy_acceptance() && ! empty( $_POST['signup-privacy-policy-check'] ) && empty( $_POST['signup-privacy-policy-accept'] ) ) {
				$bp->signup->errors['signup_privacy_policy'] = $this->error_message('err_only_bp_privacy_policy');
			}
			
		}
		
		if ( bp_is_active( 'xprofile' ) && isset( $_POST['signup_profile_field_ids'] ) && ! empty( $_POST['signup_profile_field_ids'] ) ) {
			$new_error = new WP_Error();
			
			if ( $bb ) {
				
				$_field_id = bp_xprofile_nickname_field_id();
				$nickname_field = 'field_' . $_field_id;
				
				if ( xprofile_check_is_required_field( $_field_id ) && empty( $_POST[ $nickname_field ] ) ) {
					$this->benrueeg_error( $new_error, 'user_name', $this->error_message('err_mp_empty') );
				}
				
			}
			
			// Let's compact any profile field info into an array.
			$profile_field_ids = explode( ',', $_POST['signup_profile_field_ids'] );
			
			// Loop through the posted fields formatting any datebox values then validate the field.
			foreach ( (array) $profile_field_ids as $field_id ) {
				if ( $bb && $field_id == bp_xprofile_nickname_field_id() ) continue;
				bp_xprofile_maybe_format_datebox_post_data( $field_id );
				// Create errors for required fields without values.
				if ( xprofile_check_is_required_field( $field_id ) && empty( $_POST[ 'field_' . $field_id ] ) && ! bp_current_user_can( 'bp_moderate' ) ) {
					if ( $bb ) {
						$this->benrueeg_error( $new_error, 'user_name', $this->error_message('err_bb_required_field'), $field_id );
						} else {
						$bp->signup->errors['field_' . $field_id] = $this->error_message('err_bb_required_field');
					}
				}
			}
		}
	}
	
	function remove_bb_validate_nickname(){
		return remove_filter( 'xprofile_validate_field', 'bp_xprofile_validate_nickname_value' );
	}
	
	function remove_bb_validate_character_limit_value(){
		return remove_filter( 'xprofile_validate_field', 'bb_xprofile_validate_character_limit_value' );
	}
	
	function remove_bb_validate_restricted_email(){
		return remove_action( 'user_profile_update_errors', 'bb_validate_restricted_email_on_registration', PHP_INT_MAX, 3 );
	}
	
	function __wpmubp( $result ){
		global $wpdb;
		
		$bb_err = $mu_err = $err_backend = $space_s_e_m = false;
		
		if (! is_wp_error($result['errors'])) {
			return $result;
		}
		
		$result['user_name'] = preg_replace( '/\\\\{1,}/', '\\\\\\', $result['user_name'] ); // remove duplicate backslash in user name if the backslash is inputed in multisite registration form
		
		$user_name = ! $this->is_wp_signup_page() && $this->mubp() ? 'orig_username' : 'user_name';
		$username = $result[$user_name];
		
		$email = $result['user_email'];
		
		$allow = $this->options('p_num');
		/*
		$valid_name = $this->func_illegal_user_logins( false, $username );
		$valid_num = $this->func_limit_username_NUM( false, $username );
		$valid_space = $this->func_no_space_registration( false, $username );
		$valid_invalidname = $this->func_spc_cars_user_logins( false, $username );
		*/
		
		$this->func_validation( true, $username );
		
		//$valid_email = $this->func_limit_username_EMAIL( false, false, $email );
		$this->user__email( $email );
		
		$original_error = $result['errors'];
		$new_error = new WP_Error();
		$min_length = (int) $this->options('min_length');
		$max_length = (int) $this->options('max_length');
		
		$username_nk = $this->bb() && ( ! $this->mu() || ( $this->mu() && ! is_admin() && ! $this->is_wp_signup_page() ) ) ? __( 'Nickname' ) : __( 'Username' );
		
		$er_name = $this->error_message( 'err_mp_names_limit', $username_nk ); 
		$er_min = $this->error_message( 'err_mp_min_length', $username_nk );
		$filter_err_min_length = apply_filters( 'err_mp_min_length_mubp_BENrueeg_RUE', $er_min );
		$er_max = $this->error_message('err_mp_max_length');
		$filter_err_max_length = apply_filters( 'err_mp_max_length_mubp_BENrueeg_RUE', $er_max );
		$er_digits_less = $this->error_message( 'err_mp_digits_less', $username_nk );
		$er_space = $this->error_message( 'err_mp_spaces', $username_nk );	 
		$er_just_num = $this->error_message( 'err_mp_names_num', $username_nk );	 
		$er_illegal_name = $this->error_message( 'err_mp_spc_cars', $username_nk );
		$er_name_not_email = $this->error_message( 'err_mp_name_not_email' );
		$er_uppercase = $this->error_message( 'err_mp_uppercase', $username_nk );
		$er_start_end_space = $this->error_message( 'err_mp_start_end_space', $username_nk );
		$er_username_empty = $this->error_message( 'err_mp_empty', $username_nk );
		$er_exist_login = $this->error_message( 'err_mp_exist_login', $username_nk );
		$er_bb_required_field = $this->error_message( 'err_bb_required_field' );
		
		$er_empty_user_email = $this->error_message('err_mp_empty_user_email');
		$er_invalid_user_email = $this->error_message('err_mp_invalid_user_email');
		$er_exist_email = $this->error_message('err_mp_exist_user_email');
		$er_emails_limit = $this->error_message('err_mp_emails_limit');
		$err_signup_admin_login_exists = $this->error_message( 'err_mp_admin_signup_username_exists' );
		$err_signup_login_exists = $this->error_message( 'err_mu_signup_username_exists', $username_nk );
		$err_signup_email_exists = $this->error_message( 'err_mu_signup_email_exists' );
		$err_signup_admin_email_exists = $this->error_message( 'err_mp_admin_signup_email_exists' );
		$err_bp_signup_login_exists = $this->error_message( 'err_bp_signup_username_exists', $username_nk );
		$err_bp_signup_email_exists = $this->error_message( 'err_bp_signup_email_exists' );
		
		$pr = $this->error_message( $this->only_mu() ? 'err_mp_partial' : 'err_bp_partial', '', $username );
		
		/*
			// Has someone already signed up for this username?
			$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE user_login = %s", $username ) );
			if ( $signup instanceof stdClass ) {
			$registered_at = mysql2date( 'U', $signup->registered );
			$now           = time();
			$diff          = $now - $registered_at;
			// If registered more than two days ago, cancel registration and let this signup go through.
			if ( $diff <= 2 * DAY_IN_SECONDS )
			$new_error->add('user_name', apply_filters( 'username_reserved_filter_BENrueeg_RUE',__('That username is currently reserved but may be available in a couple of days.') ));
			}
		*/	
		/*
		$signups = $this->bp() ? BP_Signup::get( array(
		'user_login' => $username,
		) ) : false;
		
		$signup = isset( $signups['signups'] ) && ! empty( $signups['signups'][0] ) ? $signups['signups'][0] : false;
		*/
		
		if ( trim($username) == '' ) {
			$mu_err = $err_backend = true;
			$this->benrueeg_error( $new_error, 'user_name', $er_username_empty);
		} else {
		
			// delete signup_username
			$this->delete_signups_by_user_login( $username );
			// delete signup_username
			
			if ( $this->func_space_s_e_m( $username ) || ( $this->func_s( $username ) && ! $this->ben_username_empty( $username ) ) ) {
				$space_s_e_m = $err_backend = true;
				$this->benrueeg_error( $new_error, 'user_name', $er_start_end_space);
			} elseif ( ! $this->benrueeg_validate_username( $username ) ) {
				$bb_err = $mu_err = $err_backend = true;
				$this->benrueeg_error( $new_error,  'user_name', $er_illegal_name );	
			} else {
		
			// signup_username
				if ( $this->signup_username_exists( $username ) ) {
					$this->_unset( $original_error, 'user_name' );
					$this->_unset( $original_error, 'user_email' );
				}
				
				if ( $this->username_is_pendding_signup( $username ) ) {
					$this->_unset( $original_error, 'user_name' );
					if ( $this->mu() ) {
						$this->benrueeg_error( $new_error, 'user_name', is_admin() && current_user_can( 'create_users' ) ? $err_signup_admin_login_exists : $err_signup_login_exists);
					} else {
						$bb_err = $err_backend = true;
						$this->benrueeg_error( $new_error, 'user_name', $err_bp_signup_login_exists);
					}
				}
			// signup_username	
				
			}
			
			if ( ! $mu_err && ( username_exists( $username ) || ( $this->bb() && $this->nickname_exists( $username ) ) ) ) {
				$bb_err = $err_backend = true;
				$this->_unset( $original_error, 'user_name' );
				
				if ( $this->username_is_pendding_signup( $username ) == false ) {
					$this->benrueeg_error( $new_error, 'user_name', $er_exist_login);
				}
			}
			
			
			if ( $this->benrueeg_validate_username( $username ) && ! preg_match( '/^([A-Za-z0-9-_\.]+)$/', $username ) ) {
				$bb_err = true;
			}
			
		}
		
		if ( ! $err_backend ) {
			
			if ( $this->valid_partial )
			$this->benrueeg_error( $new_error,  'user_name', $pr );
			
			if ( $this->name_not__email && ! $this->mu() )
			$this->benrueeg_error( $new_error, 'user_name', $er_name_not_email);
			
			if ( $this->invalid_names )
			$this->benrueeg_error( $new_error, 'user_name', $er_name);
			
			if ( $this->bb() && ! empty($min_length) && $min_length <= 3 )
			$bb_err = true;
			
			if ( $this->length_min )
			$this->benrueeg_error( $new_error, 'user_name', $filter_err_min_length );
			
			$nickname_max_length = apply_filters( 'xprofile_nickname_max_length', 32 );
			if ( $this->bb() && empty( $max_length ) && mb_strlen( $username ) > $nickname_max_length ) {
				$bb_err = true;
				$this->benrueeg_error( $new_error, 'user_name', str_replace( array( '%uname%', '%max%' ), array( $username_nk, $nickname_max_length ), $filter_err_max_length ) );
			} elseif ( $this->length_max ) {
				$this->benrueeg_error( $new_error, 'user_name', str_replace( array( '%uname%', '%max%' ), array( $username_nk, $max_length ), $filter_err_max_length ) );
			}
			
			if ( $this->valid_num_less && ! preg_match( '/^\+?\d+$/', $username ) )
			$this->benrueeg_error( $new_error, 'user_name', $er_digits_less);
			
			if ( preg_match('/ /', $username) ) {
				$this->benrueeg_error( $new_error, 'user_name', $er_space);
			} 
			
			if ( $this->uppercase_names )
			$this->benrueeg_error( $new_error, 'user_name', $er_uppercase);
			
		}
		
		$match_ = array();
		preg_match( '/[0-9]*/', $username, $match_ );
		if ( $match_[0] == $username && ! $this->options('p_num') && $this->mu() ||
		preg_match( '/^\+?\d+$/', $username ) && ! $this->options('p_num') && $this->mu() ) {
			$new_error->add('user_name', $er_just_num);
		} 
		
		// emails error
		if ( (trim($email) == '' && ! isset($_POST['email'])) || (is_admin() && isset($_POST['email']) && trim($_POST['email']) == '') ) {
			$mu_err = true;
			$this->_unset( $original_error, 'user_email' );
			$new_error->add('user_email', $er_empty_user_email);
		} 
		
		if ( ( ! is_email($email) && ! $mu_err  ) || (is_admin() && isset($_POST['email']) && ! is_email($_POST['email'])) ) {
			$mu_err = true;
			$this->_unset( $original_error, 'user_email' );
			$new_error->add('user_email', $er_invalid_user_email);
		}
		
	// signup_email
		$this->delete_signups_by_user_email( $email, $this->_unset( $original_error, 'user_email' ) );
		
		if ( $this->email_is_pendding_signup( $email ) ) {
			$this->_unset( $original_error, 'user_email' );
			if ( $this->mu() ) {
				$new_error->add('user_email', is_admin() && current_user_can( 'create_users' ) ? $err_signup_admin_email_exists : $err_signup_email_exists);
			} else {
				$new_error->add('user_email', $err_bp_signup_email_exists);
			}
		} else {
	// signup_email	
		
			if ( $this->exist__user_email ) {
				$mu_err = true;
				$this->_unset( $original_error, 'user_email' );
				$new_error->add('user_email', $er_exist_email);
			}
		
		}
		
		if ( $this->registration_errors_in_backend() && ( $this->restricted_emails || $this->restricted_domain_emails ) )
		$new_error->add('user_email', $er_emails_limit);
		// emails error
		
		if ($this->bp()) {
			
			$match_ = array();
			preg_match( '/[0-9]*/', $username, $match_ );
			if ( $match_[0] == $username && $this->options('p_num') && ! $this->mubp() ||
			preg_match( '/^\+?\d+$/', $username ) && $this->options('p_num') && ! $this->mubp() ) {
				$this->benrueeg_error( $new_error, 'user_name', $er_just_num);
				} else if ( $match_[0] == $username && !$this->options('p_num') ) {
				$this->_unset( $original_error, 'user_name' );
			}
			
			if ( false !== strpos( $username, '_' ) && !$this->mubp() && !$this->bb() )
			$this->_unset( $original_error, 'user_name' );
			
		}
		
		if ($this->bb() && $bb_err)
		$this->remove_bb_validate_nickname();
		
		$pattern = $this->get_mu_lang__($username);
		
		preg_match( $pattern, $username, $match );
		
		$matchCount = preg_match( $pattern, $username, $match );
		$match__s = $matchCount > 0 ? $match[0] : '';
		
		foreach( $original_error->get_error_codes() as $code ){
			$get_messages = $result['errors']->get_error_messages($code);
			foreach(  $get_messages as $message ){
				if ( $code != 'user_email' && $this->mu() && ! preg_match( '/^\+?\d+$/', $username ) ) {
					
					if ( $username != $match__s ) {
						
						if ( $mu_err == false && $space_s_e_m == false ) {
							$ok_chars = $er_illegal_name;
							$new_error->add('user_name', $ok_chars);
						}
						
						} elseif ( preg_match( '/[^a-z0-9]/', $username ) || strlen( $username ) < 4 || strlen( $username ) > 60 ) {
						$this->_unset( $original_error, 'user_name' );
						} else {
						$new_error->add($code, $message);
					}
					} else if ( $code == 'user_email' && ! $mu_err ) {
					$new_error->add($code, $message);	
				} 
				
			}
		}
		
		$result['errors'] = $new_error;
		
		return $result;
	}
	
	function benrueeg_bp_xprofile_validate_nickname_value( $retval, $field_id, $value, $user_id ) {
		global $wpdb;
		
		if ( ! $this->bb() ) return $retval;
		
		if ( $field_id != bp_xprofile_nickname_field_id() ) {
			return $retval;
		}
		
		if ( $retval ) {
			return $retval;
		}
		
		$er_username_empty = $this->error_message( 'err_mp_empty' );
		$er_exist_login = $this->error_message( 'err_mp_exist_login' );
		$er_exist_login_filter = apply_filters( 'benrueeg_error_msg_exists_nickname', $er_exist_login );
		$min_length = (int) $this->options('min_length');
		$max_length = (int) $this->options('max_length');
		$er_max = $this->error_message('err_mp_max_length');
		$filter_err_max_length = apply_filters( 'err_mp_max_length_mubp_BENrueeg_RUE', $er_max );
		$er_min = $this->error_message( 'err_mp_min_length', __( 'Nickname' ) );
		$filter_err_min_length = apply_filters( 'err_mp_min_length_mubp_BENrueeg_RUE', $er_min );
		$err_signup_admin_login_exists = $this->error_message( 'err_mp_admin_signup_username_exists' );
		$err_signup_login_exists = $this->error_message( 'err_mu_signup_username_exists', __( 'Nickname' ) );
		$err_bp_signup_login_exists = $this->error_message( 'err_bp_signup_username_exists', __( 'Nickname' ) );
		
		$bb_err = false;
		$value      = strtolower( $value );
		$field_name = xprofile_get_field( $field_id )->name;
		$old_nickname = get_user_meta( $user_id, 'nickname', true );
		
		if ( '' === trim($value) ) {
			return $er_username_empty;
		}
		
		if ( $old_nickname == $value ) {
			$bb_err = true;
		} elseif ( $this->benrueeg_validate_username( $value ) && ! preg_match( '/^([A-Za-z0-9-_\.]+)$/', $value ) ) {
			$bb_err = true;
		}
		
		if ( $bb_err ) {
			$this->remove_bb_validate_nickname();
		}
		
		$this->delete_signups_by_user_login( $value );
		
		if ( ! $this->benrueeg_validate_username( $value ) && $bb_err == false ) {
			return $this->error_message( 'err_mp_spc_cars' );
		} elseif ( preg_match('/ /', $value) && $old_nickname != $value ) {
			return $this->error_message( 'err_mp_spaces' );
		}
		
		if ( $this->username_is_pendding_signup( $value ) ) {
			if ( $this->mu() ) {
				return is_admin() && current_user_can( 'create_users' ) ? $err_signup_admin_login_exists : $err_signup_login_exists;
			} else {
				return $err_bp_signup_login_exists;
			}
		} elseif ( $this->nickname_exists( $value, $user_id ) ) {
			return $er_exist_login_filter;
		}
		
		if ( ! empty( $min_length ) && $min_length <= 3 ) {
			$this->remove_bb_validate_nickname();
		}
			
		$length_space = $this->options('length_space');
		$mbstrlen = preg_match( '/^\+\d+$/', $value ) ? mb_strlen( $value ) - 1 : mb_strlen( $value );
		$strlen = $mbstrlen - substr_count( $value, ' ' );
		
		if ( $old_nickname != $value && $strlen < $min_length && ! empty( $min_length ) && ! empty( $value ) ) {
			return $filter_err_min_length;
		}
		
		$v_max_length = $strlen > $max_length || $strlen > 60;
		
		$nickname_max_length = apply_filters( 'xprofile_nickname_max_length', 32 );
		if ( $old_nickname != $value && empty( $max_length ) && mb_strlen( $value ) > $nickname_max_length ) {
			return str_replace( array( '%uname%', '%max%' ), array( __( 'Nickname' ), $nickname_max_length ), $filter_err_max_length );
		} elseif ( $old_nickname != $value && $v_max_length && ! empty( $max_length ) && ! empty( $value ) ) {
			return str_replace( array( '%uname%', '%max%' ), array( __( 'Nickname' ), $max_length ), $filter_err_max_length );
		}
		
		$old_user_info = get_userdata($user_id);
		
		if ( $old_user_info && ! $this->benrueeg_validate_username( $old_user_info->user_login ) ) {
			if ( $this->options('varchar') == 'enabled' ) {
				$this->glob_user_info = $old_user_info->user_login;
				remove_all_filters ('pre_user_login');
				add_filter( 'pre_user_login', array( $this, '_sanitized_user_login_update' ) );
			} else {
				
				if ( apply_filters( 'benrueeg_old_user_login_invalid_up_msg', true ) ) {
					$this->old_user_login_invalid_message( $old_user_info->user_login );
				}
				
			}
		}
		
		return $retval;
	}
	
	function benrueeg_bb_xprofile_validate_character_limit_value( $retval, $field_id, $value ) {
		
		if ( ! $this->bb() ) return $retval;
		
		if ( ! in_array( (int) $field_id, array( bp_xprofile_firstname_field_id(), bp_xprofile_lastname_field_id() ), true ) ) {
			return $retval;
		}
		
		$value = strtolower( $value );
		
		if ( function_exists( 'normalizer_is_normalized' ) && function_exists( 'normalizer_normalize' )	) {
			try {
				// Ensures that the combined characters are treated as a single character.
				if ( ! normalizer_is_normalized( $value ) ) {
					$value = normalizer_normalize( $value );
				}
				} catch ( Exception $e ) {
				// Ignore the exception, continue execution.
			}
		}
		
		$max_length = (int) $this->options('max_length');
		$_err_max_length = $this->error_message( 'err_mp_max_length' );
		
		$field_name = xprofile_get_field( $field_id )->name;
		
		// Must be shorter than 32 characters.
		$field_length = (int) apply_filters( 'bb_xprofile_field_character_max_length', 32 );
		$value_length = function_exists( 'mb_strlen' ) ? mb_strlen( $value ) : strlen( $value );
		$err_character_limit_value = '';
		
		if ( empty( $max_length ) && $value_length > $field_length ) {
			$err_character_limit_value = str_replace( array( '%uname%', '%max%' ), array( __( $field_name ), $field_length ), $_err_max_length );
		} elseif ( ! empty( $max_length ) && $value_length > $max_length ) {
			$err_character_limit_value = str_replace( array( '%uname%', '%max%' ), array( __( $field_name ), $max_length ), $_err_max_length );
		}
		
		if ( ! empty( $err_character_limit_value ) ) {
			$this->remove_bb_validate_character_limit_value();
			$retval = $err_character_limit_value;
		}
		
		return $retval;
	}
	
	function _signup_extra_fields() {
		if ( trim( $this->options('txt_form') ) != '' ) return;
		echo '<style type="text/css">#wp-signup-username-description { display: none; }</style>';
	}
	
	function _bp_not_boss_settings_action_general() {
	    global $wpdb;
		
		if ( ! $this->bp_not_boss() ) {
			return;
		}
			
		if ( ! bp_is_post_request() ) {
			return;
		}

		// Bail if no submit action.
		if ( ! isset( $_POST['submit'] ) ) {
			return;
		}

		// Bail if not in settings.
		if ( ! bp_is_settings_component() || ! bp_is_current_action( 'general' ) ) {
			return;
		}

		// 404 if there are any additional action variables attached
		if ( bp_action_variables() ) {
			bp_do_404();
			return;
		}

		// Define local defaults
		$bp            = buddypress();           // The instance
		$email_error   = false;                  // invalid|blocked|taken|empty|nochange
		$feedback_type = 'error';                // success|error
		$feedback      = array();                // array of strings for feedback.
		$user_id       = bp_displayed_user_id(); // The ID of the user being displayed.
		$path_chunks   = array( bp_get_settings_slug() );

		// Nonce check.
		check_admin_referer( 'bp_settings_general' );

		// Validate the user again for the current password when making a big change.
		if ( ( is_super_admin() ) || ( ! empty( $_POST['pwd'] ) && wp_check_password( $_POST['pwd'], $bp->displayed_user->userdata->user_pass, $user_id ) ) ) {

			/* Email Change Attempt ******************************************/

			if ( ! empty( $_POST['email'] ) ) {

				// What is missing from the profile page vs signup -
				// let's double check the goodies.
				$user_email     = sanitize_email( esc_html( trim( $_POST['email'] ) ) );
				$old_user_email = $bp->displayed_user->userdata->user_email;
				$this->user__email( $user_email );

				// User is changing email address.
				if ( $old_user_email !== $user_email ) {
					// Run some tests on the email address.
					$email_checks = bp_core_validate_email_address( $user_email );
					$_signup_email = false;
					
					$this->delete_signups_by_user_email( $user_email );
					
					if ( $this->signup_email_exists( $user_email, 0 ) ) {
						$email_error = 'benrueeg_signup_email_exists';
						$_signup_email = true;
					}
					
					if ( $this->restricted_emails || $this->restricted_domain_emails ) {
						$email_error = 'benrueeg_restricted_email';
					}

					if ( true !== $email_checks ) {
						if ( isset( $email_checks['invalid'] ) ) {
							$email_error = 'invalid';
						}

						if ( isset( $email_checks['in_use'] ) && ! $_signup_email ) {
							$email_error = 'taken';
						}
					}

				// No change.
				} else {
					$email_error = false;
				}

			// Email address cannot be empty.
			} else {
				$email_error = 'empty';
			}

		}
		
		// Email feedback.
		switch ( $email_error ) {
			case 'benrueeg_signup_email_exists':
				$feedback['email_invalid'] = $this->error_message( 'err_bp_signup_email_exists' );
				break;
			case 'benrueeg_restricted_email':
				$feedback['email_invalid'] = $this->error_message('err_mp_emails_limit');
				break;
			case 'invalid':
				$feedback['email_invalid'] = $this->error_message('err_mp_invalid_user_email');
				break;
			case 'taken':
				$feedback['email_taken'] = $this->error_message('err_mp_exist_user_email');
				break;
			case 'empty':
				$feedback['email_empty'] = $this->error_message('err_mp_empty_user_email');
				break;
			case false:
				// No change.
				break;
		}

		if ( ! empty( $email_error ) ) {

			// Set the URL to redirect the user to.
			$path_chunks[] = 'general';
			$redirect_to   = bp_displayed_user_url( bp_members_get_path_chunks( $path_chunks ) );

			// Set the feedback.
			bp_core_add_message( implode( "\n", $feedback ), $feedback_type );

			// Redirect to prevent issues with browser back button.
			bp_core_redirect( $redirect_to );
		
		}
	}
	
	function _bb_settings_action_general() {
	    global $wpdb;
		
		if ( ! $this->bb() )  {
			return;
		}
		
		if ( ! bp_is_post_request() ) {
			return;
		}

		// Bail if no submit action.
		if ( ! isset( $_POST['submit'] ) ) {
			return;
		}

		// Bail if not in settings.
		if ( ! bp_is_settings_component() || ! bp_is_current_action( 'general' ) ) {
			return;
		}

		// 404 if there are any additional action variables attached
		if ( bp_action_variables() ) {
			bp_do_404();
			return;
		}

		// Define local defaults.
		$bp            = buddypress(); // The instance.
		$email_error   = '';           // Email error code: invalid|blocked|taken|empty|nochange.
		$feedback_type = 'error';      // success|error.
		$feedback      = array();      // array of strings for feedback.

		// Nonce check.
		check_admin_referer( 'bp_settings_general' );

		// Validate the user again for the current password when making a big change.
		if ( ( is_super_admin() ) || ( ! empty( $_POST['pwd'] ) && wp_check_password( $_POST['pwd'], $bp->displayed_user->userdata->user_pass, bp_displayed_user_id() ) ) ) {

			/* Email Change Attempt ******************************************/

			if ( ! empty( $_POST['email'] ) ) {

				// What is missing from the profile page vs signup -
				// let's double check the goodies.
				$user_email     = sanitize_email( esc_html( trim( $_POST['email'] ) ) );
				$old_user_email = $bp->displayed_user->userdata->user_email;
				$this->user__email( $user_email );

				// User is changing email address.
				if ( $old_user_email !== $user_email ) {

					// Run some tests on the email address.
					$email_checks = bp_core_validate_email_address( $user_email );
					$_signup_email = false;

                    $this->delete_signups_by_user_email( $user_email );
					
					if ( $this->signup_email_exists( $user_email, 0 ) ) {
						$email_error = 'benrueeg_signup_email_exists';
						$_signup_email = true;
					}
					
					if ( $this->restricted_emails || $this->restricted_domain_emails ) {
						$email_error = 'benrueeg_restricted_email';
					}

					if ( true !== $email_checks ) {
					
					
						if ( isset( $email_checks['invalid'] ) ) {
							$email_error = 'invalid';
						}

						if ( isset( $email_checks['in_use'] )  && ! $_signup_email ) {
							$email_error = 'taken';
						}
					}

					// No change.
				} else {
					$email_error = '';
				}

				// Email address cannot be empty.
			} else {
				$email_error = 'empty';
			}

			// Clear cached data, so that the changed settings take effect
			// on the current page load.
			clean_user_cache( bp_displayed_user_id() );
		}

		// Email feedback.
		switch ( $email_error ) {
			case 'benrueeg_signup_email_exists':
				$feedback['email_invalid'] = $this->error_message( 'err_bp_signup_email_exists' );
				break;
			case 'benrueeg_restricted_email':
				$feedback['email_invalid'] = $this->error_message('err_mp_emails_limit');
				break;
			case 'invalid':
				$feedback['email_invalid'] = $this->error_message('err_mp_invalid_user_email');
				break;
			case 'taken':
				$feedback['email_taken'] = $this->error_message('err_mp_exist_user_email');
				break;
			case 'empty':
				$feedback['email_empty'] = $this->error_message('err_mp_empty_user_email');
				break;
			case false:
				// No change.
				break;
		}
		
		if ( ! empty( $email_error ) ) {
			// Set the feedback.
			bp_core_add_message( implode( "\n", $feedback ), $feedback_type );
			// Redirect to prevent issues with browser back button.
			bp_core_redirect( trailingslashit( bp_displayed_user_domain() . bp_get_settings_slug() . '/general' ) );
		
		}
	}
	
	/**
		* if the user_login to activated is invalid (the language changed after registration).
	*/
	function get_user_login_from_activation_key( $activation_key ) {
		
		$signups = BP_Signup::get( array(
		'activation_key' => $activation_key,
		) );
		
		if ( empty( $signups['signups'] ) ) {
			return false;
		}
		
		$signup = $signups['signups'][0];
		
		if ( $signup ) {
			$user_login = $signup->user_login; 
			return $user_login;
		}
		
		return false; // Return false if no user is found with that key
	}
	
	function _bp_members_action_activate_account() {
		
		if ( ! bp_is_current_component( 'activate' ) ) {
			return;
		}
		
		if ( is_user_logged_in() ) {
			return;
		}
		
		if ( ! empty( $_POST['key'] ) ) {
			$key = wp_unslash( $_POST['key'] );
			// Backward compatibility with templates using `method="get"` in their activation forms.
			} elseif ( ! empty( $_GET['key'] ) ) {
			$key = wp_unslash( $_GET['key'] );
		}
		
		if ( empty( $key ) ) {
			return;
		}
		
		$redirect = bp_get_activation_page();
		$user_login = $this->get_user_login_from_activation_key( $key );
		
		if ( $user_login && ! $this->benrueeg_validate_username( $user_login ) ) {
			remove_action( 'bp_actions', 'bp_members_action_activate_account' );
			bp_core_redirect( add_query_arg( 'benrueeg_activated', '0', $redirect ) );				
		}
		
	}
	
	function _username_exists_bp_before_activate( $user_id ) {
		return true;
	}
	
	function _bp_core_signup_before_activate( $signup_ids ) {
		
		$to_activate = BP_Signup::get(
		array(
		'include' => $signup_ids,
		)
		);
		
		if ( ! $signups = $to_activate['signups'] ) {
			return false;
		}
		
		$result = array();
		
		foreach ( $signups as $signup ) {
			
			if ( ! $this->benrueeg_validate_username( $signup->user_login ) ) {
				add_filter('username_exists', array($this, '_username_exists_bp_before_activate'), 999, 1);
				$result[] = $signup->signup_id;
			}
			
		}
		
		if ( $result ) {
			if ( is_network_admin() ) {
				$base_url = network_admin_url( 'users.php' );
				} else {
				$base_url = bp_get_admin_url( 'users.php' );
			}
			
			wp_redirect( add_query_arg( 'benrueeg_notactivated', implode( ',' ,  $result), $base_url . '?page=bp-signups' ) );
			exit();
		}
		
	}
	
	function page_bp_signups_message() {
		global $wpdb;
		if ( isset( $_REQUEST['benrueeg_notactivated'] ) ) {
			$arr = explode( ",", $_REQUEST['benrueeg_notactivated'] );
			$ids = implode( ',', array_fill( 0, count( $arr ), '%d' ) );
			$users = $wpdb->get_col( $wpdb->prepare( "SELECT user_login FROM {$wpdb->prefix}signups WHERE signup_id IN ($ids) ", $arr ) );
			if ( $users ) {
				$invalid_users = array_map( function( $value ) { return '<span style="color:red;">' . $value . '</span>'; }, $users );
				printf( '<div class="notice notice-error is-dismissible"><p>%1$s: %2$s</p></div>', $this->error_message( 'err_mp_admin_activation_chrs' ), implode( ', ', $invalid_users ) );
		    }
		}
	}
	
	function benrueeg_notactivated_frontend_message() {
		if ( isset( $_GET['benrueeg_activated'] ) ) {
			printf( '<div class="%1$s"><p>%2$s</p></div>', 'benrueeg-notactiv-front-msg', $this->error_message( 'err_mp_activation_chrs' ) );
		}
	}
	
	// mu
	function sanitized_login_mu_activate_signup( $sanitized_user_login ) {
	    global $wpdb;
		
		if ( ! $this->is_wp_activate_page() ) return $sanitized_user_login;
		
		$key    = '';
		
		if ( isset( $_GET['key'] ) && isset( $_POST['key'] ) && $_GET['key'] !== $_POST['key'] ) {
			return $sanitized_user_login;
			} elseif ( ! empty( $_GET['key'] ) ) {
			$key = sanitize_text_field( $_GET['key'] );
			} elseif ( ! empty( $_POST['key'] ) ) {
			$key = sanitize_text_field( $_POST['key'] );
		}
		
		if ( $key ) {
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key ) );
		
			if ( ! empty( $signup ) && ! $this->benrueeg_validate_username( $signup->user_login ) ) {
				return '';
			}
		}
		
		return $sanitized_user_login;
	}
	
	function _wpmu_activate_signup(
	#[\SensitiveParameter]
	$key
	) {
		global $wpdb;
		
		$signup = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM $wpdb->signups WHERE activation_key = %s", $key ) );
		
		if (  ! $this->benrueeg_validate_username( $signup->user_login ) ) {
			return new WP_Error( 'benrueeg_create_user', $this->error_message( 'err_mp_activation_chrs' ), $signup );
		}
	}
	
	function _get_header_activation( /*$name */) {
		/*
		if ( 'wp-activate' !== $name ) {
			return;
		}
		*/
        if ( ! $this->is_wp_activate_page() ) return;
		
		$key    = '';
		$result = null;
		
		if ( isset( $_GET['key'] ) && isset( $_POST['key'] ) && $_GET['key'] !== $_POST['key'] ) {
			wp_die( __( 'A key value mismatch has been detected. Please follow the link provided in your activation email.' ), __( 'An error occurred during the activation' ), 400 );
			} elseif ( ! empty( $_GET['key'] ) ) {
			$key = sanitize_text_field( $_GET['key'] );
			} elseif ( ! empty( $_POST['key'] ) ) {
			$key = sanitize_text_field( $_POST['key'] );
		}
	
		if ( $key ) {
			$result = $this->_wpmu_activate_signup( $key );
		}
		
		if ( null === $result || ( is_wp_error( $result ) && ( 'benrueeg_create_user' !== $result->get_error_code() ) )  ) {
			return;
		}
		
		nocache_headers();
		
		// Fix for page title.
		global $wp_query;
		$wp_query->is_404 = false;
		
		remove_all_actions( 'get_header' );
		get_header( 'wp-activate' );
		
		$blog_details = get_site();
		?>
		<div id="signup-content" class="widecolumn">
			<div class="wp-activate-container">
				<h2><?php _e( 'An error occurred during the activation' ); ?></h2>
				<?php if ( is_wp_error( $result ) ) : ?>
				<p><?php echo esc_html( $result->get_error_message() ); ?></p>
				<?php endif; ?>
				<?php
				?>
			</div>
		</div>
		<?php
		get_footer( 'wp-activate' );
		exit;
	}
	// mu
	/**
		* if the user_login to activated is invalid (the language changed after registration).
	*/
	
}