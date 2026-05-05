<?php
/*
* Plugin Name: Restrict Usernames Emails Characters
* Update URI: https://wordpress.org/plugins/restrict-usernames-emails-characters/
* Plugin URI: https://benaceur-php.com/?p=2268
* Description: Restrict the usernames in registration, email, characters and symbols or email from specific domain names or language ...
* Version: 5.0.1
* Author: benaceur
* Text Domain: restrict-usernames-emails-characters
* Domain Path: /lang
* Author URI: https://benaceur-php.com/
* License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) exit;

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
	define( 'BENRUEEG_NTP', BENRUEEG_NT . '/' . BENRUEEG_NT . '.php' );

function benrueeg_rue_plug_load_glob_classes() {
	$files = array(
		'functions' => 'inc/functions.php',
		'admin_notes' => 'inc/admin-notes.php',
		'validation' => 'classes/classe_val.php',
		'chars' => 'classes/classe_chars.php',
		'mubp' => 'classes/classe_mubp.php',
		'errors' => 'classes/classe_errors.php',
		'page_setts' => 'page-setts.php',
		'page_nl' => 'classes/page_nl.php',
		'options' => 'classes/options.php'
	);
	
	foreach ( $files as $file ) {
		require_once( $file );
	}
}

class benrueeg_rue_plug_glob {
	
	protected $opt = 'BENrueeg_RUE_settings';
	protected $opt_Tw = 'BENrueeg_RUE_settings_Tw';
	protected $TRT = 'restrict-usernames-emails-characters';
	protected $ntb = 'news-ticker-benaceur';
	protected $mntb = 'month-name-translation-benaceur';
	protected $benrueeg_requiresPHP = '5.4';
	
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
	protected $invalid_names = false;
	protected $invalid = false;
	protected $uppercase_names = false;
	protected $name_not__email = false;
	protected $space_start_end_multi = false;
	protected $space = false;
	protected $length_min = false;
	protected $length_max = false;
	protected $restricted_emails = false;
	protected $restricted_domain_emails = false;
	protected $glob_user_info = '';
	protected $glob_nicename_info = '';
	
	protected function hooks() {
		
		$this->opts = array(
		'option' => $this->get_option( $this->opt ),
		'option_Tw' => $this->get_option( $this->opt_Tw )
		);
		
		add_action('admin_init', array( $this, 'val' ));
		add_action('admin_enqueue_scripts', array($this, 'style_admin'));
		add_action('init', array($this, 'maintenance_mode'));
		
		add_action($this->mu() ? 'network_admin_menu' : 'admin_menu', array($this, 'func__settings'));
		$prefix = is_network_admin() ? 'network_admin_' : '';
		add_filter("{$prefix}plugin_action_links_" . plugin_basename(__FILE__), array($this, 'setts_link'));
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
		
		if ( $this->mu() ) {
			add_action( 'network_admin_edit_ben742198_settings', array( $this, 'update_network_options' ) );
			add_action( 'network_admin_edit_ben742198_tw_settings', array( $this, 'update_network_options_tw' ) );
			add_action( 'signup_extra_fields', array( $this, '_signup_extra_fields' ) );
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
		// If you need all network users use 'blog_id' => 0,
		$args = array( 'blog_id' => 0, 'fields' => array( 'ID', 'user_login', 'user_nicename', 'display_name' ), 'orderby' => 'ID', 'order' => 'ASC' );
		$args = apply_filters( 'benrueeg_filter_updb_per_get_users', $args );
		
		if ( $getIDS )
		$args['exclude'] = $getIDS;
		
		$users = get_users( $args );
		
		// store nickname sanitized
		$allID = array();
		$arr = array();
		$userid_notexists = array();
		$count1 = 1;
		$count = $error = 0;
		
		foreach ($users as $user) {
			
			$user_id  = $user->ID;
			$allID[] = $user->ID;
			
			$v = $varchar_enabled && $only_not_latin_up_db_diabled ? true : false;
			$is_only_latin = ! $this->sanitized_containts_non_latin($user->user_login) ? true : false;
			
			// hash
			if ( $this->sanitized_containts_non_latin($user->user_login) || $v ) {
				$_hash_user_nicename = hash( 'sha1', $user_id . '-' . $user->user_login );
				$hash_user_nicename = apply_filters( 'benrueeg_user_nicename_updb_sha', $_hash_user_nicename, $user );
				$hash_user_nicename = $this->duplicated_usernacename('users', $user_id, $hash_user_nicename, true);
				
				$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $hash_user_nicename, $user_id ) );
			}
			// hash
			
			// user login
			if ( $is_only_latin && $v == false ) {
				
				//$user_nicename_userlogin = rawurldecode(sanitize_title($user->user_login));
				$_user_nicename_userlogin = sanitize_title($user->user_login);
				$user_nicename_userlogin = apply_filters( 'benrueeg_user_nicename_updb', $_user_nicename_userlogin, $user );
				$user_nicename_userlogin = $this->duplicated_usernacename('users', $user_id, $user_nicename_userlogin);
				
				$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $user_nicename_userlogin, $user_id ) );
				//$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET user_nicename = %s WHERE ID = %d", $this->urlencode_strtolower($user_nicename_userlogin), $user_id ) );
			}
			// user login
			
			// stored in benrueeg_users table
			if ( $not_request ) {
				
				$user_nicename = $this->author_slug_structure($user);
				$nicename = apply_filters( 'benrueeg_user_nice_name_updb', $user_nicename, $user );
				$nicename = mb_substr( $nicename, 0, 100 );
				
				if ( ! $this->author_slug_option('hash') ) {
					$nicename = $this->duplicated_usernacename('benrueeg_users', $user_id, $nicename);
				}
				
				$author_link_check = $this->benrueeg_users_var('id', array('user_id', $user_id, 'd'));
				/*
					if ($author_link_check) {
					$this->update_user_nice_name($user_id, $nicename);
					} else {
					$this->add_user_nice_name($user_id, $nicename);
					}
				*/
				if ( $author_link_check ) {
					$wpdb->query( $wpdb->prepare( "UPDATE {$this->benrueeg_users_table()} SET user_nice_name = %s WHERE `user_id` = '%d' ", $nicename, $user_id ) );
					} else {
					$new_user = array(
					'id'             => NULL,
					'user_id'        => $user_id,
					'user_nice_name' => $nicename
					);
					$wpdb->insert( $this->benrueeg_users_table(), $new_user, array( '%d', '%d', '%s' ) );
				}
				
			}
			// stored in benrueeg_users table
			
			//
			//$check_userid_exists = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1", $user_nicename, $user_login ) );
			//$check_userid_exists = $this->benrueeg_users_var('user_id', array('user_id', $user_id, 'd'));
			//if ( $check_userid_exists )
			$userid_notexists[] = $user_id;
			//
			
			$count++;
			
			if ( $limit && $count1++ == $limit ) {
				break;
			}
			
		}
		
		if ( $limit ) {
			// store all users id updated
			$this->update_option( 'benrueeg_nicename_store_all_users_id', ! $getIDS ? $allID : array_merge( $getIDS, $allID ) );
			if ( $error )
			$this->update_option( 'benrueeg_nicename_error_store_all_users_id', $getcounterror + $error );
		}
		$completed['count_rows_updated'] = $count;
		$completed['status_update'] = $error ? false : true;
		$completed['count_error_update'] = (int) $error;
		$this->update_option('benrueeg_n_store_all_completed_ids', $completed);	// option: if the update database precess is complete
		
		// delete from benrueeg_users_table the non-existent user id in users table
		if ( ! $this->unfinished_per_up_process()) {
			if ( $this->benrueeg_users_count() && $this->benrueeg_users_count() > $this->count_users() ) {
				
				$benrueeg_users_ids = $users_ids = array();
				$getIDS_ = $this->get_option('benrueeg_nicename_store_all_users_id');
				if ( $getIDS_ ) {
					$users_ids = $getIDS_;
					} else {
					$users_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users " );
				}
				
				$benrueeg_users_ids = $wpdb->get_col( "SELECT user_id FROM {$this->benrueeg_users_table()} " );
				$result = array_diff( $benrueeg_users_ids, $users_ids );
				foreach( $result as $id ) {
					$this->delete_user_nice_name( $id );
				}
			}
		}
		// delete from benrueeg_users_table the non-existent user id in users table
		
	}
	/*
		Ex:
		add_filter( 'benrueeg_user_nice_name_updb', '_benrueeg_user_nice_name_updb', 10, 2 );
		function _benrueeg_user_nice_name_updb( $user_nicename, $user ) {
		
		return $user_nicename;
		}
		
		add_filter( 'benrueeg_user_nice_name_profile_updb', '_benrueeg_user_nice_name_profile_updb', 10, 2 );
		function _benrueeg_user_nice_name_profile_updb( $user_nicename, $user ) {
		
		return $user_nicename;
		}
	*/
	
	function __load() {
		
		if ( 'on' !== $this->options('enable') ) return;
		
		$request = $this->benrueeg_users_not_exists_or_empty() || $this->options('author_slug') == 'disable' || ($this->options('varchar') == 'enabled' && $this->options('author_slug') == 'hash' && $this->options('only_not_latin_up_db') == 'disable') ? false : true;
		
		if ( $request && apply_filters( 'benrueeg_rue_author_link', true ) ) {
			
			add_filter('request', array($this, '_request'), 10);
			add_filter('author_link', array($this, '_author_link'), 10, 3);
			add_filter('pre_post_link', array($this, '_pre_post_link'), 10, 3);
			
			if ( $this->bp_not_boss() ) {
				add_filter('bp_members_get_user_slug', array($this, '_member_bp_link'), 10, 2);
			}
			
			if ( $this->bb() ) {
				add_filter('bp_core_get_user_domain', array($this, '_bp_core_get_user_domain'), 10, 2);
				add_filter('bp_core_get_userid_from_nicename', array($this, '_bp_core_get_userid'), 10, 2);
			}
			
			if ( $this->bp() ) {
				add_filter('bp_get_the_profile_field_value', array($this, '_bp_get_the_profile_field_value'), 10, 3);
			    add_filter('bp_displayed_user_domain', array($this, 'redirect_after_edit_profile_update'), 10, 1 );
			}
			
		}
		
		add_filter('insert_user_meta', array($this, '_insert_user_meta'), 999, 4); // lors de l'inscription ou l'ajout d'un nouveau utilisateur depuis le panaux d'administration (backend)
		add_action('updated_user_meta', array($this, '_updated_user_meta'), 100, 4);
		//add_action('wp_update_user', array($this, '_wp_update_user'), 100, 3);
		add_action('deleted_user', array($this, '_wp_delete_user'), 100, 1);
		
		if ( $this->options('varchar') == 'enabled' ) {
			add_filter('pre_user_nicename', array($this, 'pre__user_nicename'), 100, 1);
		}
		
		if ( $this->bp() ) {
			add_filter('bp_get_displayed_user_mentionname', array($this, 'bp_displayed_user_mentionname'), 100, 1);
			add_filter('bp_activity_get_generated_content_part', array($this, 'bp_activity_generated_content_part'), 100, 2);
			
			add_action('bp_actions', array($this, '_bp_not_boss_settings_action_general'), 10, 1);
			add_action('bp_actions', array($this, '_bb_settings_action_general'), 10, 1);
			add_action('bp_actions', array($this, '_bp_members_action_activate_account'), 10, 1);
			add_action('bp_core_signup_before_activate', array($this, '_bp_core_signup_before_activate'), 10, 1);
			add_action('admin_notices', array($this, 'page_bp_signups_message'));
			add_action('network_admin_notices', array($this, 'page_bp_signups_message'));
			add_action('template_notices', array($this, 'benrueeg_notactivated_frontend_message'));
		}
		
		add_action('user_profile_update_errors', array($this, '_user_profile_update_errors'), 100, 3);
		
		$this->foreac();
		add_filter( 'gettext', array($this, 'trans_errors'), 10, 3 );
		
		add_filter ('sanitize_user', array($this, 'func__CHARS'), 9999, 3);
		
		if ( $this->mu() ) {
			add_filter('wpmu_validate_user_signup', array($this, '__wpmubp'), 10, 1);
			add_action( 'get_header', array($this, '_get_header_activation'), 10, 1 );
			add_filter( 'pre_user_login', array( $this, 'sanitized_login_mu_activate_signup' ) );
		}
		
		if ( $this->bp() ) {
			add_filter('bp_core_validate_user_signup', array($this, '__wpmubp'), 10, 1);
			add_action('bp_signup_validate', array($this, 'benrueeg_bp_signup_validate' ));
			
			if ( ! $this->bb() )
			add_filter('bp_nouveau_feedback_messages', array($this, 'to_bp_register_form'));
		}
		
		add_action( 'register_form', array($this, 'txt_register_form') );
		
		if ( $this->bb() ) {
			add_filter( 'xprofile_validate_field', array($this, 'benrueeg_bp_xprofile_validate_nickname_value'), 9, 4 );
			add_filter( 'xprofile_validate_field', array($this, 'benrueeg_bb_xprofile_validate_character_limit_value'), 9, 3 );
			add_action( 'bp_before_register_page', array($this, 'txt_register_form' ) );
		}
		
		add_action('wp_ajax_dismissed_notice_old_user_login_invalid', array($this, 'ajax_notice'));
		
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
			if ( ! $this->benrueeg_users_table_exists() )
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
		if ( isset( $_POST['benrueeg_rue_remove_up_all_user_nicename'] ) ) { // isset: delete option of process update user_nicename per
			add_user_meta( get_current_user_id(), 'benrueeg_rue_mgs_remove_file_update_db', '1' ); // msg if process update user_nicename per is deleted
			$remove = true;
		}
		
		if ( isset( $_POST['benrueeg_rue_up_all_user_nicename'] ) ) {
			
			$limit_nm_rows_update_db = $this->_option('limit_nm_rows_update_db') ? (int) trim($_POST['BENrueeg_RUE_settings']['limit_nm_rows_update_db']) : 0;
			if ( ! $limit_nm_rows_update_db )
			$remove = true; // if "Limit the number of users to update" is empty restart the update process from the beginning
			
			$varchar_enabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'enabled' ? true : false;
			$varchar_disabled = $this->_option('varchar') && $_POST['BENrueeg_RUE_settings']['varchar'] == 'disabled' ? true : false;
			$only_not_latin_up_db_diabled = $this->_option('only_not_latin_up_db') && $_POST['BENrueeg_RUE_settings']['only_not_latin_up_db'] == 'disable' ? true : false;
			$v = $varchar_enabled && $this->author_slug_option('hash') && $only_not_latin_up_db_diabled ? true : false;
			
			if ( $this->author_slug_option('disable') || $v ) {
				if ( $this->benrueeg_users_table_exists() ) {
					$check = $this->benrueeg_users_count();
					if ( $check )
					$wpdb->query("TRUNCATE TABLE {$this->benrueeg_users_table()} ");
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
		
		$min_length = (int) $this->options('min_length');
		$max_length = (int) $this->options('max_length');
		
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
		
		// if "Enter another language below" field is empty do not save $posted_options['lang'] and $posted_options['selectedLanguage'] and $posted_options['langWlatin'] and show error message
		if ($posted_options['lang'] == 'select_lang' && trim($posted_options['selectedLanguage']) == '') {
			$old_options = $this->get_option('BENrueeg_RUE_settings');
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
				$old_options = $this->get_option('BENrueeg_RUE_settings');
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
				
				$specialChars = $this->chars_removed_from_allow_spc_cars(); // remove from "allow_spc_cars" option
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
		
		wp_enqueue_style('global_admin_css', plugin_dir_url( __FILE__ ) . '/admin/global-style.css', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script('benrueeg_rue-global-admin_js', plugin_dir_url( __FILE__ ) . '/admin/global-js.js', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script( 'benrueeg_rue-global-admin_js' );
		$global_params = array(
		);
		wp_localize_script( 'benrueeg_rue-global-admin_js', 'benrueegrue_globjs', $global_params );
		
		$page = $this->mu() ? 'toplevel' : 'settings';	
		if ( $hook_suffix  != "{$page}_page_" . BENRUEEG_RUE ) return;
		
		wp_enqueue_style('admin_css', plugin_dir_url( __FILE__ ) . '/admin/style.css', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script('BENrueeg_RUE-admin_js', plugin_dir_url( __FILE__ ) . '/admin/js.js', '', $this->BENrueeg_RUE_version());
		wp_enqueue_script( 'BENrueeg_RUE-admin_js' );
		$BENrueeg_RUE_select_params = array(
		'benrueeg_on'           => $this->options('enable') == 'on' ? true : false,
		'alert_up_if_plug_off'  => __( 'First enable the plugin and save changes, then update the users', 'restrict-usernames-emails-characters' ),
		'wait_a_little'         => _x( 'Wait a little ...', 'params_js_o', 'restrict-usernames-emails-characters' ),
		'remove_wait_a_little'  => __( 'Save Changes', 'restrict-usernames-emails-characters' ),
		'reset_succ'            => _x( 'Settings reset successfully', 'params_js_o', 'restrict-usernames-emails-characters' ),
		'msg_valid_json'        => __( 'Please upload a valid .json file', 'restrict-usernames-emails-characters' ),
		'is_mu'                 => $this->mu() ? true : false,
		'is_rtl'                => is_rtl() ? true : false,
		'msg_up_all_nicename'   => __( 'Are you sure to updating the database (user_nicename)?', 'restrict-usernames-emails-characters' ),
		'process_incomplete'    => $this->unfinished_per_up_process() ? false : true,
		'method_up_process_per' => $this->_up_process_per_method('auto') ? 'auto' : 'manual',
		'intval_up_process'     => apply_filters( 'filter_benrueeg_intval_update_process', 5 ),
		'alert_up_domain_part'  => __( 'Don&#39;t forget to update the error message for this option in the Error Messages section.', 'restrict-usernames-emails-characters' ),
		);
		wp_localize_script( 'BENrueeg_RUE-admin_js', 'BENrueeg_RUE_jsParams', $BENrueeg_RUE_select_params );
		wp_enqueue_script('jquery');
		wp_enqueue_script( 'jquery-form' );
	}
	
	function scripts() { 
		wp_register_script('BENrueeg_RUE-not_file_js',false);
		wp_enqueue_script( 'BENrueeg_RUE-not_file_js' );
		
		$BENrueeg_RUE__params = array(
		);
		
		wp_localize_script( 'BENrueeg_RUE-not_file_js', 'BENrueeg_RUE_js_Params', $BENrueeg_RUE__params );
	}
	
	function to_bp_register_form($txt){
		if ( trim( $this->options('txt_form') ) == '' ) return $txt;
		$txt['request-details']['message'] = $this->options('txt_form');
		return $txt;
	}
	
	function txt_register_form(){
		if ( trim( $this->options('txt_form') ) == '' ) return;
		$txt_form = $this->options('txt_form');
		$text = $this->bp() ? $txt_form : '<div id="benrueeg_txt_form">' . $txt_form . '</div><style type="text/css">#benrueeg_txt_form {margin-bottom:10px;}</style>';
		echo $text;
	}
	
	function VerPlugUp(){
		if ( !apply_filters( 'benrueeg_rue_filter_msg_old_ver_plug', true ) ) return;
		if ( !current_user_can(apply_filters( 'benrueeg_rue_filter_mu_cap', 'update_plugins' ))) return;
		
		$n_plugin = "".BENRUEEG_NAME."/".BENRUEEG_NAME.".php";
		$v = $this->BENrueeg_RUE_version();		
		$update_file = $n_plugin;
		$url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . $update_file), 'upgrade-plugin_' . $update_file);
		if ($v < $this->plug_last_v(BENRUEEG_NAME)) {
			echo "<div class='BENrueeg_RUE-mm411112'><div id='BENrueeg_RUE-mm411112-divtoBlink'>" . __( "You are using Version", 'restrict-usernames-emails-characters' ) . ' '. $v . ", ". __("There is a newer version, it's recommended to",'restrict-usernames-emails-characters')." <a href=".$url.">". __("update now",'restrict-usernames-emails-characters')."</a>.</div></div>";
			echo "
			<script>
			jQuery(document).ready(function(){
			jQuery('.BENrueeg_RUE-mm4111172p').delay(400).slideToggle('slow');
			}); 
			</script>";
		}
	}
	
	/**
	 * AJAX handler close dismissible notices for notice_old_user_login_invalid.
	 */
	function ajax_notice() {
		
		$notice_type = isset($_POST['notice_type']) ? sanitize_text_field($_POST['notice_type']) : '';

		if ('ajax_benrueeg_old_user_login_invalid_type' === $notice_type && get_site_transient( 'benrueeg_old_user_login_invalid' )) {
			delete_site_transient( 'benrueeg_old_user_login_invalid' );
		}
		
		if ( 'ajax_benrueeg_new_note_important_signups_1_9_type' === $notice_type && false !== get_site_transient( 'benrueeg_new_note_important_signups_1_9' ) ) {
			delete_site_transient( 'benrueeg_new_note_important_signups_1_9' );
		}

		// The end of AJAX handler
		wp_die();
	}
	
	function admin__notice() {
		
		if ( $this->can_create_users() && apply_filters( 'benrueeg_old_user_login_invalid_up_msg', true ) ) {
			$t_info = get_site_transient( 'benrueeg_old_user_login_invalid' );
			if ( $t_info && $this->options( 'varchar' ) != 'enabled' ) {
				$mli = sprintf( __( 'Your database contains a user(s): (%1$s) with invalid letters or characters. It is recommended that you enable the &#34;Solved the problem of not being able to register with certain languages&#34; option if you previously allowed these characters and then disallowed them, and you still have users with these characters in the database. Otherwise, if you never allowed these characters, simply delete these names without enabling this option.', 'restrict-usernames-emails-characters' ), implode( ', ', $t_info['user_login'] ) );
				printf( '<div id="benrueeg-notice-dismissible-notes-ajax-close" class="notice notice-error is-dismissible notice-benrueeg_old_user_login_invalid"><p>%1$s</p><p>%2$s: %3$s</p><p>%4$s</p></div>', $mli, __( "This message was generated (error appeared) on", 'restrict-usernames-emails-characters' ), $t_info['date'], BENRUEEG_NT );
			}
		}
		
		if ( get_site_transient( 'benrueeg_new_note_important_signups_1_9' ) !== false && current_user_can( apply_filters( 'benrueeg_rue_manage_settings_cap', $this->mu() ? 'manage_network_options' : 'manage_options' ) ) ) {
				$msg1 = __( 'In this version of the plugin, the error messages in the "Error Messages" section have been reset to their default values for programming reasons. You can now modify them as you wish.', 'restrict-usernames-emails-characters' );
				printf( '<div id="benrueeg-notice-dismissible-notes-ajax-close" class="notice notice-info is-dismissible notice-new_note_important_signups_1_9"><p>- %1$s</p><p>- %2$s</p><p>%3$s</p></div>', $msg1, __( "New notes to consult, in the &#34;Important!&#34; section.", 'restrict-usernames-emails-characters' ), BENRUEEG_NT );
		}
		
		$store_limit = $this->get_option('benrueeg_nicename_store_all_users_id'); // option (store users id updated)
		$updb_completed = $this->get_option('benrueeg_n_store_all_completed_ids'); // option: if the update database precess is complete
		$getcounterror = $this->get_option('benrueeg_nicename_error_store_all_users_id');
		
		if ( $updb_completed && $this->is_options_page() && $this->can_create_users() ) {
			
			if ( $store_limit ) {
				$not = count( $store_limit ) - (int) $getcounterror;
				} else {
				$not = (int) $updb_completed['count_rows_updated'] - (int) $updb_completed['count_error_update'];
			}
			echo '<style>#setting-error-settings_updated {display:none;}</style>';
			
			if ($store_limit && (int) $updb_completed['count_error_update'] == 0 && !$getcounterror) {
				
				$updating = $this->_up_process_per_method('manual') ? __( 'Continue updating...', 'restrict-usernames-emails-characters' ) : '<span id="BENrueeg_countdown_up_process">' . apply_filters( 'filter_benrueeg_intval_update_process', 5 ) . '</span>';
				$msgContinue = (count($store_limit) >= $this->count_users()) ? __( 'Finished', 'restrict-usernames-emails-characters' ) : $updating;	
				
				$n = sprintf( _n( '%s user were updated from', '%s users were updated from', count($store_limit), 'restrict-usernames-emails-characters' ), '<span style="font-family:tahoma; font-weight:700;">' . count($store_limit) . '</span>' );
				$message = sprintf( '%s %s %d%s', 
				'<span style="font-family:DroidKufiRegular,Tahoma,sans-serif,Arial; font-size:22px;">',
				$n . '<span style="font-family:tahoma; font-weight:700;">',
				$this->count_users(),
				'</span></span>'
				) .' <span style="font-family:tahoma; font-size:15px;">---> '. $msgContinue . '</span>';
				
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
				sprintf( __( '%d were updated and %d failed from %d user(s)', 'restrict-usernames-emails-characters' ), $not, $store_limit ? $getcounterror : (int) $updb_completed['count_error_update'], $this->count_users() ),
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
		
		if ( ! $store_limit ) {
			$this->delete_option('benrueeg_n_store_all_completed_ids');	// option: if the update database precess is complete
		}
		
		$cap = $this->mu() ? 'manage_network_options' : 'manage_options';
		if ( ! current_user_can(apply_filters( 'manage_setts_cap_BENrueeg_RUE', $cap ) ) ) return;
		
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
			
		}
		
		if ( $this->options('lang') != 'default_lang' && $this->options('varchar') != 'enabled' ) {
			$message = __( 'If you choose a language other than the default language &#34;Choose language (characters) in username&#34;, option &#34;Solved the problem of not being able to register with certain languages&#34; must be activated', 'restrict-usernames-emails-characters' );
			printf( '<div class="%1$s"><p style="%2$s">%3$s</p><p style="padding-top:0;font-size:14px;">%4$s</p></div>', esc_attr( 'notice notice-error' ), esc_html( 'font-family:DroidKufiRegular,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;font-size:16px;padding-bottom:0;' ), esc_html( $message ), BENRUEEG_NT );
		}
		
		if ( $this->get_option( 'benrueeg_rue_1_7____notice' ) ) {
			$class = 'notice notice-warning';
			$glob = $this->mu() ? network_admin_url( 'admin.php' ) : admin_url( BENRUEEG_O_G );
			$url = $glob . "?page=restrict_usernames_emails_characters&benrueegrue1-7-dismissed";
			$title = __( 'Restrict Usernames Emails Characters', 'restrict-usernames-emails-characters' );
			$message = __( 'In this version of the plugin, the method of saving the nicename (author slug) when registering a new user or updating their data has been changed. Therefore, if you are using the &#34;Solved the problem of not being able to register with certain languages&#34; option, you now need to: First: read how the plugin works, at the bottom of this page, and second: update the database: &#34;update all users with just one click or in batches&#34;.', 'restrict-usernames-emails-characters' );
			printf( '<div style="font-family:DroidKufiRegular,Tahoma,sans-serif,Arial; font-size:15px !important; line-height:1.7 !important; background:#dacbcb;" class="%1$s"><p style="font-size:15px !important; font-style:italic; font-weight:bold; letter-spacing:1px; line-height:1 !important; color:#002a54;">%2$s</p><p style="font-size:15px !important; line-height:1.7 !important; color:#002a54;">%3$s <a href="%4$s">%5$s</a></p></div>', esc_attr( $class ), $title, $message, $url,  __( 'hide', 'restrict-usernames-emails-characters' ) ); 
		}
		
		if ( ! $this->is_options_page() || (!$this->mu() && get_option('users_can_register') == '1') || ($this->mu() && in_array(get_site_option('registration'), array('user','all'))) ) return;
		
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
		
		if ( ! $this->mu_bp() || ($this->bp() && isset($_POST['user_login']) && ! $this->bb()) ) {
			add_filter('validate_username', array($this,'func_validation'), 10, 2);
			add_filter( 'user_registration_email', array( $this, 'user__email' ), 10, 1 );
		}
	}
	
	function user__email( $email ) {
		
		$_email = $this->options("emails_limit_strtolower") == 'strtolower' ? strtolower(trim($email)) : trim($email);
		$__email = $this->options("email_domain_strtolower") == 'strtolower' ? strtolower(trim($email)) : trim($email);
		
		if ( $email == '' ) $this->empty__user_email = true;
		if ( ! is_email( $email ) ) $this->invalid__user_email = true;
		if ( email_exists( $email ) ) $this->exist__user_email = true;
		
		$list_emails = $this->options("emails_limit_strtolower") == 'strtolower' ? strtolower($this->options('emails_limit')) : $this->options('emails_limit');
		$list_emails = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $list_emails))));
		if ( in_array( $_email, $list_emails ) && $email != '' && ! email_exists( $email ) ){
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
		
		if ( $n && trim($domain) != '' ) {
			$this->restricted_domain_emails = true;
		}
		
		return $email;		
	}
	
	function _unset($errors, $code ) {
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
			
			if ( $text == '<strong>Error:</strong> Could not register you&hellip; please contact the <a href="mailto:%s">site admin</a>!' && $err_registration_user && ! $this->mu_bp() )
			$translations = str_replace("%eml%", $filter_err, __( $err_registration_user, 'restrict-usernames-emails-characters' ));
			
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
		$list_selt_lang_b = $list__selt_lang != '' ? explode( ',', $list__selt_lang ) : array();
		
		$n = array();
		foreach ($list_selt_lang_b as $val){
			//if (!in_array($val, $this->scriptNames())) continue;
			if (@preg_match_all('/\p{'. $val .'}+/u', $username, $matches) === false) continue;
			//if (preg_match_all('/\\\p{'. $val .'}+/u', $username, $arr))
			//if (@preg_match_all('/\p{'. $val .'}+/u', $username, $arr))
			if (! empty($list__selt_lang) && preg_match_all('/\p{'. $val .'}+/u', $username, $matches))
			$n[] = '\p{' . trim($val) . '}';
		}
		
		$list_selt_lang = ! empty($list__selt_lang) && $n ? implode('', $n) : '';
		
		$wLatin = $this->options('langWlatin') == 'w_latin_lang' ? 'A-Za-z' : '';
		
		$pattern = '/[\x{064B}-\x{065F}]/u'; // منع الحركات إذ اخترنا اللغة العربية ضمن لغات أخرى
		$remp = array($pattern, '[ـ]');
		if ( preg_match('/ـ/', $allow_spc_cars) ) {
			unset($remp[1]);
		}
		
		$default_lang_AS = $allow_spc_cars ? preg_replace('|[^A-Za-z0-9 _.\-@\\'. $list_chars .']|u', '', $username) : preg_replace('|[^A-Za-z0-9 _.\-@]|u', '', $username);
		$all_lang_AS = $allow_spc_cars ? preg_replace('|[^\p{L}0-9 _.\-@\\'. $list_chars .'\x80-\xFF]|u', '', $username) : preg_replace('|[^\p{L}0-9 _.\-@\x80-\xFF]|u', '', $username);
		
		$arab_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Arabic}0-9 _.\-@\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Arabic}0-9 _.\-@]|u', '', $username);
		$arab_lang_AS = preg_replace( $remp, '', $arab_lang_AS ); // منع الحركات من اللغة العربية
		
		$cyr_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Cyrillic}0-9 _.\-@\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Cyrillic}0-9 _.\-@]|u', '', $username);
		
		$arab_cyr_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin .'\p{Arabic}\p{Cyrillic}0-9 _.\-@\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin .'\p{Arabic}\p{Cyrillic}0-9 _.\-@]|u', '', $username);
		$arab_cyr_lang_AS = preg_replace( $remp, '', $arab_cyr_lang_AS ); // منع الحركات من اللغة العربية
		
		$selected_lang_AS = $allow_spc_cars ? preg_replace('|[^'. $wLatin . $list_selt_lang .'0-9 _.\-@\\'. $list_chars .']|u', '', $username) : preg_replace('|[^'. $wLatin . $list_selt_lang .'0-9 _.\-@]|u', '', $username);
		
		if ( ! empty($list__selt_lang) && preg_match('/Arabic/', $list__selt_lang) ) {
			$selected_lang_AS = preg_replace( $remp, '', $selected_lang_AS );
		}
		
		return array($default_lang_AS,$all_lang_AS,$arab_lang_AS,$cyr_lang_AS,$arab_cyr_lang_AS,$selected_lang_AS);
	}
	
	function lang__mu($username) {
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_chars_ = array_map('trim', explode(PHP_EOL, $allow_spc_cars));
		$list_chars = implode('\\', $list_chars_);
		
		$list__selt_lang = $this->options('selectedLanguage');
		$list_selt_lang_b = $list__selt_lang != '' ? explode( ',', $list__selt_lang ) : array();
		
		$n = array();
		foreach ($list_selt_lang_b as $val){
			//if (!in_array($val, $this->scriptNames())) continue;
			if (@preg_match('/\p{'. $val .'}+/u', '') === false) continue;
			//if (preg_match_all('/\\\p{'. $val .'}+/u', $username, $arr))
			if (! empty($list__selt_lang) && preg_match_all('/\p{'. $val .'}+/u', $username, $matches))	
			$n[] = '\p{' . trim($val) . '}';
		}
		
		$list_selt_lang = ! empty($list__selt_lang) && $n ? implode('', $n) : '';
		
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
	
	function get_mu_lang__( $username ) {
		
		$r_ = $this->lang__mu($username);
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
	function bp() { // buddypress or buddyboss
		return function_exists('bp_is_active');	
	}
	
	function bb() {
		return ( is_plugin_active( 'buddyboss-platform/bp-loader.php' ) || function_exists('bp_core_set_bbpress_buddypress_active') );
	}
	
	function bp_not_boss() {
		return ( $this->bp() && ! $this->bb() );	
	}
	
	function mu_bp() {
		return ( $this->mu() || $this->bp() );
	}
	
	function mubp() {
		return ( $this->mu() && $this->bp() );
	}
	
	function only_mu() {
		return ( $this->mu() && ! $this->mubp() );	
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
	
	function pre__user_nicename( $user_nicename ) {
		return rawurldecode($user_nicename);
	}
	/*
		function user__register( $user_id, $userdata ) {
		global $wpdb;
		
		if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		$this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
		
		if ($this->bp() && apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false ))
		return;
		
		if ( $this->updb_user_nicename( $user_id ) ) {
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
		}
		}
	*/

	function _insert_user_meta( $meta, $user, $update, $userdata ) {
		global $wpdb;
		
		$user_id = $user->ID;
		/*
			C'est pour eviter que nickname en characters non latin soit supprimer en utilisant sanitize_title car cette dernière est utilisé dans cette fonction "bb_validate_user_nickname_on_user_register" par baddyboss
			les lignes ci-dessous sont utilisés par:
			add_filter( 'insert_user_meta', 'bb_validate_user_nickname_on_user_register', 10, 3 ); dans bp-xprofile-filters.php
		*/
		if ( $this->bb() && ! $update && $this->options('varchar') == 'enabled' ) {
			if ( isset( $meta['nickname'] ) && ! empty( $meta['nickname'] ) ) {
				$meta['nickname'] = rawurldecode( sanitize_title( $meta['nickname'] ) );
				} elseif ( isset( $user->user_nicename ) && ! empty( $user->user_nicename ) ) {
				$meta['nickname'] = rawurldecode( sanitize_title( $user->user_nicename ) );
				} elseif ( isset( $user->user_login ) && ! empty( $user->user_login ) ) {
				$meta['nickname'] = rawurldecode( sanitize_title( $user->user_login ) );
			}
		}
		
		if ( apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ) )
		$this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
		
		if ( ! $update && ( $this->options('varchar') != 'enabled' || apply_filters( 'benrueeg_rue_wp_update_user', false ) ) )
		return $meta;
		
		//$cond = $update && ! $this->sanitized_containts_non_latin( $user->user_login ) && $this->options('langWlatin') == 'only_lang' && $this->options('lang') != 'default_lang' ? true : false;
		
		//if ( $cond == false && ! $this->sanitized_containts_non_latin( $user->user_nicename ) && ! $this->bb() ) // if user_nicename (Characters) is latin return
		//return $meta; // , $update ? true : ''
		if ( ! $update && ( ! $this->sanitized_containts_non_latin( $user->user_nicename ) ) ) // if user_nicename (Characters) is latin return
		return $meta;
		
		$user_nicename = apply_filters( 'benrueeg_rue_wp_update_user_user_nicename', $this->updb_user_nicename( $user_id, $update ? true : '' ), $user_id );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", sanitize_title( $user_nicename ), $user_id ) );
		
		return $meta;
	}
	
	function _updated_user_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		global $wpdb;
		
		if ($meta_key != 'nickname' || $this->options('author_slug') != 'nickname')
		return;
		
		$user_nice_name = rawurldecode(sanitize_title($meta_value));
		$user_nice_name = mb_substr( $user_nice_name, 0, 100 );
		$user_nice_name = $this->duplicated_usernacename('benrueeg_users', $object_id, $user_nice_name);
		
		if ( apply_filters( 'benrueeg_rue_updated_user_meta_nice_name', true ))
		$this->update_user_nice_name($object_id, $user_nice_name);
	}
	/*
		function _wp_update_user( $user_id, $userdata, $userdata_raw ) {
		global $wpdb;
		
		if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		$this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
		
		if ($this->options('varchar') != 'enabled')
		return;
		
		if ( apply_filters( 'benrueeg_rue_wp_update_user', false ) )
		return;
		
		if ( ! $this->sanitized_containts_non_latin($userdata['user_nicename']) ) // if user_nicename (Characters) is latin return
		return;
		
		$user_nicename = apply_filters( 'benrueeg_rue_wp_update_user_user_nicename', $this->updb_user_nicename( $user_id ), $user_id );
		$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", sanitize_title( $user_nicename ), $user_id ) );
		}
	*/
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
	
	function _user_profile_update_errors( $errors, $update, $user ) {
		global $wpdb, $pagenow;
		
		$username_nk = ! $update && $this->bb() && ! $this->mu() ? __( 'Username' ) : '';
		
		$min_length = (int) $this->options('min_length');
		$max_length = (int) $this->options('max_length');
		
		$er_name = $this->error_message( $this->mu_bp() ? 'err_mp_names_limit' : 'err_names_limit', $username_nk ); 
		$er_min = $this->error_message( $this->mu_bp() ? 'err_mp_min_length' : 'err_min_length', $username_nk );
		$filter_err_min_length = apply_filters( 'err_mp_min_length_mubp_BENrueeg_RUE', $er_min );
		$er_max = $this->error_message( $this->mu_bp() ? 'err_mp_max_length' : 'err_max_length' );
		$filter_err_max_length = apply_filters( 'err_mp_max_length_mubp_BENrueeg_RUE', $er_max );
		$er_digits_less = $this->error_message( $this->mu_bp() ? 'err_mp_digits_less' : 'err_digits_less', $username_nk );
		$er_space = $this->error_message( $this->mu_bp() ? 'err_mp_spaces' : 'err_spaces', $username_nk );	 
		$er_just_num = $this->error_message( $this->mu_bp() ? 'err_mp_names_num' : 'err_names_num', $username_nk );	 
		$er_illegal_name = $this->error_message( $this->mu_bp() ? 'err_mp_spc_cars' : 'err_spc_cars', $username_nk );
		$er_name_not_email = $this->error_message( $this->mu_bp() ? 'err_mp_name_not_email' : 'err_name_not_email' );
		$er_uppercase = $this->error_message( $this->mu_bp() ? 'err_mp_uppercase' : 'err_uppercase', $username_nk );
		$er_start_end_space = $this->error_message( $this->mu_bp() ? 'err_mp_start_end_space' : 'err_start_end_space', $username_nk );
		$er_username_empty = $this->error_message( $this->mu_bp() ? 'err_mp_empty' : 'err_empty', $username_nk );
		$er_exist_login = $this->error_message( $this->mu_bp() ? 'err_mp_exist_login' : 'err_exist_login', $username_nk );
		
		$er_empty_user_email = $this->error_message( $this->mu_bp() ? 'err_mp_empty_user_email' : 'err_empty_user_email' );
		$er_invalid_user_email = $this->error_message( $this->mu_bp() ? 'err_mp_invalid_user_email' : 'err_invalid_user_email' );
		$er_exist_email = $this->error_message( $this->mu_bp() ? 'err_mp_exist_user_email' : 'err_exist_user_email' );
		$er_emails_limit = $this->error_message( $this->mu_bp() ? 'err_mp_emails_limit' : 'err_emails_limit' );
		$pr = $this->error_message( ! $this->mu_bp() ? 'err_partial' : ( $this->only_mu() ? 'err_mp_partial' : 'err_bp_partial' ), $username_nk, $user->user_login );
		$err_signup_admin_login_exists = $this->error_message( 'err_mp_admin_signup_username_exists' );
		$err_signup_admin_email_exists = $this->error_message( 'err_mp_admin_signup_email_exists' );
		$err_signup_email_exists = $this->error_message( 'err_mu_signup_email_exists' );
		$err_bp_signup_email_exists = $this->error_message( 'err_bp_signup_email_exists' );
		$signup_email = $this->signup_email_exists( $user->user_email );
		
		$bb_restricted_email = $validate_err0 = $validate_err = $validate_err2 = $validate_err02 = false;
		
		if ( ! $update ) { // not mu
			
			$this->func_validation( true, $user->user_login );
			$user_login_err = $valid_err = $username_empty = $space_s_e_m = $exists1 = false;
			
			if ( isset( $_POST['user_login'] ) && '' === trim( $_POST['user_login'] ) ) {
				$user_login_err = $username_empty = true;
				$errors->add( 'benrueeg_user_login', $er_username_empty );
			}
			
			// delete signup_username
			if ( isset( $_POST['user_login'] ) && $this->signup_username_exists( $_POST['user_login'], 1 ) && apply_filters( 'benrueeg_rue_delete_signup_item_activeted', true ) ) {
				$wpdb->delete( "{$this->prefix()}signups", array( 'user_login' => $_POST['user_login'], 'active' => 1 ) );
			}
			
			if ( $this->signup_username_exists( $user->user_login, 1 ) ) {
				if ( $this->mu_bp() && apply_filters( 'benrueeg_rue_delete_signup_item_activeted', true ) ) {
					$wpdb->delete( "{$this->prefix()}signups", array( 'user_login' => $user->user_login, 'active' => 1 ) );
				}
			}
			// delete signup_username	
			
			if ( isset( $_POST['user_login'] ) && $this->func_space_s_e_m($_POST['user_login']) && ! $user_login_err ) {
				$space_s_e_m = $validate_err = true;
				$this->_unset( $errors,'user_login' );
				$errors->add( 'benrueeg_user_login', $er_start_end_space );
			} elseif ( isset( $_POST['user_login'] ) && ! $this->benrueeg_validate_username( $_POST['user_login'] ) && '' ==! trim( $_POST['user_login'] ) && ! $space_s_e_m ) {
				$user_login_err = $valid_err = $validate_err = true;					
				$errors->add( 'benrueeg_user_login', $er_illegal_name );
			}
				
			if ( ! $validate_err ) {
				
			// signup_username
			    if ( $this->mu_bp() ) {
					if ( $this->signup_username_exists( $user->user_login ) ) {
						$this->_unset( $errors,'user_name' );
						$this->_unset( $errors,'user_email' );
					}
					
					if ( $this->username_is_pendding_signup( $user->user_login ) ) {
						$this->_unset( $errors,'user_name' );
						$user_login_err = $valid_err = $validate_err2 = $exists1 = true;
						$errors->add( 'benrueeg_user_login', $err_signup_admin_login_exists );
					}
				}
			// signup_username	
				
				if ( ( username_exists( $user->user_login ) ) || ( $this->nickname_exists( $user->user_login ) && $this->bb() ) ) {
					$user_login_err = $valid_err = $validate_err2 = true;
					if ( $this->username_is_pendding_signup( $user->user_login ) == false ) {
						$errors->add( 'benrueeg_user_login', $er_exist_login );
					}
				}
				
			}
				
			if ( ! ( $validate_err || $validate_err2 ) ) {
				
				if ( ( ( $this->mu_bp() && preg_match('/ /', $user->user_login) ) || $this->space ) && ! $username_empty && ! $space_s_e_m ) {
					$errors->add( 'benrueeg_user_login', $er_space );
				}
				
				if ( $this->valid_num ) {
					$errors->add( 'benrueeg_user_login', $er_just_num );
				}
				
				if ( $this->valid_partial ) {
					$errors->add( 'benrueeg_user_login', $pr );
				}
				
				if ( $this->name_not__email && ! $this->mu() ) {
					$errors->add( 'benrueeg_user_login', $er_name_not_email );
				}
				
				if ( $this->invalid_names ) {
					$errors->add( 'benrueeg_user_login', $er_name );
				}
				
				if ( $this->length_min ) {
					$errors->add( 'benrueeg_user_login', $filter_err_min_length );
				}
				
				$nickname_max_length = apply_filters( 'xprofile_nickname_max_length', 32 );
				if ( $this->bb() && empty( $max_length ) && mb_strlen( $user->user_login ) > $nickname_max_length ) {
					$errors->add( 'benrueeg_user_login', str_replace( array( '%uname%', '%max%' ), array( __( 'Username' ), $nickname_max_length ), $filter_err_max_length ) );
					} elseif ( $this->length_max ) {
					$errors->add( 'benrueeg_user_login', str_replace( array( '%uname%', '%max%' ), array( __( 'Username' ), $max_length ), $filter_err_max_length ) );
				}
				
				if ( $this->valid_num_less && ! preg_match( '/^\+?\d+$/', $user->user_login ) ) {
					$errors->add( 'benrueeg_user_login', $er_digits_less );
				}
				
				if ( $this->uppercase_names ) {
					$errors->add( 'benrueeg_user_login', $er_uppercase );
				}
				
			}
			
			if ($valid_err && $this->bb())
			$this->_unset( $errors,'nickname_exists' );
			
			if ($user_login_err)
			$this->_unset( $errors,'user_login' );
			
			// Checking email address.
			if ( empty( $user->user_email ) ) {
			    $validate_err0 = true;
				$this->_unset( $errors,'empty_email' );
				$errors->add( 'empty_email', $er_empty_user_email, array( 'form-field' => 'email' ) );
			} elseif ( ! is_email( $user->user_email ) ) {
				$bb_restricted_email = $validate_err0 = true;
				$this->_unset( $errors,'invalid_email' );
				$errors->add( 'invalid_email', $er_invalid_user_email, array( 'form-field' => 'email' ) );
			}
			
			
			if ( ! $validate_err0 ) {
				
			// signup_email
			    if ( $this->mu_bp() ) {
					if ( $signup_email ) {
						$this->_unset( $errors,'email_exists' );
						if ( $this->signup_email_exists( $user->user_email, 1 ) && apply_filters( 'benrueeg_rue_delete_signup_item_activeted', true ) ) {
							$wpdb->delete( "{$this->prefix()}signups", array( 'user_email' => $user->user_email, 'active' => 1 ) );
						}
					}
					
					if ( $this->email_is_pendding_signup( $user->user_email ) ) {
						$exists1 = $validate_err02 = true;
						$this->_unset( $errors,'email_exists' );
						$errors->add( 'email_exists', $err_signup_admin_email_exists, array( 'form-field' => 'email' ) );
					}
				}
			// signup_email	
				
				if ( email_exists( $user->user_email ) && $this->email_is_pendding_signup( $user->user_email ) == false ) {
					$validate_err02 = true;
					$this->_unset( $errors,'email_exists' );
					$errors->add( 'email_exists', $er_exist_email, array( 'form-field' => 'email' ) );
				}
				
			}
				
			if ( $this->registration_errors_in_backend() && ! ( $validate_err0 || $validate_err02 ) ) {
				$this->user__email( $user->user_email );
				
				if ( $this->restricted_emails || $this->restricted_domain_emails ) {
					$errors->add( 'email_exists', $er_emails_limit, array( 'form-field' => 'email' ) );
				}
			}
			
		}
		
		if ( $update ) {
			
			$old_user_info = get_userdata($user->ID);
			
			// method 1
			/*
				pour résoudre le problème du user login invalid lors du mise à jour du profil d'un utilisateur
				et le problème du user_nicename doublé parce qui l'est désinfecté (sanitized_user_login)
				dans wp-includes/user.php :
				$sanitized_user_login = sanitize_user( $userdata['user_login'], true );
				$pre_user_login = apply_filters( 'pre_user_login', $sanitized_user_login );
				$user_login = trim( $pre_user_login );
				$user_nicename_check = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->users WHERE user_nicename = %s AND user_login != %s LIMIT 1", $user_nicename, $user_login ) );
			*/
			//$userCond = $old_user_info->user_login === $user->user_login && '' !== $old_user_info->user_login && '' === sanitize_user( $old_user_info->user_login, true ) ? true : false;
			$userCond = $old_user_info->user_login === $user->user_login && '' !== $old_user_info->user_login ? true : false;

			if ( $userCond && ! $this->benrueeg_validate_username( $old_user_info->user_login ) ) {
				
				$this->glob_user_info = $old_user_info->user_login;
				remove_all_filters ('pre_user_login');
				add_filter( 'pre_user_login', array( $this, '_sanitized_user_login_update' ) );
				
				$this->glob_nicename_info = $old_user_info->user_nicename;
				remove_all_filters ('pre_user_nicename');
				add_filter( 'pre_user_nicename', array( $this, 'pre__user_nicename' ), 100, 1 );
				add_filter( 'pre_user_nicename', array( $this, '_sanitized_user_nicename_update' ) );
				
				if ( 'enabled' !== $this->options('varchar') && apply_filters( 'benrueeg_old_user_login_invalid_up_msg', true ) ) {
					/*
					  * message for administrator if user login is invalid when the user profile or edit user is updated from the backend (if varchar is disabled),
					  * L'activation de l'option varchar empêche uniquement le message de réapparaître une fois que l'administrateur a été averti qui il y'à un nom d'utilisateur (user login) invalide. 
					*/
					$this->old_user_login_invalid_message( $old_user_info->user_login );
				}
				
			}
			// method 1
			
			/*
				or method 2
				
				$userCond = $old_user_info->user_login === $user->user_login && '' !== $old_user_info->user_login;
				//$user_invalid = $userCond && '' === sanitize_user( $old_user_info->user_login, true ) ? true : false;
				$user_invalid = $userCond && ! $this->benrueeg_validate_username( $old_user_info->user_login ) ? true : false;
				//$user_valid = $userCond && $this->benrueeg_validate_username( $old_user_info->user_login ) ? true : false;
				
				$nickCond = $old_user_info->nickname === $user->nickname && '' !== $old_user_info->nickname;
				$nick_invalid = $nickCond && ! $this->benrueeg_validate_username( $old_user_info->nickname ) ? true : false;
				//$nick_valid = $nickCond && $this->benrueeg_validate_username( $old_user_info->nickname ) ? true : false; 
				
				$nickChanged = $old_user_info->nickname !== $user->nickname && '' !== $user->nickname ? true : false;
				
				if ( ( $user_invalid && ! $this->bb() ) ||
				( $this->bb() && ( $user_invalid || $nick_invalid ) && ( ! $nickChanged || ( $nickChanged && $this->benrueeg_validate_username( $user->nickname ) ) ) )
				) {
				remove_all_filters ('sanitize_user');
				add_filter ('sanitize_user', array($this, 'func__CHARS_update'), 9999, 3);
				}
			*/
			
			if ( '' === $user->user_login ) {
				$this->_unset( $errors,'user_login' );
				$errors->add( 'benrueeg_user_login', $er_username_empty );
			}
			
			if ( '' === $user->nickname && ! $this->bb() ) {
				if ( $this->bp() ) {
					$err__empty = str_replace( '%uname%', __( 'Nickname' ), $this->options_Tw('err_mp_empty') != '' ? __( $this->options_Tw('err_mp_empty'), 'restrict-usernames-emails-characters' ) : __( 'Please enter a %uname%.', 'restrict-usernames-emails-characters' ) );
					} else {
					$err__empty = str_replace( '%uname%', __( 'Nickname' ), $this->options_Tw('err_empty') != '' ? __( $this->options_Tw('err_empty'), 'restrict-usernames-emails-characters' ) : __( '<strong>ERROR</strong>: Please enter a %uname%.', 'restrict-usernames-emails-characters' ) );
				}
				$this->_unset( $errors,'nickname' );
				$errors->add( 'benrueeg_user_login', $err__empty );
			}
			
			$err_bp_signup_login_exists = $this->error_message( 'err_bp_signup_username_exists', __( 'Nickname' ) );
			
			if ( $this->bb() ) {
				if ( $this->username_is_pendding_signup( $user->nickname ) ) {
					$errors->add( 'benrueeg_user_login', $err_bp_signup_login_exists );
				} elseif ( $this->nickname_exists( $user->nickname, $user->ID ) ) {
					$errors->add( 'benrueeg_user_login', $er_exist_login );
				}
			}
			
		}
		
		if ( $this->bb() && isset( $user->nickname ) ) {
			
			$old_nickname = get_user_meta($user->ID, 'nickname', true);
			$new_nickname = $user->nickname;
			
			$this->_unset( $errors,'nickname' );
			$this->_unset( $errors,'nickname_exists' );
			
			/* Je n'ai pas utilisé cette restriction ni des restrictions similaires car un nickname n'est pas le nom d'utilisateur lors de la mise à jour des données d'un membre existant, au contraire, le nickname est identique au nom d'utilisateur lors de l'inscription d'un nouveau membre, donc il est soumis à toutes les restrictions comme: valid_partial , uppercase_names, length_min etc ...
				$this->func_validation( true, $new_nickname );
				
				if ( $this->invalid_names )
				$errors->add('user_name', $er_name);
			*/
			
			if ( '' === $new_nickname ) {
				$this->_unset( $errors,'user_login' );
				$errors->add( 'benrueeg_user_login', $er_username_empty );
			} elseif ( $old_nickname != $new_nickname && ! $this->benrueeg_validate_username( $new_nickname ) ) {
				$errors->add('user_name', $er_illegal_name);
				} elseif ( $old_nickname != $new_nickname && preg_match('/ /', $new_nickname) ) {
				$errors->add('user_name', $er_space);
			}
			
			$er_min = $this->error_message( 'err_mp_min_length', __( 'Nickname' ) );
			$filter_err_min_length = apply_filters( 'err_mp_min_length_mubp_BENrueeg_RUE', $er_min );
			
			$length_space = $this->options('length_space');
			$mbstrlen = preg_match( '/^\+\d+$/', $new_nickname ) ? mb_strlen( $new_nickname ) - 1 : mb_strlen( $new_nickname );
			$strlen = $mbstrlen - substr_count($new_nickname , ' ');
			
			if ( $old_nickname != $new_nickname && $strlen < $min_length && ! empty( $min_length ) && ! empty( $new_nickname ) ) {
				$errors->add('user_name', $filter_err_min_length);
			}
			
			$v_max_length = $strlen > $max_length || $strlen > 60;
			
			$nickname_max_length = apply_filters( 'xprofile_nickname_max_length', 32 );
			if ( $old_nickname != $new_nickname && empty( $max_length ) && mb_strlen( $new_nickname ) > $nickname_max_length ) {
				$errors->add('user_name', str_replace( array( '%uname%', '%max%' ), array( __( 'Nickname' ), $nickname_max_length ), $filter_err_max_length ) );
			} elseif ( $old_nickname != $new_nickname && $v_max_length && ! empty( $max_length ) && ! empty( $new_nickname ) ) {
				$errors->add('user_name', str_replace( array( '%uname%', '%max%' ), array( __( 'Nickname' ), $max_length ), $filter_err_max_length ) );
			}
			
		}
		
		if ( $update ) {
		
			$c_email = get_user_meta( get_current_user_id(), '_new_email', true );
			if ( 'profile.php' === $pagenow && $c_email ) {
				$user_user_email = $c_email['newemail'];
			} else {
				$user_user_email = $user->user_email;
			}
		
			$owner_id  = email_exists( $user->user_email );
			$_owner_id = email_exists( $user_user_email ); // newemail
			$exists = false;
			
			// Checking email address.
			if ( empty( $user->user_email ) ) {
				$this->_unset( $errors,'empty_email' );
				$bb_restricted_email = true;
				$errors->add( 'empty_email', $er_empty_user_email, array( 'form-field' => 'email' ) );
			} elseif ( ! is_email( $user_user_email ) ) {
				$this->_unset( $errors,'invalid_email' );
				$bb_restricted_email = true;
				$errors->add( 'invalid_email', $er_invalid_user_email, array( 'form-field' => 'email' ) );
			} else {
		
				if ( $this->mu_bp() ) {
				
				    $this->delete_signups_by_user_email( $user_user_email, $this->_unset( $errors, 'email_exists' ) );
					
					if ( $this->email_is_pendding_signup( $user_user_email ) ) {

						if ( ! ( $_owner_id && $_owner_id === $user->ID ) ) {
							$this->_unset( $errors,'email_exists' );
							
							if ( $this->mu() ) {
								$errors->add( 'email_exists', ( 'profile.php' === $pagenow && ! $this->can_create_users() ) ? $err_signup_email_exists : $err_signup_admin_email_exists, array( 'form-field' => 'email' ) );
							} else {
								$errors->add( 'email_exists', $this->can_create_users() ? $err_signup_admin_email_exists : $err_bp_signup_email_exists, array( 'form-field' => 'email' ) );
							}
							
							if ( get_user_meta( get_current_user_id(), '_new_email', true ) ) {
								delete_user_meta( get_current_user_id(), '_new_email' );
							}
						}
						
						$exists = true;
					}
				
				}
				
				if ( $owner_id && $owner_id !== $user->ID && $this->email_is_pendding_signup( $user_user_email ) == false ) {
					$this->_unset( $errors,'email_exists' );
					$errors->add( 'email_exists', $er_exist_email, array( 'form-field' => 'email' ) );
				}
		
				if ( ! $exists && $this->registration_errors_in_backend() && ( ! $_owner_id || ( $_owner_id !== $owner_id ) ) ) {
					$this->user__email( $user_user_email );
					
					if ( $this->restricted_emails || $this->restricted_domain_emails ) {
						$errors->add( 'email_exists', $er_emails_limit, array( 'form-field' => 'email' ) );
						if ( get_user_meta( get_current_user_id(), '_new_email', true ) ) {
							delete_user_meta( get_current_user_id(), '_new_email' );
						}
					}
				}
					
			}
			
		}
		
		if ( $bb_restricted_email && $this->bb() ) {
			$this->_unset( $errors,'user_email' );
			$this->remove_bb_validate_restricted_email();
		}
		
	}
	
	function user__nicename_mu( $user_id) {
		global $wpdb;
		
		if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		$this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
		
		if (apply_filters( 'benrueeg_turn_off_filter_user_nicename_mu', false )) 
		return; // to turn off //add_filter( 'benrueeg_turn_off_filter_user_nicename_mu', '__return_true' );
		
		if ( $this->updb_user_nicename( $user_id ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
		}
	}
	
	function bp_signup_user( $user_id  ) {
		global $wpdb;
		
		if (apply_filters( 'benrueeg_rue_wp_update_user_nice_name', true ))
		$this->up_benrueeg_users_nicename( $user_id ); // in benrueeg_users
		
		if (apply_filters( 'benrueeg_filter_bp_activated_turn_off_user_nicename', false )) return; // to turn off //add_filter( 'benrueeg_filter_bp_activated_turn_off_user_nicename', '__return_true' );
		
		if ( $this->updb_user_nicename( $user_id ) ) {
			$wpdb->query( $wpdb->prepare( "UPDATE $wpdb->users SET `user_nicename` = %s WHERE `ID` = %d", $this->updb_user_nicename( $user_id ), $user_id ) );
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
					printf( '<style type="text/css">#setting-error-settings_updated, .benrueeg_rue_process_msg_up_db up_process.settings_updated {display:none;} #restrict-usernames-updb-msg {background:#cfdbcbd6 !important;}</style><div class="benrueeg_rue_process_msg_up_db up_process settings_updated"><p>%1$s</p></div>', __( 'Settings saved successfully', 'restrict-usernames-emails-characters' ) );
				}
			}
			
			if ( $process_update && $this->is_options_page() ) {
				printf( '<style type="text/css">#setting-error-settings_updated, .benrueeg_rue_process_msg_up_db up_process.settings_updated {display:none;} #restrict-usernames-updb-msg {background:#cfdbcbd6 !important;}</style><div class="benrueeg_rue_process_msg_up_db up_process settings_updated"><p>%1$s</p></div>', __( 'File deleted successfully', 'restrict-usernames-emails-characters' ) );
				delete_user_meta( $cur_userid, 'benrueeg_rue_mgs_remove_file_update_db' ); // msg if process update user_nicename per is deleted
			}
			
			} else {
			
			$this->delete_option('benrueeg_nicename_msg_only_store_all_ids'); // option: لإظهار رسالة تحديث قاعدة البيانات فقط وليس رسالة حفظ الإعدادات	
			
		}
		
		// process message
		if ( $this->is_options_page() && $this->benrueeg_users_table_exists() ) {
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
	<style type="text/css">
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

benrueeg_rue_plug_load_glob_classes();
benrueeg_rue_plug_options::get_instance()->load();

/*
	function benrueeg_rue_remove_all_filters() {
	return remove_all_filters( 'login_errors' );	
	}
	add_action( 'wp_loaded', 'benrueeg_rue_remove_all_filters' );
*/
/*
	Ex:
	function my_custom_login_error_msg( $error ) {
	return 'Here is the connection error that occurred.';
	}
	add_filter( 'login_errors', 'my_custom_login_error_msg' );
*/	