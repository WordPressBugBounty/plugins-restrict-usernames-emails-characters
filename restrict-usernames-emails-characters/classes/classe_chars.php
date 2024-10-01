<?php
	
	if ( ! class_exists( 'ben_plug_restrict_usernames_emails_characters_CHARS' ) ) :
	
	class ben_plug_restrict_usernames_emails_characters_CHARS extends ben_plug_restrict_usernames_emails_characters_validation {
		
		public function __construct() {
			parent::__construct();
		}
		
		public function func__CHARS( $username, $raw_username, $strict ) {
			global $wpdb,$pagenow;
			
			$wp_username = $username;
			
			// $raw_username: The username prior to sanitization
			$dis_all_symbs = $this->options('all_symbs');
			
			$lang = $this->options('lang');
			
			$allow_spc_cars = $this->options('allow_spc_cars');
		    $list_chars_ = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $allow_spc_cars))));
			$list_chars = implode('\\', $list_chars_);
			
			//Strip HTML Tags
			//$username = $this->ben_wp_strip_all_tags($raw_username);
			$username = wp_strip_all_tags($raw_username);
			
			if ( empty($allow_spc_cars) && $lang != 'all_lang' || (empty($allow_spc_cars) && $lang == 'all_lang' && $dis_all_symbs) ) {
			    $username = remove_accents ($username);
			} elseif ( (! empty($allow_spc_cars) && $lang != 'all_lang') || (! empty($allow_spc_cars) && $lang == 'all_lang' && $dis_all_symbs) ) {
			    $username = $this->benrueeg_remove_accents ($username);
			}

            //$old_username = $username;
			
	        // Remove percent-encoded characters.
	        $username = preg_replace( '|%([a-fA-F0-9][a-fA-F0-9])|', '', $username );
	        // Remove HTML entities.
	        $username = preg_replace( '/&.+?;/', '', $username );
	        /*
			if (trim($username) == '') { // to resolve this problem: notice: function wp_object_cache::add was called incorrectly. cache key must not be an empty string.
				$username = $old_username;
				return $username;
			}
	        */
			if ($strict)
			{
				
				$user_name  = isset($_POST['user_login']) ? $_POST['user_login']: '';
		        $user_id    = isset($_POST['user_id']) ? $_POST['user_id']: 0;
				$user_id    = $user_id && current_user_can( 'edit_user', $user_id ) ? $user_id : false;
	            $user = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $wpdb->users WHERE ID = %d", $user_id) );
				
				if ( $user ) {
		            $user_id    = $user->ID;
					$user_login = $user->user_login;
	            } else {
		            $user_id    = false;
					$user_login = '';
	            }
				
				$username = $this->options('varchar') != 'enabled' ? $wp_username : $this->get_lang__($username);
				
				/*
				- if user exist (updating) to prevent "Cannot create a user with an empty login name" error when this language is disabled
				$username != $raw_username (this language "$user_login" is not selected)
				- if update and not new user login and in 'user-edit.php' or 'profile.php' and $user_id exist and user_login containts non latin characters
				*/
				$update = isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'update' ? true : false;
				$user_name = $user_name ? $user_name == $user_login : true;
				$username = $update && ($username != $raw_username) && $user_id && $user_name && is_admin() && in_array($pagenow, apply_filters( 'benrueeg_rue_sanitize_user_pages', array('user-edit.php','profile.php'))) && apply_filters( 'benrueeg_rue_sanitize_user', true ) ? rawurldecode($raw_username) : $username;
			}
			
			return $username;
		}
		
    function benrueeg_remove_accents( $text, $locale = '' ) {
	if ( ! preg_match( '/[\x80-\xff]/', $text ) ) {
		return $text;
	}

	if ( seems_utf8( $text ) ) {

		/*
		 * Unicode sequence normalization from NFD (Normalization Form Decomposed)
		 * to NFC (Normalization Form [Pre]Composed), the encoding used in this function.
		 */
		if ( function_exists( 'normalizer_is_normalized' )
			&& function_exists( 'normalizer_normalize' )
		) {
			if ( ! normalizer_is_normalized( $text ) ) {
				$text = normalizer_normalize( $text );
			}
		}

		$chars = array(
			// Decompositions for Latin-1 Supplement.
			'ª' => 'a',
			'º' => 'o',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'AE',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ð' => 'D',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'TH',
			'ß' => 's',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'ae',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'd',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ü' => 'u',
			'ý' => 'y',
			'þ' => 'th',
			'ÿ' => 'y',
			'Ø' => 'O',
			// Decompositions for Latin Extended-A.
			'Ā' => 'A',
			'ā' => 'a',
			'Ă' => 'A',
			'ă' => 'a',
			'Ą' => 'A',
			'ą' => 'a',
			'Ć' => 'C',
			'ć' => 'c',
			'Ĉ' => 'C',
			'ĉ' => 'c',
			'Ċ' => 'C',
			'ċ' => 'c',
			'Č' => 'C',
			'č' => 'c',
			'Ď' => 'D',
			'ď' => 'd',
			'Đ' => 'D',
			'đ' => 'd',
			'Ē' => 'E',
			'ē' => 'e',
			'Ĕ' => 'E',
			'ĕ' => 'e',
			'Ė' => 'E',
			'ė' => 'e',
			'Ę' => 'E',
			'ę' => 'e',
			'Ě' => 'E',
			'ě' => 'e',
			'Ĝ' => 'G',
			'ĝ' => 'g',
			'Ğ' => 'G',
			'ğ' => 'g',
			'Ġ' => 'G',
			'ġ' => 'g',
			'Ģ' => 'G',
			'ģ' => 'g',
			'Ĥ' => 'H',
			'ĥ' => 'h',
			'Ħ' => 'H',
			'ħ' => 'h',
			'Ĩ' => 'I',
			'ĩ' => 'i',
			'Ī' => 'I',
			'ī' => 'i',
			'Ĭ' => 'I',
			'ĭ' => 'i',
			'Į' => 'I',
			'į' => 'i',
			'İ' => 'I',
			'ı' => 'i',
			'Ĳ' => 'IJ',
			'ĳ' => 'ij',
			'Ĵ' => 'J',
			'ĵ' => 'j',
			'Ķ' => 'K',
			'ķ' => 'k',
			'ĸ' => 'k',
			'Ĺ' => 'L',
			'ĺ' => 'l',
			'Ļ' => 'L',
			'ļ' => 'l',
			'Ľ' => 'L',
			'ľ' => 'l',
			'Ŀ' => 'L',
			'ŀ' => 'l',
			'Ł' => 'L',
			'ł' => 'l',
			'Ń' => 'N',
			'ń' => 'n',
			'Ņ' => 'N',
			'ņ' => 'n',
			'Ň' => 'N',
			'ň' => 'n',
			'ŉ' => 'n',
			'Ŋ' => 'N',
			'ŋ' => 'n',
			'Ō' => 'O',
			'ō' => 'o',
			'Ŏ' => 'O',
			'ŏ' => 'o',
			'Ő' => 'O',
			'ő' => 'o',
			'Œ' => 'OE',
			'œ' => 'oe',
			'Ŕ' => 'R',
			'ŕ' => 'r',
			'Ŗ' => 'R',
			'ŗ' => 'r',
			'Ř' => 'R',
			'ř' => 'r',
			'Ś' => 'S',
			'ś' => 's',
			'Ŝ' => 'S',
			'ŝ' => 's',
			'Ş' => 'S',
			'ş' => 's',
			'Š' => 'S',
			'š' => 's',
			'Ţ' => 'T',
			'ţ' => 't',
			'Ť' => 'T',
			'ť' => 't',
			'Ŧ' => 'T',
			'ŧ' => 't',
			'Ũ' => 'U',
			'ũ' => 'u',
			'Ū' => 'U',
			'ū' => 'u',
			'Ŭ' => 'U',
			'ŭ' => 'u',
			'Ů' => 'U',
			'ů' => 'u',
			'Ű' => 'U',
			'ű' => 'u',
			'Ų' => 'U',
			'ų' => 'u',
			'Ŵ' => 'W',
			'ŵ' => 'w',
			'Ŷ' => 'Y',
			'ŷ' => 'y',
			'Ÿ' => 'Y',
			'Ź' => 'Z',
			'ź' => 'z',
			'Ż' => 'Z',
			'ż' => 'z',
			'Ž' => 'Z',
			'ž' => 'z',
			'ſ' => 's',
			// Decompositions for Latin Extended-B.
			'Ə' => 'E',
			'ǝ' => 'e',
			'Ș' => 'S',
			'ș' => 's',
			'Ț' => 'T',
			'ț' => 't',
			// Euro sign.
			'€' => 'E',
			// GBP (Pound) sign.
			'£' => '',
			// Vowels with diacritic (Vietnamese). Unmarked.
			'Ơ' => 'O',
			'ơ' => 'o',
			'Ư' => 'U',
			'ư' => 'u',
			// Grave accent.
			'Ầ' => 'A',
			'ầ' => 'a',
			'Ằ' => 'A',
			'ằ' => 'a',
			'Ề' => 'E',
			'ề' => 'e',
			'Ồ' => 'O',
			'ồ' => 'o',
			'Ờ' => 'O',
			'ờ' => 'o',
			'Ừ' => 'U',
			'ừ' => 'u',
			'Ỳ' => 'Y',
			'ỳ' => 'y',
			// Hook.
			'Ả' => 'A',
			'ả' => 'a',
			'Ẩ' => 'A',
			'ẩ' => 'a',
			'Ẳ' => 'A',
			'ẳ' => 'a',
			'Ẻ' => 'E',
			'ẻ' => 'e',
			'Ể' => 'E',
			'ể' => 'e',
			'Ỉ' => 'I',
			'ỉ' => 'i',
			'Ỏ' => 'O',
			'ỏ' => 'o',
			'Ổ' => 'O',
			'ổ' => 'o',
			'Ở' => 'O',
			'ở' => 'o',
			'Ủ' => 'U',
			'ủ' => 'u',
			'Ử' => 'U',
			'ử' => 'u',
			'Ỷ' => 'Y',
			'ỷ' => 'y',
			// Tilde.
			'Ẫ' => 'A',
			'ẫ' => 'a',
			'Ẵ' => 'A',
			'ẵ' => 'a',
			'Ẽ' => 'E',
			'ẽ' => 'e',
			'Ễ' => 'E',
			'ễ' => 'e',
			'Ỗ' => 'O',
			'ỗ' => 'o',
			'Ỡ' => 'O',
			'ỡ' => 'o',
			'Ữ' => 'U',
			'ữ' => 'u',
			'Ỹ' => 'Y',
			'ỹ' => 'y',
			// Acute accent.
			'Ấ' => 'A',
			'ấ' => 'a',
			'Ắ' => 'A',
			'ắ' => 'a',
			'Ế' => 'E',
			'ế' => 'e',
			'Ố' => 'O',
			'ố' => 'o',
			'Ớ' => 'O',
			'ớ' => 'o',
			'Ứ' => 'U',
			'ứ' => 'u',
			// Dot below.
			'Ạ' => 'A',
			'ạ' => 'a',
			'Ậ' => 'A',
			'ậ' => 'a',
			'Ặ' => 'A',
			'ặ' => 'a',
			'Ẹ' => 'E',
			'ẹ' => 'e',
			'Ệ' => 'E',
			'ệ' => 'e',
			'Ị' => 'I',
			'ị' => 'i',
			'Ọ' => 'O',
			'ọ' => 'o',
			'Ộ' => 'O',
			'ộ' => 'o',
			'Ợ' => 'O',
			'ợ' => 'o',
			'Ụ' => 'U',
			'ụ' => 'u',
			'Ự' => 'U',
			'ự' => 'u',
			'Ỵ' => 'Y',
			'ỵ' => 'y',
			// Vowels with diacritic (Chinese, Hanyu Pinyin).
			'ɑ' => 'a',
			// Macron.
			'Ǖ' => 'U',
			'ǖ' => 'u',
			// Acute accent.
			'Ǘ' => 'U',
			'ǘ' => 'u',
			// Caron.
			'Ǎ' => 'A',
			'ǎ' => 'a',
			'Ǐ' => 'I',
			'ǐ' => 'i',
			'Ǒ' => 'O',
			'ǒ' => 'o',
			'Ǔ' => 'U',
			'ǔ' => 'u',
			'Ǚ' => 'U',
			'ǚ' => 'u',
			// Grave accent.
			'Ǜ' => 'U',
			'ǜ' => 'u',
		);
		
		$allow_spc_cars = $this->options('allow_spc_cars');
		$list_chars = array_filter(array_unique(array_map('trim', explode(PHP_EOL, $allow_spc_cars))));
		$chars = $this->array_remove_keys($chars, $list_chars);

		// Used for locale-specific rules.
		if ( empty( $locale ) ) {
			$locale = get_locale();
		}

		/*
		 * German has various locales (de_DE, de_CH, de_AT, ...) with formal and informal variants.
		 * There is no 3-letter locale like 'def', so checking for 'de' instead of 'de_' is safe,
		 * since 'de' itself would be a valid locale too.
		 */
		if ( str_starts_with( $locale, 'de' ) ) {
			$chars['Ä'] = 'Ae';
			$chars['ä'] = 'ae';
			$chars['Ö'] = 'Oe';
			$chars['ö'] = 'oe';
			$chars['Ü'] = 'Ue';
			$chars['ü'] = 'ue';
			$chars['ß'] = 'ss';
		} elseif ( 'da_DK' === $locale ) {
			$chars['Æ'] = 'Ae';
			$chars['æ'] = 'ae';
			$chars['Ø'] = 'Oe';
			$chars['ø'] = 'oe';
			$chars['Å'] = 'Aa';
			$chars['å'] = 'aa';
		} elseif ( 'ca' === $locale ) {
			$chars['l·l'] = 'll';
		} elseif ( 'sr_RS' === $locale || 'bs_BA' === $locale ) {
			$chars['Đ'] = 'DJ';
			$chars['đ'] = 'dj';
		}

		$text = strtr( $text, $chars );
	} else {
		$chars = array();
		// Assume ISO-8859-1 if not UTF-8.
		$chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
			. "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
			. "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
			. "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
			. "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
			. "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
			. "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
			. "\xec\xed\xee\xef\xf1\xf2\xf3"
			. "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
			. "\xfc\xfd\xff";

		$chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';

		$text                = strtr( $text, $chars['in'], $chars['out'] );
		$double_chars        = array();
		$double_chars['in']  = array( "\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe" );
		$double_chars['out'] = array( 'OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th' );
		$text                = str_replace( $double_chars['in'], $double_chars['out'], $text );
	}

	return $text;
    }
	
	function scriptNames() {
	return array(
    'Arabic',
    'Armenian',
    'Avestan',
    'Balinese',
    'Bamum',
    'Batak',
    'Bengali',
    'Bopomofo',
    'Brahmi',
    'Braille',
    'Buginese',
    'Buhid',
    'Canadian_Aboriginal',
    'Carian',
    'Chakma',
    'Cham',
    'Cherokee',
    'Common',
    'Coptic',
    'Cuneiform',
    'Cypriot',
    'Cyrillic',
    'Deseret',
    'Devanagari',
    'Egyptian_Hieroglyphs',
    'Ethiopic',
    'Georgian',
    'Glagolitic',
    'Gothic',
    'Greek',
    'Gujarati',
    'Gurmukhi',
    'Han',
    'Hangul',
    'Hanunoo',
    'Hebrew',
    'Hiragana',
    'Imperial_Aramaic',
    'Inherited',
    'Inscriptional_Pahlavi',
    'Inscriptional_Parthian',
    'Javanese',
    'Kaithi',
    'Kannada',
    'Katakana',
    'Kayah_Li',
    'Kharoshthi',
    'Khmer',
    'Lao',
    'Latin',
    'Lepcha',
    'Limbu',
    'Linear_B',
    'Lisu',
    'Lycian',
    'Lydian',
    'Malayalam',
    'Mandaic',
    'Meetei_Mayek',
    'Meroitic_Cursive',
    'Meroitic_Hieroglyphs',
    'Miao',
    'Mongolian',
    'Myanmar',
    'New_Tai_Lue',
    'Nko',
    'Ogham',
    'Old_Italic',
    'Old_Persian',
    'Old_South_Arabian',
    'Old_Turkic',
    'Ol_Chiki',
    'Oriya',
    'Osmanya',
    'Phags_Pa',
    'Phoenician',
    'Rejang',
    'Runic',
    'Samaritan',
    'Saurashtra',
    'Sharada',
    'Shavian',
    'Sinhala',
    'Sora_Sompeng',
    'Sundanese',
    'Syloti_Nagri',
    'Syriac',
    'Tagalog',
    'Tagbanwa',
    'Tai_Le',
    'Tai_Tham',
    'Tai_Viet',
    'Takri',
    'Tamil',
    'Telugu',
    'Thaana',
    'Thai',
    'Tibetan',
    'Tifinagh',
    'Ugaritic',
    'Vai',
    'Yi'
    );
	}
				
	}
				
	endif;