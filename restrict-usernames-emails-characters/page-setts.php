<?php
	
	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_options' ) ) :
	
	class ben_plug_restrict_usernames_emails_characters_options extends ben_plug_restrict_usernames_emails_characters_errors {
		
		public function __construct() {
			parent::__construct();
		}
		
		function func__settings() {
			
			$page_title = 'Restrict Usernames Emails Characters Admin Page';
			$menu_title = __( 'Restrict Usernames Emails Characters ...', 'restrict-usernames-emails-characters' );
			$menu_slug = BENRUEEG_RUE;
			$function = array( $this, 'BENrueeg_RUE_options_page' );
			
			if ( $this->mu() ) {
			$capability = apply_filters( 'manage_setts_cap_BENrueeg_RUE', 'manage_network_options' );
			$menu = add_menu_page( $page_title, $menu_title, $capability, $menu_slug, $function );
			} else {
			$capability = apply_filters( 'manage_setts_cap_BENrueeg_RUE', 'manage_options' );
			$menu = add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );
			}
		}
		
		function settings__init() {
			
			if ( isset( $_GET['benrueegrue1-7-dismissed'] ) && $this->get_option( 'benrueeg_rue_1_7____notice' ) ) { // if $BENrueeg_ver <= '1.7'
                $this->delete_option( 'benrueeg_rue_1_7____notice' );
	            nocache_headers();
		        wp_redirect( remove_query_arg( array( 'benrueegrue1-7-dismissed' ), stripslashes( $_SERVER['REQUEST_URI'] ) ) );
		        exit;
	        }
			
			register_setting( 'group_on', $this->opt, array($this, 'opt_option_validate') );
			register_setting('group_tw', $this->opt_Tw, array($this, 'opt_tw_option_validate'));
			
			add_settings_section(
			'BENrueeg_RUE_Page_section_one', 
			null, //__( 'Your section description', 'restrict-usernames-emails-characters' ), 
			null, 
			'group_on'
			);
			
			// add_settings_field section 1
			
			add_settings_field(
			'enable', 
			_x( 'Enable', 'label_settings_field', 'restrict-usernames-emails-characters' ), 
			array( $this, 'func__chec_enable' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'enable',
			_x( 'Enable the plugin', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'<br><br><div class="BENrueeg_RUE_to-tri"></div>'
			)
			);
			
			add_settings_field(
			'namelogin', 
			__( 'The name of the user_login field in registration form', 'restrict-usernames-emails-characters' ), 
			array( $this, 'func__input_text1' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'namelogin',
			'<br><em>' . __( 'leave it empty if the registration form is default', 'restrict-usernames-emails-characters' ) . '</em>',
			'width:190px; direction:ltr; color:red;',
			)
			);
			
			add_settings_field(
			'field_id_nameemail', 
			__( 'The name of the user_email field in registration form', 'restrict-usernames-emails-characters' ), 
			array( $this, 'func__input_text1' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'nameemail',
			'<br><em>' . __( 'leave it empty if the registration form is default', 'restrict-usernames-emails-characters' ) . '</em>',
			'width:190px; direction:ltr; color:red;',
			)
			);

			add_settings_field(
			'id_to_put_input_text1', 
			null, 
			array( $this, 'func__to_put' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array()
			);
			
			$fields_checkbox = array(
			array(
			'uid' => 'p_space',
			'label' => _x( 'Not allow spaces in usernames', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Not allow use the spaces between words or characters in the user name', 'label_settings_field', 'restrict-usernames-emails-characters' )
			),
			array(
			'uid' => 'p_num',
			'label' => !$this->mu() ? _x( 'Not allow use only numbers in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ) :
			_x( 'allow use only digits (numbers) in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => !$this->mu() ? _x( 'Not allow use only numbers, for example: 4752442 or +4752442', 'label_settings_field', 'restrict-usernames-emails-characters' ) :
			_x( 'allow use only numbers, for example: 4752442 or +4752442', 'label_settings_field', 'restrict-usernames-emails-characters' )
			),
			array(
			'uid' => 'digits_less',
			'label' => _x( 'The digits must be less than the characters in username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'The digits (numbers) must be less than the characters in username.', 'label_settings_field', 'restrict-usernames-emails-characters' )
			),
			array(
			'uid' => 'uppercase',
			'label' => !$this->mu() ? _x( 'No uppercase in username', 'label_settings_field', 'restrict-usernames-emails-characters' ) :
			_x( 'Use uppercase (if latin is enabled)', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => ''
			),
			array(
			'uid' => 'name_not__email',
			'label' => _x( 'Do not allow usernames that are email addresses', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => ''
			),
			array(
			'uid' => 'all_symbs',
			'label' => _x( 'Prevent the use of all Symbols and letters accented in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => ''
			)
			);
			foreach( $fields_checkbox as $field_ch ){
				if ( $field_ch['uid'] == 'name_not__email' && $this->mu() || $field_ch['uid'] == 'p_space' && $this->mu_bp() ) continue;
				add_settings_field( 
				$field_ch['uid'], 
				$field_ch['label'], 
				array( $this, 'func__chec' ), 
				'group_on', 
				'BENrueeg_RUE_Page_section_one',
				array($field_ch['uid'],$field_ch['label-em'])
				);
			}
			
			add_settings_field(
			'id_to_put1', 
			null, 
			array( $this, 'func__to_put' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array()
			);
			
			add_settings_field( 
			'lang', 
			__( 'Choose language (characters) in username.', 'restrict-usernames-emails-characters' ), 
			array( $this, 'func__rad_lang' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'lang',
			__( 'Default language by wordpress (Latin letters)', 'restrict-usernames-emails-characters' ),
			__( 'Arabic', 'restrict-usernames-emails-characters' ),
			__( 'Cyrillic', 'restrict-usernames-emails-characters' ),
			__( 'Arabic and Cyrillic', 'restrict-usernames-emails-characters' ),
			__( 'All languages (all letters and numbers and accented as: é û)', 'restrict-usernames-emails-characters' ),
			__( 'Enter another language below', 'restrict-usernames-emails-characters' )
			)
			);
			
			add_settings_field(
			'selectedLanguage', 
			null, 
			array( $this, 'func__rad_selectedLanguage' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'selectedLanguage',
			'class' => 'BENrueeg_RUE_showdiv2',
			'width:300px;',
			'<br /><em><label for="selectedLanguage">'. __( 'copy your language from <a target="_blank" href="http://benaceur-php.com/?p=2281">this page</a>', 'restrict-usernames-emails-characters' ) .'</label></em>',
			'<br /><em><label for="selectedLanguage">'. __( 'Separate between language by commas, for example: Hebrew,Greek,Ethiopic', 'restrict-usernames-emails-characters' ) .'</label></em>'
			)
			);
			
			add_settings_field( 
			'langWlatin', 
			null, //__( 'Choose language (characters) in username.', 'restrict-usernames-emails-characters' ), 
			array( $this, 'func__rad_langWlatin' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'langWlatin',
			'class' => 'BENrueeg_RUE_showdiv',
			__( 'with latin', 'restrict-usernames-emails-characters' ),
			__( 'only (not recommended)', 'restrict-usernames-emails-characters' )
			)
			);
			
			add_settings_field(
			'id_to_put2', 
			null, 
			array( $this, 'func__to_put' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array()
			);
			
			$fields_textarea = array(
			array(
			'uid' => 'disallow_spc_cars',
			'label' => _x( 'Prevent the use of characters (Symbols) permitted by wordpress', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => $this->bb() ? __( 'Symbols permitted by wordpress is: _ . -<br />Place each character in one line, for example: <br />_<br />.', 'restrict-usernames-emails-characters' ) : __( 'Symbols permitted by wordpress is: _ . - @<br />Place each character in one line, for example: <br />@<br />.<br />-', 'restrict-usernames-emails-characters' ) . 
			'<div class="tri2-BENrueeg_RUE"></div>',
			'dir' => 'direction:ltr;',
			'siz' => 'font-weight:bold;',
			'remv_lines' => 'remv_lines1',
			),
			array(
			'uid' => 'allow_spc_cars',
			'label' => __( 'Allow this characters (Symbols or characters accented as é û) &#34;no recommended&#34;','restrict-usernames-emails-characters' ),
			'label-em' => _x( '<span style="color:red; font-style:normal;">The following symbols <span style="background:#e9cd7a;color:#343414;padding:0 4px;">&#39; &#34; &#92; &#60; &#62;</span> have been blocked<br />Because allowing these symbols can cause problems when the registration</span><br />Place each character in one line, for example: <br />(<br />+<br />é', 'label_settings_field', 'restrict-usernames-emails-characters' ) . 
			'<div class="tri2-BENrueeg_RUE"></div>',
			'dir' => 'direction:ltr;',
			'siz' => 'font-weight:bold;',
			'remv_lines' => 'remv_lines2',
			),
			array(
			'uid' => 'emails_limit',
			'label' => _x( 'Not allow these mails', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Place each email in one line, for example: <br />emaila@yahoo.com<br />emailb@gmail.com', 'label_settings_field', 'restrict-usernames-emails-characters' ) . 
			'<div class="tri2-BENrueeg_RUE"></div>',
			'dir' => 'direction:ltr;',
			'siz' => '',
			'remv_lines' => 'remv_lines3',
			),
			array(
			'uid' => 'names_limit',
			'label' => _x( 'Not allow these names', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Place each name in one line, for example: <br />nameA<br />nameB<br />nameC', 'label_settings_field', 'restrict-usernames-emails-characters' ) . 
			'<div class="tri2-BENrueeg_RUE"></div>',
			'dir' => '',
			'siz' => '',
			'remv_lines' => 'remv_lines4',
			),
			array(
			'uid' => 'names_limit_partial',
			'label' => _x( 'Restriction by part (contain,doesn&#8217;t contain,starts with,ends with)', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'For example, if we enter "ben", any name containing this part will be blocked as "benaceur"<br />Place each word in one line', 'label_settings_field', 'restrict-usernames-emails-characters' ) . 
			'<div class="tri2-BENrueeg_RUE"></div>',
			'dir' => '',
			'siz' => '',
			'remv_lines' => 'remv_lines5',
			),
			array(
			'uid' => 'email_domain',
			'label' => _x( 'Not allow these emails domain', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => __( 'Place each domain name in one line', 'restrict-usernames-emails-characters' ) . '<br>
			<input type="button" id="sub-BENrueeg_RUE-display-istiamal" class="button-secondary" value="'. __('How to use:', 'restrict-usernames-emails-characters') .'">
			<div id="BENrueeg_RUE-display-istiamal" style="display: none;">'. __( '<p>1- Option: "<b>Restrict the following</b>"</p><p>This option allows us to block every domain ending by "gmail.com" for example or "com"</p><p>For example, if we put it like this:</p><p>gmail.com</p><p>net</p><p>In this case, any mail that ends with "gmail.com" or that with "net" will be blocked.</p><p>&nbsp;</p><p>2- option: "<b>Restrict everything except the following (after @)</b>"</p><p>If we choose this option, we have to put only what comes after the (@)</p><p>For example:</p><p>gmail.com</p><p>ben.com</p><p>sibwor.org</p><p>In this case, all mail that does not end with "gmail.com" or "ben.com" or "sibwor.org" will be blocked.</p><p>&nbsp;</p><p>3- option: "<b>Restrict everything except the following (after .)</b>"</p><p>If we choose this option, we have to put only what comes after the last dot (.)</p><p>For example:</p><p>com</p><p>org</p><p>In this case, all mail that does not end with "com" or does not end with "org" will be blocked.</p>', 'restrict-usernames-emails-characters' ) . '</div><div class="tri2-BENrueeg_RUE"></div>',
			'dir' => 'direction:ltr;',
			'siz' => '',
			'remv_lines' => 'remv_lines6',
			)
			);
			foreach( $fields_textarea as $field_ttarea ){
				if ( $field_ttarea['uid'] == 'disallow_spc_cars' && $this->mu() ) continue;
				
					add_settings_field(
					$field_ttarea['uid'], 
					$field_ttarea['label'], 
					array( $this, 'func__texta' ), 
					'group_on', 
					'BENrueeg_RUE_Page_section_one',
					array($field_ttarea['uid'],$field_ttarea['label-em'],$field_ttarea['dir'],$field_ttarea['siz'],$field_ttarea['remv_lines'])
					);
			}
			
			$fields_text_length = array(
			array(
			'uid' => 'min_length',
			'label' => _x( 'min length of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-l-input' => 'width:60px;'
			),
			array(
			'uid' => 'max_length',
			'label' => _x( 'max length of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-l-input' => 'width:60px;'
			)
			);
			foreach( $fields_text_length as $f_text_length ){
				add_settings_field( 
				$f_text_length['uid'], 
				$f_text_length['label'], 
				array( $this, 'func__text_length' ), 
				'group_on', 
				'BENrueeg_RUE_Page_section_one',
				array($f_text_length['uid'],$f_text_length['sty-w-l-input'])
				);
			}
			
			if ($this->bp()) {
			add_settings_field(
			'id_to_put3', 
			null, 
			array( $this, 'func__to_put' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array()
			);
			}
			
			$tr = '<br /><div class="BENrueeg_RUE_to-tri"></div>';
			$fields_ch_length = array(
			array(
			'uid' => 'length_space',
			'label' => _x( 'length with space', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'take account the space in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			),
			array(
			'uid' => 'remove_bp_field_name',
			'label' => __( 'Remove the name field from the form of registration', 'restrict-usernames-emails-characters' ),
			'label-em' => null,
			),
			array(
			'uid' => 'hide_bp_profile_section',
			'label' => __( 'Hide the entire section of the profile in the form of registration', 'restrict-usernames-emails-characters' ),
			'label-em' => __( 'But if you want to add other fields, do not check this box', 'restrict-usernames-emails-characters' ),
			)
			);
			foreach( $fields_ch_length as $f_ch_length ){
				if ( $f_ch_length['uid'] == 'length_space' && $this->mu_bp() ) continue;
				if ( in_array($f_ch_length['uid'] ,array('remove_bp_field_name','hide_bp_profile_section')) && !$this->bp() ) continue;
				add_settings_field( 
				$f_ch_length['uid'], 
				$f_ch_length['label'], 
				array( $this, 'func__chec_length' ), 
				'group_on', 
				'BENrueeg_RUE_Page_section_one',
				array($f_ch_length['uid'],$f_ch_length['label-em'])
				);
			}
			
			add_settings_field(
			'id_to_put4', 
			null, 
			array( $this, 'func__to_put' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array()
			);
			
			$add_txt = $this->mu() ? __( 'Replace (Must be at least 4 characters ...) in registration form by:', 'restrict-usernames-emails-characters' ) : __( 'Add text (notice) to the registration form', 'restrict-usernames-emails-characters' );
			add_settings_field(
			'txt_form', 
			$add_txt, 
			array( $this, 'func__texta_txtform' ),
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'txt_form',
			__( 'You can use HTML, as for example:', 'restrict-usernames-emails-characters' ) . '<br /><p style="direction:ltr;width:98%;">&lt;span style="font-family:your_font_here; color:your_color_here;" class="">your text&lt;/span></p>'
			)
			);
			
			$fields__radio = array(
			array(
			'uid' => 'del_all_opts',
			'label' => '<br />' . __( 'Delete all data and settings for plugin of the database.', 'restrict-usernames-emails-characters' ),
			'radio1' => __( 'Remove all settings and data of the plugin from database when the plugin is disabled', 'restrict-usernames-emails-characters' ),
			'radio2' => __( 'Do not delete', 'restrict-usernames-emails-characters' ),
			'val1' => 'delete_opts',
			'val2' => 'no_delete_opts',
			'em' => '',
			'tr' => '',
			),
			array(
			'uid' => 'varchar',
			'label' => '<div style="margin-top:10px; padding:0;">' . __( 'Solved the problem of not being able to register with certain languages', 'restrict-usernames-emails-characters' ) .'</div>',
			'radio1' => __( 'Enable', 'restrict-usernames-emails-characters' ),
			'radio2' => __( 'Disable', 'restrict-usernames-emails-characters' ),
			'val1' => 'enabled',
			'val2' => 'disabled',
			'em' => '<br /><br /><em style="font-size:15px;line-height:1.9;">' . __( "If you only use registration with the Latin alphabet, leave this option disabled. If the registration is with other letters such as Arabic for example, this option must be activated to solve the problem of an error message indicating that 'Couldn&#8217;t register please contact the webmaster' or the registration is carried out but without the 'user nicename' (the author’s nicename (slug)) in database, this problem occurs When the user name is a little long as for example: 'اسم لقب اسم لقب' After encoding it to become a link.", 'restrict-usernames-emails-characters' ) . 
			'<div style="padding:8px 2px 0; color:blue; font-size:15px;line-height:1.7;">' . __( 'After each change in this option (Solved the problem of not being able to register with certain languages), the database needs to be updated using this option: &#34;update all users with just one click or in batches&#34;, to update the link for all members', 'restrict-usernames-emails-characters' ) . '</div>' . 
			'</em>',
			'tr' => '',
			)
			);
			
			foreach( $fields__radio as $fields_radio ){
				add_settings_field( 
				$fields_radio['uid'], 
				$fields_radio['label'], 
				array( $this, 'func__rad' ), 
				'group_on', 
				'BENrueeg_RUE_Page_section_one',
				array($fields_radio['uid'],$fields_radio['radio1'],$fields_radio['radio2'],$fields_radio['val1'],$fields_radio['val2'],$fields_radio['em'],$fields_radio['tr'])
				);
			}
			
			add_settings_field(
			'author_slug', 
			'<div style="margin-top:37px; padding:0;">' . __( 'Author Slug Structure', 'restrict-usernames-emails-characters' ) .'</div>', 
			array( $this, 'func_dis_usernice' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'author_slug',
			__( 'user login', 'restrict-usernames-emails-characters' ),
			__( 'nickname', 'restrict-usernames-emails-characters' ),
			__( 'first name', 'restrict-usernames-emails-characters' ),
			__( 'last name', 'restrict-usernames-emails-characters' ),
			__( 'display name', 'restrict-usernames-emails-characters' ),
			__( 'hash (numbers & latin letters)', 'restrict-usernames-emails-characters' ),
			__( 'disable this option (default)', 'restrict-usernames-emails-characters' ),
			__( 'Choose how to display the Author Slug', 'restrict-usernames-emails-characters' ),
			__( 'After each change in this option (Author Slug Structure), the database needs to be updated using this option: &#34;update all users with just one click or in batches&#34;, to update the link for all members', 'restrict-usernames-emails-characters' ),
			)
			);
			
			add_settings_field(
			'limit_nm_rows_update_db', 
			'<div style="margin-top:2px; padding:0;">' . __( 'Update (all or per part) of the author&#39;s slug for all users', 'restrict-usernames-emails-characters' ) .'</div>', 
			array( $this, 'func__usernice' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'limit_nm_rows_update_db',
			'------------------<div class="benrueeg_rue_msg_update_db" style="text-decoration:underline; text-underline-offset:0.5em; padding:0; margin:10px 0; font-weight: bold;">'. __( 'If &#34;Solved the problem of not being able to register with certain languages&#34; option is enabled:', 'restrict-usernames-emails-characters' ) .'</div><div class="benrueeg_rue_msg_update_db" style="padding:0; margin-bottom:10px; font-weight: bold;">' . __( 'Important:' ) .'</div>',
			'<em class="benrueeg_rue_msg_update_db">'. __( '- Each member has their nicename (author slug), which is a link. WordPress takes a copy of the user login when registering a new member and converts it to a link called user_nicename. The problem is that in some languages, like Arabic, when the name is converted to a link, it becomes symbols like: %D8%A7%D8%B3%D9%85 therefore, a problem occurs during registration and it&#39;s not possible to register a new user. This plugin solves the problem by encrypting the user_nicename and it will consist of just latin letters and numbers, With the ability to control how the link to members&#39; posts appears (author slug) through this option: &#34;Author Slug Structure&#34;', 'restrict-usernames-emails-characters' ) .'</em>',
			'<em class="benrueeg_rue_msg_update_db">'. __( '- Before you activate the &#34;Solved the problem of not being able to register with certain languages&#34; option, you must test the letters of the language you have chosen by converting them into a link via this function rawurlencode(). Example: If we choose, for example, Arabic, we put some letters in the function like this, for example: rawurlencode(&#39;اسم&#39;), if it appears in the name this symbol % then you must activate the &#34;Solved the problem of not being able to register with certain languages&#34; option.', 'restrict-usernames-emails-characters' ) .'</em>',
			)
			);
			
			add_settings_field(
			'only_not_latin_up_db', 
			'<div style="margin-top:20px; padding:0;">' . __( 'Update (convert) only names (author slug) not latin', 'restrict-usernames-emails-characters' ) .'</div>', 
			array( $this, 'func2__usernice' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'only_not_latin_up_db',
			__( 'Enabled (default)', 'restrict-usernames-emails-characters' ),
			__( 'Disabled', 'restrict-usernames-emails-characters' ),
			'<em style="font-size:15px;line-height:1.9;">'. __( 'Choose how to update (convert) user_nicename when registering a new member or when updating a member&#39;s personal data or when updating the database (update all users with just one click or in batches), if you choose &#34;Enabled (by default)&#34; only non-Latin names will be updated, and if you choose &#34;Disabled&#34; all will be updated.', 'restrict-usernames-emails-characters' ) .'</em>',
			'<em style="font-size:15px;line-height:1.9;">'. __( 'Note: This option only works when Option &#34;Solved the problem of not being able to register with certain languages&#34; is activated and Option &#34;hash (numbers & latin letters)&#34; is selected, or Option &#34;Solved the problem of not being able to register with certain languages&#34; is activated and Option &#34;disable this option&#34; is selected.', 'restrict-usernames-emails-characters' ) .'</em>'
			)
			);
			
			add_settings_field(
			'disable_top_sub', 
			'<div style="margin-top:0; " class="BENrueeg_RUE_to-tri"></div><div style="margin-top:30px; padding:0;">' . __( 'Remove the submit button (top)', 'restrict-usernames-emails-characters' ) .'</div>', 
			array( $this, 'func__disable_top_sub' ), 
			'group_on', 
			'BENrueeg_RUE_Page_section_one',
			array(
			'disable_top_sub',
			'<em style="font-size:15px;line-height:1.9;"></em>'
			)
			);
			
			// add_settings_field section 1
			
			add_settings_section(
			'BENrueeg_RUE_Page_section_tw', 
			__( '', 'restrict-usernames-emails-characters' ), 
			null, 
			'group_tw'
			);
			
			$fields_text_s2 = array(
			array(
			'uid' => $this->mu_bp() ? 'err_mp_empty' : 'err_empty',
			'label' => _x( 'Error: Enter a username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_exist_login' : 'err_exist_login',
			'label' => _x( 'Error: username exist', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_spaces' : 'err_spaces',
			'label' => _x( 'Error: space in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => '',
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_start_end_space' : 'err_start_end_space',
			'label' => _x( 'Error: multi whitespace and at the beginning or the end of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => '',
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_names_num' : 'err_names_num',
			'label' => _x( 'Error: only numbers in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => '',
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_spc_cars' : 'err_spc_cars',
			'label' => _x( 'Error: Characters (Symbols) in the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_emails_limit' : 'err_emails_limit',
			'label' => _x( 'Error: restricted emails', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => '',
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_names_limit' : 'err_names_limit',
			'label' => _x( 'Error: restricted usernames', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_min_length' : 'err_min_length',
			'label' => _x( 'Error: min length of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( "use %min% to change the value automatically", 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_max_length' : 'err_max_length',
			'label' => _x( 'Error: max length of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( "use %max% to change the value automatically", 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => 'err_partial',
			'label' => _x( 'Error: part of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( "use %part% to change the value automatically", 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => 'err_mp_partial',
			'label' => _x( 'Error: part of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( "use %part% to change the value automatically", 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => 'err_bp_partial',
			'label' => _x( 'Error: part of the username', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( "use %part% to change the value automatically", 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_digits_less' : 'err_digits_less',
			'label' => _x( 'Error: The digits less than characters.', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'The digits (numbers) less than characters.', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_uppercase' : 'err_uppercase',
			'label' => _x( 'Error: No uppercase (A-Z) in username.', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Do not allow use the uppercase (A-Z) in username.', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_name_not_email' : 'err_name_not_email',
			'label' => _x( 'Error: usernames that are email addresses', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => '',
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_empty_user_email' : 'err_empty_user_email',
			'label' => _x( 'Error: type your email address', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_invalid_user_email' : 'err_invalid_user_email',
			'label' => _x( 'Error: invalid email address', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => $this->mu_bp() ? 'err_mp_exist_user_email' : 'err_exist_user_email',
			'label' => _x( 'Error: exist email address', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'Leave it blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			),
			array(
			'uid' => 'err_registration_user',
			'label' => _x( 'Error: Couldn&#8217;t register please contact the webmaster', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'label-em' => _x( 'use %eml% to get the administrator email automatically<br>Leave this field blank to use the default translation', 'label_settings_field', 'restrict-usernames-emails-characters' ),
			'sty-w-input' => 'border: 1px solid red;'
			)
			);
		
			foreach( $fields_text_s2 as $field_t_s2 ){
				
			$mu = !$this->only_mu();
			$bp = !$this->bp();
				
				if ( in_array($field_t_s2['uid'], array('err_registration_user','err_partial')) && $this->mu_bp() || $field_t_s2['uid'] == 'err_bp_partial' && $bp || $field_t_s2['uid'] == 'err_mp_partial' && $mu ) continue;
				
				add_settings_field(
				$field_t_s2['uid'], 
				$field_t_s2['label'], 
				array( $this, 'func__text_s2' ), 
				'group_tw', 
				'BENrueeg_RUE_Page_section_tw',
				array($field_t_s2['uid'],$field_t_s2['label-em'],$field_t_s2['sty-w-input'])
				);
			}
			
			
		}
		
		function func__to_put( $args) { 
			echo '<div class="BENrueeg_RUE_to-tri"></div>';
		}
		
		function func__chec_enable( $args) {
			
			printf(
			'<div class="checkboxT-BENrueeg_RUE">
			<input value="" name="%4$s[%1$s]" type="hidden">
			<input value="on" type="checkbox" id="%1$s" name="%4$s[%1$s]" %2$s />
			<label for="%1$s"></label>
			</div><em><label for="%1$s"> %3$s</label></em>%5$s',
			$args[0],
			checked ( $this->options($args[0]), 'on', false ),
			//checked ( 'on', $this->options($args[0]), false ),
			//checked( isset( $this->options($args[0]) ), true, false ),
			$args[1],
			$this->opt,
			$args[2]
			);
			
		}
		
		function func__input_text1( $args) {
			
			$html = '<input type="text" style="'  . $args[2] . ' text-align:center; margin-bottom:6px; padding:6px;" name="'.$this->opt.'['  . $args[0] . ']" value="' . esc_attr( $this->options($args[0]) ) . '">';
			$html .= $args[1];
			
			echo $html;
		}
		
		function func__chec( $args) {
			
			printf(
			'<label class="switch-BENrueeg_RUE">
			<input value="" name="%4$s[%1$s]" type="hidden">
			<input value="on" type="checkbox" class="switch-input-BENrueeg_RUE" id="%1$s" name="%4$s[%1$s]" %2$s />
			<span class="switch-label-BENrueeg_RUE" data-on="On" data-off="Off"></span>
			<span class="switch-handlel-BENrueeg_RUE"></span>
			</label>
			<br /><em><label for="%1$s"> %3$s</label></em>',
			$args[0],
			checked ( $this->options($args[0]), 'on', false ),
			//checked ( 'on', $this->options($args[0]), false ),
			//checked( isset( $this->options($args[0]) ), true, false ),
			$args[1],
			$this->opt
			);
			
		}
		
		function func__texta( $args) { 
		
		    if (is_rtl()) {
            $css = 'ltr'; $align = 'left';
	        } else {
            $css = 'rtl'; $align = 'right';
	        }

            if (in_array($args[0], array('names_limit','names_limit_partial'))) {
			echo '<div style="margin: 0 0 12px; padding: 0;"><input type="button" id="b77t-ntbCat-'. $args[0] .'" class="button-secondary" value="'. __("Text direction", "restrict-usernames-emails-characters") .'"></div>
			<style>
			.b77tae_main {text-align:'. $align .'; direction:'. $css .';}
			</style>
			';
			}

			$ertex = $this->options($args[0]);  
			$cel1 = $cel2 = $cel3 = $cel4 = '';
			
			if ($args[0] == "emails_limit") {
			$cel1 = sprintf( '<div style="padding:0;">
            <select class="emails_limit_strtolower" name="%1$s" >
            <option value="strtolower" %2$s>%3$s</option>
            <option value="original" %4$s>%5$s</option>
            </select></div><div style="margin:5px 0 5px 0; padding:0; max-width:400px;"><em>%6$s</em></div>
			<div style="margin-bottom:10px; padding:0;"><em>%7$s</em></div>',
			$this->option("emails_limit_strtolower"),
			selected( $this->options("emails_limit_strtolower"), 'strtolower', false ),
			__("Make lowercase equal uppercase (enabled)", "restrict-usernames-emails-characters"),
			selected( $this->options("emails_limit_strtolower"), 'original', false ),
			__("Make lowercase equal uppercase (disabled)", "restrict-usernames-emails-characters"),
			__("If we choose the &#34;Make lowercase letters equal uppercase&#34; option&#44; (latin letters only) it becomes for example:", "restrict-usernames-emails-characters"),
			"emailb@gmail.com = eMailb@gmAil.com = EmAilB@Gmail.com"
			);
			}
			
			if ($args[0] == "names_limit") {
			$cel2 = sprintf( '<div style="padding:0;">
            <select class="names_limit_strtolower" name="%1$s" >
            <option value="strtolower" %2$s>%3$s</option>
            <option value="original" %4$s>%5$s</option>
            </select></div><div style="margin:5px 0 5px 0; padding:0; max-width:400px;"><em>%6$s</em></div>
			<div style="margin-bottom:10px; padding:0;"><em>%7$s</em></div>',
			$this->option("names_limit_strtolower"),
			selected( $this->options("names_limit_strtolower"), 'strtolower', false ),
			__("Make lowercase equal uppercase (enabled)", "restrict-usernames-emails-characters"),
			selected( $this->options("names_limit_strtolower"), 'original', false ),
			__("Make lowercase equal uppercase (disabled)", "restrict-usernames-emails-characters"),
			__("If we choose the &#34;Make lowercase letters equal uppercase&#34; option&#44; (latin letters only) it becomes for example:", "restrict-usernames-emails-characters"),
			"name a = Name A = NAME a"
			);
			}
			
			if ($args[0] == "names_limit_partial") {
			$cel3 = sprintf( '<select class="cs_email_domain_opt" name="%1$s" >
            <option value="restrict_contain" %2$s>%3$s</option>
            <option value="restrict_except" %4$s>%5$s</option>
			<option value="restrict_without_start" %6$s>%7$s</option>
			<option value="restrict_without_end" %8$s>%9$s</option>
            </select>
			<div style="margin: 10px 0 1px 0; padding:0;">
            <select class="names_partial_strtolower" name="%10$s" >
            <option value="strtolower" %11$s>%12$s</option>
            <option value="original" %13$s>%14$s</option>
            </select></div><div style="margin:5px 0 5px 0; padding:0; max-width:400px;"><em>%15$s</em></div>
			<div style="margin-bottom:10px; padding:0;"><em>%16$s</em></div>',
			$this->option("names_limit_partial_opt"),
			selected( $this->options("names_limit_partial_opt"), 'restrict_contain', false ),
			__("Restriction of username that contains", "restrict-usernames-emails-characters"),
			selected( $this->options("names_limit_partial_opt"), 'restrict_except', false ),
			__("Restriction of username that doesn't contain", "restrict-usernames-emails-characters"),
			selected( $this->options("names_limit_partial_opt"), 'restrict_without_start', false ),
			__("Restriction of each username doesn't start with", "restrict-usernames-emails-characters"),
			selected( $this->options("names_limit_partial_opt"), 'restrict_without_end', false ),
			__("Restriction of each username doesn't end with", "restrict-usernames-emails-characters"),
			$this->option("names_partial_strtolower"),
			selected( $this->options("names_partial_strtolower"), 'strtolower', false ),
			__("Make lowercase equal uppercase (enabled)", "restrict-usernames-emails-characters"),
			selected( $this->options("names_partial_strtolower"), 'original', false ),
			__("Make lowercase equal uppercase (disabled)", "restrict-usernames-emails-characters"),
			__("If we choose the &#34;Make lowercase letters equal uppercase&#34; option&#44; (latin letters only) it becomes for example:", "restrict-usernames-emails-characters"),
			"admin = Admin = aDmin"
			);
			}
			
			if ($args[0] == "email_domain") {
			$cel4 = sprintf( '<select class="cs_email_domain_opt" name="%1$s" >
            <option value="restrict" %2$s>%3$s</option>
            <option value="not_restrict_at" %4$s>%5$s</option>
			<option value="not_restrict_dot" %6$s>%7$s</option>
            </select>
			<div style="margin: 10px 0 1px 0; padding:0;">
            <select class="email_domain_strtolower" name="%8$s" >
            <option value="strtolower" %9$s>%10$s</option>
            <option value="original" %11$s>%12$s</option>
            </select></div><div style="margin:5px 0 5px 0; padding:0; max-width:400px;"><em>%13$s</em></div>
			<div style="margin-bottom:10px; padding:0;"><em>%14$s</em></div>',
			$this->option("email_domain_opt"),
			selected( $this->options("email_domain_opt"), 'restrict', false ),
			__("Restrict the following", "restrict-usernames-emails-characters"),
			selected( $this->options("email_domain_opt"), 'not_restrict_at', false ),
			__("Restrict everything except the following (after @)", "restrict-usernames-emails-characters"),
			selected( $this->options("email_domain_opt"), 'not_restrict_dot', false ),
			__("Restrict everything except the following (after .)", "restrict-usernames-emails-characters"),
			$this->option("email_domain_strtolower"),
			selected( $this->options("email_domain_strtolower"), 'strtolower', false ),
			__("Make lowercase equal uppercase (enabled)", "restrict-usernames-emails-characters"),
			selected( $this->options("email_domain_strtolower"), 'original', false ),
			__("Make lowercase equal uppercase (disabled)", "restrict-usernames-emails-characters"),
			__("If we choose the &#34;Make lowercase letters equal uppercase&#34; option&#44; (latin letters only) it becomes for example:", "restrict-usernames-emails-characters"),
			"gmail.com = Gmail.com = GMAIL.com"
			);
			}

			//$html = $args[0] == "email_domain" ? $inp .'<br><br>' : '';
			$html = $cel1 . $cel2 . $cel3 . $cel4 . '<textarea id="'  . $args[4] . '" style="'.$args[2].$args[3].'" cols="35%" rows="7" name="'.$this->opt.'['  . $args[0] . ']" >' . $ertex . '</textarea>';
			//$html = '<input type="text" id="'  . $args[0] . '" name="BENrueeg_RUE_settings['  . $args[0] . ']" value="' . $options[''  . $args[0] . ''] . '">';
			$html .= '<br /><em><label for="'  . $args[0] . '"> '  . $args[1] . '</label></em>';
			
			echo $html;
		}
		
		function func__texta_txtform( $args) { 
			
			$ertex = $this->options($args[0]);  
			
			$html ='<textarea id="'  . $args[0] . '" style="height: 100px; min-width:98%;" cols="35%" rows="7" name="'.$this->opt.'['  . $args[0] . ']" >' . esc_textarea( $ertex ) . '</textarea>';
			$html .= '<br /><em><label for="'  . $args[0] . '"> '  . $args[1] . '</label></em>';
			
			echo $html;
		}
		
		function func__rad( $args) { 
			
			$html = '<div class="BENrueeg_RUE_to-tri"></div><br /><label for="'  . $args[0] . '"><input type="radio" id="'  . $args[0] . '" name="'.$this->opt.'['  . $args[0] . ']" value="'  . $args[3] . '" '  . checked($args[3], $this->options($args[0]), false) . '> '  . $args[1] . '</label>';
			$html .= '<br /><label for="'  . $args[0] . '-2"><input type="radio" id="'  . $args[0] . '-2" name="'.$this->opt.'['  . $args[0] . ']" value="'  . $args[4] . '" '  . checked($args[4], $this->options($args[0]), false) . '> '  . $args[2] . '</label>';
			$html .= $args[5];
			$html .= $args[6];
			
			echo $html;
		}
		
		function func_dis_usernice( $args) {
			
			$html  = '<div style="" class="BENrueeg_RUE_to-tri"></div><select style="margin-top:30px; text-align:center;" name="'. $this->opt .'['  . $args[0] . ']">';
			$html .= '<option value="userlogin" '. selected( $this->options($args[0]), 'userlogin', false ) .'> '  . $args[1] . '</option>';
			$html .= '<option value="nickname" '. selected( $this->options($args[0]), 'nickname', false ) .'> '  . $args[2] . '</option>';
			$html .= '<option value="first_name" '. selected( $this->options($args[0]), 'first_name', false ) .'> '  . $args[3] . '</option>';
			$html .= '<option value="last_name" '. selected( $this->options($args[0]), 'last_name', false ) .'> '  . $args[4] . '</option>';
			$html .= '<option value="displayname" '. selected( $this->options($args[0]), 'displayname', false ) .'> '  . $args[5] . '</option>';
			$html .= '<option value="hash" '. selected( $this->options($args[0]), 'hash', false ) .'> '  . $args[6] . '</option>';
			$html .= '<option value="disable" '. selected( $this->options($args[0]), 'disable', false ) .'> '  . $args[7] . '</option>';
			$html .= '</select>';
			$html .= '<div style="padding:8px 2px 0;">'. $args[8] . '</div><div style="padding:8px 2px 0; color:blue; font-size:15px;line-height:1.7;">'. $args[9] . '</div>------------------';
			
			echo $html;
		}

		function func__usernice($args) {
			//$hide = $this->options('enable') == 'on' ? '' : 'disabled="disabled"';
			$hide = '';
			$up = __( 'update all users with just one click or in batches', 'restrict-usernames-emails-characters');
			$up .= '<br><span style="padding:0 10px;"></span>' . __( '(Don&#39;t forget to take a backup of the database)', 'restrict-usernames-emails-characters');
			$up2 = __( 'Stop the process of updade database (per part)', 'restrict-usernames-emails-characters');
		    if ($args[0] == "limit_nm_rows_update_db") {
			$cel = '<div class="childbenn-checkbox" style="color:red; margin-bottom:30px;padding:8px 0 0;"><label style="cursor:pointer;" for="benrueeg_rue_15_nicename_nickname"><input type="checkbox" name="benrueeg_rue_up_all_user_nicename" id="benrueeg_rue_15_nicename_nickname" value="1" '. $hide .' >'. $up .'</label></div>';	
			$cel2 = sprintf( '<input onclick="return confirm(\'%s\')" type="submit" name="benrueeg_rue_remove_up_all_user_nicename" class="button-secondary" id="benrueeg_rue_16_nicename_nickname" value="%s" ><div style="margin:7px 0 5px; padding:3px 0 0;">%s</div>',  __( 'Are you sure to remove the process of updade database (per part)?', 'restrict-usernames-emails-characters' ), __( 'delete process file', 'restrict-usernames-emails-characters'), $up2 );	
			$cel2 .= '<div style="margin-bottom:30px; padding:3px 0 0;">'. __( 'and delete the message at the top', 'restrict-usernames-emails-characters') .'</div>';
			}
			
			$html  = $cel;
			$html .= '<input type="text" style="width:120px; text-align:center;" id="text_s2" name="'.$this->opt.'['  . $args[0] . ']" value="' . esc_attr( $this->options($args[0]) ) . '">';
			$html .= '<div style="margin:7px 0 5px; padding:3px 0 0;">'. __( 'Limit the number of users to update (in batches) with every click, if your database is big', 'restrict-usernames-emails-characters') .'</div><div style="margin:5px 0 5px; padding:3px 0 0;">'. __( 'Ex: 5000', 'restrict-usernames-emails-characters') .'</div>';
			$html .= '<div style="margin-bottom:40px; padding:2px 0 0;">'. __( 'Leave it blank to update all users at once', 'restrict-usernames-emails-characters') .'</div>';
			$html .= $this->get_option('benrueeg_nicename_store_all_users_id') ? $cel2 : '';
			$html .= $args[1] . '<div style="padding:0;margin-bottom:18px;">'. $args[2] .'</div><div style="padding:0;margin-bottom:18px;">'. $args[3] .'</div>';
			
			echo $html;
		}
		
		function func2__usernice( $args) {
			
			$html  = '<select style="margin-top:30px; text-align:center;" name="'.$this->opt.'['  . $args[0] . ']">';
			$html .= '<option value="enable" '.selected( $this->options($args[0]), 'enable', false ).'> '  . $args[1] . '</option>';
			$html .= '<option value="disable" '.selected( $this->options($args[0]), 'disable', false ).'> '  . $args[2] . '</option>';
			$html .= '</select>';
			$html .= '<div style="padding:8px 2px 0;">'. $args[3] . '</div><div style="padding:8px 2px 0; color:blue;">'. $args[4] . '</div>';
			
			echo $html;
		}
		
		function func__text_length( $args) {
			
			$html = '<input type="text" style="'  . $args[1] . ' text-align:center;" id="text_s2" name="'.$this->opt.'['  . $args[0] . ']" value="' . esc_attr( $this->options($args[0]) ) . '">';
			//$html .= '<br /><em><label for="'  . $args[0] . '"> '  . $args[1] . '</label></em>';
			
			echo $html;
		}
		
		function func__disable_top_sub( $args) {
			
			printf(
			'<label id="%1$s_2" class="switch-BENrueeg_RUE">
			<input value="" name="%3$s[%1$s]" type="hidden">
			<input value="on" type="checkbox" class="switch-input-BENrueeg_RUE" id="%1$s" name="%3$s[%1$s]" %2$s />
			<span class="switch-label-BENrueeg_RUE" data-on="On" data-off="Off"></span>
			<span class="switch-handlel-BENrueeg_RUE"></span>
			</label>',
			$args[0],
			checked ( $this->options($args[0]), 'on', false ),
			$this->opt
			);
			
		}
		
		function func__chec_length( $args) { 
			
			printf(
			'<label class="switch-BENrueeg_RUE">
			<input value="" name="%4$s[%1$s]" type="hidden">
			<input value="on" type="checkbox" class="switch-input-BENrueeg_RUE" id="%1$s" name="%4$s[%1$s]" %2$s />
			<span class="switch-label-BENrueeg_RUE" data-on="On" data-off="Off"></span>
			<span class="switch-handlel-BENrueeg_RUE"></span>
			</label>
			<br /><em><label for="%1$s"> %3$s</label></em>',
			$args[0],
			checked ( $this->options($args[0]), 'on', false ),
			//checked ( 'on', $this->options($args[0]), false ),
			//checked( isset( $this->options($args[0]) ), true, false ),
			$args[1],
			$this->opt
			);
			
		}
		
		function func__rad_lang( $args) { 
			
			$html = '<select id="BENrueeg_RUE_showelemselect" class="BENrueeg_RUE-blockSelect" style="text-align:center;" name="'.$this->opt.'['  . $args[0] . ']">';
			$html .= '<option value="default_lang" '.selected( $this->options($args[0]), 'default_lang', false ).'> '  . $args[1] . '</option>';
			$html .= '<option value="arab_lang" '.selected( $this->options($args[0]), 'arab_lang', false ).'> '  . $args[2] . '</option>';
			$html .= '<option value="cyr_lang" '.selected( $this->options($args[0]), 'cyr_lang', false ).'> '  . $args[3] . '</option>';
			$html .= '<option value="arab_cyr_lang" '.selected( $this->options($args[0]), 'arab_cyr_lang', false ).'> '  . $args[4] . '</option>';
			$html .= '<option value="all_lang" '.selected( $this->options($args[0]), 'all_lang', false ).'> '  . $args[5] . '</option>';
			$html .= '<option value="select_lang" '.selected( $this->options($args[0]), 'select_lang', false ).'> '  . $args[6] . '</option>';
			$html .= '</select>';
			$html .= '<div style="color:red;" class="all-languages-not-msg-benrueeg_rue">'. __( 'Selecting all languages is not recommended.', 'restrict-usernames-emails-characters' ) .'</div>';
			
			echo $html;
		}
		
		function func__rad_selectedLanguage( $args) {
			
			$required = $this->options('lang') == 'select_lang' ? 'required' : '';
			$html  = '<input type="text" style="'  . $args[1] . '" id="text_s3" name="'.$this->opt.'['  . $args[0] . ']" value="' . $this->options($args[0]) . '" '. $required .' >';
			$html .= $args[2];
			$html .= $args[3];
			
			echo $html;
		}
		
		function func__rad_langWlatin( $args) { 
			
			$html  = '<div style="padding:0;" class="benrueeg_rue_c_lw"></div>';
			$html .= '<select id="benrueeg_rue_c_lw" class="BENrueeg_RUE-blockSelect" style="margin-bottom:10px; text-align:center;" name="'.$this->opt.'['  . $args[0] . ']">';
			$html .= '<option value="w_latin_lang" '.selected( $this->options($args[0]), 'w_latin_lang', false ).'> '  . $args[1] . '</option>';
			$html .= '<option value="only_lang" '.selected( $this->options($args[0]), 'only_lang', false ).'> '  . $args[2] . '</option>';
			$html .= '</select>';
			
			echo $html;
		}
		
		function func__text_s2( $args) { 
			
			$html  = '<input type="text" style="'  . $args[2] . '" id="text_s2" name="'. $this->opt_Tw.'['  . $args[0] . ']" value="' . esc_html( $this->options_Tw($args[0]) ) . '">';
			$html .= '<br /><em><label for="'  . $args[0] . '"> '  . $args[1] . '</label></em>';
			
			echo $html;
		}
		
		function dir_css() {
			
			$l = is_rtl() ? 'left' : 'right';
			$r = is_rtl() ? 'right' : 'left';
			$lang = $this->options('lang');
			
			echo "
			<style type='text/css'>
			#BENrueeg_RUE_dashicons-menu {float:$l;}
			.wrap.BENrueeg_RUE_wrap_red { border-$r: 3px solid red; }
			.wrap.BENrueeg_RUE_wrap_bott { border-$r: 3px solid #00A0D2; }
			.BENrueeg_RUE-mm411112 #BENrueeg_RUE-mm411112-divtoBlink{border-$r:3px solid #FF5757;}
			</style>
			";
			if ($lang == 'default_lang' || $lang == 'all_lang') {
				echo "<style type='text/css'>.BENrueeg_RUE_showdiv{display:none;} #BENrueeg_RUE_showelemselect{margin-bottom:7px;}</style>";
			} 
			if ($lang != 'select_lang') {
				echo "<style type='text/css'>.BENrueeg_RUE_showdiv2{display:none;}
				</style>";
			}
			if ($lang != 'all_lang') {
				echo "<style type='text/css'>.all-languages-not-msg-benrueeg_rue{display:none;}
				</style>";
			}
		}
		
		function BENrueeg_RUE_options_page(  ) {
		
		$r = 'restrict_usernames_emails_characters&tab';   
		
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'general_settings';
		if( isset( $_GET[ 'tab' ] ) ) {
		$active_tab = $_GET[ 'tab' ];
		} // end if
		
		if (in_array(get_user_locale(), array('ar','ary'))) {
		echo '<style>.wrap.rue table th {font-family:DroidKufiRegular,Tahoma,Arial;font-size:15px;font-weight:normal;line-height:1.5;}</style>';	
		} else {
		echo '<style>.wrap.rue table th {font-family:Tahoma,Arial;}</style>';	
		}
		
		$action1 = $this->mu() ? "edit.php?action=ben742198_settings" : "options.php";	
		$action2 = $this->mu() ? "edit.php?action=ben742198_tw_settings" : "options.php";	
		?>
		
        <div class="wrap"><h2 style="padding-bottom:13px;font-size:21px;">Restrict Usernames Emails Characters V <?php echo $this->BENrueeg_RUE_version(); ?></h2>
		<?php
		if( $active_tab == 'general_settings' && !$this->is_php_8_1_wpcore() ) {
		?><div style='display:none;' class='BENrueeg_RUE-mm4111172p'><?php $this->VerPlugUp(); ?></div><?php
		} ?>
		
        <h2 class="nav-tab-wrapper BENrueeg_RUE-ntw">
		<a href="?page=<?php echo $r ?>=general_settings" class="nav-tab <?php echo $active_tab == 'general_settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'General Settings', 'restrict-usernames-emails-characters' ); ?></a>
		<a href="?page=<?php echo $r ?>=error_messages" class="nav-tab <?php echo $active_tab == 'error_messages' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Error Messages', 'restrict-usernames-emails-characters' ); ?></a>
		<a href="?page=<?php echo $r ?>=important" class="nav-tab <?php echo $active_tab == 'important' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Important!', 'restrict-usernames-emails-characters' ); ?></a>
		<a href="?page=<?php echo $r ?>=extentions" class="nav-tab <?php echo $active_tab == 'extentions' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Extentions', 'restrict-usernames-emails-characters' ); ?></a>
        <span id="BENrueeg_RUE_dashicons-menu" style="margin-top:16px;" class="dashicons dashicons-menu"></span>
		</h2></div>
		
		<?php
		
		if( $this->mu() && !is_plugin_active_for_network(BENRUEEG_NTP)) {
		if( $active_tab == 'general_settings' || $active_tab == 'error_messages' ) {
		$href = network_admin_url('plugins.php?plugin_status=all');
		$url = '<a target="_blank" href="'.$href.'">'. __( 'network', 'restrict-usernames-emails-characters' ) .'</a>';
		printf( '<div style="background:#f7b2b2; border: none; border-radius:4px; margin:10px;" class="notice notice-error is-dismissible"><p>%1$s %2$s %3$s</p></div>',
		__( "The plugin must be enabled on the", 'restrict-usernames-emails-characters' ),
		$url,
		__( "to work without any problem", 'restrict-usernames-emails-characters' )
		);
		}
		}
		
        if( $active_tab == 'general_settings' ) {
			if ( ! $this->check_table_exists('benrueeg_users') )
				$this->benrueeg_tables();
		?>
		<div class="wrap rue">
		<h2 id="benSett-themes"><span style="margin-top:6px;" class="dashicons dashicons-editor-spellcheck"></span> <?php _e('General Settings','restrict-usernames-emails-characters'); ?></h2>
		<div>
		<form id="form-BENrueeg_RUE_1" action="<?php echo $action1; ?>" method="post">
		<?php
		if ($this->options('disable_top_sub') != 'on')
			printf( '<input name="submit" id="submit-ftb1-BENrueeg_RUE_top" class="button-BENrueeg_RUE_top ftb11" type="submit" value="%1$s" />', __( 'Save Changes', 'restrict-usernames-emails-characters' ));
	
		settings_fields( 'group_on' );
		do_settings_sections( 'group_on' );
		
		if ($this->mu()) {
		echo '<textarea style="display:none;" name="'.$this->opt.'[disallow_spc_cars]" >'. $this->options('disallow_spc_cars') .'</textarea>';
		echo '<input value="'. $this->options('name_not__email') .'" name="'.$this->opt.'[name_not__email]" type="hidden" />';
		}
		
		if ($this->mu_bp()) {
		echo '<input value="'. $this->options('p_space') .'" name="'.$this->opt.'[p_space]" type="hidden" />';
		echo '<input value="'. $this->options('length_space') .'" name="'.$this->opt.'[length_space]" type="hidden" />';
		}
		
		if (!$this->bp()) {
		echo '<input value="'. $this->options('remove_bp_field_name') .'" name="'.$this->opt.'[remove_bp_field_name]" type="hidden" />';
		echo '<input value="'. $this->options('hide_bp_profile_section') .'" name="'.$this->opt.'[hide_bp_profile_section]" type="hidden" />';
		}
		?>
		<p style="margin-top:50px;" class="submit">
		<input name="submit" id="submit-ftb1-BENrueeg_RUE_top" class="button-BENrueeg_RUE_top ftb12" type="submit" value="<?php _e( 'Save Changes', 'restrict-usernames-emails-characters' ); ?>"/>
		</p>
		</form>
		</div>
		</div>
		
		<div class="wrap"><div class="postbox"><div class="inside">
		<p id="BENrueeg_RUE_wrap_t"><span class="dashicons dashicons-yes"></span><?php _e('Reset default settings', 'restrict-usernames-emails-characters');?></p>
		<form id="form-BENrueeg_RUE_2" method="post" action="">
		<?php wp_nonce_field('nonce_BENrueeg_RUE_reset_general_opt'); ?> 
		<input type="hidden" name="BENrueeg_RUE_reset_general_opt" value="1" />
		<input type="submit" id="submit-ftb2-BENrueeg_RUE" value="<?php _e('Reset general option', 'restrict-usernames-emails-characters');?>" class="button-secondary" />
		</form>
		</div></div></div>
		<div id="BENrueeg_RUE_saveResult"></div>
		
	    <script type="text/javascript">
        jQuery(function ($) {

            $(".button-BENrueeg_RUE_top.ftb11,.button-BENrueeg_RUE_top.ftb12").click(function(){
		        var s = $('#form-BENrueeg_RUE_1 input[name="benrueeg_rue_up_all_user_nicename"]'); // update the user_nicename for (all) members
		        var e = $('.checkboxT-BENrueeg_RUE #enable'); // button for enable the plugin
		
		    if (s.is(':checked')){
				
				if( !BENrueeg_RUE_jsParams.benrueeg_on || (BENrueeg_RUE_jsParams.benrueeg_on && ! e.is(':checked')) ) {
					$( "#benrueeg_rue_15_nicename_nickname" ).prop( "checked", false );
					$('.button-BENrueeg_RUE_top.ftb11,.button-BENrueeg_RUE_top.ftb12').val(BENrueeg_RUE_jsParams.remove_wait_a_little);
                    alert(BENrueeg_RUE_jsParams.alert_up_if_plug_off);
					return false;
                }
				
	            var choice = confirm(BENrueeg_RUE_jsParams.msg_up_all_nicename);
				if(choice === true) {
					setTimeout(function(){
	                    $(".benrueeg_rue_process_msg_up_db.up_process").slideDown(140);	
                    }, 200);
                    return true;
                }
                return false;
		    }
            });
        });
		
		/*
		jQuery(function ($) {
			
		$("#submit-ftb1-BENrueeg_RUE_top").click(function(){
                if (!$('.checkboxT-BENrueeg_RUE #enable').is(":checked") && $('#benrueeg_rue_15_nicename_nickname').is(":checked")) {
					$( "#benrueeg_rue_15_nicename_nickname" ).prop( "checked", false );
                    alert('قم بتفعيل الإضافة أولا');
                } 
		});		
			
        });
		*/
		
		jQuery(function($) {
		
		$('#BENrueeg_RUE_showelemselect').change(function(){
			
		if($('#BENrueeg_RUE_showelemselect').val() == 'all_lang') {
		$('.all-languages-not-msg-benrueeg_rue').show();
		} else {
		$('.all-languages-not-msg-benrueeg_rue').hide();
		}
			
		var arrVal = ['default_lang','all_lang'];
		var valSlected = $('#BENrueeg_RUE_showelemselect').val();
		
		if(arrVal.indexOf(valSlected) !== -1) {
		//if($.inArray(valSlected,arrVal) !== -1) {
		//if($('#BENrueeg_RUE_showelemselect').val() == 'default_lang' || $('#BENrueeg_RUE_showelemselect').val() == 'all_lang') {
		$('.BENrueeg_RUE_showdiv').hide();
		$('#BENrueeg_RUE_showelemselect').css('margin-bottom', '7px');
		} else {
		$('.BENrueeg_RUE_showdiv').show();
		$('#BENrueeg_RUE_showelemselect').css('margin-bottom', '0');
		}
			
		if($('#BENrueeg_RUE_showelemselect').val() == 'select_lang') {
		$('.BENrueeg_RUE_showdiv2').show();
		$('.BENrueeg_RUE_showdiv2 #text_s3').prop('required',true);
		} else {
		$('.BENrueeg_RUE_showdiv2 #text_s3').prop('required',false);	
		$('.BENrueeg_RUE_showdiv2').hide();
		}
		});
		});
		</script>
		<?php
        } elseif ($active_tab == 'error_messages') { ?>
		<div class="wrap rue">
		<h2 id="benSett-themes"><span style="margin-top:6px;" class="dashicons dashicons-editor-spellcheck"></span> <?php _e('Error Messages','restrict-usernames-emails-characters'); ?></h2>
		<div>
		<form id="form-BENrueeg_RUE_0" action="<?php echo $action2; ?>" method="post">
		<?php
		settings_fields( 'group_tw' );
		do_settings_sections( 'group_tw' );
		
		if ($this->mu_bp() ) {
			
		if ( $this->only_mu() ) {
		echo '<input value="' . esc_html( $this->options_Tw('err_bp_partial') ) . '" name="'.$this->opt_Tw.'[err_bp_partial]" type="hidden" />';
		} else {
		echo '<input value="' . esc_html( $this->options_Tw('err_mp_partial') ) . '" name="'.$this->opt_Tw.'[err_mp_partial]" type="hidden" />';
		}
			
		foreach($this->array_tw_word() as $err) {
		echo '<input value="' . esc_html( $this->options_Tw($err) ) . '" name="'.$this->opt_Tw.'[' . $err . ']" type="hidden" />';
		}
		} else {
		foreach($this->array_tw_mubp() as $err_mp) {
		echo '<input value="' . esc_html( $this->options_Tw($err_mp) ) . '" name="'.$this->opt_Tw.'[' . $err_mp . ']" type="hidden" />';
		}
		}
		?>          
		<p class="submit">
		<input name="submit" id="submit-ftb0-BENrueeg_RUE" class="button-BENrueeg_RUE" type="submit" value="<?php _e( 'Save Changes', 'restrict-usernames-emails-characters' ); ?>"/>
		</p>
		</form>
		</div>
		</div>
		
		<div class="wrap"><div class="postbox"><div class="inside">
		<p id="BENrueeg_RUE_wrap_t"><span class="dashicons dashicons-yes"></span><?php _e('Reset default settings', 'restrict-usernames-emails-characters');?></p>
		<form id="form-BENrueeg_RUE_3" method="post" action="">
		<?php wp_nonce_field('nonce_BENrueeg_RUE_reset_err_mgs'); ?> 
		<input type="hidden"  name="BENrueeg_RUE_reset_err_mgs" value="1" />
		<input type="submit" id="submit-ftb3-BENrueeg_RUE" value="<?php _e('Reset error_messages', 'restrict-usernames-emails-characters');?>" class="button-secondary" />
		</form>
		</div></div></div>
		<div id="BENrueeg_RUE_saveResult"></div>
		<?php }
		
		if( $active_tab == 'general_settings' || $active_tab == 'error_messages' ) { ?>
		<!-- import export -->
		<div class="wrap"><div class="postbox"><div class="inside">
		
		<h3><span><?php _e('Export Settings', 'restrict-usernames-emails-characters'); ?></span></h3>
		<div style="margin:0;" class="inside">
		<p><?php _e( 'Export the plugin settings as a .json file. This allows you to easily import the configuration.', 'restrict-usernames-emails-characters' ); ?></p>
		<form method="post">
		<input type="hidden" name="BENrueeg_RUE_action" value="export_settings" /></p>
		<?php wp_nonce_field( 'BENrueeg_RUE_export_nonce', 'BENrueeg_RUE_export_nonce' ); ?>
		<?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
		</p>
		</form>
		</div><!-- .inside -->
		</div></div></div>
		
		<div class="wrap"><div class="postbox"><div class="inside">
		<h3><span><?php _e('Import Settings', 'restrict-usernames-emails-characters'); ?></span></h3>
		<div style="margin:0;" class="inside">
		<p><?php _e( 'Import the plugin settings from the saved .json file.', 'restrict-usernames-emails-characters' ); ?></p>
		<form id="BENrueeg_RUE_export__file" method="post" enctype="multipart/form-data">
		<p>
		<input type="file" name="import_file" id="BENrueeg_RUE_jsonfileToUpload" />
		</p>
		<p>
		<input type="hidden" name="BENrueeg_RUE_action" value="import_settings" />
		<?php wp_nonce_field( 'BENrueeg_RUE_import_nonce', 'BENrueeg_RUE_import_nonce' ); ?>
		<input type="submit" id="BENrueeg_RUE_export__file-sub" value="<?php _e( 'Import' );?>" class="button-secondary" />
		</p>
		</form>
		<div style="display:none;" id="BENrueeg_RUE_export-loading-div-background">
		<?php _e('Importing parameter files is in progress, wait ...', 'restrict-usernames-emails-characters') ?>  
		</div>
		<div style="display:none;" class="BENrueeg_RUE_export__file">
        <p><?php _e('The parameters file was imported successfully.', 'restrict-usernames-emails-characters') ?></p>
		</div>
		</div><!-- .inside -->
		</div></div></div><!-- .metabox-holder -->
		<!-- import export -->
		<?php } ?>
		
		<?php	if( $active_tab == 'important' ) { ?>
		<div class="wrap BENrueeg_RUE_wrap_padd"><div class="postbox"><div style="height:16px;" class="inside">
		<p style="padding-top:2px;" id="BENrueeg_RUE_wrap_t"><span style="margin-top:2px;" class="dashicons dashicons-megaphone"></span> <?php _e('Important to read', 'restrict-usernames-emails-characters');?></p>
		</div></div></div>
		
		<div class="wrap BENrueeg_RUE_wrap_red"><div class="postbox"><div class="inside">
		<p>1- <?php _e("If you use the hook 'login_errors' this also affects registration error messages, it's preferable to use the hook 'authentificate' if you want to modify the connection error messages, this is why I deactivated the hook 'login_errors', so if you want to reactivate it, put this line in the file 'functions.php':", 'restrict-usernames-emails-characters'); ?></p>
		<pre class="benrueeg_remove_all_filters">remove_action( 'wp_loaded', 'benrueeg_remove_all_filters' );</pre>
		<p style="clear:both;"><?php _e('- If you want help about the modification of connection (log in) error messages contact me by creating a new post with your question on: <a target="_blank" href="http://benaceur-php.com/general-support/">general-support</a>', 'restrict-usernames-emails-characters');;?></p>
		<p></p>
		<p>2- <?php _e('In this "Restriction by part" parameter, if you are using the second or 3rd or 4th option and you have entered more than one line, you can modify the separator that appears in the error message by this hook:', 'restrict-usernames-emails-characters'); ?></p>
		<pre class="benrueeg_remove_all_filters">add_filter( 'filter_benrueeg_rue_partial_separator', function() {return ' | ';});</pre>
		</div></div></div>
		<?php } ?>
		
		<?php	if( $active_tab == 'extentions' ) { ?>
		<div class="wrap BENrueeg_RUE_wrap_padd"><div class="postbox"><div style="height:16px;" class="inside">
		<p style="padding-top:2px;" id="BENrueeg_RUE_wrap_t"><span class="dashicons dashicons-admin-plugins"></span> <?php _e('Other plugins of my development', 'restrict-usernames-emails-characters');?></p>
		</div></div></div>
		
		<?php
		
		include_once('admin/my-plugins.php'); 
		
		} ?>
		
		<div class="wrap BENrueeg_RUE_wrap_bott"><div class="postbox"><div class="inside">
		<p id="BENrueeg_RUE_wrap_t"><span class="dashicons dashicons-star-filled"></span> <?php _e("The evaluation of the plugin is important for continuity, If you're finding this plugin useful, please rate it on: <a target='_blank' href='https://wordpress.org/support/plugin/restrict-usernames-emails-characters/reviews/?filter=5'>this link</a>", 'restrict-usernames-emails-characters');?></p>
		</div></div></div>
		<?php
		
		printf(
		'<div class="BENrueeg_RUE_bottom">
		<div>© Copyright 2016 - %s <a target="_blank" href="https://benaceur-php.com/">benaceur php</a></div>
		<div>This is the support of the plugin: <a target="_blank" href="https://benaceur-php.com/?p=2268">support</a></div>
		</div>',
		date('Y')
		);
		
		$this->dir_css();
		
		}
		
	}
		
	endif;				