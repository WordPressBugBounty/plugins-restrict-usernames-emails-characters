<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class benrueeg_rue_plug_options extends benrueeg_rue_plug_nl {
	
	protected $BENrueeg_ver = '1.9.1';
	protected static $instance = NULL;
	
	public static function get_instance() {
		if ( NULL === self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}
	
	public function load() {
		$this->hooks();
	}
	
	function ver_base() {
		return $this->get_option(BENRUEEG_RUE_VER_B);
	}
	
	function all_options() {
		
		return array (
		'enable' => 'on',
		'enable_err_backend' => 'disable',
		'p_space' => '',
		'p_num' => '',
		'digits_less' => '',
		'uppercase' => '',
		'name_not__email' => '',
		'all_symbs' => '',
		'lang' => 'default_lang',
		'langWlatin' => 'w_latin_lang',
		'selectedLanguage' => '',
		'disallow_spc_cars' => '',
		'allow_spc_cars' => '',
		'emails_limit' => '',
		'names_limit' => '',
		'names_limit_partial' => '',
		'email_domain_opt' => 'restrict',
		'names_limit_partial_opt' => 'restrict_contain',
		'emails_limit_strtolower' => 'strtolower',
		'names_limit_strtolower' => 'strtolower',
		'email_domain_strtolower' => 'strtolower',
		'names_partial_strtolower' => 'strtolower',
		'email_domain' => $this->home_url(),
		'min_length' => '',
		'max_length' => '',
		'length_space' => '',
		'txt_form' => '',
		'del_all_opts' => 'no_delete_opts',
		'varchar' => 'disabled',
		'limit_nm_rows_update_db' => '',
		'up_process_per_method' => 'manual',
		'only_not_latin_up_db' => 'enable',
		'author_slug' => 'disable',
		'disable_top_sub' => '',
		);
	}
	
	function options_tw_word() {
		
		return array (
		'err_spaces' => "<strong>ERROR</strong>: Spaces are not allowed in the username.",
		'err_names_num' => "<strong>ERROR</strong>: You cannot use only numbers in the username.",
		'err_spc_cars' => '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.',
		'err_names_limit' => '<strong>ERROR</strong>: This username is not allowed, choose another please.',
		'err_min_length' => "<strong>ERROR</strong>: Username must be at least %min% characters.",
		'err_max_length' => "<strong>ERROR</strong>: Username may not be longer than %max% characters.",
		'err_partial' => "<strong>ERROR</strong>: This part <font color='#FF0000'>%part%</font> is not allowed in username.",
		'err_digits_less' => "<strong>ERROR</strong>: The digits must be less than the characters in username.",
		'err_name_not_email' => '<strong>ERROR</strong>: Using an email address as a name is not permitted.',
		'err_uppercase' => '<strong>ERROR</strong>: No uppercase (A-Z) in username.',
		'err_start_end_space' => '<strong>ERROR</strong>: is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.',
		'err_empty' => '<strong>ERROR</strong>: Please enter a %uname%.',
		'err_exist_login' => '<strong>ERROR</strong>: This username is already registered. Please choose another one.',
		'err_empty_user_email' => '<strong>ERROR</strong>: Please type the email address.',
		'err_invalid_user_email' => '<strong>ERROR</strong>: The email address is not correct.',
		'err_exist_user_email' => '<strong>ERROR</strong>: This email is already registered, please choose another one.',
		'err_emails_limit' => '<strong>ERROR</strong>: This email is not allowed, choose another please.',
		'err_registration_user' => "<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href='mailto:%eml%'>webmaster</a> !"
		);
	}
	
	function options_tw_mupb() {
		
		return array (
		'err_mp_spaces' => "Spaces are not allowed in the %uname%.",
		'err_mp_names_num' => "You cannot use only numbers in the %uname%.",
		'err_mp_spc_cars' => 'This %uname% is invalid because it uses illegal characters. Please enter a valid %uname%.',
		'err_mp_names_limit' => 'Sorry, that %uname% is not allowed.',
		'err_mp_min_length' => "The %uname% must be at least %min% characters.",
		'err_mp_max_length' => "The %uname% may not be longer than %max% characters.",
		'err_mp_partial' => "This part <font color='#FF0000'>%part%</font> is not allowed in %uname%.",
		'err_bp_partial' => "This part (%part%) is not allowed in %uname%.",
		'err_mp_digits_less' => "The digits must be less than the characters in %uname%.",
		'err_mp_name_not_email' => 'Using an email address as a name is not permitted.',
		'err_mp_uppercase' => 'The use of capital letters (A-Z) in the %uname% is not allowed.',
		'err_mp_start_end_space' => 'is not allowed to use multi whitespace or whitespace at the beginning or the end of the %uname%.',
		'err_mp_empty' => 'Please enter a %uname%.',
		'err_mp_exist_login' => 'Sorry, that %uname% already exists!',
		'err_bb_password' => 'Please make sure to enter your password.',
		'err_bb_required_field' => 'This is a required field.',
		'err_mp_empty_user_email' => 'Please type the email address.',
		'err_mp_invalid_user_email' => 'Please enter a valid email address.',
		'err_mp_exist_user_email' => 'This email is already registered, please choose another one.',
		'err_mp_emails_limit' => 'Sorry, that email address is not allowed!',
		'err_bb_email_twice' => 'Please make sure to enter your email twice.',
		'err_bb_email_not_match' => 'The emails entered do not match.',
		'err_bb_password_twice' => 'Please make sure to enter your password twice.',
		'err_bb_password_not_match' => 'The passwords entered do not match.',
		'err_only_bp_pass_not_strong' => 'Your password is not strong enough to be allowed on this site. Please use a stronger password.',
		'err_only_bp_privacy_policy' => 'You must indicate that you have read and agreed to the Privacy Policy.',
		'err_mu_signup_username_exists' => 'That %uname% is currently reserved but may be available in a couple of days.',
		'err_mu_signup_email_exists' => 'That email address is pending activation and is not available for new registration. If you made a previous attempt with this email address, please check your inbox for an activation email. If left unconfirmed, it will become available in a couple of days.',
		'err_mp_admin_signup_username_exists' => 'That username is currently reserved (pending activation - in wp_signups table).',
		'err_mp_admin_signup_email_exists' => 'That email address is currently reserved (pending activation - in wp_signups table).',
		'err_bp_signup_username_exists' => 'This %uname% is currently reserved, but may become available later.',
		'err_bp_signup_email_exists' => 'This email address is currently reserved, but may become available later.',
		'err_mp_activation_chrs' => 'The activation process failed because the username contained prohibited letters or symbols (perhaps they were not prohibited during registration but were prohibited afterward)',
		'err_mp_admin_activation_chrs' => 'The activation process failed because there are prohibited letters or symbols in the following name(s) (perhaps they were not prohibited during registration and were then prohibited afterward)'
		);
	}
	
	function array_tw_word() {
		$k = $this->options_tw_word();
		return array_keys($k);
	}
	
	function array_tw_mubp() {
		$k = $this->options_tw_mupb();
		return array_keys($k);
	}
	
	function update_tw_mubp() {
		$val = $this->get_option($this->opt_Tw);
		$arr = array_diff_key( $val, array_flip($this->array_tw_mubp()) );
		$arr_updated = apply_filters( 'old_options_tw_mupb_filter_BENrueeg_RUE', $this->options_tw_mupb() );
		$array = array_merge($arr, $arr_updated);
		return $array;
	}
	
	function update_tw_word() {
		$val = $this->get_option($this->opt_Tw);
		$arr = array_diff_key( $val, array_flip($this->array_tw_word()) );
		$arr_updated = apply_filters( 'old_options_tw_word_filter_BENrueeg_RUE',$this->options_tw_word() );
		$array = array_merge($arr_updated, $arr);
		return $array;
	}
	
	function multidimensional_parse_args( &$a, $b ) {
		$a = (array) $a;
		$b = (array) $b;
		$result = $b;
		foreach ( $a as $k => &$v ) {
			if ( is_array( $v ) && isset( $result[ $k ] ) ) {
				$result[ $k ] = $this->multidimensional_parse_args( $v, $result[ $k ] );
				} else {
				$result[ $k ] = $v;
			}
		}
		return $result;
	}
	
	function ben_parse_args( $option, $get_option, $default_options ) {
		$ops_merged = $this->multidimensional_parse_args( $get_option, $default_options );
		return $this->update_option( $option, $ops_merged );
	}
	
	function option( $value ){ // ex: $this->option( 'column_num' )
		$opt = "BENrueeg_RUE_settings[$value]";
		return $opt;
	}
	
	function options( $value ){ // $this->options( 'enable' )
		
		$opts = $this->get_option( $this->opt );
		$opt_s = isset( $opts[$value] ) && str_replace( ' ','', $opts[$value] ) != '' ? $opts[$value] : '';
		
		return $opt_s;
	}
	
	function _option( $name ){ // $this->_option( 'enable' )
		return isset( $_POST['BENrueeg_RUE_settings'][$name] );
	}
	
	function author_slug_option( $value ){
		return isset( $_POST['BENrueeg_RUE_settings']['author_slug'] ) && $_POST['BENrueeg_RUE_settings']['author_slug'] == $value;
	}
	
	function option_Tw( $value ){ // ex: $this->option_Tw( 'err_mp_activation_chrs' )
		$opt = "BENrueeg_RUE_settings_Tw[$value]";
		return $opt;
	}
	
	function options_Tw( $value ){ // $this->options_Tw( 'err_spaces' )
		$opts = $this->get_option( $this->opt_Tw );
		$opt_s = isset( $opts[$value] ) && str_replace( ' ','', $opts[$value] ) != '' ? $opts[$value] : '';
		return $opt_s;
	}
	
	function all_options_tw() {
		return array_merge( $this->options_tw_word(), $this->options_tw_mupb() );
	}
	
	function val() {
		
		$no_val = $this->get_option( $this->opt );
		$no_val_Tw = $this->get_option( $this->opt_Tw );
		
		if ( $this->ver_base() === false || $no_val === false || $no_val_Tw === false ) {
			
			if ( ! $this->benrueeg_users_table_exists() ) {
				$this->benrueeg_tables();
			}
			
			$this->add_option( $this->opt, $this->all_options() );
			$this->add_option( $this->opt_Tw, $this->all_options_tw() );
			$this->add_option( BENRUEEG_RUE_VER_B, $this->BENrueeg_ver );
			
			if ($this->is_options_page()) {
				$this->BENrueeg_redirect(); exit;
			}
			
		} else if ( $this->BENrueeg_ver != $this->ver_base() ) {
			
			if ( ! $this->benrueeg_users_table_exists() ) {
				$this->benrueeg_tables();
			}
			
			$this->ben_parse_args( $this->opt, $no_val, $this->all_options() );
			
			$this->ben_parse_args( $this->opt_Tw, $no_val_Tw, $this->all_options_tw() );
			
			if ( $this->ver_base() <= "1.5" ) {
				$this->delete_option( 'benrueeg_rue_wordpress_core_ver' );
			}
			
			if ( $this->ver_base() <= "1.7" ) {
				$this->change_varchar();
				$this->RemoveMuPlugin();
				$no__val = $this->get_option( $this->opt );	
				unset( $no__val['nicename_nickname'] );
				if ( $this->options( 'varchar' ) == 'enabled' ) {
					$this->add_option( 'benrueeg_rue_1_7____notice', 1 );
				}
				$this->update_option( $this->opt, $no__val );
			}
			
			if ( $this->ver_base() <= "1.9" ) {
				$no_191_val = $this->get_option( $this->opt );
				$no_191__val = $this->array_remove_keys( $no_191_val, array( 'remove_bp_field_name','hide_bp_profile_section','namelogin','nameemail' ) );
				$this->update_option( $this->opt, $no_191__val );
				$this->update_option( $this->opt_Tw, $this->update_tw_word() );
				$this->update_option( $this->opt_Tw, $this->update_tw_mubp() );
				set_site_transient( 'benrueeg_new_note_important_signups_1_9', true, MONTH_IN_SECONDS );
			}
			
			$this->update_option( BENRUEEG_RUE_VER_B, $this->BENrueeg_ver );
			
			if ( get_option( 'benrueeg_rue_opt_wordpress_core_version' ) !== false ) {
				delete_option( 'benrueeg_rue_opt_wordpress_core_version' );
			}
			
			if ( $this->is_options_page() ) {
				$this->BENrueeg_redirect(); exit;
			}
			
		}
	
		$nonce = isset( $_REQUEST['_wpnonce'] ) ? esc_attr( $_REQUEST['_wpnonce'] ) : '';
		
		if ( isset( $_POST['BENrueeg_RUE_reset_general_opt'] ) && wp_verify_nonce( $nonce, 'nonce_BENrueeg_RUE_reset_general_opt' ) ) {
			$this->update_option( $this->opt, $this->all_options() );
		}
		
		if ( isset( $_POST['BENrueeg_RUE_reset_err_mgs'] ) && wp_verify_nonce( $nonce, 'nonce_BENrueeg_RUE_reset_err_mgs' ) ) {
			
			if ( $this->mu() ) {
				update_site_option( $this->opt_Tw, $this->update_tw_mubp() );
				} else if ( $this->bp() ) {
				update_option( $this->opt_Tw, $this->update_tw_mubp() );
				} else {
				update_option( $this->opt_Tw, $this->update_tw_word() );
			}
			
		}
		
		/*
			benrueeg_rue_up_all_user_nicename  // update the user_nicename for all members or per
			benrueeg_rue_remove_up_all_user_nicename // delete option of process update user_nicename per
		*/
		if ( isset( $_POST['benrueeg_rue_up_all_user_nicename'] ) && !isset( $_POST['benrueeg_rue_remove_up_all_user_nicename'] ) && $this->options( 'enable' ) == 'on' && ! ( isset( $_POST['BENrueeg_RUE_settings'] ) && $_POST['BENrueeg_RUE_settings']['enable'] != 'on' ) ) {
			
			if ( ! $this->get_option( 'benrueeg_nicename_msg_only_store_all_ids' ) ) {
				$opts['user_id'] = get_current_user_id();
				$opts['time'] = time();
				$this->add_option( 'benrueeg_nicename_msg_only_store_all_ids', $opts ); // الموقع تحت للجميع ما عدا الذي قام بالتحديث + لإظهار رسالة تحديث قاعدة البيانات فقط وليس رسالة حفظ الإعدادات
			}
			if ( $this->can_create_users() && $this->benrueeg_users_table_exists() ) {
				$this->updb_user_nicename_per();
			}
		}
		
	}
	// v def
	
	function errors_message( $user_name = '', $username = '' ) {
		
		if ( ! $user_name ) {
			$user_name = $this->bb() && ! ( $this->mu() && $this->is_wp_signup_page() ) ? __( 'Nickname' ) : __( 'Username' );
		}
		
		$err_min_length = str_replace( '%min%', $this->options('min_length'), $this->options_Tw('err_min_length') != '' ? __( $this->options_Tw('err_min_length'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: Username must be at least %min% characters.", 'restrict-usernames-emails-characters' ) );
		$err_max_length = str_replace( '%max%', $this->options('max_length'), $this->options_Tw('err_max_length') != '' ? __( $this->options_Tw('err_max_length'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: Username may not be longer than %max% characters.", 'restrict-usernames-emails-characters' ) );
		$err_partial = str_replace( '%part%', $this->func__part( $username ), $this->options_Tw('err_partial') != '' ? __( $this->options_Tw('err_partial'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: This part <font color='#FF0000'>%part%</font> is not allowed in username.", 'restrict-usernames-emails-characters' ) );
		
		$err_empty = str_replace( '%uname%', __( 'Username' ), $this->options_Tw('err_empty') != '' ? __( $this->options_Tw('err_empty'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: Please enter a %uname%.', 'restrict-usernames-emails-characters' ) );
		
		$err_mp_min_length = str_replace( array( '%min%', '%uname%' ), array( $this->options('min_length'), $user_name ), $this->options_Tw('err_mp_min_length') != '' ? __( $this->options_Tw('err_mp_min_length'), 'restrict-usernames-emails-characters' ) : __( "The %uname% must be at least %min% characters.", 'restrict-usernames-emails-characters' ) );
		$err_mp_spaces = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_spaces') != '' ? __( $this->options_Tw('err_mp_spaces'), 'restrict-usernames-emails-characters' ) : __( "Spaces are not allowed in the %uname%.", 'restrict-usernames-emails-characters' ) );
		$err_mp_names_num = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_names_num') != '' ? __( $this->options_Tw('err_mp_names_num'), 'restrict-usernames-emails-characters' ) : __( "You cannot use only numbers in the %uname%.", 'restrict-usernames-emails-characters' ) );
		$err_mp_spc_cars = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_spc_cars') != '' ? __( $this->options_Tw('err_mp_spc_cars'), 'restrict-usernames-emails-characters' ) : __( 'This %uname% is invalid because it uses illegal characters. Please enter a valid %uname%.', 'restrict-usernames-emails-characters' ) );
		$err_mp_names_limit = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_names_limit') != '' ? __( $this->options_Tw('err_mp_names_limit'), 'restrict-usernames-emails-characters' ) : __( 'Sorry, that %uname% is not allowed.', 'restrict-usernames-emails-characters' ) );
		$err_mp_partial = str_replace( array( '%part%', '%uname%' ), array( $this->func__part( $username ), $user_name ), $this->options_Tw('err_mp_partial') != '' ? __( $this->options_Tw('err_mp_partial'), 'restrict-usernames-emails-characters' ) : __( "This part <font color='#FF0000'>%part%</font> is not allowed in %uname%.", 'restrict-usernames-emails-characters' ) );
		$err_bp_partial = str_replace( array( '%part%', '%uname%' ), array( $this->func__part( $username ), $user_name ), $this->options_Tw('err_bp_partial') != '' ? __( $this->options_Tw('err_bp_partial'), 'restrict-usernames-emails-characters' ) : __( "This part (%part%) is not allowed in %uname%.", 'restrict-usernames-emails-characters' ) );
		$err_mp_digits_less = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_digits_less') != '' ? __( $this->options_Tw('err_mp_digits_less'), 'restrict-usernames-emails-characters' ) : __( "The digits must be less than the characters in %uname%.", 'restrict-usernames-emails-characters' ) );
		$err_mp_uppercase = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_uppercase') != '' ? __( $this->options_Tw('err_mp_uppercase'), 'restrict-usernames-emails-characters' ) : __( 'The use of capital letters (A-Z) in the %uname% is not allowed.', 'restrict-usernames-emails-characters' ) );
		$err_mp_start_end_space = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_start_end_space') != '' ? __( $this->options_Tw('err_mp_start_end_space'), 'restrict-usernames-emails-characters' ) : __( 'is not allowed to use multi whitespace or whitespace at the beginning or the end of the %uname%.', 'restrict-usernames-emails-characters' ) );
		$err_mp_empty = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_empty') != '' ? __( $this->options_Tw('err_mp_empty'), 'restrict-usernames-emails-characters' ) : __( 'Please enter a %uname%.', 'restrict-usernames-emails-characters' ) );
		$err_mp_exist_login = str_replace( '%uname%', $user_name, $this->options_Tw('err_mp_exist_login') != '' ? __( $this->options_Tw('err_mp_exist_login'), 'restrict-usernames-emails-characters' ) : __( 'Sorry, that %uname% already exists!', 'restrict-usernames-emails-characters' ) );
		$err_mu_signup_username_exists = str_replace( '%uname%', $user_name, $this->options_Tw('err_mu_signup_username_exists') != '' ? __( $this->options_Tw('err_mu_signup_username_exists'), 'restrict-usernames-emails-characters' ) : __( 'That %uname% is currently reserved but may be available in a couple of days.', 'restrict-usernames-emails-characters' ) );
		$err_bp_signup_username_exists = str_replace( '%uname%', $user_name, $this->options_Tw('err_bp_signup_username_exists') != '' ? __( $this->options_Tw('err_bp_signup_username_exists'), 'restrict-usernames-emails-characters' ) : __( 'This %uname% is currently reserved, but may become available later.', 'restrict-usernames-emails-characters' ) );
		
		return array (
		'err_spaces' => $this->options_Tw('err_spaces') != '' ? __( $this->options_Tw('err_spaces'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: Spaces are not allowed in the username.", 'restrict-usernames-emails-characters' ),
		'err_names_num' => $this->options_Tw('err_names_num') != '' ? __( $this->options_Tw('err_names_num'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: You cannot use only numbers in the username.", 'restrict-usernames-emails-characters' ),
		'err_spc_cars' => $this->options_Tw('err_spc_cars') != '' ? __( $this->options_Tw('err_spc_cars'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.', 'restrict-usernames-emails-characters' ),
		'err_names_limit' => $this->options_Tw('err_names_limit') != '' ? __( $this->options_Tw('err_names_limit'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: This username is not allowed, choose another please.', 'restrict-usernames-emails-characters' ),
		'err_min_length' => $err_min_length,
		'err_max_length' => $err_max_length,
		'err_partial' => $err_partial,
		'err_digits_less' => $this->options_Tw('err_digits_less') != '' ? __( $this->options_Tw('err_digits_less'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: The digits must be less than the characters in username.", 'restrict-usernames-emails-characters' ),
		'err_name_not_email' => $this->options_Tw('err_name_not_email') != '' ? __( $this->options_Tw('err_name_not_email'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: Using an email address as a name is not permitted.', 'restrict-usernames-emails-characters' ),
		'err_uppercase' => $this->options_Tw('err_uppercase') != '' ? __( $this->options_Tw('err_uppercase'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: No uppercase (A-Z) in username.', 'restrict-usernames-emails-characters' ),
		'err_start_end_space' => $this->options_Tw('err_start_end_space') != '' ? __( $this->options_Tw('err_start_end_space'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.', 'restrict-usernames-emails-characters' ),
		'err_empty' => $err_empty,
		'err_exist_login' => $this->options_Tw('err_exist_login') != '' ? __( $this->options_Tw('err_exist_login'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: This username is already registered. Please choose another one.', 'restrict-usernames-emails-characters' ),
		'err_empty_user_email' => $this->options_Tw('err_empty_user_email') != '' ? __( $this->options_Tw('err_empty_user_email'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: Please type the email address.', 'restrict-usernames-emails-characters' ),
		'err_invalid_user_email' => $this->options_Tw('err_invalid_user_email') != '' ? __( $this->options_Tw('err_invalid_user_email'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: The email address is not correct.', 'restrict-usernames-emails-characters' ),
		'err_exist_user_email' => $this->options_Tw('err_exist_user_email') != '' ? __( $this->options_Tw('err_exist_user_email'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: This email is already registered, please choose another one.', 'restrict-usernames-emails-characters' ),
		'err_emails_limit' => $this->options_Tw('err_emails_limit') != '' ? __( $this->options_Tw('err_emails_limit'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: This email is not allowed, choose another please.', 'restrict-usernames-emails-characters' ),
		'err_registration_user' => $this->options_Tw('err_registration_user') != '' ? __( $this->options_Tw('err_registration_user'), 'restrict-usernames-emails-characters' ) : __( "<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href='mailto:%eml%'>webmaster</a> !", 'restrict-usernames-emails-characters' ),
		'err_mp_spaces' => $err_mp_spaces,
		'err_mp_names_num' => $err_mp_names_num,
		'err_mp_spc_cars' => $err_mp_spc_cars,
		'err_mp_names_limit' => $err_mp_names_limit,
		'err_mp_min_length' => $err_mp_min_length,
		'err_mp_max_length' => $this->options_Tw('err_mp_max_length') != '' ? __( $this->options_Tw('err_mp_max_length'), 'restrict-usernames-emails-characters' ) : __( "The %uname% may not be longer than %max% characters.", 'restrict-usernames-emails-characters' ),
		'err_mp_partial' => $err_mp_partial,
		'err_bp_partial' => $err_bp_partial,
		'err_mp_digits_less' => $err_mp_digits_less,
		'err_mp_name_not_email' => $this->options_Tw('err_mp_name_not_email') != '' ? __( $this->options_Tw('err_mp_name_not_email'), 'restrict-usernames-emails-characters' ) : __( 'Using an email address as a name is not permitted.', 'restrict-usernames-emails-characters' ),
		'err_mp_uppercase' => $err_mp_uppercase,
		'err_mp_start_end_space' => $err_mp_start_end_space,
		'err_mp_empty' => $err_mp_empty,
		'err_mp_exist_login' => $err_mp_exist_login,
		'err_bb_password' => $this->options_Tw('err_bb_password') != '' ? __( $this->options_Tw('err_bb_password'), 'restrict-usernames-emails-characters' ) : __( 'Please make sure to enter your password.', 'restrict-usernames-emails-characters' ),
		'err_bb_required_field' => $this->options_Tw('err_bb_required_field') != '' ? __( $this->options_Tw('err_bb_required_field'), 'restrict-usernames-emails-characters' ) : __( 'This is a required field.', 'restrict-usernames-emails-characters' ),
		'err_mp_empty_user_email' => $this->options_Tw('err_mp_empty_user_email') != '' ? __( $this->options_Tw('err_mp_empty_user_email'), 'restrict-usernames-emails-characters' ) : __( 'Please type the email address.', 'restrict-usernames-emails-characters' ),
		'err_mp_invalid_user_email' => $this->options_Tw('err_mp_invalid_user_email') != '' ? __( $this->options_Tw('err_mp_invalid_user_email'), 'restrict-usernames-emails-characters' ) : __( 'Please enter a valid email address.', 'restrict-usernames-emails-characters' ),
		'err_mp_exist_user_email' => $this->options_Tw('err_mp_exist_user_email') != '' ? __( $this->options_Tw('err_mp_exist_user_email'), 'restrict-usernames-emails-characters' ) : __( 'This email is already registered, please choose another one.', 'restrict-usernames-emails-characters' ),
		'err_mp_emails_limit' => $this->options_Tw('err_mp_emails_limit') != '' ? __( $this->options_Tw('err_mp_emails_limit'), 'restrict-usernames-emails-characters' ) : __( 'Sorry, that email address is not allowed!', 'restrict-usernames-emails-characters' ),
		'err_bb_email_twice' => $this->options_Tw('err_bb_email_twice') != '' ? __( $this->options_Tw('err_bb_email_twice'), 'restrict-usernames-emails-characters' ) : __( 'Please make sure to enter your email twice.', 'restrict-usernames-emails-characters' ),
		'err_bb_email_not_match' => $this->options_Tw('err_bb_email_not_match') != '' ? __( $this->options_Tw('err_bb_email_not_match'), 'restrict-usernames-emails-characters' ) : __( 'The emails entered do not match.', 'restrict-usernames-emails-characters' ),
		'err_bb_password_twice' => $this->options_Tw('err_bb_password_twice') != '' ? __( $this->options_Tw('err_bb_password_twice'), 'restrict-usernames-emails-characters' ) : __( 'Please make sure to enter your password twice.', 'restrict-usernames-emails-characters' ),
		'err_bb_password_not_match' => $this->options_Tw('err_bb_password_not_match') != '' ? __( $this->options_Tw('err_bb_password_not_match'), 'restrict-usernames-emails-characters' ) : __( 'The passwords entered do not match.', 'restrict-usernames-emails-characters' ),
		'err_only_bp_pass_not_strong' => $this->options_Tw('err_only_bp_pass_not_strong') != '' ? __( $this->options_Tw('err_only_bp_pass_not_strong'), 'restrict-usernames-emails-characters' ) : __( 'Your password is not strong enough to be allowed on this site. Please use a stronger password.', 'restrict-usernames-emails-characters' ),
		'err_only_bp_privacy_policy' => $this->options_Tw('err_only_bp_privacy_policy') != '' ? __( $this->options_Tw('err_only_bp_privacy_policy'), 'restrict-usernames-emails-characters' ) : __( 'You must indicate that you have read and agreed to the Privacy Policy.', 'restrict-usernames-emails-characters' ),
		'err_mu_signup_username_exists' => $err_mu_signup_username_exists,
		'err_mu_signup_email_exists' => $this->options_Tw('err_mu_signup_email_exists') != '' ? __( $this->options_Tw('err_mu_signup_email_exists'), 'restrict-usernames-emails-characters' ) : __( 'That email address is pending activation and is not available for new registration. If you made a previous attempt with this email address, please check your inbox for an activation email. If left unconfirmed, it will become available in a couple of days.', 'restrict-usernames-emails-characters' ),
		'err_mp_admin_signup_username_exists' => $this->options_Tw('err_mp_admin_signup_username_exists') != '' ? __( $this->options_Tw('err_mp_admin_signup_username_exists'), 'restrict-usernames-emails-characters' ) : __( 'That username is currently reserved (pending activation - in wp_signups table).', 'restrict-usernames-emails-characters' ),
		'err_mp_admin_signup_email_exists' => $this->options_Tw('err_mp_admin_signup_email_exists') != '' ? __( $this->options_Tw('err_mp_admin_signup_email_exists'), 'restrict-usernames-emails-characters' ) : __( 'That email address is currently reserved (pending activation - in wp_signups table).', 'restrict-usernames-emails-characters' ),
		'err_bp_signup_username_exists' => $err_bp_signup_username_exists,
		'err_bp_signup_email_exists' => $this->options_Tw('err_bp_signup_email_exists') != '' ? __( $this->options_Tw('err_bp_signup_email_exists'), 'restrict-usernames-emails-characters' ) : __( 'This email address is currently reserved, but may become available later.', 'restrict-usernames-emails-characters' ),
		'err_mp_activation_chrs' => $this->options_Tw('err_mp_activation_chrs') != '' ? __( $this->options_Tw('err_mp_activation_chrs'), 'restrict-usernames-emails-characters' ) : __( 'The activation process failed because the username contained prohibited letters or symbols (perhaps they were not prohibited during registration but were prohibited afterward)', 'restrict-usernames-emails-characters' ),
		'err_mp_admin_activation_chrs' => $this->options_Tw('err_mp_admin_activation_chrs') != '' ? __( $this->options_Tw('err_mp_admin_activation_chrs'), 'restrict-usernames-emails-characters' ) : __( 'The activation process failed because there are prohibited letters or symbols in the following name(s) (perhaps they were not prohibited during registration and were then prohibited afterward)', 'restrict-usernames-emails-characters' )
		);
	}
	
	function error_message( $name, $user_name = '', $username = '' ){ // $this->error_message( 'err_empty' )
		$msg = $this->errors_message( $user_name, $username );
		return $msg[$name];
	}
	
}	