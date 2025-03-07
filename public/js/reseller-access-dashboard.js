/**
 * Frontend dashboard JavaScript for the Reseller Access plugin.
 *
 * @since 1.0.0
 */
(function( $ ) {
    'use strict';

    /**
     * Document ready function.
     */
    $(document).ready(function() {
        // Handle profile update form
        $('#reseller-profile-form').on('submit', function(e) {
            e.preventDefault();
            updateProfile();
        });
        
        // Handle password update form
        $('#reseller-password-form').on('submit', function(e) {
            e.preventDefault();
            updatePassword();
        });
    });

    /**
     * Update the user profile via AJAX.
     */
    function updateProfile() {
        $.ajax({
            url: reseller_access_dashboard.ajax_url,
            type: 'post',
            data: {
                action: 'reseller_update_profile',
                nonce: reseller_access_dashboard.nonce,
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                email: $('#email').val()
            },
            beforeSend: function() {
                $('#profile-update-message').removeClass('success error').hide();
            },
            success: function(response) {
                if (response.success) {
                    $('#profile-update-message').removeClass('error').addClass('success').html(response.data.message).show();
                } else {
                    $('#profile-update-message').removeClass('success').addClass('error').html(response.data.message).show();
                }
            },
            error: function() {
                $('#profile-update-message').removeClass('success').addClass('error').html(reseller_access_dashboard.messages.error).show();
            }
        });
    }

    /**
     * Update the user password via AJAX.
     */
    function updatePassword() {
        // Basic validation
        const newPassword = $('#new_password').val();
        const confirmPassword = $('#confirm_password').val();
        
        if (newPassword !== confirmPassword) {
            $('#password-update-message').removeClass('success').addClass('error').html(reseller_access_dashboard.messages.password_mismatch).show();
            return;
        }
        
        $.ajax({
            url: reseller_access_dashboard.ajax_url,
            type: 'post',
            data: {
                action: 'reseller_update_password',
                nonce: reseller_access_dashboard.nonce,
                current_password: $('#current_password').val(),
                new_password: newPassword,
                confirm_password: confirmPassword
            },
            beforeSend: function() {
                $('#password-update-message').removeClass('success error').hide();
            },
            success: function(response) {
                if (response.success) {
                    $('#password-update-message').removeClass('error').addClass('success').html(response.data.message).show();
                    $('#reseller-password-form')[0].reset();
                } else {
                    $('#password-update-message').removeClass('success').addClass('error').html(response.data.message).show();
                }
            },
            error: function() {
                $('#password-update-message').removeClass('success').addClass('error').html(reseller_access_dashboard.messages.error).show();
            }
        });
    }

})( jQuery );
