<?php
/*
 * Plugin Name: Restrict Usernames Emails Characters
 * Update URI: https://wordpress.org/plugins/restrict-usernames-emails-characters/
 * Plugin URI: https://benaceur-php.com/?p=2268
 * Description: Restrict the usernames in registration, email, characters and symbols or email from specific domain names or language ...
 * Version: 4.1
 * Author: benaceur
 * Text Domain: restrict-usernames-emails-characters
 * Domain Path: /lang
 * Author URI: https://benaceur-php.com/
 * License: GPL2
*/

	if ( ! defined( 'ABSPATH' ) ) exit;
	
	if ( ! defined( 'BENRUEEG_DIR_CLASSES' ) ) 
		define( 'BENRUEEG_DIR_CLASSES', 'classes/' );
		
	if ( ! defined( 'BENRUEEG_EXT' ) ) 
		define( 'BENRUEEG_EXT', '.php' );
		
	if (!defined('BENRUEEG_RUE')) 
		define("BENRUEEG_RUE", "restrict_usernames_emails_characters");
		
	if (!defined('BENRUEEG_RUE_VER_B')) 
		define("BENRUEEG_RUE_VER_B", "restrict_usernames_emails_characters_ver_base");
		
	if (!defined('BENRUEEG_O_G')) 
		define("BENRUEEG_O_G", "options-general.php");
		
	if (!defined('BENRUEEG_NAME'))
		define('BENRUEEG_NAME', trim(dirname(plugin_basename(__FILE__)), '/'));
		
	if (!defined('BENRUEEG_URL'))
		define('BENRUEEG_URL', WP_PLUGIN_URL . '/' . BENRUEEG_NAME);
		
	if ( ! defined( 'BENRUEEG_DIR' ) ) 
		define( 'BENRUEEG_DIR', plugin_dir_path( __FILE__ ) );
		
	if ( ! defined( 'BENRUEEG_NT' ) ) 
		define( 'BENRUEEG_NT', 'restrict-usernames-emails-characters' );
		
	if ( ! defined( 'BENRUEEG_NTP' ) ) 
		define( 'BENRUEEG_NTP', BENRUEEG_NT . '/restrict-usernames-emails-characters.php' );
	
	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_glob' ) ) :
	class ben_plug_restrict_usernames_emails_characters_glob {
		
		protected $BENrueeg_ver = '1.9';
		protected $benrueeg_requiresPHP = '5.3.19';
		protected $opt = 'BENrueeg_RUE_settings';
		protected $opt_Tw = 'BENrueeg_RUE_settings_Tw';
		protected $TRT = 'restrict-usernames-emails-characters';
		protected $ntb = 'news-ticker-benaceur';
		protected $mntb = 'month-name-translation-benaceur';
		protected $nmib = 'notification-msg-interface-benaceur';
		protected $napb = 'notification-admin-panel-benaceur';
		protected $signup_username = '#signup_username';
		protected $signup_name = '#field_1';
		
		protected $valid_partial = false;
		protected $valid_charts = false;
		protected $valid_num = false;
		protected $valid_num_less = false;
		protected $preg = false;
		protected $empty__user_email = false;
		protected $invalid__user_email = false;
		protected $exist__user_email = false;
		protected $exist__login = false;
		protected $opts;
		protected $B_name = false;
		protected $invalid__name = false;
		protected $invalid_names = false;
		protected $invalid = false;
		protected $uppercase_names = false;
		protected $name_not__email = false;
		protected $space_start_end_multi = false;
		protected $B___name = false;
		protected $space = false;
		protected $length_min = false;
		protected $length_max = false;
		protected $restricted_emails = false;
		protected $restricted_domain_emails = false;
		protected $invalid_chars_allow = false;
		
		public function __construct() {
			
			$this->opts = array(
			'option' => $this->get_option( $this->opt ),
			'option_Tw' => $this->get_option( $this->opt_Tw )
			);
            
			add_action('admin_init', array( $this, 'val' ));
			add_action('admin_enqueue_scripts', array($this, 'style_admin'));
			add_action('init', array($this, 'maintenance_mode'));
			
			add_action($this->mu() ? 'network_admin_menu' : 'admin_menu', array($this, 'func__settings'));
			$prefix = is_network_admin() ? 'network_admin_' : '';
			add_filter("{$prefix}plugin_action_links_". plugin_basename(__FILE__), array($this, 'setts_link'));
			add_action('admin_notices', array($this, 'admin__notice'));
			add_action('network_admin_notices', array($this, 'admin__notice'));
			add_filter('plugin_row_meta', array($this, 'row_meta'), 10, 2);
			
			$funcs_ =  array('settings__init','_exp','imp');
			foreach ( $funcs_ as $function_ ) {
				add_action('admin_init', array( $this, $function_ ));
			}
			
			add_action('wp_enqueue_scripts', array($this, 'scripts'));
            add_shortcode('ruec_sc', array($this, 'shortcode_msg_errs'));
		    register_activation_hook( __FILE__, array($this, 'BENrueeg_RUE_activated'));
			register_deactivation_hook( __FILE__, array($this, 'BENrueeg_RUE_deactivated'));
			add_action('init', array($this, 'load_textdomain'));
			add_action('wp_loaded', array($this, 'wp__loaded'));
			add_action('admin_head', array($this, 'admin__head'));
			
			$load = $this->mu() ? 'toplevel' : 'settings';
			add_action("load-{$load}_page_restrict_usernames_emails_characters", array($this, 'selected_language'));
			//add_action("load-{$load}_page_restrict_usernames_emails_characters", array($this, 'varchar'));
			/*
			foreach ( array('user-new','profile','user-edit') as $page ) {
			    add_action( "load-{$page}.php", array($this, 'varchar') );
			}
			*/
			if ( $this->mu() ) {
			add_action('network_admin_edit_ben742198_settings', array($this, 'update_network_options'));
			add_action('network_admin_edit_ben742198_tw_settings', array($this, 'update_network_options_tw'));
			}
			
		    $this->__load();
			
		}
		
		function is_options_page() {
			
			$glob = $this->mu() ? 'admin.php' : BENRUEEG_O_G;
			if ($GLOBALS['pagenow'] == $glob && isset($_GET['page']) && $_GET['page'] == BENRUEEG_RUE)
			return true;
			return false;
		}
		
		function BENrueeg_redirect() {
			return wp_safe_redirect( $this->mu() ? network_admin_url( 'admin.php?page='. BENRUEEG_RUE ) : admin_url( 'options-general.php?page='. BENRUEEG_RUE ) );
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
  
        function ben_parse_args($option, $get_option, $default_options) {
		    $ops_merged = $this->multidimensional_parse_args($get_option, $default_options);
		    return $this->update_option($option, $ops_merged);
	    }
		
	    function option($value){ // ex: $this->option('column_num')
		$opt = "BENrueeg_RUE_settings[$value]";
		return $opt;
	    }
		
		function options($value){ // $this->options('enable')
			
			$opts = $this->get_option( $this->opt );
			$opt_s = isset($opts[$value]) && str_replace(' ','', $opts[$value]) != '' ? $opts[$value] : '';
			
			return $opt_s;
		}
		
		function _option($name){ // $this->_option('enable')
			return isset( $_POST['BENrueeg_RUE_settings'][$name] );
		}
		
		function author_slug_option($value){
			return isset( $_POST['BENrueeg_RUE_settings']['author_slug'] ) && $_POST['BENrueeg_RUE_settings']['author_slug'] == $value;
		}
		
		function options_Tw($value){ // $this->options_Tw('err_spaces')
			
			$opts = $this->get_option( $this->opt_Tw );
			$opt_s = isset($opts[$value]) && str_replace(' ','', $opts[$value]) != '' ? $opts[$value] : '';
			
			return $opt_s;
		}
		
		function plug_last_v($plugin){
			
			if( ! function_exists( 'plugins_api' ) ) {
				include_once ABSPATH . '/wp-admin/includes/plugin-install.php'; 
			}
			$api = plugins_api( 'plugin_information', array(
			'slug' => $plugin,
			'fields' => array( 'version' => true )
			) );
			
			if( is_wp_error( $api ) ) return;
			
			return $api->version;
		}
		
		function wp__less_than($ver) {
			if ( version_compare( get_bloginfo('version'), "$ver", '<') ) return true;
			return false;		
		}
		
		function is_php_8_1_wpcore() {
		    if (version_compare( PHP_VERSION, '8.1', '>=' ) && version_compare( get_bloginfo('version'), '6.2', '<'))
		    return true;
		}
		
	    function array_remove_keys($array, $keys) {
 
            $assocKeys = array();
            foreach($keys as $key) {
                $assocKeys[$key] = true;
            }
 
            return array_diff_key($array, $assocKeys);
        }	
	    
		protected function ben_username_empty($username) {
			
			$wout_sp =  preg_replace( '/\s+/', '', $username );
			if ( empty( $wout_sp ) ) return true;
			return false;
		}
	    
		protected function ben_username_exists($username) {
			global $wpdb;
			
			$user__login = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->users WHERE user_login = %s", $username) );
			if ( $user__login != null ) return true;
			return false;
		}
		
		function validation($valid, $name) {
			/*
			if ($valid && $this->ben_username_empty($name) ){
				$valid = false;
				$this->B_name = true;
			}
			*/
			if ( !$valid ){
				$valid = false;
				$this->invalid__name = true;
			} 
			
			return $valid;
		}
		
        /*
		only latin:
		! $this->sanitized_containts_non_latin($user->user_login)
        -----------
		
		if varchar is enabled or disabled:
		
		'user login (default)': user_nice_name in benrueeg_users table is updated to (rawurldecode(sanitize_title(user_login))) and if user_login is non latin the user_nicename is updated in database to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is enabled
		'nickname': user_nice_name in benrueeg_users table is updated to (rawurldecode(sanitize_title(nickname))) and if user_login is non latin the user_nicename is updated in database to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is enabled
		'display name': user_nice_name in benrueeg_users table is updated to (rawurldecode(sanitize_title(display_name))) and if user_login is non latin the user_nicename is updated in database to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is enabled
		'disable this option': user_nice_name in benrueeg_users table is deleted and if user_login is non latin the user_nicename is updated in database to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is disabled
		-----------
		
		if varchar is disabled:
		'hash (numbers & latin letters)': user_nice_name in benrueeg_users table is updated to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login is non latin the user_nicename in database is updated to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is enabled
		
		-----------
		if varchar is enabled and 'hash (numbers & latin letters)' is selected:
		
		if 'Update (convert) only names (author slug) not latin' is is enabled:
		user_nice_name in benrueeg_users table is updated to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login is non latin the user_nicename in database is updated to (hash( 'sha1', $user->ID . '-' . $user->user_login )) and if user_login containts only latin characters the user_nicename is updated in database to (sanitize_title(user_login)) + request and author_link filters is enabled
		if 'Update (convert) only names (author slug) not latin' is is disabled:
		all user_nice_name in benrueeg_users table is deleted and all user_nicename is updated in database to (hash( 'sha1', $user->ID . '-' . $user->user_login )) + request and author_link filters is disabled
		-----------
		*/
		
		/*
		ex filter:
        function benrueeg_rue_up_user_nicename_per( $user_nicename, $user ) {
	    if ($user->ID == 474)
	        $user_nicename = sanitize_title( 'user 1' );
	    if ($user->ID == 471)
	        $user_nicename = sanitize_title( 'user 2' );
	
	    return $user_nicename;
        }
        //add_filter( 'user_nicename_updb_filter_benrueeg_rue', 'benrueeg_rue_up_user_nicename_per', 10, 2 );
		*/
		function updb_user_nicename_per() {
		global $wpdb;
		
		$limit_nm_rows_update_db = $this->_option('limit_nm_rows_update_db') ? (int) trim($_POST['BENrueeg_RUE_settings']['limit_nm_rows_update_db']) : 0;
		$only_not_latin_up_db_enabled = $this->_option('only_not_latin_up_db') && $_POST['BENrueeg_RUE_settings']['only_not_latin_up_db'] == 'enable' ? true : false;
		$only_not_latin_up_db_diabled = $this->_option('only_not_latin_up_db') && $_POST['BENrueeg_RUE_settings']['only_not_latin_up_db'] == 'disable' ? true : false;
        $varchar_enabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'enabled' ? true : false;
		$varchar_disabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'disabled' ? true : false;
        $not_request = $this->author_slug_option('disable') || ($varchar_enabled && $this->author_slug_option('hash') && $only_not_latin_up_db_diabled) ? false : true;
		$getIDS = $this->get_option('benrueeg_nicename_store_all_users_id');
		
		$getcounterror = $this->get_option('benrueeg_nicename_error_store_all_users_id');
		$limit = $limit_nm_rows_update_db;
		
		if ($limit && $getIDS && count($getIDS) >= $this->count_users()) return;
		
		$getIDS = $getIDS ? $getIDS : array();
		$args = array( 'fields' => array( 'ID', 'user_login', 'user_nicename', 'display_name' ), 'exclude' => $getIDS, 'orderby' => 'ID', 'order' => 'ASC' );
		if (!$getIDS)
			unset($args['exclude']);
		
		$users = get_users( $args );
        //$users = $wpdb->get_results( "SELECT ID, user_login, user_nicename, display_name FROM $wpdb->users ORDER BY ID ASC " );
		
		// store nickname sanitized
        $allID = array();
		$arr = array();
		$count1 = 1;
		$count = $error = 0;
		
        foreach ($users as $user) {
			
			$user_id  = $user->ID;
			$allID[] = $user->ID;
			
			$v = $varchar_enabled && $only_not_latin_up_db_diabled ? true : false;
			$is_only_latin = ! $this->sanitized_containts_non_latin($user->user_login) ? true : false;
			
			// hash
			if ( $this->sanitized_containts_non_latin($user->user_login) || $v ) {
			$hash_user_nicename = hash( 'sha1', $user_id . '-' . $user->user_login );
			$hash_user_nicename = apply_filters( 'user_nicename_updb_filter_benrueeg_rue', $hash_user_nicename, $user );
			
				$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $hash_user_nicename, $user_id ) );
			}
			// hash
			
			// user login
			if ( $is_only_latin && $v == false ) {
			
			//$user_nicename_userlogin = rawurldecode(sanitize_title($user->user_login));
			$user_nicename_userlogin = sanitize_title($user->user_login);
			$user_nicename_userlogin = apply_filters( 'user_nicename_updb_filter_benrueeg_rue', $user_nicename_userlogin, $user );
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
			
				$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $user_nicename_userlogin, $user_id ) );
				//$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $this->urlencode_strtolower($user_nicename_userlogin), $user_id ) );
		    }
			// user login
		
		    // stored in benrueeg_users table
			if ( $not_request ) {
			
			$user_nicename = $this->author_slug_structure($user);
			$nicename = apply_filters( 'user_nice_name_up_filter_benrueeg_rue', $user_nicename, $user );
			$nicename = mb_substr( $nicename, 0, 100 );
			
			if ( ! $this->author_slug_option('hash') ) {
			$user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $nicename, $user_id ) );

	        if ( $user_nicename_check ) {
		        $suffix = 2;
		        while ( $user_nicename_check ) {
			        // user_nicename allows 100 chars. Subtract one for a hyphen, plus the length of the suffix.
			        $base_length         = 99 - mb_strlen( $suffix );
			        $alt_user_nicename   = mb_substr( $nicename, 0, $base_length ) . "-$suffix";
			        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $alt_user_nicename, $user_id ) );
			        ++$suffix;
		        }
			$nicename = $alt_user_nicename;
	        }
			}
			
			$author_link_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_id` = '%d' LIMIT 1", $user_id ) );
			/*
			if ($author_link_check) {
				$this->update_user_nice_name($user_id, $nicename);
			} else {
				$this->add_user_nice_name($user_id, $nicename);
			}
			*/
			if ($author_link_check) {
			    $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}benrueeg_users SET user_nice_name = %s WHERE `user_id` = '%d' ", $nicename, $user_id ) );
			} else {
				$new_user = array(
					'id'             => NULL,
					'user_id'        => $user_id,
					'user_nice_name' => $nicename
				);
			    $wpdb->insert( $wpdb->prefix . 'benrueeg_users', $new_user, array( '%d', '%d', '%s' ) );
			}
			
			}
		    // stored in benrueeg_users table
		
		    $count++;
			
		    if ($limit && $count1++ == $limit) {
                break;
            }
		}
		
		if ($limit) {
		// store all users id updated
		$this->update_option('benrueeg_nicename_store_all_users_id', !$getIDS ? $allID : array_merge($getIDS, $allID));
		if ($error)
		$this->update_option('benrueeg_nicename_error_store_all_users_id', $getcounterror + $error);
		}
		$completed['count_rows_updated'] = $count;
		$completed['status_update'] = $error ? false : true;
		$completed['count_error_update'] = (int) $error;
		$this->update_option('benrueeg_n_store_all_completed_ids', $completed);	// option: if the update database precess is complete
		
		}
	
		function __load() {
			
			if ( $this->options('enable') == '' ) return;
			
			$request = $this->benrueeg_users_not_exists_or_empty() || $this->options('author_slug') == 'disable' || ($this->options('varchar') == 'enabled' && $this->options('author_slug') == 'hash' && $this->options('only_not_latin_up_db') == 'disable') ? false : true;
			
			if ( $request && apply_filters( 'benrueeg_rue_author_link', true ) ) {
				
			add_filter('request', array($this, '_request'), 10);
			add_filter('author_link', array($this, '_author_link'), 10, 3);
			
			if ( $this->bp_not_boss() ) {
			    add_filter('bp_members_get_user_slug', array($this, '_member_bp_link'), 10, 2);
			}
			
			if ( $this->bb() ) {
			    add_filter('bp_core_get_user_domain', array($this, '_bp_core_get_user_domain'), 10, 4);
			    add_filter('bp_core_get_userid_from_nicename', array($this, '_bp_core_get_userid'), 10, 2);
			}
			
			}
			
			add_action('updated_user_meta', array($this, '_updated_user_meta'), 100, 4);
			add_action('wp_update_user', array($this, '_wp_update_user'), 100, 3);
			add_action('deleted_user', array($this, '_wp_delete_user'), 100, 1);
			
			if ( $this->options('varchar') == 'enabled' ) {
			add_filter('pre_user_nicename', array($this, 'pre__user_nicename'), 100, 1);
			}
			
			if ( $this->bp() ) {
			add_filter('bp_get_displayed_user_mentionname', array($this, 'bp_displayed_user_mentionname'), 100, 1);
			add_filter('bp_activity_get_generated_content_part', array($this, 'bp_activity_generated_content_part'), 100, 2);
			} else {
			//add_action('user_profile_update_errors', array($this, 'user__update'), 100, 3);
			}
			
			if ( $this->mu() ) {
			add_action('wpmu_new_user', array($this, 'user__nicename_mu'), 100);
			add_action('added_existing_user', array($this, 'user__nicename_mu'), 100);
			} elseif ( $this->bp() ) {
		    add_action('bp_core_signup_user', array($this, 'bp_signup_user'), 100, 1);
		    add_action('bp_core_activated_user', array($this, 'bp_signup_user'), 100, 1);
			//add_action('xprofile_data_after_save', array($this, 'bp_xprofile_data_after_save'), 100, 1);
			//} else {
			//add_action('user_register', array($this, 'user__register'), 100, 2);
			}
			
			add_action('user_profile_update_errors', array($this, 'bb_user_profile_update_errors'), 100, 3);
			
		    $namelogin = $this->options('namelogin'); // filter user_login field in registration form
		    $user_login = $namelogin == '' ? 'user_login' : $namelogin;
		    $bp_signup_username = apply_filters( 'benrueeg_rue_bp_signup_username', $user_login );
		
		    if ( !$this->mu_bp() || ($this->bp() && isset($_POST[$bp_signup_username]) && !$this->bb()) ) {
			add_action('user_register', array($this, 'user__register'), 100, 2);	
			}
			
			add_action('wp_head', array($this, 'head_reg' ));
			add_action('register_form', array($this, 'to_register_form'), 99);
			add_filter('bp_nouveau_feedback_messages', array($this, 'to__register_form'));
			
			add_filter('validate_username', array($this,'validation'), 10, 2);
			$this->foreac();
			add_filter( 'gettext', array($this, 'trans_errors'), 10, 3 );
			
			add_filter ('sanitize_user', array($this, 'func__CHARS'), 9999, 3);
			
			if ( $this->mu() )
			add_filter('wpmu_validate_user_signup', array($this, 'wpmubp__ben'));
		
			if ( $this->bp() )
			add_filter('bp_core_validate_user_signup', array($this, 'wpmubp__ben'), 10, 1);
		
		    if ( $this->bb() )
		    add_action('bp_signup_validate', array($this, 'benrueeg_bp_signup_validate' ));
		
		}
		
		function ver_base() {
			return $this->get_option(BENRUEEG_RUE_VER_B);
		}
		
		function BENrueeg_RUE_version() {
			$plugin_data = get_plugin_data( __FILE__ );
			return $plugin_data['Version'];
		}
		
		public function BENrueeg_RUE_activated() {
		if ( $this->wp__less_than('3.0') )  {
		deactivate_plugins( BENRUEEG_NTP );
		wp_die(sprintf( '%1$s %2$s+', __('<strong>Core Control:</strong> Sorry, This plugin (Restrict Usernames Emails Characters) requires WordPress', 'restrict-usernames-emails-characters'), '3.0+' ));
		} elseif (version_compare( PHP_VERSION, $this->benrueeg_requiresPHP, '<' )) {
		deactivate_plugins( BENRUEEG_NTP ); 
		$message = __( '<strong>Core Control:</strong> Sorry, This plugin (Restrict Usernames Emails Characters) requires PHP', 'restrict-usernames-emails-characters' );
		$message = sprintf( '%1$s %2$s+', $message, $this->benrueeg_requiresPHP );
		wp_die($message);
		} else {
			if ( ! $this->check_table_exists('benrueeg_users') )
		        $this->benrueeg_tables();	
		}
		}
		
		public function BENrueeg_RUE_deactivated() {
		if ($this->options('del_all_opts') == 'delete_opts') {
		$this->delete_option('BENrueeg_RUE_settings');
		$this->delete_option('BENrueeg_RUE_settings_Tw');
		$this->delete_option('restrict_usernames_emails_characters_ver_base');
		$this->delete_option('benrueeg_rue_wordpress_core_nace');
		$this->delete_option('benrueeg_nicename_msg_only_store_all_ids');
		$this->delete_option('benrueeg_nicename_store_all_users_id');
		$this->delete_option('benrueeg_nicename_error_store_all_users_id');
		$this->delete_option('benrueeg_n_store_all_completed_ids');
		$this->delete_option('benrueeg_rue_1_7____notice');
		}
		}
		
		public function load_textdomain() {
            load_plugin_textdomain( 'restrict-usernames-emails-characters', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}
		
		public function wp__loaded() {
			global $wpdb;
			
			$remove = false;
			if (isset($_POST['benrueeg_rue_remove_up_all_user_nicename'])) { // isset: delete option of process update user_nicename per
			    add_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_remove_file_update_db', '1' ); // msg if process update user_nicename per is deleted
				$remove = true;
			}
			
			if (isset( $_POST['benrueeg_rue_up_all_user_nicename'] )) {
				
			    $limit_nm_rows_update_db = $this->_option('limit_nm_rows_update_db') ? (int) trim($_POST['BENrueeg_RUE_settings']['limit_nm_rows_update_db']) : 0;
				if (!$limit_nm_rows_update_db)
					$remove = true; // if "Limit the number of users to update" is empty restart the update process from the beginning
				
				$varchar_enabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'enabled' ? true : false;
			    $varchar_disabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'disabled' ? true : false;
				$only_not_latin_up_db_diabled = $this->_option('only_not_latin_up_db') && $_POST['BENrueeg_RUE_settings']['only_not_latin_up_db'] == 'disable' ? true : false;
				$v = $varchar_enabled && $this->author_slug_option('hash') && $only_not_latin_up_db_diabled ? true : false;
				
				if ( $this->author_slug_option('disable') || $v ) {
					if ( $this->check_table_exists('benrueeg_users') ) {
					$check = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}benrueeg_users LIMIT 1" );
					if ( $check )
					    $wpdb->query("TRUNCATE TABLE {$wpdb->prefix}benrueeg_users ");
					}
				}
			}
			
			if ( $remove ) {
				$this->delete_option('benrueeg_nicename_error_store_all_users_id'); // option (store errors count if update is limited)
				$this->delete_option('benrueeg_nicename_store_all_users_id'); // option (store users id updated)
				$this->delete_option('benrueeg_n_store_all_completed_ids'); // if the update database precess is complete
			}

		}
		
		function setts_link($link){
		$plugin_url = $this->mu() ? network_admin_url( 'admin.php?page='. BENRUEEG_RUE ) : admin_url( 'options-general.php?page='. BENRUEEG_RUE );
		$link[] = "<a href='$plugin_url'>". __("Settings", 'restrict-usernames-emails-characters') .'</a>';
		return $link;
		}
		
		function row_meta($links, $file) {
		
		if ( strpos( $file, 'restrict-usernames-emails-characters' ) !== false ) {
		$new_links = array(
		//'donate' => '<a href="http://benaceur-php.com/" target="_blank">'.__('Donate','restrict-usernames-emails-characters').'</a>',
		'support' => '<a href="https://benaceur-php.com/?p=2268" target="_blank">Support</a>'
		);
		
		$links = array_merge( $links, $new_links );
		}
		
		return $links;
		}
		
		function shortcode_msg_errs($err){
		
		$min_length = $this->options('min_length');
		$max_length = $this->options('max_length');
		
		extract(shortcode_atts(array(
		'err' => 'err'
		), $err));
		
		switch ($err) {
		case 'min-length':
		return $min_length;
		break;
		case 'max-length':
		return $max_length;
		break;
		}
		}
		
		// v def
		// isset( $no_val[$rr]) of checkbox 
		function home_url() {
		$homeUrl_ = get_home_url();
		$find = array( 'http://', 'https://', 'www.' );
		$replace = '';
		$homeUrl = str_replace( $find, $replace, $homeUrl_ );
		return $homeUrl;
		}
		
		function all_options() {
		
		return array (
		'enable' => 'on',
		'namelogin' => '',
		'nameemail' => '',
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
		'remove_bp_field_name' => '',
		'hide_bp_profile_section' => '',
		'txt_form' => '',
		'del_all_opts' => 'no_delete_opts',
		'varchar' => 'disabled',
		'limit_nm_rows_update_db' => '',
		'only_not_latin_up_db' => 'enable',
		'author_slug' => 'disable',
		'disable_top_sub' => '',
		);
		}
		
		function options_tw_word() {
		
		return array (
		'err_spaces' => "<strong>ERROR</strong>: It's not allowed to use spaces in username.",
		'err_names_num' => "<strong>ERROR</strong>: You can't register with just numbers.",
		'err_spc_cars' => '<strong>ERROR</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.',
		'err_emails_limit' => '<strong>ERROR</strong>: This email is not allowed, choose another please.',
		'err_names_limit' => '<strong>ERROR</strong>: This username is not allowed, choose another please.',
		'err_min_length' => "<strong>ERROR</strong>: Username must be at least %min% characters.",
		'err_max_length' => "<strong>ERROR</strong>: Username may not be longer than %max% characters.",
		'err_partial' => "<strong>ERROR</strong>: This part <font color='#FF0000'>%part%</font> is not allowed in username.",
		'err_digits_less' => "<strong>ERROR</strong>: The digits must be less than the characters in username.",
		'err_name_not_email' => '<strong>ERROR</strong>: Do not allow usernames that are email addresses.',
		'err_uppercase' => '<strong>ERROR</strong>: No uppercase (A-Z) in username.',
		'err_start_end_space' => '<strong>ERROR</strong>: is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.',
		'err_empty' => '<strong>ERROR</strong>: Please enter a username.',
		'err_exist_login' => '<strong>ERROR</strong>: This username is already registered. Please choose another one.',
		'err_empty_user_email' => '<strong>ERROR</strong>: Please type your email address.',
		'err_invalid_user_email' => '<strong>ERROR</strong>: The email address isn&#8217;t correct.',
		'err_exist_user_email' => '<strong>ERROR</strong>: This email is already registered, please choose another one.',
		'err_registration_user' => "<strong>ERROR</strong>: Couldn&#8217;t register you&hellip; please contact the <a href='mailto:%eml%'>webmaster</a> !"
		);
		}
		
		function options_tw_mupb() {
		
		return array (
		'err_mp_spaces' => "It's not allowed to use spaces in username.",
		'err_mp_names_num' => "You can't register with just numbers.",
		'err_mp_spc_cars' => 'This username is invalid because it uses illegal characters. Please enter a valid username.',
		'err_mp_emails_limit' => 'This email is not allowed, choose another please.',
		'err_mp_names_limit' => 'This username is not allowed, choose another please.',
		'err_mp_min_length' => "Username must be at least %min% characters.",
		'err_mp_max_length' => "Username may not be longer than %max% characters.",
		'err_mp_partial' => "This part <font color='#FF0000'>%part%</font> is not allowed in username.",
		'err_bp_partial' => "This part (%part%) is not allowed in username.",
		'err_mp_digits_less' => "The digits must be less than the characters in username.",
		'err_mp_name_not_email' => 'Do not allow usernames that are email addresses.',
		'err_mp_uppercase' => 'No uppercase (A-Z) in username.',
		'err_mp_start_end_space' => 'is not allowed to use multi whitespace or whitespace at the beginning or the end of the username.',
		'err_mp_empty' => 'Please enter a username.',
		'err_mp_exist_login' => 'This username is already registered. Please choose another one.',
		'err_mp_empty_user_email' => 'Please type your email address.',
		'err_mp_invalid_user_email' => 'The email address isn&#8217;t correct.',
		'err_mp_exist_user_email' => 'This email is already registered, please choose another one.'
		);
		}
		
		function all_options_tw() {
		return array_merge($this->options_tw_word(),$this->options_tw_mupb());
		}
		
		function val() {
		
		$no_val = $this->get_option($this->opt);
		$no_val_Tw = $this->get_option($this->opt_Tw);
		
		if ($this->ver_base() === false || $no_val === false || $no_val_Tw === false) {
			
			if ( ! $this->check_table_exists('benrueeg_users') )
		        $this->benrueeg_tables();	
		
		$this->add_option($this->opt, $this->all_options());
		$this->add_option($this->opt_Tw, $this->all_options_tw());
		$this->add_option( BENRUEEG_RUE_VER_B, $this->BENrueeg_ver);
		
		if ($this->is_options_page()) {
		$this->BENrueeg_redirect(); exit;
		}
		
		} else if ( $this->BENrueeg_ver != $this->ver_base() ) {
			
			if ( ! $this->check_table_exists('benrueeg_users') )
		        $this->benrueeg_tables();	
		
		$this->ben_parse_args($this->opt, $no_val, $this->all_options());
		
		$this->ben_parse_args($this->opt_Tw, $no_val_Tw, $this->all_options_tw());
		
		if ( $this->ver_base() <= "1.5" ) {
		$this->delete_option( 'benrueeg_rue_wordpress_core_ver');
		}
		
		if ( $this->ver_base() <= "1.7" ) {
			$this->change_varchar();
		    $this->RemoveMuPlugin();
		    $no__val = $this->get_option($this->opt);	
	        unset($no__val['nicename_nickname']);
			if ( $this->options('varchar') == 'enabled' ) {
				$this->add_option( 'benrueeg_rue_1_7____notice', 1 );
			}
	        $this->update_option( $this->opt, $no__val);
		}
		
		$this->update_option(BENRUEEG_RUE_VER_B, $this->BENrueeg_ver);
		
		if (get_option( 'benrueeg_rue_opt_wordpress_core_version' ) !== false)
		delete_option( 'benrueeg_rue_opt_wordpress_core_version' );
		
		if ($this->is_options_page()) {
		$this->BENrueeg_redirect(); exit;
		}
		
		}
		
		$nonce = isset($_REQUEST['_wpnonce']) ? esc_attr( $_REQUEST['_wpnonce'] ) : '';
		
		if ( isset( $_POST['BENrueeg_RUE_reset_general_opt'] ) && wp_verify_nonce( $nonce, 'nonce_BENrueeg_RUE_reset_general_opt' ) ) {
		$this->update_option($this->opt, $this->all_options());
		}
		
		if ( isset( $_POST['BENrueeg_RUE_reset_err_mgs'] ) && wp_verify_nonce( $nonce, 'nonce_BENrueeg_RUE_reset_err_mgs' ) ) {
		
		if ($this->mu()) {
		update_site_option($this->opt_Tw, $this->update_tw_mubp());
		} else if ($this->bp()) {
		update_option($this->opt_Tw, $this->update_tw_mubp());
		} else {
		update_option($this->opt_Tw, $this->update_tw_word());
		}
		
		}
		
		/*
		benrueeg_rue_up_all_user_nicename  // update the user_nicename for all members or per
		benrueeg_rue_remove_up_all_user_nicename // delete option of process update user_nicename per
		*/
		if ( isset( $_POST['benrueeg_rue_up_all_user_nicename'] ) && !isset( $_POST['benrueeg_rue_remove_up_all_user_nicename'] ) && $this->options('enable') == 'on' && !( isset( $_POST['BENrueeg_RUE_settings'] ) && $_POST['BENrueeg_RUE_settings']['enable'] != 'on' ) ) {
		    
			if (!$this->get_option('benrueeg_nicename_msg_only_store_all_ids')) {
			$opts['user_id'] = get_current_user_id();
	        $opts['time'] = time();
			$this->add_option('benrueeg_nicename_msg_only_store_all_ids', $opts); // الموقع تحت للجميع ما عدا الذي قام بالتحديث + لإظهار رسالة تحديث قاعدة البيانات فقط وليس رسالة حفظ الإعدادات
		    }
			if ( current_user_can(apply_filters( 'benrueeg_rue_filter_updb_cap', 'create_users' )) && $this->check_table_exists('benrueeg_users') ) {
			    $this->updb_user_nicename_per();
			}
		}
		
		}
		// v def
		
		function remove_empty_lines($string) {
            //$lines = explode("\n", str_replace(array("\r\n", "\r"), "\n", $string));
			$lines = explode("\n", str_replace(array("\r\n", "\r"), PHP_EOL, $string));
            $lines = array_map('trim', $lines);
            $lines = array_filter($lines, function($value) {
            return $value !== '';
            });
            //return implode("\n", $lines);
			return implode(PHP_EOL, $lines);
        }
		
		function opt_option_validate($posted_options) {
			
        if (isset( $_POST['benrueeg_rue_remove_up_all_user_nicename'] )) { // if delete option of process update user_nicename per is isset do not save changes
	    $old_options = get_option('BENrueeg_RUE_settings');
	    $posted_options = $old_options ? $old_options : $posted_options;
		return $posted_options;
        }
		
		// if "Enter another language below" field is empty do not save $posted_options['lang'] and $posted_options['selectedLanguage'] and $posted_options['langWlatin'] and show error message
		if ($posted_options['lang'] == 'select_lang' && trim($posted_options['selectedLanguage']) == '') {
		$old_options = get_option('BENrueeg_RUE_settings');
			if ($old_options) {
				$posted_options['lang'] = $old_options['lang'];
				$posted_options['selectedLanguage'] = $old_options['selectedLanguage'];
				$posted_options['langWlatin'] = $old_options['langWlatin'];
			} else {
				$posted_options = $posted_options;
			}
			
		    add_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_empty', '1' );
		}
		
		// if "Enter another language below" field is empty do not save $posted_options['lang'] and $posted_options['selectedLanguage'] and $posted_options['langWlatin'] and show error message
		if ($posted_options['lang'] == 'select_lang') {
		
		$list__selt_lang = trim($posted_options['selectedLanguage']);
		$list_selt_lang_b = explode( ',', $list__selt_lang );
		
		$err = array();
		foreach ($list_selt_lang_b as $val){
			if (@preg_match('/\\p{'. $val .'}+/u', '') === false)
				$err[] = true;
		}
		
		if (trim($posted_options['selectedLanguage']) != '' && $err) {
		$old_options = get_option('BENrueeg_RUE_settings');
			if ($old_options) {
				$posted_options['lang'] = $old_options['lang'];
				$posted_options['selectedLanguage'] = $old_options['selectedLanguage'];
				$posted_options['langWlatin'] = $old_options['langWlatin'];
			} else {
				$posted_options = $posted_options;
			}
			
		    add_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_invalid', '1' ); // if "Enter another language below" language is invalid
		}
		
		}
		
		//Create our array for storing the validated options 
	    $output = array();
	
        foreach($posted_options as $key => $value) {
		if (isset( $posted_options[$key] ))	{
			
		$specialChars = array("'", '"', "\\", "<", ">"); // remove characters
        //$str = preg_replace( "/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $str );
		
		$k = $key == 'allow_spc_cars' ? $this->remove_empty_lines( str_replace($specialChars, '', $posted_options[$key]) ) : $posted_options[$key];
			
        $output[$key] = (trim($posted_options[$key]) == '') ? $posted_options[$key] : wp_kses_post( $k );
		}
		}
        return $output;
        }
		
		function opt_tw_option_validate($posted_options) {
			
		//Create our array for storing the validated options 
	    $output = array();
	
        foreach($posted_options as $key => $value) {
		if (isset( $posted_options[$key] ))	
        $output[$key] = (trim($posted_options[$key]) == '') ? trim($posted_options[$key]) : wp_kses_post( trim($posted_options[$key]) );
        }
        return $output;
        }
		
		function style_admin($hook_suffix ) {
			
		wp_enqueue_script('benrueeg_rue-global-admin_js', plugin_dir_url( __FILE__ ).'/admin/global-js.js', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script( 'benrueeg_rue-global-admin_js' );
		$global_params = array(
		);
		wp_localize_script( 'benrueeg_rue-global-admin_js', 'benrueegrue_globjs', $global_params );
			
		$page = $this->mu() ? 'toplevel' : 'settings';	
		if ( $hook_suffix  != "{$page}_page_" . BENRUEEG_RUE ) return;
			
		wp_enqueue_style('admin_css', plugin_dir_url( __FILE__ ).'/admin/style.css', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script('BENrueeg_RUE-admin_js', plugin_dir_url( __FILE__ ).'/admin/js.js', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script( 'BENrueeg_RUE-admin_js' );
		$BENrueeg_RUE_select_params = array(
		'benrueeg_on'          => $this->options('enable') == 'on' ? true : false,
		'alert_up_if_plug_off' => __( 'First enable the plugin and save changes, then update the users', 'restrict-usernames-emails-characters' ),
		'wait_a_little'        => _x( 'Wait a little ...', 'params_js_o', 'restrict-usernames-emails-characters' ),
		'remove_wait_a_little' => __( 'Save Changes', 'restrict-usernames-emails-characters' ),
		'reset_succ'           => _x( 'Settings reset successfully', 'params_js_o', 'restrict-usernames-emails-characters' ),
		'msg_valid_json'       => __( 'Please upload a valid .json file', 'restrict-usernames-emails-characters' ),
		'is_mu'                => $this->mu() ? true : false,
		'is_rtl'               => is_rtl() ? true : false,
		'msg_up_all_nicename'  => __( 'Are you sure to updating the database (user_nicename)?', 'restrict-usernames-emails-characters' )
		);
		wp_localize_script( 'BENrueeg_RUE-admin_js', 'BENrueeg_RUE_jsParams', $BENrueeg_RUE_select_params );
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-form' );
		}
		
		function scripts() { 
		wp_register_script('BENrueeg_RUE-not_file_js',false);
		wp_enqueue_script( 'BENrueeg_RUE-not_file_js' );
		
		$BENrueeg_RUE__params = array(
		'is_field_name_removed' => $this->options('remove_bp_field_name') == 'on' ? true : false
		);
		wp_localize_script( 'BENrueeg_RUE-not_file_js', 'BENrueeg_RUE_js_Params', $BENrueeg_RUE__params );
		}
		
		function to_register_form(){
		if ($this->options('txt_form') == '') return;
		$txt = $this->options('txt_form');
		$filter = apply_filters( 'benrueeg_filter_class_txt_register_form', 'benrueeg_txt_register_form' );
		echo "<p class='$filter'>$txt</p>";
		}
		
		function to__register_form($txt){
		if ($this->options('txt_form') == '') return $txt;
		$txt['request-details']['message'] = $this->options('txt_form');
		return $txt;
		}	
		
		function bp_field($signup_name = true, $signup_section = true) {
		
		$display = 'display:none;';
		
		if ( $this->options('remove_bp_field_name') == 'on' && $signup_name || $this->options('hide_bp_profile_section') == 'on' && $signup_section )
		return $display;
		
		return '';
		}
		
		function VerPlugUp(){
		$enable = true;
		if (apply_filters( 'benrueeg_rue_filter_msg_old_ver_plug', $enable ) === false) return;
		if ( !current_user_can(apply_filters( 'benrueeg_rue_filter_mu_cap', 'update_plugins' ))) return;
		
		$n_plugin = "".BENRUEEG_NAME."/".BENRUEEG_NAME.".php";
		$v = $this->BENrueeg_RUE_version();		
		$update_file = $n_plugin;
		$url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $update_file), 'upgrade-plugin_' . $update_file);
		if ($v < $this->plug_last_v(BENRUEEG_NAME)) {
		echo "<div class='BENrueeg_RUE-mm411112'><div id='BENrueeg_RUE-mm411112-divtoBlink'>". __("You are using Version",'restrict-usernames-emails-characters').' '.$v.", ". __("There is a newer version, it's recommended to",'restrict-usernames-emails-characters')." <a href=".$url.">". __("update now",'restrict-usernames-emails-characters')."</a>.</div></div>";
		echo "
		<script>
		jQuery(document).ready(function(){
		jQuery('.BENrueeg_RUE-mm4111172p').delay(400).slideToggle('slow');
		}); 
		</script>";
		}
		}
		
		function admin__notice() {
		
		$store_limit = $this->get_option('benrueeg_nicename_store_all_users_id'); // option (store users id updated)
		$updb_completed = $this->get_option('benrueeg_n_store_all_completed_ids'); // option: if the update database precess is complete
		$getcounterror = $this->get_option('benrueeg_nicename_error_store_all_users_id');
		
		if ($updb_completed && $this->is_options_page() && current_user_can(apply_filters( 'benrueeg_rue_filter_updb_cap', 'create_users' ))) {
			
		if ($store_limit) {
		$not = count($store_limit) - (int) $getcounterror;
		} else {
		$not = (int) $updb_completed['count_rows_updated'] - (int) $updb_completed['count_error_update'];
		}
		echo '<style>#setting-error-settings_updated {display:none;}</style>';
			
		if ($store_limit && (int) $updb_completed['count_error_update'] == 0 && !$getcounterror) {
			
		$msgContinue = (count($store_limit) >= $this->count_users()) ? __( 'Finished', 'restrict-usernames-emails-characters' ) : __( 'Continue updating...', 'restrict-usernames-emails-characters' );	
		
		$n = sprintf( _n( '%s user were updated for', '%s users were updated for', count($store_limit), 'restrict-usernames-emails-characters' ), '<span style="font-family:tahoma; font-weight:700;">' . count($store_limit) . '</span>' );
		$message = sprintf( '%s %s %d%s', 
		'<span style="font-family:DroidKufiRegular,Tahoma,sans-serif,Arial; font-size:22px;">',
		$n . '<span style="font-family:tahoma; font-weight:700;">',
		$this->count_users(),
		'</span></span>'
		) .' <span style="font-family:tahoma; font-size:15px;">-> '. $msgContinue . '</span>';
		
		printf( '<div style="background:#33ea00d6;" id="restrict-usernames-updb-msg" class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-success is-dismissible' ), $message );	
		
		if (count($store_limit) >= $this->count_users()) { // remove update process if all is update without error
			$this->delete_option('benrueeg_nicename_store_all_users_id'); // option (store users id updated)
			$this->delete_option('benrueeg_n_store_all_completed_ids'); // if the update database precess is complete
		}
		
		} elseif ( !$store_limit && (int) $updb_completed['count_error_update'] == 0 && $updb_completed['status_update'] == true && (int) $updb_completed['count_rows_updated'] >= $this->count_users()) {
		
		$message = sprintf( '%s%s%s', 
		'<span style="font-size:20px;">',
		__( 'All users have been successfully updated', 'restrict-usernames-emails-characters' ),
		'</span>'
		);
		
		printf( '<div style="background:#33ea00d6;" id="restrict-usernames-updb-msg" class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-success is-dismissible' ), $message );	
		} elseif ((int) $updb_completed['count_error_update'] > 0 || $getcounterror) {
		$message = sprintf( '%s%s%s', 
		'<span style="font-size:20px;">',
		sprintf( __( '%d were updated and %d failed for %d user(s)', 'restrict-usernames-emails-characters' ), $not, $store_limit ? $getcounterror : (int) $updb_completed['count_error_update'], $this->count_users() ),
		'</span>'
		);
		
		printf( '<div style="background:#ffa2a2d6;" class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error is-dismissible' ), $message );	
		} else {
		$message = sprintf( '%s%s%s', 
		'<span style="font-size:20px;">',
		__( 'Updating all users failed', 'restrict-usernames-emails-characters' ),
		'</span>'
		);
		
		printf( '<div style="background:#ffa2a2d6;" class="%1$s"><p>%2$s</p></div>', esc_attr( 'notice notice-error is-dismissible' ), $message );	
		}
		
		}
		
		if (!$store_limit) {
		$this->delete_option('benrueeg_n_store_all_completed_ids');	// option: if the update database precess is complete
		}
		
		$cap = $this->mu() ? 'manage_network_options' : 'manage_options';
		if ( !current_user_can(apply_filters( 'manage_setts_cap_BENrueeg_RUE', $cap )) ) return;
		
		$error_class = 'notice notice-error is-dismissible';
		
		if (version_compare( PHP_VERSION, $this->benrueeg_requiresPHP, '<' ) && is_plugin_active( BENRUEEG_NTP )) {
		    deactivate_plugins( BENRUEEG_NTP );
		    $message = __( '<strong>Core Control:</strong> Sorry, This plugin (Restrict Usernames Emails Characters) requires PHP', 'restrict-usernames-emails-characters' );
		    printf( '<div class="%1$s"><p>%2$s %3$s+</p></div>', esc_attr( $error_class ), $message, $this->benrueeg_requiresPHP );
		}
		
		if ( $this->is_options_page() ) {
		
		$selectedLanguage_meta = get_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_empty', true ); // if "Enter another language below" field is empty
		$list__selt_lang = $this->options('selectedLanguage');
		if ( $selectedLanguage_meta || ($this->options('lang') == 'select_lang' && trim($list__selt_lang) == '' && !$selectedLanguage_meta) ) {
		$message = __( 'Please select a language in &#34;Enter another language below&#34;', 'restrict-usernames-emails-characters' );
		
		printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $error_class ), esc_html( $message ) );
		
		    if ($selectedLanguage_meta)
		        delete_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_empty' );
		}
		
        $selectedLanguage_meta_invalid = get_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_invalid', true ); // if "Enter another language below" language is invalid
		if ( $selectedLanguage_meta_invalid ) {
		$message = __( 'Please enter a valid language in &#34;Enter another language below&#34;', 'restrict-usernames-emails-characters' );
		
		printf( '<div style="background:#ffa2a2d6;" class="%1$s"><p style="font-size:14px; color:#441d1d;">%2$s</p></div>', esc_attr( $error_class ), esc_html( $message ) );
		
		    if ($selectedLanguage_meta_invalid)
		        delete_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_selectedLanguage_invalid' );
		}
		
		if ($this->options('lang') != 'default_lang' && $this->options('varchar') != 'enabled') {
			$message = __( 'If you choose a language other than the default language &#34;Choose language (characters) in username&#34;, option &#34;Solved the problem of not being able to register with certain languages&#34; must be activated', 'restrict-usernames-emails-characters' );
			printf( '<div class="%1$s"><p style="%2$s">%3$s</p></div>', esc_attr( $error_class ), esc_html( 'font-family:DroidKufiRegular,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif; font-size:16px;' ), esc_html( $message ) );
		}
		
		}
		
		if ( $this->get_option( 'benrueeg_rue_1_7____notice' ) ) {
		$class = 'notice notice-warning';
		$glob = $this->mu() ? network_admin_url('admin.php') : admin_url(BENRUEEG_O_G);
		$url = $glob . "?page=restrict_usernames_emails_characters&benrueegrue1-7-dismissed";
		$title = __( 'Restrict Usernames Emails Characters', 'restrict-usernames-emails-characters' );
		$message = __( 'In this version of the plugin, the method of saving the nicename (author slug) when registering a new user or updating their data has been changed. Therefore, if you are using the &#34;Solved the problem of not being able to register with certain languages&#34; option, you now need to: First: read how the plugin works, at the bottom of this page, and second: update the database: &#34;update all users with just one click or in batches&#34;.', 'restrict-usernames-emails-characters' );
		printf( '<div style="font-family:DroidKufiRegular,Tahoma,sans-serif,Arial; font-size:15px !important; line-height:1.7 !important; background:#dacbcb;" class="%1$s"><p style="font-size:15px !important; font-style:italic; font-weight:bold; letter-spacing:1px; line-height:1 !important; color:#002a54;">%2$s</p><p style="font-size:15px !important; line-height:1.7 !important; color:#002a54;">%3$s <a href="%4$s">%5$s</a></p></div>', esc_attr( $class ), $title, $message, $url,  __( 'hide', 'restrict-usernames-emails-characters' ) ); 
	    }
		
		if ( !$this->is_options_page() || (!$this->mu() && get_option('users_can_register') == '1') || ($this->mu() && in_array(get_site_option('registration'), array('user','all'))) ) return;
		
		$class = 'notice notice-error is-dismissible';
		$href = $this->mu() ? network_admin_url( 'settings.php' ) : admin_url(BENRUEEG_O_G);
		$url = '<a target="_blank" href="'.$href.'">'. __( 'here', 'restrict-usernames-emails-characters' ) .'</a>';
		$message = __( 'Registration is currently closed! open it:', 'restrict-usernames-emails-characters' );
		
		printf( '<div class="%1$s"><p>%2$s %3$s</p></div>', esc_attr( $class ), esc_html( $message ), $url ); 
		}
		
		function _exp() {
		if( empty( $_POST['BENrueeg_RUE_action'] ) || 'export_settings' != $_POST['BENrueeg_RUE_action'] )
		return;
		if( ! wp_verify_nonce( $_POST['BENrueeg_RUE_export_nonce'], 'BENrueeg_RUE_export_nonce' ) )
		return;
		if( ! current_user_can( $this->mu() ? 'manage_network_options' : 'manage_options' ) )
		return;
		
		nocache_headers();
		header( 'Content-Type: application/json; charset=utf-8' );
		$filename = 'restrict-usernames-emails-characters-settings-export-' . date("d-M-Y__H-i", current_time( 'timestamp', 0 )) . '.json';
		header( 'Content-Disposition: attachment; filename='.$filename );
		// cache
		header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1
		header("Pragma: no-cache"); // HTTP 1.0
		header("Expires: 0"); // Proxies
		
		$AllOptions_BENrueeg_RUE = array( $this->opt, $this->opt_Tw );
		foreach($AllOptions_BENrueeg_RUE as $optionN_BENrueeg_RUE) {
		
		$options = array($optionN_BENrueeg_RUE => $this->get_option($optionN_BENrueeg_RUE));
		foreach ($options as $key => $value) {
		$value = maybe_unserialize($value);
		$need_options[$key] = $value;
		}
		$need__options = version_compare( PHP_VERSION, '5.4.0', '>=' ) ? json_encode($need_options, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : json_encode($need_options);
		$json_file = $need__options;
		}
		echo $json_file;
		exit;
		}
		
		/**
		* Process a settings import from a json file
		*/
		
		function imp() {
		
		if( empty( $_POST['BENrueeg_RUE_action'] ) || 'import_settings' != $_POST['BENrueeg_RUE_action'] ) return;
		if( ! wp_verify_nonce( $_POST['BENrueeg_RUE_import_nonce'], 'BENrueeg_RUE_import_nonce' ) )	return;
		if( ! current_user_can( $this->mu() ? 'manage_network_options' : 'manage_options' ) ) return;
		
		$import_file = isset($_FILES['import_file']) ? $_FILES['import_file']['name'] : '';
		$extension = explode( '.', $import_file );
		$extension = strtolower(end($extension));
		if( $extension != 'json' ) {
		wp_die( __( 'Please upload a valid .json file', 'restrict-usernames-emails-characters' ) );
		} else {
		
		// Retrieve the settings from the file and convert the json object to an array.
		$import_file = isset($_FILES['import_file']) ? $_FILES['import_file']['tmp_name'] : '';
	
		$file_impor = file_get_contents($import_file);
		$options = json_decode($file_impor, true);
		foreach ($options as $key => $value) {
		$this->update_option($key, $value);
		}
		$this->BENrueeg_redirect(); exit;
		}
		}
		
		function foreac() {
		add_action( 'register_post', array( $this, 'func_errors' ), 9, 3 );
		
		$namelogin = $this->options('namelogin'); // filter user_login field in registration form
		$user_login = $namelogin == '' ? 'user_login' : $namelogin;
		$bp_signup_username = apply_filters( 'benrueeg_rue_bp_signup_username', $user_login );
		
		if ( !$this->mu_bp() || ($this->bp() && isset($_POST[$bp_signup_username]) && !$this->bb()) ) {
		add_filter('validate_username', array($this,'func_validation'), 10, 2);
		add_filter( 'user_registration_email', array( $this, 'user__email' ), 10, 1 );
		}
		}
		
		function user__email( $email ) {
			
		$_email = $this->options("emails_limit_strtolower") == 'strtolower' ? strtolower(trim($email)) : trim($email);
		$__email = $this->options("email_domain_strtolower") == 'strtolower' ? strtolower(trim($email)) : trim($email);
			
		if (!$this->mu_bp()) {
		    $nameemail = $this->options('nameemail'); // filter user_email field in registration form
			$email = $nameemail == '' ? $email : (isset($_POST[$nameemail]) ? $_POST[$nameemail] : $email) ;
		}
			
		if ( $email == '' ) $this->empty__user_email = true;
		if ( ! is_email( $email ) ) $this->invalid__user_email = true;
		if ( email_exists( $email ) ) $this->exist__user_email = true;
		
		$list_emails = $this->options("emails_limit_strtolower") == 'strtolower' ? strtolower($this->options('emails_limit')) : $this->options('emails_limit');
		$list_emails = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $list_emails))));
		if ( in_array( $_email, $list_emails ) && $email != '' && !email_exists( $email ) ){
		$this->restricted_emails = true;
		}
		
		$ListDomainEmails = $this->options("email_domain_strtolower") == 'strtolower' ? strtolower($this->options('email_domain')) : $this->options('email_domain');
		$ListDomainEmails = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $ListDomainEmails))));
        
		$n = false;
		$domain = $this->options('email_domain');
		
		if ($this->options('email_domain_opt') == 'restrict') {
			
		foreach(array('@','.') as $exp) {
		$ex = explode($exp, $__email);
		if ( in_array(end($ex), $ListDomainEmails) ) $n = true;
		}
		
		} elseif ($this->options('email_domain_opt') == 'not_restrict_at') {
			
		$e_x = explode('@', $__email);
		if (!in_array(end($e_x), $ListDomainEmails)) $n = true;	
		
		} elseif ($this->options('email_domain_opt') == 'not_restrict_dot') {
			
		$e_x = explode('.', $__email);
		if (!in_array(end($e_x), $ListDomainEmails)) $n = true;
		
		}

        if ($n && trim($domain) != '') $this->restricted_domain_emails = true;
		
		return $email;		
		}
		
		protected function _unset($errors, $code ) {
		$errors->remove( $code );
		}
		
		function trans_errors ( $translations, $text, $domain ) {
			
		if ( $domain == 'default' ) {
			
		$txt_form = $this->options('txt_form');
		if ( $text == '(Must be at least 4 characters, lowercase letters and numbers only.)' && trim($txt_form) != '' && $this->mu() ) {
		    $translations = $txt_form;
		}
	
	    $err_registration_user = $this->options_Tw('err_registration_user');
		$filter_err = apply_filters( 'filter_benrueeg_err_admin_email', $this->get_option( 'admin_email' ) );
		$err = str_replace("%eml%", $filter_err, $err_registration_user);

		if ( $text == '<strong>Error</strong>: Couldn&#8217;t register you&hellip; please contact the <a href="mailto:%s">webmaster</a> !' && $err_registration_user && !$this->mu_bp() )
		$translations = __($err,'restrict-usernames-emails-characters');
		
		}
		
		return $translations;
		}
		
		function ben_wp_strip_all_tags($string, $remove_breaks = false) {
		$string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
		$string = strip_tags($string);
		
		if ( $remove_breaks )
		$string = preg_replace('/[\r\n\t ]+/', ' ', $string);
		
		return $string;
		}
		
		function lang__($username) {
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_chars_ = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $allow_spc_cars))));
		$list_chars = implode('\\', $list_chars_);
		
		$list__selt_lang = $this->options('selectedLanguage');
		$list_selt_lang_b = explode( ',', $list__selt_lang );
		
		$n = array();
		foreach ($list_selt_lang_b as $val){
			//if (!in_array($val, $this->scriptNames())) continue;
			if (@preg_match('/\\p{'. $val .'}+/u', '') === false) continue;
		    //if (preg_match_all('/\\\p{'. $val .'}+/u', $username, $arr))
		    //if (@preg_match_all('/\p{'. $val .'}+/u', $username, $arr))
		    if (! empty($list__selt_lang) && preg_match_all('/\\p{'. $val .'}+/u', $username, $matches))
		        $n[] = '\p{' . trim($val) . '}';
		}
		
		$list_selt_lang = ! empty($list__selt_lang) ? implode('', $n) : '';
		
		$wLatin = $this->options('langWlatin') == 'w_latin_lang' ? 'A-Za-z' : '';
		
		$default_lang_AS = $allow_spc_cars ? preg_replace('|[^A-Za-z0-9 _.\-@\\\\\\'. $list_chars .']|u', '', $username) : preg_replace('|[^A-Za-z0-9 _.\-@]|u', '', $username);
		$all_lang_AS = $allow_spc_cars ? preg_replace('|[^\p{L}0-9 _.\-@\\\\\\'. $list_chars .'\x80-\xFF]|u', '', $username) : preg_replace('|[^\p{L}0-9 _.\-@\x80-\xFF]|u', '', $username);
		$arab_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Arabic}0-9 _.\-@\\\\\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Arabic}0-9 _.\-@]|u', '', $username);
		$cyr_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Cyrillic}0-9 _.\-@\\\\\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Cyrillic}0-9 _.\-@]|u', '', $username);
		$arab_cyr_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Arabic}\p{Cyrillic}0-9 _.\-@\\\\\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Arabic}\p{Cyrillic}0-9 _.\-@]|u', '', $username);
		$selected_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin . $list_selt_lang .'0-9 _.\-@\\\\\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin . $list_selt_lang .'0-9 _.\-@]|u', '', $username);
		
		return array($default_lang_AS,$all_lang_AS,$arab_lang_AS,$cyr_lang_AS,$arab_cyr_lang_AS,$selected_lang_AS);
		}
		
		function lang__mu($username) {
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_chars_ = array_map('trim', explode(PHP_EOL, $allow_spc_cars));
		$list_chars = implode('\\', $list_chars_);
		
		$list__selt_lang = $this->options('selectedLanguage');
		$list_selt_lang_b = explode( ',', $list__selt_lang );
		
		$n = array();
		foreach ($list_selt_lang_b as $val){
			//if (!in_array($val, $this->scriptNames())) continue;
			if (@preg_match('/\\p{'. $val .'}+/u', '') === false) continue;
		//if (preg_match_all('/\\\p{'. $val .'}+/u', $username, $arr))
		if (! empty($list__selt_lang) && preg_match_all('/\\p{'. $val .'}+/u', $username, $matches))	
		$n[] = '\p{' . trim($val) . '}';
		}
		
		$list_selt_lang = ! empty($list__selt_lang) ? implode('', $n) : '';
		
		$wLatin = $this->options('langWlatin') == 'w_latin_lang' ? 'A-Za-z' : '';
		
		$default_lang_AS = $allow_spc_cars ? '/^[A-Za-z0-9\\'. $list_chars .'\s]+$/u' : '/^[A-Za-z0-9\s]+$/u';
		$all_lang_AS = $allow_spc_cars ? '/^[\p{L}0-9\\'. $list_chars .'\x80-\xFF\s]+$/u' : '/^[\p{L}0-9\x80-\xFF\s]+$/u';
		$arab_lang_AS = $allow_spc_cars ? '/^['. $wLatin .'0-9\p{Arabic}\\'. $list_chars .'\s]+$/u' : '/^['. $wLatin .'0-9\p{Arabic}\s]+$/u';
		$cyr_lang_AS = $allow_spc_cars ? '/^['. $wLatin .'0-9\p{Cyrillic}\\'. $list_chars .'\s]+$/u' : '/^['. $wLatin .'0-9\p{Cyrillic}\s]+$/u';
		$arab_cyr_lang_AS = $allow_spc_cars ? '/^['. $wLatin .'0-9\p{Arabic}\p{Cyrillic}\\'. $list_chars .'\s]+$/u' : '/^['. $wLatin .'0-9\p{Arabic}\p{Cyrillic}\s]+$/u';
		$selected_lang_AS = $allow_spc_cars ? '/^['. $wLatin . $list_selt_lang.'0-9\\'. $list_chars .'\s]+$/u' : '/^['. $wLatin . $list_selt_lang.'0-9\s]+$/u';
		
		return array($default_lang_AS,$all_lang_AS,$arab_lang_AS,$cyr_lang_AS,$arab_cyr_lang_AS,$selected_lang_AS);
		}
		
        function get_lang__( $username ) {
			
			$r_ = $this->lang__($username);
			$lang = $this->options('lang');
				
				if ($lang == 'default_lang') {
				$username = $r_[0];
				} else if ($lang == 'all_lang') {
				$username = $r_[1];
				} else if ($lang == 'arab_lang') {
				$username = $r_[2];
				} else if ($lang == 'cyr_lang') {
				$username = $r_[3];
				} else if ($lang == 'arab_cyr_lang') {
				$username = $r_[4];
				} else if ($lang == 'select_lang') {
				$username = $r_[5];
				}

	        return $username;
        }
		
		function selected_language() {
		if ( ( isset($_GET['settings-updated']) || (isset($_GET['updated']) && $this->mu()) ) && $this->options('lang') != 'select_lang' && $this->is_options_page() ) {
		$no_val = $this->get_option($this->opt);	
	    $no_val['selectedLanguage'] = '';
	    $this->update_option( $this->opt, $no_val);
		}
		}
		
		function mu() {
		return is_multisite();	
		}
		function bp() {
		return function_exists('bp_is_active');	
		}
		/*
		function bb() {
		return class_exists('buddypress') ? buddypress()->buddyboss : false;	
		}
		*/
		function bb() {
            $bboss_plugin_file = 'buddyboss-platform/bp-loader.php';
            $bp_sitewide_plugins     = array();

            if ( $this->mu() ) {
	            // get network-activated plugins.
	            foreach ( get_site_option( 'active_sitewide_plugins', array() ) as $key => $value ) {
		            $bp_sitewide_plugins[] = $key;
	            }
            }
            $bb_plugins   = array_merge( $bp_sitewide_plugins, get_option( 'active_plugins' ) );
            $bb_plugins[] = isset( $_REQUEST['plugin'] ) ? $_REQUEST['plugin'] : array();

            // check if BuddyPress is activated.
            if ( in_array( $bboss_plugin_file, $bb_plugins ) ) {
	            return true;
            }
			
			return false;
		}
		
		function bp_not_boss() {
		return $this->bp() && !$this->bb();	
		}
		
		function mu_bp() {
		return (is_multisite() || function_exists('bp_is_active') );
		}
		function mubp() {
		return (is_multisite() && function_exists('bp_is_active') );
		}
		function is__signup() {
		if ( strpos( $_SERVER[ 'PHP_SELF' ], apply_filters( 'wp_signup_mu_filter_BENrueeg_RUE','wp-signup.php' ) ) )
		return true;
		return false;
		}
		function only_mu() {
		return ($this->mu() && !$this->mubp());	
		}
		
		function add_option($option, $value) {
		return $this->mu() ? add_site_option($option, $value) : add_option($option, $value);
		}
		
		function get_option($option, $default = false) {
		return $this->mu() ? get_site_option($option, $default) : get_option($option, $default);
		}
		
		function update_option($option, $value) {
		return $this->mu() ? update_site_option($option, $value) : update_option($option, $value);
		}
		
		function delete_option($option) {
		return $this->mu() ? delete_site_option($option) : delete_option($option);
		}
		
		function can_create_users() {
		return current_user_can('create_users');	
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
		$arr_updated = apply_filters( 'old_options_tw_mupb_filter_BENrueeg_RUE',$this->options_tw_mupb() );
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
		
        function pre__user_nicename( $user_nicename ) {
          return rawurldecode($user_nicename);
        }
		
        function user__register( $user_id, $userdata ) {
			global $wpdb;
		
			if ($this->bp() && apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false ))
				return;
			
			if ( $this->updb_user_nicename( $user_id ) ) {
			   $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
		       $this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
			}
		}
		
        /*
        function user__register( $sanitized_user_login, $user_email, $errors ) {
			add_filter( 'wp_pre_insert_user_data', array($this, 'pre_user_data_reg'), 100, 4 );
        }
		
        function pre_user_data_reg($data, $update, $user_id, $userdata) {
		global $wpdb;
		
		    $this->updb_user_nicename( $user_id, true ); // user meta
		
			if ($this->bp() && apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false ))
				return $data;
			
			if ( ! $this->updb_user_nicename( $user_id ) )
				return $data;
		
                //$user = get_user_by( 'email', $data[ 'user_email' ] );
                $data[ 'user_nicename' ] = mb_substr( $this->updb_user_nicename( $user_id ), 0, 50 );
			
            return $data;
  
		}
		*/

		/*
        function user__update( $errors, $update, $user ) {
			if ($update)
			    $this->updb_user_nicename( $user->ID, true ); // user meta
			//add_filter( 'wp_pre_insert_user_data', array($this, 'pre_user_data'), 100, 4 );
        }
        
        function pre_user_data($data, $update, $user_id, $userdata) {
		
		    $this->updb_user_nicename( $user_id, true ); // user meta
		
			if ($this->bp() && apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false ))
				return $data;
			
			if (!$this->updb_user_nicename( $user_id ) || empty($this->updb_user_nicename( $user_id )))
				return $data;
	
	            $data[ 'user_nicename' ] = mb_substr( $this->updb_user_nicename( $user_id, false, true ), 0, 50 );
			
            return $data;
		}
		
		function _wp_update_user( $user_id, $userdata, $userdata_raw ) {
        global $wpdb;
		
		    $user_obj = get_userdata( $user_id );
		
		    if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		        $this->updb_user_nicename( $user_id, true ); // user meta
			
            if ( ! $this->sanitized_containts_non_latin($user_obj->user_login) ) // only latin 
				return;
			
			//if ($this->get_option('benrueeg_userlogin_containts_non_latin_not_exists'))
				//return;
			
			if (apply_filters( 'benrueeg_rue_wp_update_user', true )) {
		
	        if ( ! $this->updb_user_nicename( $user_id, false, true ) )
				return;	
        	
			$data_user_nicename = mb_substr( $this->updb_user_nicename( $user_id, false, true ), 0, 50 );
		    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $data_user_nicename, $user_id ) );
		    //$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", sanitize_title( $userdata['user_nicename'] ), $user_id ) );
			
			}
		}
		*/
		function _updated_user_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		global $wpdb;
		
			if ($meta_key != 'nickname' || $this->options('author_slug') != 'nickname')
				return;
		
			$user_nice_name = rawurldecode(sanitize_title($meta_value));
			$user_nice_name = mb_substr( $user_nice_name, 0, 100 );
			
	        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $user_nice_name, $object_id ) );

	        if ( $user_nicename_check ) {
		        $suffix = 2;
		        while ( $user_nicename_check ) {
			        // user_nicename allows 100 chars. Subtract one for a hyphen, plus the length of the suffix.
			        $base_length         = 99 - mb_strlen( $suffix );
			        $alt_user_nicename   = mb_substr( $user_nice_name, 0, $base_length ) . "-$suffix";
			        $user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT user_id FROM {$wpdb->prefix}benrueeg_users WHERE `user_nice_name` = '%s' AND `user_id` != '%d' LIMIT 1", $alt_user_nicename, $object_id ) );
			        ++$suffix;
		        }
		    $user_nice_name = $alt_user_nicename;
	        }
		
		    if ( apply_filters( 'benrueeg_rue_updated_user_meta_nice_name', true ))
		        $this->update_user_nice_name($object_id, $user_nice_name);
		}
		
		function _wp_update_user( $user_id, $userdata, $userdata_raw ) {
        global $wpdb;
		
		    if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		        $this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
			
			if ($this->options('varchar') != 'enabled')
				return;
			
            if ( apply_filters( 'benrueeg_rue_wp_update_user', false ) )
				return;
			
            if ( ! $this->sanitized_containts_non_latin($userdata['user_nicename']) ) // only latin 
				return;
	    
		    $user_nicename = apply_filters( 'benrueeg_rue_wp_update_user_user_nicename', $this->updb_user_nicename( $user_id ), $user_id );
		    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", sanitize_title( $user_nicename ), $user_id ) );
		}
		/*
		ex:
		function benrueeg_rue_up_user_nicename( $user_nicename, $user_id ) {
	
	        if ($user_id == 474)
	            $user_nicename = 'user 1';
	
	        return $user_nicename;
        }
        //add_filter( 'benrueeg_rue_wp_update_user_user_nicename', 'benrueeg_rue_up_user_nicename', 10, 2 );
		*/
		
		function _wp_delete_user( $user_id ) {
		    $this->delete_user_nice_name($user_id);
		}
		
		// remove Invalid Nickname error on buddyboss
        function bb_user_profile_update_errors( $errors, $update, $user ) {
			
			if (!$this->bb())
				return;
			
		    if ( $update && isset( $user->nickname ) ) {
				
				$old_nickname = get_user_meta($user->ID, 'nickname', true);
				$new_nickname = $user->nickname;
			    $new_nickname_sanitized = $this->get_lang__($new_nickname);
			    $er_illegal_name = $this->options_Tw('err_mp_spc_cars') != '' && __( 'This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters') ? __( $this->options_Tw('err_mp_spc_cars'),'restrict-usernames-emails-characters') : __( 'This username is invalid because it uses illegal characters. Please enter a valid username.','restrict-usernames-emails-characters' );
           
		        $this->_unset( $errors,'nickname' );
				
				if ($old_nickname != $new_nickname && $new_nickname != $new_nickname_sanitized) {
					$errors->add('user_name', $er_illegal_name);
				}

		    }
	    }
		
        function user__nicename_mu( $user_id) {
		global $wpdb;
			
		    if (apply_filters( 'benrueeg_turn_off_filter_user_nicename_mu', false )) 
				return; // to turn off //add_filter( 'benrueeg_turn_off_filter_user_nicename_mu', '__return_true' );
			
			if ( $this->updb_user_nicename( $user_id ) ) {
			    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
		        $this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
			}
		}
		
        function bp_signup_user( $user_id  ) {
		global $wpdb;
			
		    if (apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false )) return; // to turn off //add_filter( 'benrueeg_filter_bp_activated_turn_off_user_nicename', '__return_true' );

            if ( $this->updb_user_nicename( $user_id ) ) {
			    $wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
		        $this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
			}
		}
		/*
		function bp_xprofile_data_after_save( $data ) {
        global $wpdb;
	
		    $this->updb_user_nicename( $data->user_id, true ); // user meta
			
			//$data_user_nicename = mb_substr( $this->updb_user_nicename( $data->user_id, false, true ), 0, 50 );
			//$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $data_user_nicename, $data->user_id ) );
		}
		
		function bp_activated_user( $user_id ) {
		    if (apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false )) return; // to turn off //add_filter( 'benrueeg_filter_bp_activated_turn_off_user_nicename', '__return_true' );
			$this->updb_user_nicename( $user_id );
        }
		*/
		function bp_displayed_user_mentionname( $user_login ) {
		    if (apply_filters( 'benrueeg_bp_displayed_user_mentionname', false )) return $user_login; // to turn off //add_filter( 'benrueeg_bp_displayed_user_mentionname', '__return_false' );
			
			$user_id = bp_displayed_user_id();
			
			$slug_name = function_exists('bp_displayed_user_id') ? $this->get_user_nice_name( $user_id ) : '';
			if ($slug_name) {
				$login = mb_strlen($slug_name) > 20 && $this->containts_only_latin_letters_numbers( $slug_name ) ? mb_substr($slug_name, 0, 17) . '...' : $slug_name;
			} else {
			    $login = rawurldecode($user_login);	
			    $login = mb_strlen($login) > 20 && $this->containts_only_latin_letters_numbers( $login ) ? mb_substr($login, 0, 17) . '...' : $login;
			}
			
			return apply_filters( 'benrueeg_bp_length_displayed_user_mentionname', $login, $user_id);
		}
		
		function bp_activity_generated_content_part( $content_part, $property ) {
		    if (apply_filters( 'benrueeg_bp_displayed_user_mentionname', false ) || $property != 'user_mention_name') return $content_part; // to turn off //add_filter( 'benrueeg_bp_displayed_user_mentionname', '__return_false' );
			return rawurldecode($content_part);
		}
		
        function update_network_options() {
        global $new_whitelist_options;

        $nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
			
        if ( ! wp_verify_nonce( $nonce, 'group_on-options' ) ) {
            wp_die( 'Sorry, you failed the nonce test.' );
        }
			
        // This is the list of registered options.
        $options = $new_whitelist_options['group_on'];
			
	    if ( $options ) {

		    foreach ( $options as $option ) {

			    $option = trim( $option );
			    $value  = null;
			    if ( isset( $_POST[ $option ] ) ) {
				    $value = $_POST[ $option ];
				    if ( ! is_array( $value ) ) {
					     $value = wp_kses_post( trim($value) );
				    }
				    $value = wp_unslash( $value );
			    }
			    update_site_option( $option, $value );
		    }

	    }

            // At last we redirect back to our options page.
            wp_redirect(add_query_arg(array('page' => BENRUEEG_RUE,
               'updated' => 'true'), network_admin_url('admin.php')));
            exit;
        }
		
        function update_network_options_tw() {
        global $new_whitelist_options;

        $nonce = isset($_REQUEST['_wpnonce']) ? $_REQUEST['_wpnonce'] : '';
			
        if ( ! wp_verify_nonce( $nonce, 'group_tw-options' ) ) {
            wp_die( 'Sorry, you failed the nonce test.' );
        }

        // This is the list of registered options.
        $options = $new_whitelist_options['group_tw'];
			
	    if ( $options ) {

		    foreach ( $options as $option ) {

			    $option = trim( $option );
			    $value  = null;
			    if ( isset( $_POST[ $option ] ) ) {
				    $value = $_POST[ $option ];
				    if ( ! is_array( $value ) ) {
					     $value = wp_kses_post( trim($value) );
				    }
				    $value = wp_unslash( $value );
			    }
			    update_site_option( $option, $value );
		    }

	    }

            // At last we redirect back to our options page.
            wp_redirect(add_query_arg(array('page' => BENRUEEG_RUE . '&tab=error_messages',
               'updated' => 'true'), network_admin_url('admin.php')));
            exit;
        }
		
		/*
		ex for the filter:
		add_filter( 'old_options_tw_word_filter_BENrueeg_RUE', 'tw_word' );
		function tw_word($args){
		$args['err_names_num'] = 'message here';
		return $args;
		}
		*/
		
		function admin__head() {
			
			$cur_userid = get_current_user_id();
			$process_update = get_user_meta( $cur_userid, 'benrueeg_rue_mgs_remove_file_update_db' ); // if process update user_nicename per is deleted
			
			if (!$this->get_option('benrueeg_nicename_msg_only_store_all_ids')) { // option: لإظهار رسالة تحديث قاعدة البيانات فقط وليس رسالة حفظ الإعدادات
			
		    if ( isset($_GET['settings-updated']) || (isset($_GET['updated']) && $this->mu()) ) {
			    if (!$process_update && $this->is_options_page()) {
		            printf( '<style>#setting-error-settings_updated, .benrueeg_rue_process_msg_up_db up_process.settings_updated {display:none;} #restrict-usernames-updb-msg {background:#cfdbcbd6 !important;}</style><div class="benrueeg_rue_process_msg_up_db up_process settings_updated"><p>%1$s</p></div>', __( 'Settings saved successfully', 'restrict-usernames-emails-characters' ) );
				}
			}
			
		    if ( $process_update && $this->is_options_page() ) {
		    printf( '<style>#setting-error-settings_updated, .benrueeg_rue_process_msg_up_db up_process.settings_updated {display:none;} #restrict-usernames-updb-msg {background:#cfdbcbd6 !important;}</style><div class="benrueeg_rue_process_msg_up_db up_process settings_updated"><p>%1$s</p></div>', __( 'File deleted successfully', 'restrict-usernames-emails-characters' ) );
				delete_user_meta( $cur_userid, 'benrueeg_rue_mgs_remove_file_update_db' ); // msg if process update user_nicename per is deleted
			}
			
			} else {
				
			$this->delete_option('benrueeg_nicename_msg_only_store_all_ids'); // option: لإظهار رسالة تحديث قاعدة البيانات فقط وليس رسالة حفظ الإعدادات	
				
			}
			
			// process message
			if ( $this->is_options_page() && $this->check_table_exists('benrueeg_users') ) {
		        printf( '<div class="benrueeg_rue_process_msg_up_db up_process"><p>%1$s</p></div>', __('The database is being updated, please wait...', 'restrict-usernames-emails-characters') );
			}
			
			/*
			sanitized_userlogin_containts_non_latin_exists and update: 
			
			if ($this->is_options_page()) {
			if ( isset($_GET['settings-updated']) || (isset($_GET['updated']) && $this->mu()) ) {
				if ( !$this->sanitized_userlogin_containts_non_latin_exists() && $this->options('lang') == 'default_lang' ) {
					$this->add_option('benrueeg_userlogin_containts_non_latin_not_exists', true);
				} else {
					$this->delete_option('benrueeg_userlogin_containts_non_latin_not_exists');
				}
			}
		    }
			*/
            ?>
            <style>
		    <?php
		    if ($this->options("emails_limit_strtolower") == 'strtolower') {echo "select.emails_limit_strtolower {color: green !important;}";} else {echo "select.emails_limit_strtolower {color: inherit;}";}
		    if ($this->options("names_limit_strtolower") == 'strtolower') {echo "select.names_limit_strtolower {color: green !important;}";} else {echo "select.names_limit_strtolower {color: inherit;}";}
		    if ($this->options("names_partial_strtolower") == 'strtolower') {echo "select.names_partial_strtolower {color: green !important;}";} else {echo "select.names_partial_strtolower {color: inherit;}";}
		    if ($this->options("email_domain_strtolower") == 'strtolower') {echo "select.email_domain_strtolower {color: green !important;}";} else {echo "select.email_domain_strtolower {color: inherit;}";}
		    ?>
		    @font-face {
            font-family: DroidKufiRegular;
            src: url(<?php echo plugins_url( 'admin/fonts/DroidKufi-Regular.eot' , __FILE__ ); ?>);
            src: url(<?php echo plugins_url( 'admin/fonts/DroidKufi-Regular.eot' , __FILE__ ); ?>?#iefix) format("embedded-opentype"),
            url(<?php echo plugins_url( 'admin/fonts/droidkufi-regular.ttf' , __FILE__ ); ?>) format("truetype"),
		    url(<?php echo plugins_url( 'admin/fonts/droidkufi-regular.woff2' , __FILE__ ); ?>) format("woff2"),
	        url(<?php echo plugins_url( 'admin/fonts/droidkufi-regular.woff' , __FILE__ ); ?>) format("woff");
            }
            </style>
            <?php
        }
		
	}
	endif; // if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_glob' ) )
		
	function ben_plug_restrict_usernames_emails_characters_glob_files() {
		
		$files = array(
		'validation' => 'classe_val.php',
		'chars' => 'classe_chars.php',
		'mubp' => 'classe_mubp.php',
		'errors' => 'classe_errors.php',
		'page_setts' => 'page-setts.php',
		'page_nl' => 'page_nl.php'
		);
		
		foreach ( $files as $k => $v ) {
		$cls = $k == 'page_setts' ? '' : BENRUEEG_DIR_CLASSES;
		require_once( $cls .  $v );
		}
	}
		
	ben_plug_restrict_usernames_emails_characters_glob_files();
		
	    new ben_plug_restrict_usernames_emails_characters_nl();
		//$benrueeg_cl->_actions();

	function benrueeg_remove_all_filters() {
		return remove_all_filters( 'login_errors' );	
	}
	add_action( 'wp_loaded', 'benrueeg_remove_all_filters' );