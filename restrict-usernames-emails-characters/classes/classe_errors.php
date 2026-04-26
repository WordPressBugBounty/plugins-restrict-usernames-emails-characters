<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class benrueeg_rue_plug_errors extends benrueeg_rue_plug_mu_bp {
	
	public function func_errors( $login, $user_email, $errors ) {
		
		$empty = $_errors = false;
		
		$list_chars_a = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $this->options('allow_spc_cars')))));
		$list_chars_allow = implode($list_chars_a);
		
		$ori_user_login = isset($_POST['user_login']) ? $_POST['user_login'] : '';
		
		//$errors->remove('invalid_username');
		
		if ( '' === trim( $login ) ) { // = empty + invalid
			$_errors = true;
			$errors->remove('invalid_username');
			$errors->errors['empty_username'][0] = $this->error_message('err_empty');
		}
		
		if ( $this->space_start_end_multi ){
			$_errors = true;
			$errors->remove('invalid_username');
			$errors->errors['empty_username'][0] = $this->error_message('err_start_end_space');
		}
		
		$userLogin = $this->options("names_partial_strtolower") == 'strtolower' ? strtolower( $ori_user_login ) : $ori_user_login;
		if ( $this->valid_partial ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_partial', '', $userLogin); 
		}
		
		if ( $this->invalid ) {
			//if ( ! $this->benrueeg_validate_username( $login ) ) {
			$_errors = true;
			$errors->remove('empty_username');
			$errors->errors['invalid_username'][0] = $this->error_message('err_spc_cars');
		}
		
		if ( $this->exist__login ) {
			$_errors = true;
			$errors->remove('username_exists');
			$errors->errors['empty_username'][0] = $this->error_message('err_exist_login');
		}
		
		if ( $this->space ){
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_spaces');
		} 
		
		if ( $this->invalid_names ){
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_names_limit');
		}
		
		if ( $this->uppercase_names ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_uppercase');
		}
		
		if ( $this->name_not__email ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_name_not_email');
		}
		
		$filter_err_min_length_BENrueeg_RUE = apply_filters( 'err_min_length_BENrueeg_RUE', $this->error_message('err_min_length') );
		if( $this->length_min ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = do_shortcode($filter_err_min_length_BENrueeg_RUE);
		}
		
		$filter_err_max_length_BENrueeg_RUE = apply_filters( 'err_max_length_BENrueeg_RUE', $this->error_message('err_max_length') );
		if( $this->length_max ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = do_shortcode($filter_err_max_length_BENrueeg_RUE);
		}
		
		if ( $this->valid_num ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_names_num');
		}
		
		if ( $this->valid_num_less ) {
			$_errors = true;
			$errors->errors['empty_username'][0] = $this->error_message('err_digits_less');
		}
		
		// email address.
		$useremail = $user_email;
		
		if ( $this->empty__user_email ) {
			$_errors = true;
			$errors->remove('empty_email');
			$errors->remove('invalid_email');
			$errors->errors['empty_email'][0] =  $this->error_message('err_empty_user_email');
			} elseif ( $this->invalid__user_email ) {
			$_errors = true;
			$errors->remove('invalid_email');
			$errors->errors['empty_email'][0] = $this->error_message('err_invalid_user_email');
			$useremail = '';
			} elseif ( $this->exist__user_email ) {
			$_errors = true;
			$errors->remove('email_exists');
			$errors->errors['empty_email'][0] = $this->error_message('err_exist_user_email');
			} elseif ( $this->restricted_emails || $this->restricted_domain_emails ){
			$_errors = true;
			$errors->remove('invalid_email');
			$errors->errors['empty_email'][0] = $this->error_message('err_emails_limit');
		}
		
		if ( $_errors ) {
			remove_all_filters( 'login_errors' );
		}
		
	}
	
}	