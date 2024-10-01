<?php

	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_errors' ) ) :
	
	class ben_plug_restrict_usernames_emails_characters_errors extends ben_plug_restrict_usernames_emails_characters_mu_bp {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function func_errors( $login, $user_email, $errors ) {
			
			$empty = false;

		    $list_chars_a = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $this->options('allow_spc_cars')))));
			$list_chars_allow = implode($list_chars_a);
			
		    $namelogin = $this->options('namelogin'); // filter user_login field in registration form
			$isset_user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
			$ori_user_login = $namelogin == '' ? $isset_user_login : (isset($_POST[$namelogin]) ? $_POST[$namelogin] : $login) ;
			$user_login = $namelogin == '' ? $login : (isset($_POST[$namelogin]) ? $_POST[$namelogin] : $login) ;
			
			//$errors->remove('invalid_username');
			
			if ( '' === trim($login) ) { // = empty + invalid
				$errors->remove('invalid_username');
				if ($this->options_Tw('err_empty') != '' && __( '<strong>ERROR</strong>: Please enter a username.','restrict-usernames-emails-characters')) 
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_empty'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __('<strong>Error</strong>: Please enter a username.');
			} 
			/*
			if ( $this->invalid ){
				$errors->remove('empty_username');
				if ($this->options_Tw('err_spc_cars') != '' && __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters'))
				$errors->errors['invalid_username'][0] = __('invalid','restrict-usernames-emails-characters'); 
				else
				$errors->errors['invalid_username'][0] = __( '<strong>Error</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' );
			}
			*/
			/*
			if ('' === trim($user_login)) {
				//$errors->remove('invalid_username');
				//$errors->remove('empty_username');
				if ($this->options_Tw('err_empty') != '' && __( '<strong>ERROR</strong>: Please enter a username.','restrict-usernames-emails-characters')) 
				$errors->errors['empty2_username'][0] = __($this->options_Tw('err_empty'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty2_username'][0] = __('<strong>Error</strong>: Please enter a username.');
			}
			*/
			if ( $this->space_start_end_multi ){
				if ($this->options_Tw('err_start_end_space') != '' && __( '<strong>ERROR</strong>: is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.','restrict-usernames-emails-characters')) 	
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_start_end_space'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __('<strong>ERROR</strong>: is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.','restrict-usernames-emails-characters');
			}

			$pr = $this->options_Tw('err_partial') != '' && __( "<strong>ERROR</strong>: This part <font color='#FF0000'>%part%</font> is not allowed in username.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_partial'),'restrict-usernames-emails-characters') : __( "<strong>ERROR</strong>: This part <font color='#FF0000'>%part%</font> is not allowed in username.",'restrict-usernames-emails-characters' ) ;
			$userLogin = $this->options("names_partial_strtolower") == 'strtolower' ? strtolower($ori_user_login) : $ori_user_login;
			if ( $this->valid_partial )
			    $errors->errors['empty_username'][0] = str_replace("%part%", $this->func__part($userLogin), $pr); 
			
			if ( $this->invalid || $this->invalid_chars_allow || $this->valid_charts ){
				$errors->remove('empty_username');
				if ($this->options_Tw('err_spc_cars') != '' && __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters'))
				$errors->errors['invalid_username'][0] = __($this->options_Tw('err_spc_cars'),'restrict-usernames-emails-characters'); 
				else
				$errors->errors['invalid_username'][0] = __( '<strong>Error</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.' );
			}
		
			if ( $this->exist__login ) {
				if ($this->options_Tw('err_exist_login') != '' && __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.','restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_exist_login'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __( '<strong>Error</strong>: This username is already registered. Please choose another one.' );
			}
			
			if ( $this->space ){
				if ($this->options_Tw('err_spaces') != '' && __( "<strong>ERROR</strong>: It's not allowed to use spaces in username.",'restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_spaces'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __("<strong>ERROR</strong>: It's not allowed to use spaces in username.",'restrict-usernames-emails-characters');
			} 
			
			if ( $this->invalid_names ){
				if ($this->options_Tw('err_names_limit') != '' && __( '<strong>ERROR</strong>: This username is not allowed, choose another please.','restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_names_limit'),'restrict-usernames-emails-characters'); 
				else
				$errors->errors['empty_username'][0] = __( '<strong>Error</strong>: Sorry, that username is not allowed.' ); 
			}
			
			if ( $this->uppercase_names ) {
				if ($this->options_Tw('err_uppercase') != '' && __( '<strong>ERROR</strong>: No uppercase (A-Z) in username.','restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_uppercase'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __('<strong>ERROR</strong>: No uppercase (A-Z) in username.','restrict-usernames-emails-characters');
			}
			
			if ( $this->name_not__email ) {
				if ($this->options_Tw('err_name_not_email') != '' && __( '<strong>ERROR</strong>: Do not allow usernames that are email addresses.','restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_name_not_email'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __('<strong>ERROR</strong>: Do not allow usernames that are email addresses.', 'restrict-usernames-emails-characters');
			}
			
			$min = $this->options_Tw('err_min_length') != '' && __( "<strong>ERROR</strong>: Username must be at least %min% characters.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_min_length'),'restrict-usernames-emails-characters') : __( "<strong>ERROR</strong>: Username must be at least %min% characters.",'restrict-usernames-emails-characters' ) ;
			$filter_err_min_length_BENrueeg_RUE = apply_filters( 'err_min_length_BENrueeg_RUE',$min );
			$min_length = $this->options('min_length');
			if( $this->length_min ) {
				$errors->errors['empty_username'][0] = str_replace("%min%", $min_length, do_shortcode($filter_err_min_length_BENrueeg_RUE));
			}
			
			$max = $this->options_Tw('max_length') != '' && __( "<strong>ERROR</strong>: Username may not be longer than %max% characters.",'restrict-usernames-emails-characters') ? __($this->options_Tw('err_max_length'),'restrict-usernames-emails-characters') : __( "<strong>ERROR</strong>: Username may not be longer than %max% characters.",'restrict-usernames-emails-characters' ) ;
			$filter_err_max_length_BENrueeg_RUE = apply_filters( 'err_max_length_BENrueeg_RUE',$max );
			$max_length = $this->options('max_length');
			if( $this->length_max ) {
				$errors->errors['empty_username'][0] = str_replace("%max%", $max_length, do_shortcode($filter_err_max_length_BENrueeg_RUE));
			}
			
			if ( $this->valid_num ) {
				if ($this->options_Tw('err_names_num') != '' && __( "<strong>ERROR</strong>: You can't register with just numbers.",'restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_names_num'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __("<strong>ERROR</strong>: You can't register with just numbers.",'restrict-usernames-emails-characters');
			}
			
			if ( $this->valid_num_less ) {
				if ($this->options_Tw('err_digits_less') != '' && __( "<strong>ERROR</strong>: The digits must be less than the characters in username.",'restrict-usernames-emails-characters'))
				$errors->errors['empty_username'][0] = __($this->options_Tw('err_digits_less'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_username'][0] = __("<strong>ERROR</strong>: The digits must be less than the characters in username.",'restrict-usernames-emails-characters');
			}
			
	        // email address.
		    $nameemail = $this->options('nameemail'); // filter user_email field in registration form
			$useremail = $nameemail == '' ? $user_email : (isset($_POST[$nameemail]) ? $_POST[$nameemail] : $user_email) ;
			
	        if ( $this->empty__user_email ) {
				$errors->remove('empty_email');
				$errors->remove('invalid_email');
				if ($this->options_Tw('err_empty_user_email') != '' && __( '<strong>ERROR</strong>: Please type your email address.','restrict-usernames-emails-characters'))
		        $errors->errors['empty_email'][0] = __( $this->options_Tw('err_empty_user_email'),'restrict-usernames-emails-characters' );
				else
		        $errors->errors['empty_email'][0] = __( '<strong>Error</strong>: Please type your email address.' );
			} elseif ( $this->invalid__user_email ) {
				$errors->remove('invalid_email');
				if ($this->options_Tw('err_invalid_user_email') != '' && __( '<strong>ERROR</strong>: The email address isn&#8217;t correct.','restrict-usernames-emails-characters'))
		        $errors->errors['empty_email'][0] = __( $this->options_Tw('err_invalid_user_email'),'restrict-usernames-emails-characters' );
				else
		        $errors->errors['empty_email'][0] = __( '<strong>Error</strong>: The email address isn&#8217;t correct.' );
		        $useremail = '';
	        } elseif ( $this->exist__user_email ) {
				$errors->remove('email_exists');
				if ($this->options_Tw('err_exist_user_email') != '' && __( '<strong>ERROR</strong>: This email is already registered, please choose another one.','restrict-usernames-emails-characters'))
		        $errors->errors['empty_email'][0] = __( $this->options_Tw('err_exist_user_email'),'restrict-usernames-emails-characters' );
				else
		        $errors->errors['empty_email'][0] = __( '<strong>Error</strong>: This email is already registered, please choose another one.' );
	        } elseif ( $this->restricted_emails || $this->restricted_domain_emails ){
				$errors->remove('invalid_email');
				if ($this->options_Tw('err_emails_limit') != '' && __( '<strong>ERROR</strong>: This email is not allowed, choose another please.','restrict-usernames-emails-characters'))
				$errors->errors['empty_email'][0] = __($this->options_Tw('err_emails_limit'),'restrict-usernames-emails-characters');
				else
				$errors->errors['empty_email'][0] = __('<strong>ERROR</strong>: This email is not allowed, choose another please.','restrict-usernames-emails-characters');
			}

			
			return $errors;
		}
		
	}
	
	endif;
	
