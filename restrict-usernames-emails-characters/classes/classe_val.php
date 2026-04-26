<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class benrueeg_rue_plug_validation extends benrueeg_rue_plug_glob {
	
	use benrueeg_class_rue_plug_functions;
	//use benrueeg_class_rue_plug_functions, benrueeg_rue_plug_other;
	
	private $va1 = true;
	
	public function func__part( $login ) {
		$names_limit_partial = trim($this->options('names_limit_partial'));
		if ($names_limit_partial == '') return false;
		
		$names_limit_partial = $this->options("names_partial_strtolower") == 'strtolower' ? strtolower($names_limit_partial) : $names_limit_partial;
		$names_partial = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $names_limit_partial))));
		$spr = apply_filters( 'filter_benrueeg_rue_partial_separator', ' | ' ); // add_filter( 'filter_benrueeg_rue_partial_separator', function() {return ' , ';});
		$partial_opt = $this->options("names_limit_partial_opt");
		
		$c = true;
		$a = $d = $e = $s = $end = $end_avt = false;
		foreach ( $names_partial as $_part ) {
			
			$login = $this->options("names_partial_strtolower") == 'strtolower' ? strtolower( $login ) : $login;
			
			if ($partial_opt == 'restrict_contain') {
				$a = true;
				if (strpos( $login, $_part ) !== false) {
					return $_part;
				}
				} elseif ($partial_opt == 'restrict_except') {
				$e = true;
				if (strpos( $login, $_part ) !== false) {
					$c = false;
				}
				} elseif ($partial_opt == 'restrict_without_start') {
				$s = true;
				if (strpos( $login, $_part ) === 0) {
					$d = true;
				}
				} elseif ($partial_opt == 'restrict_without_end') {
				$end_avt = true;
				if (substr($login, -strlen($_part)) === $_part) {
					$end = true;
				}
			}					
			
		}
		
		if ($a != true) {
			if ($c && !$s && !$end_avt || !$d && !$e && !$end_avt || $end_avt && !$end) {
				return implode($spr, $names_partial);
			}
		}
		
		return false;
	}
	
	function bp_field_username() {
		if ($this->bb() && function_exists('bp_xprofile_nickname_field_id')) {
			$field_id = bp_xprofile_nickname_field_id();
			$field_username = 'field_' . $field_id;
			} elseif ($this->bp()) {
			$field_username = 'signup_username';
			} else {
			$field_username = false;
		}
		
		return $field_username;
	}
	
	public function func_space_s_e_m( $login ) {
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_chars_ = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $allow_spc_cars))));
		$list_chars = implode('\\', $list_chars_);
		
		$_login = preg_match( '/[à]/', $list_chars ) ? str_replace("à", "9", $login) : $login;
		
		if ( preg_match('/^\s+|\s+$| \s+/', $_login ) ) {
			return true;
		}
		return false;
	}
	
	public function func_replogin( $login ) {
		$replogin = preg_match( '/^\+?\d+$/', $login ) && ! $this->func_space_s_e_m( $login ) ? str_replace("+", "", $login) : $login;
		return $replogin;
	}
	
	public function func_s( $login ) {
		if ( preg_match( '/^\+?\d+\s+$/', $login ) ) {
			return true;
		}
		return false;
	}
	
	public function func_validation( $valid, $login ) {
		
		$bp_signup_username = apply_filters( 'benrueeg_rue_bp_signup_username', $this->bp_field_username() );
		$_bp_signup_username = $this->bp() && $this->bp_field_username() && isset($_POST[$bp_signup_username]);
		
		if ( ! $this->registration_errors_in_backend() ) return $valid;
		
		$names_limit = $this->options('names_limit');
		$strtolower_names_limit = $this->options("names_limit_strtolower") == 'strtolower' ? strtolower($names_limit) : $names_limit;
		$strtolower_login = $this->options("names_limit_strtolower") == 'strtolower' ? strtolower( $login ) : $login;
		
		// ++++++ space_start_end_multi
		if ( $this->func_space_s_e_m( $login ) || $this->func_s( $login ) ){
			$this->space_start_end_multi = true;
			return;
		}
		
		// ++++++ invalid
		if ( ! $this->benrueeg_validate_username( $login ) ) {
			$this->invalid = true;
			return;
		}
		
		// ++++++ username_exists
		$replogin = $this->func_replogin($login);
		if ( username_exists( $login ) || username_exists( $replogin ) ){
			$this->exist__login = true;
			return $valid;
		} 
		
		// ++++++ space_start_end_multi
		if ( ! username_exists( $login ) ){
			if ( $this->func_space_s_e_m( $login ) ) {
				$this->space_start_end_multi = true;
				return $valid;
			}
		}
		
		// ++++++ space
		if ( ! $this->mu_bp() ) {
			if ( $valid && preg_match('/ /', $login) && $this->options('p_space') == 'on' ) {
				$this->space = true;
				return $valid;
			}
		}
		
		// ++++++ limit names
		$names = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $strtolower_names_limit))));
		if ( $valid && in_array( $strtolower_login, $names ) && $names_limit ){
			$this->invalid_names = true;
			return $valid;
		}	
		
		// ++++++ uppercase names
		$upper__case = $this->options('uppercase');
		$uppercase = $this->mu() ? !$upper__case : $upper__case;
		if ( $valid && preg_match('/[A-Z]/', $login ) && $uppercase ) {
			$this->uppercase_names = true;
			return $valid;
		}
		
		// ++++++ name_not__email
		//if ( $valid && is_email( $login ) && $this->options('name_not__email') ) {
		$login_not_email = preg_replace( "/\s/", "", $login ); // remove white space
		if ( $valid && preg_match( '/\^*.[@]+[a-zA-Z0-9.-]+\.[a-zA-Z]{1,}$/', $login_not_email ) && $this->options('name_not__email') ) {
			$this->name_not__email = true;
			return $valid;
		}
		
		// ++++++ partial
		if ( $valid && $this->func__part( $login ) ) {
			$this->valid_partial = true;
			return $valid;
		}
		
		// ++++++ min
		$min_length = (int) $this->options('min_length');
		$length_space = $this->options('length_space');
		$mbstrlen = preg_match( '/^\+\d+$/', $login ) ? mb_strlen( $login ) - 1 : mb_strlen( $login );
		$strlen = $length_space != 'on' || $this->mu_bp() ? $mbstrlen - substr_count($login, ' ') : $mbstrlen ;
		if ( $valid && $strlen < $min_length && ! empty($min_length) && ! empty( $login ) ) {
			$this->length_min = true;
			return $valid;
		}
		
		// ++++++ max
		$max_length = (int) $this->options('max_length');
		$v_max_length = $strlen > $max_length || $strlen > 60;
		if ( $valid && $v_max_length && ! empty($max_length) && ! empty( $login ) ) {
			$this->length_max = true;
			return $valid;
		}
		
		// ++++++ num
		if ( preg_match('/^[0-9]+$/i', $login ) || preg_match( '/^\+?\d+$/', $login ) || preg_match( '/^[0 -9]+$/i', $login ) && $this->options('p_space') != 'on' ) {
			if ( $valid && $this->options('p_num') ) {	
				$this->valid_num = true;
				return $valid;
			}
		}
		
		// ++++++ num_less
		//$va = $this->mu() ? $this->va1 : $valid ;
		
		$int = preg_replace('/[^0-9]+/', '', $login);
		$wout_sp =  preg_replace('/ /', '', $login);
		$c = mb_strlen($wout_sp) - strlen($int);
		if ( $valid && $c <= strlen($int) && $c >= 1 && $this->options('digits_less') && ! preg_match( '/^\+?\d+$/', $login ) ) {
			$this->valid_num_less = true;
			return $valid;
		}
		
		return $valid;
	}
	
}	