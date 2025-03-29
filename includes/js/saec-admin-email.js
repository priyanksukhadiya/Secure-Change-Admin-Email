jQuery(document).ready(function() {
    var insertInputButton = "<br /><input type='submit' class='button button-primary' name='secure-admin-email-change' id='secure-admin-email-changeButton' value='Change Email' /><br/><span style = 'font-size:75%;'>View the Secure Admin Email Change plugin complete privacy policy <a href='https://primaldevs.com/saec-privacy-policy.html' id='privacyPolicyLink'>here</a>.</span>";
    jQuery(insertInputButton).insertAfter("#new-admin-email-description");

    jQuery("#secure-admin-email-changeButton").click(function(event){
        var insertThisNonce = "<input type='hidden' name='secureAdminEmailAction' value='changeEmail' /> <input type='hidden' name='saec-plugin-nonce' value='" + change_admin_email_data.nonce + "' />";
        console.log(change_admin_email_data.nonce);
        jQuery(insertThisNonce).insertAfter("#new-admin-email-description");
        event.preventDefault();
        jQuery("#submit").click();
    });

    jQuery("#new-admin-email-description").text("This address is used for admin purposes.");        
});
