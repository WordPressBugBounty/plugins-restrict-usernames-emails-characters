<?php
if ( ! defined( 'ABSPATH' ) ) exit;

trait benrueeg_class_rue_plug_admin_notes {

	function admin_notes_settings() {
		?>
		<div class="inside">
			<p><b>-</b> <?php _e('In the "Error Messages" section, you can easily customize the message text. These messages relate to allowed or prohibited information for the username and email address during new member registration or when updating the user profile, etc., By means of the special programming for this plugin. Other messages appear, for example, in the member&#39;s profile when updating their data, if the BuddyPress or BuddyBoss-Platform extensions is installed, such as "Last name is required and not allowed to be empty" or "Your current password is invalid" These messages do not require any modification to their programming by our plugin, because they are correct and should remain as is. Therefore, you only need to translate them or modify their text if you wish. To do this, use a translation plugin or insert the following code into your theme&#39;s functions.php file:', 'restrict-usernames-emails-characters'); ?></p>
			<p><?php _e('For example', 'restrict-usernames-emails-characters'); ?> (buddypress/buddyboss-platform):</p>
<pre class="benrueeg_remove_all_filters">
add_filter( 'gettext', function ( $translations, $text ) {
	
if ( function_exists( 'bp_is_my_profile' ) && bp_is_my_profile() ) {
	switch ( $text ) {
	case 'No changes were made to your account.':
		$translations = 'Your new message.';
		break;
	case 'That email address is currently unavailable for use.':
		$translations = 'Your new message.';
		break;
	case 'This email address or domain has been blacklisted. If you think you are seeing this in error, please contact the site administrator.':
		$translations = 'Your new message.';
		break;
	case 'Your current password is invalid.':
		$translations = 'Your new message.';
		break;
	case 'The new password fields did not match.':
		$translations = 'Your new message.';
		break;
	case 'One of the password fields was empty.':
		$translations = 'Your new message.';
		break;
	case 'The new password must be different from the current password.':
		$translations = 'Your new message.';
		break;
	case 'Please fill in all required fields, and save your changes again.':
		$translations = 'Your new message.';
		break;
	case '%s is required and not allowed to be empty.':
		$translations = '%s Your new message.';
		break;
	case 'Your settings have been saved.':
		$translations = 'Your new message.';
		break;
	case 'No changes were made to your account.':
		$translations = 'Your new message.';
		break;
	case 'No changes were made to this account.':
		$translations = 'Your new message.';
		break;
	case 'There was a problem updating some of your profile information. Please try again.':
		$translations = 'Your new message.';
		break;
	case 'Changes saved.':
		$translations = 'Your new message.';
		break;
	}
}

/*
* Extended Profile
*/
if ( isset( $_GET['page'] ) && ( 'bp-profile-edit' === $_GET['page'] ) ) {
	switch ( $text ) {
	case 'Your changes have not been saved. Please fill in all required fields, and save your changes again.':
		$translations = 'Your new message.';
		break;
	case 'There was a problem updating some of your profile information. Please try again.':
		$translations = 'Your new message.';
		break;
	case 'Profile updated.':
		$translations = 'Your new message.';
		break;
	}
}

if ( $text == '&#60;strong&#62;Error:&#60;/strong&#62; Please enter a password.' /* && here your condition */ )  {
	$translations = '&#60;strong&#62;Error:&#60;/strong&#62; Your new message.';
}

return $translations;
}, 10, 2 );
</pre>
			<p><b>------------</b></p>
			<p><b>-</b> <?php _e("By default, registration errors for a new user do not appear in the administration panel (backend) for those with the ability to create new users (can_create_users), such as the administrator, except for fatal errors like illegal characters. The errors that do not appear to the administrator are: &#34;Error: space in the username (not in multisite, baddypress, baddyboss), only numbers in the username,  restricted emails, restricted usernames, min length of the username, max length of the username, part of the username, The digits less than characters,  No uppercase (A-Z) in username, usernames that are email addresses&#34;. If you want to apply all errors in the backend for everyone, then enable the option: &#34;Enable all errors in backend/frontend for all cap&#34;. However, if you want to change the &#34;can_create_users&#34; capability to other capability put this line in the file functions.php: ", 'restrict-usernames-emails-characters'); ?></p>
			<pre class="benrueeg_remove_all_filters">add_filter( 'benrueeg_cap_can_create_users_backend', function() {return 'a new cap here';});</pre>
			<p><b>------------</b></p>
			<p><b>-</b> <?php _e('In this "Restriction by part" parameter, if you are using the second or 3rd or 4th option and you have entered more than one line, you can modify the separator that appears in the error message by this hook:', 'restrict-usernames-emails-characters'); ?></p>
			<pre class="benrueeg_remove_all_filters">add_filter( 'filter_benrueeg_rue_partial_separator', function() {return ' | ';});</pre>
		    <?php if ( $this->bp() && ! $this->mu() ) { ?>
			<p><b>------------</b></p>
			<p><b>-</b> <?php _e('When a new member registers, their information is stored in the wp_signups table until their account is activated. After activation, the member is created in the Members table, but the information remains stored in the wp_signups table. This plugin removes this information from the table if someone tries to register with this old information (name or email address) to prevent problems related to having the same information for two different members and to clean the database. However, if you wish to disable this feature, simply add this code to the functions.php file of your active theme:', 'restrict-usernames-emails-characters'); ?></p>
			<pre class="benrueeg_remove_all_filters">add_filter( 'benrueeg_rue_delete_signup_item_activeted', '__return_false' );</pre>
		    <?php } ?>
		</div>
		<?php	
	}
	
}
