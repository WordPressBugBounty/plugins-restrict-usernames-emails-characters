<?php
	
	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_mu_bp' ) ) :
	
	class ben_plug_restrict_usernames_emails_characters_mu_bp extends ben_plug_restrict_usernames_emails_characters_CHARS {
		
		public function __construct() {
			parent::__construct();
		}
		
		function benrueeg_error( $new_error ,$code, $message ) {
			
			if ($this->bb()) {
				$bp = $this->bp() ? buddypress() : '';
				$field_id = function_exists('bp_xprofile_nickname_field_id') ? bp_xprofile_nickname_field_id() : '';
				$bp->signup->errors[ 'field_' . $field_id ] = sprintf(
					'<div class="bp-messages bp-feedback error">
					<span class="bp-icon" aria-hidden="true"></span>
					<p>%s</p>
					</div>',
					$message
				);
			} else {
			    $new_error->add($code, $message);
			}
		
		}
		
		function benrueeg_bp_signup_validate() {
			
			if (function_exists('bp_xprofile_nickname_field_id') && function_exists('xprofile_check_is_required_field')) {
				$field_id = bp_xprofile_nickname_field_id();
				$nickname_field = 'field_' . $field_id;
                if ( xprofile_check_is_required_field( $field_id ) && empty( $_POST[ $nickname_field ] ) ) {
                    $er_username_empty = $this->options_Tw('err_mp_empty') != '' && __( 'Please enter a username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_empty'),'restrict-usernames-emails-characters') : __( 'Please enter a username.','restrict-usernames-emails-characters' );	 
					$bp = buddypress();
				    $bp->signup->errors[ $nickname_field ] = sprintf(
					    '<div class="bp-messages bp-feedback error">
					    <span class="bp-icon" aria-hidden="true"></span>
					    <p>%s</p>
					    </div>',
					    $er_username_empty
				    );
                }
            }
		}
		
		function nickname_exists( $value, $user_id = null ) {
	    global $wpdb;

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
		
		function remove_bb_validate_nickname(){
			return remove_filter( 'xprofile_validate_field', 'bp_xprofile_validate_nickname_value' );
		}
		
		function wpmubp__ben( $result ){
		    global $wpdb;
			
			$bb_err = false;
			$lang = $this->options('lang');
			
			if (! is_wp_error($result['errors'])) {
				return $result;
			}
			
		    $namelogin = $this->options('namelogin'); // filter user_name field in registration form
			$orig_username = $namelogin == '' ? 'orig_username' : (isset($result[$namelogin]) ? $namelogin : 'orig_username');
			$user_name = $namelogin == '' ? 'user_name' : (isset($result[$namelogin]) ? $namelogin : 'user_name');
			$__username = !$this->is__signup() && $this->mubp() ? $orig_username : $user_name;
			$username = $result[$__username];
			
		    $nameemail = $this->options('nameemail'); // filter user_email field in registration form
			$useremail = $nameemail == '' ? 'user_email' : (isset($result[$useremail]) ? $useremail : 'user_email');
			$email = $result[$useremail];
			
			$allow = $this->options('p_num');
			/*
				$valid_name = $this->func_illegal_user_logins( false, $username );
				$valid_num = $this->func_limit_username_NUM( false, $username );
				$valid_space = $this->func_no_space_registration( false, $username );
				$valid_invalidname = $this->func_spc_cars_user_logins( false, $username );
			*/
			
			$this->func_validation( false, $username );
			
			//$valid_email = $this->func_limit_username_EMAIL( false, false, $email );
			$this->user__email( $email );
			
			$original_error = $result['errors'];
			$new_error = new WP_Error();
			//$names_limit_partial = $this->opts['option']['names_limit_partial'];
			$min_length = $this->options('min_length');
			$max_length = $this->options('max_length');
			
			$er_name = $this->options_Tw('err_mp_names_limit') != '' && __( 'This username is not allowed, choose another please.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_names_limit'),'restrict-usernames-emails-characters') : __( 'This username is not allowed, choose another please.','restrict-usernames-emails-characters' );	 
			$er_min = $this->options_Tw('err_mp_min_length') != '' && __( "Username must be at least %min% characters.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_mp_min_length'),'restrict-usernames-emails-characters') : __( "Username must be at least %min% characters.",'restrict-usernames-emails-characters' ) ;
			$filter_err_min_length = apply_filters( 'err_mp_min_length_mubp_BENrueeg_RUE',$er_min );
			$er_max = $this->options_Tw('err_mp_max_length') != '' && __( "Username may not be longer than %max% characters.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_mp_max_length'),'restrict-usernames-emails-characters') : __( "Username may not be longer than %max% characters.",'restrict-usernames-emails-characters' ) ;
			$filter_err_max_length = apply_filters( 'err_mp_max_length_mubp_BENrueeg_RUE',$er_max );
			$er_digits_less = $this->options_Tw('err_mp_digits_less') != '' && __( "The digits must be less than the characters in username.",'restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_digits_less'),'restrict-usernames-emails-characters') : __( "The digits must be less than the characters in username.",'restrict-usernames-emails-characters' );	 
			$er_space = $this->options_Tw('err_mp_spaces') != '' && __( "It's not allowed to use spaces in username.",'restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_spaces'),'restrict-usernames-emails-characters') : __( "It's not allowed to use spaces in username.",'restrict-usernames-emails-characters' );	 
			$er_just_num = $this->options_Tw('err_mp_names_num') != '' && __( "You can't register with just numbers.",'restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_names_num'),'restrict-usernames-emails-characters') : __( "You can't register with just numbers.",'restrict-usernames-emails-characters' );	 
			$er_illegal_name = $this->options_Tw('err_mp_spc_cars') != '' && __( 'This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_spc_cars') ,'restrict-usernames-emails-characters' ) : __( 'This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters' );	 
			$er_name_not_email = $this->options_Tw('err_mp_name_not_email') != '' && __( 'Do not allow usernames that are email addresses.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_name_not_email'),'restrict-usernames-emails-characters') : __( 'Do not allow usernames that are email addresses.','restrict-usernames-emails-characters' );	 
			$er_uppercase = $this->options_Tw('err_mp_uppercase') != '' && __( 'No uppercase (A-Z) in username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_uppercase'),'restrict-usernames-emails-characters') : __( 'No uppercase (A-Z) in username.','restrict-usernames-emails-characters' );	 
			$er_start_end_space = $this->options_Tw('err_mp_start_end_space') != '' && __( 'is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_start_end_space'),'restrict-usernames-emails-characters') : __( 'is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.','restrict-usernames-emails-characters' );
			$er_username_empty = $this->options_Tw('err_mp_empty') != '' && __( 'Please enter a username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_empty'),'restrict-usernames-emails-characters') : __( 'Please enter a username.','restrict-usernames-emails-characters' );
			$er_exist_login = $this->options_Tw('err_mp_exist_login') != '' && __( 'This username is already registered. Please choose another one.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_exist_login'),'restrict-usernames-emails-characters') : __( 'This username is already registered. Please choose another one.','restrict-usernames-emails-characters' );	 
			
			$er_empty_user_email = $this->options_Tw('err_mp_empty_user_email') != '' && __( 'Please type your email address.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_empty_user_email'),'restrict-usernames-emails-characters') : __( 'Please type your email address.','restrict-usernames-emails-characters' );	 
			$er_invalid_user_email = $this->options_Tw('err_mp_invalid_user_email') != '' && __( 'The email address isn&#8217;t correct.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_invalid_user_email'),'restrict-usernames-emails-characters') : __( 'The email address isn&#8217;t correct.','restrict-usernames-emails-characters' );	 
			$er_exist_email = $this->options_Tw('err_mp_exist_user_email') != '' && __( 'This email is already registered, please choose another one.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_exist_user_email'),'restrict-usernames-emails-characters') : __( 'This email is already registered, please choose another one.','restrict-usernames-emails-characters' );	 
			$er_emails_limit = $this->options_Tw('err_mp_emails_limit') != '' && __( 'This email is not allowed, choose another please.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_emails_limit'),'restrict-usernames-emails-characters') : __( 'This email is not allowed, choose another please.','restrict-usernames-emails-characters' );	 
		
		    if ($this->only_mu()) {
			$pr = $this->options_Tw('err_mp_partial') != '' && __( "This part <font color='#FF0000'>%part%</font> is not allowed in username.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_mp_partial'),'restrict-usernames-emails-characters') : __( "This part <font color='#FF0000'>%part%</font> is not allowed in username.",'restrict-usernames-emails-characters' ) ;
			} else {
			$pr = $this->options_Tw('err_bp_partial') != '' && __( "This part (%part%) is not allowed in username.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_bp_partial'),'restrict-usernames-emails-characters') : __( "This part (%part%) is not allowed in username.",'restrict-usernames-emails-characters' ) ;
			}
			
			if ( trim($username) == '' )
			$this->benrueeg_error( $new_error, 'user_name', $er_username_empty);
		
			if ( $this->func_space_s_e_m($username) || ($this->func_s($username) && !$this->ben_username_empty($username)) ) {
				//if (!$this->can_create_users())
				$this->benrueeg_error( $new_error, 'user_name', $er_start_end_space);
			}
			
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
			
			if ( !validate_username( $username ) || $this->valid_charts && !$this->can_create_users() || $this->invalid_chars_allow && !$this->can_create_users() ) {
			$bb_err = true;
			$this->benrueeg_error( $new_error,  'user_name', $er_illegal_name );	
			}
			
		    $signups = $this->bp() ? BP_Signup::get( array(
			    'user_login' => $username,
		    ) ) : false;

		    $signup = isset( $signups['signups'] ) && ! empty( $signups['signups'][0] ) ? $signups['signups'][0] : false;
			
			if ( username_exists($username) || ($this->bp() && ! empty( $signup )) || ($this->bb() && $this->nickname_exists( $username )) ) {
			$bb_err = true;
			$this->benrueeg_error( $new_error, 'user_name', $er_exist_login);
			}
			
			if ( $this->valid_partial )
			$this->benrueeg_error( $new_error,  'user_name', str_replace("%part%", $this->func__part($username), $pr) );
			
			if ( $this->name_not__email && !$this->mu() )
			$new_error->add('user_name', $er_name_not_email);
			
			if ( $this->invalid_names )
			$this->benrueeg_error( $new_error, 'user_name', $er_name);
			
			if ($this->bb() && !empty($min_length) && $min_length <= 3)
			$bb_err = true;
			
			if ( $this->length_min )
			$this->benrueeg_error( $new_error, 'user_name', str_replace("%min%", $min_length, $filter_err_min_length) );
			
			$nickname_max_length = apply_filters( 'xprofile_nickname_max_length', 32 );
			if ($this->bb() && mb_strlen($username) > $nickname_max_length) {
			$bb_err = true;
			$this->benrueeg_error( $new_error, 'user_name', str_replace("%max%", $nickname_max_length, $filter_err_max_length));
			} elseif ( $this->length_max ) {
			$this->benrueeg_error( $new_error, 'user_name', str_replace("%max%", $max_length, $filter_err_max_length));
			}
			
			if ( $this->valid_num_less && !preg_match( '/^\+?\d+$/', $username ) )
			$this->benrueeg_error( $new_error, 'user_name', $er_digits_less);
			
			if ( preg_match('/ /', $username) ) {
			$this->benrueeg_error( $new_error, 'user_name', $er_space);
			} 
			
			if ( $this->uppercase_names )
			$this->benrueeg_error( $new_error, 'user_name', $er_uppercase);
			
			// emails error
			if ($this->empty__user_email)
			$new_error->add('user_email', $er_empty_user_email);
		
			if ($this->invalid__user_email)
			$new_error->add('user_email', $er_invalid_user_email);
		
			if ($this->exist__user_email)
			$new_error->add('user_email', $er_exist_email);
		
			if ( $this->restricted_emails || $this->restricted_domain_emails )
			$new_error->add('user_email', $er_emails_limit);
			// emails error
			
			if ($this->bp()) : // if buddypress
			
			$match_ = array();
			preg_match( '/[0-9]*/', $username, $match_ );
			if ( $match_[0] == $username && $this->options('p_num') && !$this->mubp() ||
			preg_match( '/^\+?\d+$/', $username ) && $this->options('p_num') && !$this->mubp() ) {
				if (!$this->can_create_users())
				$this->benrueeg_error( $new_error, 'user_name', $er_just_num);
				} else if ( $match_[0] == $username && !$this->options('p_num') ) {
				$this->_unset( $original_error,'user_name' );
			}
			
			/*
				if ( mb_strlen( $username ) < 4 && empty($min_length) ) {
				$least_mg = $this->is__signup() ? __( 'Username must be at least 4 characters.' ) : __( 'Username must be at least 4 characters', 'buddypress' );
				if ($this->mubp())		  
				$new_error->add('user_name', $least_mg);
				else
				$new_error->add('user_name', __('Please enter a username', 'buddypress'));
				}
			*/
			
			if ( false !== strpos( $username, '_' ) && !$this->mubp() && !$this->bb() )
			$this->_unset( $original_error,'user_name' );
			
			/*
				$list_chars = array_map('trim', explode(PHP_EOL, $this->opts['option']['allow_spc_cars']));
				$list__chars = implode($list_chars);
				if ( preg_match('/[+]/', $username ) && false === strpos( ' ' . $list__chars, '+' ) ) {
				$new_error->add('user_name', __($er_illegal_name));
				} 
			*/
			
			endif; // end if buddypress
			
			if ($this->bb() && $bb_err)
				$this->remove_bb_validate_nickname();

			
			$r_ = $this->lang__mu($username);
			
			if ($lang == 'default_lang') {
				$pattern = $r_[0];
				} else if ($lang == 'all_lang') {
				$pattern = $r_[1];
				} else if ($lang == 'arab_lang') {
				$pattern = $r_[2];
				} else if ($lang == 'cyr_lang') {
				$pattern = $r_[3];
				} else if ($lang == 'arab_cyr_lang') {
				$pattern = $r_[4];
				} else if ($lang == 'select_lang') {
				$pattern = $r_[5];
			}
			
			preg_match( $pattern, $username, $match );
			
			$matchCount = preg_match( $pattern, $username, $match );
			$match__s = $matchCount > 0 ? $match[0] : '';
			
			foreach( $original_error->get_error_codes() as $code ){
				$get_messages = $result['errors']->get_error_messages($code);
				foreach(  $get_messages as $message ){
					if ( $code != 'user_email' && $this->mu() && !preg_match( '/^\+?\d+$/', $username ) ) {
						
						if ( $username != $match__s ) {
							$ok_chars = $er_illegal_name;
							$new_error->add('user_name', $ok_chars);
							} elseif ( preg_match( '/[^a-z0-9]/', $username ) || strlen( $username ) < 4 || strlen( $username ) > 60 ) {
							$this->_unset( $original_error,'user_name' );
							} else {
							$new_error->add($code, $message);
						}
						} else if ( $code == 'user_email' ) {
						$new_error->add($code, $message);	
					} 
					
				}
			}
			
			$match_ = array();
			preg_match( '/[0-9]*/', $username, $match_ );
			if ( $match_[0] == $username && !$this->options('p_num') && $this->mu() ||
			preg_match( '/^\+?\d+$/', $username ) && !$this->options('p_num') && $this->mu() ) {
				if (!$this->can_create_users())
				$new_error->add('user_name', $er_just_num);
			} 
			
			if ( $this->ben_username_empty($username) && $this->mu() )
			$new_error->add('user_name',  __( 'Please enter a username.' ));
			
			$result['errors'] = $new_error;
			
			return $result;
		}
		
		function head_reg() {
			if ( ! $this->bp() || ($this->bp() && $this->is__signup()) ) return;
			
			$signup_username = apply_filters( 'benrueeg_rue_filter_bp_signup_username', $this->signup_username );
			$signup_name = apply_filters( 'benrueeg_rue_filter_bp_signup_name', $this->signup_name );
			$signup_name_display = $this->bp_field(true, false);
			$signup_section_display = $this->bp_field(false, true);
			
			echo"
			<style type='text/css'>
			.editfield.field_1 { $signup_name_display }
			#profile-details-section { $signup_section_display }
			</style>
			
			<script type='text/javascript'>
			var url = document.location.href;
			jQuery(document).ready(function($) {
			//copy profile username to account name during registration
			//if (url.indexOf('register/') >= 0) {
			if (BENrueeg_RUE_js_Params.is_field_name_removed) {
			$('$signup_username').blur(function(){
			$('$signup_name').val($('$signup_username').val());
			});
			}
			//}
			});
			</script>
			";
		}
		
		
	}
	
	endif;