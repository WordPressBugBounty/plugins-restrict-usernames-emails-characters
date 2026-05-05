=== Restrict Usernames Emails Characters ===
Contributors: Benaceur 
Tags: restrict user, author slug, registration, anti-spam, security
Requires at least: 3.0
Tested up to: 6.9
Requires PHP: 5.4
Stable tag: 5.0.1
License: GPLv2 or later

Restrict the usernames, email addresses, characters and symbols or email from specific domain names or language in registration ...

== Description ==

This plugin allows you to Restrict a particular or certain username, email addresses or symbols,
or email from specific domain names in the form registration when registering for your site 
and you can allow to use a certain language (arabic cyrillic latin ...)
or all languages and characters and symbols, you can also control and modify all errors messages
and allow certain characters (Symbols and characters accented as é û),
and allowing you to change the author slug (defaults to the username of the author),
and you can control and adjust all settings from the plugin settings page in admin Panel. 

= and here is all plugin settings in admin Panel: =

* enable/disable the plugin
* disallow to use the spaces in username
* disallow to use only numbers in username
* disallow all characters (Symbols) in username
* disallow characters (Symbols) permitted by wordpress in username: @ - . _
* allow certain characters (Symbols and characters accented as é û)
* restrict certain email addresses
* restrict certain username
* restrict certain domain names for example: yournamesite@com
* No/yes uppercase in username
* Compatible with single site, network (multi-site), buddypress and buddyboss.
* The possibility to:
* choose language (characters) in username (arabic cyrillic latin ...) or all languages
* remove all settings and data of the plugin from database when the plugin is disabled
* reset default settings
* control and modify all errors messages
* restrict any name contains a part of word (partial matching)
* prevent the use of email in the username
* prevent the use of numbers more than letters and symbols in the user name.
* allowing you to change the author slug
* Author Slug Structure
* Update of the author's slug for all users
* Limit the number of users to update (in batches) with every click, if your database is big
* Update or convert only names (author slug) not latin
* remove name field in buddypress.
* hide or change message (Must be at least 4 characters, letters and numbers only.) of multisite.
* add an notice or text in registration form.
* etc...

= TRANSLATED IN FOLLOWING LANGUAGES: =
* Arabic
* English

= Direct support page: =
https://benaceur-php.com/?p=2268

== Installation ==

1. Upload Restrict Usernames Emails Characters to the "/wp-content/plugins/"
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Activate the plugin again in the control panel (the plugin page)
4. Control your settings from the plugin settings page in admin Panel.

== Screenshots ==

1. Options page admin panel-1
2. Options page admin panel-2
3. Options page admin panel-3
4. Options page admin panel-4
5. Options page admin panel-5
6. Options page admin panel-6
7. Options page admin panel-7
8. Options page admin panel-8
9. Options page admin panel-9
10. Options page admin panel-10

== Changelog ==

= 5.0.1 =
* Fixed an error: when saving user data in backend.
= 5.0 =
* Fixed error: The absence of the "benrueeg users" table if one or more blogs exist in a multisite.
* Fixed: author's slug when news users is activated in multisite if "Prevent the use of all Symbols and letters accented in the username" option is enabled.
* Fixed: compatibility with buddyboss.
* Fixed: error message in backend with multi and single site and baddypress and buddyboss.
* Fixed: error message for nickname in buddyboss.
* New option: "Choose the method to update the database, manually or automatically".
* New option: "Disable/Enable errors in backend for certain capability".
* Remove options: "The name of the user_login field in registration form" and "The name of the user_email field in registration form".
* Remove options: "Remove the name field from the form of registration" and "Hide the entire section of the profile in the form of registration" for baddypress and baddyboss. 
* Changing the class names.
* New filters: "filter_benrueeg_intval_update_process","benrueeg_filter_updb_per_get_users","benrueeg_cap_can_create_users","benrueeg_cap_can_create_users_backend","benrueeg_rue_delete_signup_item_activeted"
* Remove filters: "benrueeg_filter_class_txt_register_form".
* New error message: 'Error: password is required' (baddyboss).
* New error message: 'Error: Empty field' (baddypress, baddyboss).
* New error message: 'Error: email twice' (baddyboss).
* New error message: 'Error: email not match' (baddyboss).
* New error message: 'Error: password twice' (baddyboss).
* New error message: 'Error: password not match' (baddyboss).
* New error message: 'Error: password is not strong' (baddypress).
* New error message: 'Error: acceptance of the privacy policy is required' (baddypress).
* New error message: 'Error: Invalid username in activation process' (baddypress, baddyboss, multi-site).
* New error message: 'Error: Invalid username in activation process in backend (baddypress, baddyboss)'.
* New error message: 'Error: Existing username (activation pending) message in backend' (baddypress, baddyboss, multi-site).
* New error message: 'Error: Existing username (activation pending) message in frontend' (baddypress, baddyboss, multi-site).
* New error message: 'Error: Existing email address (activation pending) message in backend' (baddypress, baddyboss, multi-site).
* New error message: 'Error: Existing email address (activation pending) message in frontend' (baddypress, baddyboss, multi-site).
* Fix: User activation will be prevented if the language used for registration before activation has been changed, and an error message will be added indicating this, in (multi-site, baddypress and baddyboss) and in frontend and backend.
* Prevent username and email from being entered during registration if they already exist and are awaiting activation.
* Disabling certain options for those with "create_users" capability, such as limiting the number of characters in a name, allows them to create new users without restrictions, while also adding a filter (benrueeg_cap_can_create_users) for this purpose.
* Restrict using slashes when registering a new member.
* Restrict using (الحركات) in arabic language when registering a new member.
* New notes: Very important...
* Cancellation of direct removing of login_errors hook.
* Reset all errors messages in error messages section (due to changes made to certain error messages).
* Replacement: this filter "wp_signup_mu_filter_BENrueeg_RUE" by "benrueeg_rue_is_wp_signup_page_mu".
* Some general necessary modifications and corrections.
= 4.1.2 =
* Tested up to wordpress 6.9
* Tested up to php 8.5
* Deprecated "seems_utf8" function in wordpress 6.9
* Fix: "Allow this characters (Symbols or characters accented ...)" option.
= 4.1.1 =
* Tested up to wordpress 6.7
= 4.1 =
* New: add compatibility with buddyboss platform.
* New: add first name and last name in "Author Slug Structure" option.
* Fix: when "Prevent the use of all Symbols and letters accented in the username" option is enabled.
* Fix: "Choose language (characters) in username ->  Enter another language below" option, If we enter an invalid language.
* Requires PHP version: minimum 5.3.19
* New option: Remove the submit button (top): Ability to remove the send button at the top.
* Tested up to wordpress 6.6
= 4.0.2 =
* Fixed: "Prevent the use of all Symbols and letters accented in the username" option.
= 4.0.1 =
* Important fixes.
= 4.0 =
* Fixed: "Allow this characters" option and block of &#60; &#62; symbols.
* Changing the way to save the user_nicename in the database when registering or updating the database.
* You can now divide the total database update by the number of users to reduce the load on the server if your database is very big.
* Fixed: user nicename issue with non-Latin languages like Arabic when registering a new user or updating a user.
* Remove restrict username email character mu-plugins.
* New filters: "user_nicename_updb_filter_benrueeg_rue" and "user_nicename_register_filter_benrueeg_rue".
* New option: "Limit the number of users to update (in batches) with every click", "Update (convert) only user_nicename not latin".
* New option: "Author Slug Structure".
* New option: "Update (convert) only names (author slug) not latin".
* New notes.
* Reinstatement of the option: "Error: multi whitespace and at the beginning or the end of the username".
* Tested up to wordpress 6.5
* Other important adjustments.
= 3.1.4 =
* Fixing a security issue (relatively low-risk).
* Tested up to wordpress 6.4
* Fix: Replace (Must be at least 4 characters ...) translation option in multisite.
= 3.1.3 =
* Tested up to wordpress 6.3
= 3.1.2 =
* Tested up to wordpress 6.2
= 3.1.1 =
* Fix: Some modifications needed for compatibility with php 8.1+ (for the moment wordpress is not 100% compatible with 8.1+).
= 3.1 =
* Tested up to wordpress 6.1.
* Fixed: user nicename non Latin when a user's profile is updated with wordpress 6.1
= 3.0 =
* Fixed: user nicename issue with non-Latin languages like Arabic when wordpress is updated or database is upgraded.
* Fixed: user nicename issue with non-Latin languages like Arabic when registering a new user or updating a user.
* Fixed: "Choose language (characters) in username" option.
* Add new option: "Choose the user_nicename".
* Some other important adjustments.
= 2.9.7 =
* Fixed: preg_match_all.
= 2.9.6 =
* Add new option: "Make lowercase equal uppercase".
* Tested up to wordpress 5.8
* Some adjustments.
= 2.9.5 =
* trim the blank space in username.
= 2.9.4 =
* Tested up to wordpress 5.7
= 2.9.3 =
* Change of sanitize user filter priority.
= 2.9.2 =
* Tested up to wordpress 5.6
= 2.9.1 =
* Tested up to wordpress 5.5.
* Some adjustments in page plugin options in admin panel.
= 2.9 =
* Adding an option to change the name of username if the field 'name' of user_name in the registration form is changed.
* Adding an option to change the name of email if the field 'name' of user_email in the registration form is changed.
* Adding the options page of plugin to only network page if the multi site is installed.
* Remove option 'Allow spaces in usernames' if baddypress or multisite is enabled.
= 2.8.2 =
* Add array_filter and array_unique to array.
= 2.8.1 =
* Fixed a problem with "array_filter" in options: "Not allow these names" and "Restriction by part (contain,doesn’t contain,starts with,ends with)".
* Fixed an error in the import process of the settings file.
* Fixed an java error in options administration page.
= 2.8 =
* Tested with the latest wordpress update (5.4).
* Fixed an error when entering a language that does not exist, in "Choose language -> Enter another langage below".
* Replacing "ERROR" with "Error" in wordpress (5.4).
* Adding "text direction" option in textarea in options administration page.
* New option in "Restriction by part (contain,doesn’t contain,starts with,ends with)".
* New option: "Restrict everything except the following (after @)" and "Restrict everything except the following (after .)" in "Not allow these emails domain".
* New option "Solved the problem of not being able to register with certain languages".
* Adding an class (css) in "Add text (notice) to the registration form" option by this filter: "benrueeg_filter_class_txt_register_form".
* Fixed an error in "Import Settings" option.
* new note in "Important to read".
* Tested with the latest php version (5.1.5 to 7.4.x).
* Some other important adjustments.
= 2.7.3 =
* Tested with the latest wordpress update (5.3).
= 2.7.2 =
* Tested with the latest wordpress update (5.2).
* Some corrections.
= 2.7.1 =
* Direct support page.
= 2.7 =
* Fixed an issue if space is allowed in username in baddypress and multisite.
* Fixed an issue in other errors message in baddypress and multisite.
* Remove (Allow multi whitespace and space at the beginning or the end of the username) option.
* Added the possibility to remove name field and the possibility to hide the full profile section in baddypress.
* Remove this filter: "benrueeg_rue_filter_trans_err_must".
* Added new filters (old_options_tw_mupb_filter_BENrueeg_RUE,old_options_tw_word_filter_BENrueeg_RUE).
* Some other necessary adjustments and corrections.
= 2.6 =
* Fixed some errors that are generated in log of errors.
* Some other important adjustments.
= 2.5 =
* Fixed an issue in language (Choose language (characters) in username).
= 2.4.3 =
* An important adjustment.
= 2.4.2 =
* Fixed an issue in errors message if username (user login) exist and it's numeric and beginning is +, example: +258694.
* New filter (wp_signup_mu_filter_BENrueeg_RUE).
= 2.4.1 =
* Fixed an issue in some errors.
* An adjustment of priority of error messages.
* An adjustment in the style of settings page.
= 2.4 =
* Compatibility with plugins of registering.
* Restrict these symbols ( ' \ " ) to avoid problems when registering.
* New error messages.
* added an error message when you press the Import button with empty file or invalid json.
* An important adjustments.
= 2.3 =
* Fixed a problem in username restricted (in multisite and buddypress).
* Added new language (العربية المغربية).
* An adjustment in compatibility (old versions of wordpress).
* Added a notification if the registration is disabled.
* Other important adjustments.
= 2.2.3 =
* Fixed a problem in uppercase option.
* Fixed a problem if the username exist (in multisite or buddypress).
* Other adjustments.
= 2.2.2 =
* Fixed a problem if the username exist (in multisite or buddypress).
* Other adjustments.
= 2.2.1 =
* Added the possibility to not allowed to use multi whitespace or whitespace at the beginning or the end of the username.
* Added some filters.
* Some other adjustments.
= 2.2 =
* Compatibility with network (multi-site).
* Compatibility buddypress.
* No uppercase in username.
* Fixed a problem if a language is selected with latin.
* Added the possibility to display the restricted part in error message (partial matching).
* Prevent the use of numbers more than letters and symbols in the user name.
* Added the error message for (partial matching).
* Arrange (order) error messages.
* Prevent the use of email in the username.
* Some adjustments and corrections.
= 2.1 =
* Fixed a problem if a language is selected with latin.
* Added the possibility to display the restricted part in error message (partial matching).
* Prevent the use of numbers more than letters and symbols in the user name.
* Added the error message for (partial matching).
* Arrange (order) error messages.
= 2.0 =
* Added the possibility to restrict any name contains a part of word (partial matching).
* Tested with the latest wordpress update (4.8).
= 1.2.2 =
* Fixed some translation errors in the error messages.
= 1.2.1 =
* An adjustment in reset options.
= 1.2 =
* Some corrections.
= 1.1.4 =
* Some corrections.
= 1.1.3 =
* Added the possibility to control the settings by other capability.
* Some adjustments in translation.
= 1.1.2 =
* Fixed a problem if the field of language is empty.
* Add the possibility to limit the length of the username (min and max) and take account the space. 
= 1.1.1 =
* Added the possibility of export and import plugin settings. 
* Added the possibility to enter your language or another language. 
* Some adjustments and corrections.
= 1.1 =
* Some adjustments in page plugin options in admin panel.
= 1.0 =
* First released version.