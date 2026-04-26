// Display the content to be copied (mu-plugins)
jQuery(document).ready(function() {
    jQuery('#benrueeg_44_content_copied').click(function() {
    jQuery('#benrueeg_44_getContentMuPlugin').slideToggle("slow");
    });
});
// Display the content to be copied (mu-plugins)

// notice-dismiss Ajax
jQuery(document).ready(function($) {
    // Listen for clicks on the dismiss button for a specific notice class
    $(document).on('click', '.notice-benrueeg_old_user_login_invalid .notice-dismiss', function() {
        // Make an AJAX call to your PHP handler
        $.ajax({
            url: ajaxurl, // ajaxurl is a global variable in the admin
            type: 'POST',
            data: {
                action: 'dismissed_notice_old_user_login_invalid', // This action name maps to your PHP function
                notice_type: 'ajax_benrueeg_old_user_login_invalid_type'
            }
        });
    });
	
    $(document).on('click', '.notice-new_note_important_signups_1_9 .notice-dismiss', function() {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'dismissed_notice_old_user_login_invalid',
                notice_type: 'ajax_benrueeg_new_note_important_signups_1_9_type'
            }
        });
    });
});
// notice-dismiss Ajax