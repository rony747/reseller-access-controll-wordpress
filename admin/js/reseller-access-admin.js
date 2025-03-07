/**
 * Admin JavaScript for the Reseller Access plugin.
 *
 * @since 1.0.0
 */
(function( $ ) {
    'use strict';

    /**
     * Document ready function.
     */
    $(document).ready(function() {
        // Load resellers on page load
        loadResellers();
        
        // Handle reseller creation form
        $('#create-reseller-form').on('submit', function(e) {
            e.preventDefault();
            createReseller();
        });
        
        // Handle reseller deletion
        $(document).on('click', '.delete-reseller', function() {
            if (confirm(reseller_access_ajax.confirm_delete_text || 'Are you sure you want to delete this reseller?')) {
                const userId = $(this).data('user-id');
                deleteReseller(userId);
            }
        });
    });

    /**
     * Load resellers via AJAX.
     */
    function loadResellers() {
        $.ajax({
            url: reseller_access_ajax.ajax_url,
            type: 'get',
            data: {
                action: 'get_resellers',
                nonce: reseller_access_ajax.nonce
            },
            beforeSend: function() {
                $('#resellers-list').html('<tr><td colspan="5">Loading resellers...</td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    displayResellers(response.data.resellers);
                } else {
                    $('#resellers-list').html('<tr><td colspan="5">Error: ' + response.data.message + '</td></tr>');
                }
            },
            error: function() {
                $('#resellers-list').html('<tr><td colspan="5">Error: Could not load resellers</td></tr>');
            }
        });
    }

    /**
     * Display resellers in the table.
     * 
     * @param {Array} resellers Array of reseller objects.
     */
    function displayResellers(resellers) {
        if (!resellers || resellers.length === 0) {
            $('#resellers-list').html('<tr><td colspan="5">No resellers found</td></tr>');
            return;
        }
        
        let html = '';
        
        $.each(resellers, function(index, reseller) {
            html += '<tr>';
            html += '<td>' + reseller.username + '</td>';
            html += '<td>' + reseller.first_name + ' ' + reseller.last_name + '</td>';
            html += '<td>' + reseller.email + '</td>';
            html += '<td>' + formatDate(reseller.registered) + '</td>';
            html += '<td>';
            html += '<a href="' + reseller.edit_url + '" class="button-secondary">Edit</a> ';
            html += '<button class="button-secondary delete-reseller" data-user-id="' + reseller.id + '">Delete</button>';
            html += '</td>';
            html += '</tr>';
        });
        
        $('#resellers-list').html(html);
    }

    /**
     * Create a new reseller via AJAX.
     */
    function createReseller() {
        const formData = $('#create-reseller-form').serialize();
        
        $.ajax({
            url: reseller_access_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'create_reseller',
                nonce: reseller_access_ajax.nonce,
                username: $('#username').val(),
                email: $('#email').val(),
                first_name: $('#first_name').val(),
                last_name: $('#last_name').val(),
                password: $('#password').val(),
                send_notification: $('input[name="send_notification"]:checked').val() || 'no'
            },
            beforeSend: function() {
                $('#create-reseller-message').removeClass('success error').html('Creating reseller...');
            },
            success: function(response) {
                if (response.success) {
                    $('#create-reseller-message').removeClass('error').addClass('success').html(response.data.message);
                    $('#create-reseller-form')[0].reset();
                    loadResellers(); // Refresh the resellers list
                } else {
                    $('#create-reseller-message').removeClass('success').addClass('error').html(response.data.message);
                }
            },
            error: function() {
                $('#create-reseller-message').removeClass('success').addClass('error').html('Error: Could not create reseller');
            }
        });
    }

    /**
     * Delete a reseller via AJAX.
     * 
     * @param {number} userId User ID to delete.
     */
    function deleteReseller(userId) {
        $.ajax({
            url: reseller_access_ajax.ajax_url,
            type: 'post',
            data: {
                action: 'delete_reseller',
                nonce: reseller_access_ajax.nonce,
                user_id: userId
            },
            success: function(response) {
                if (response.success) {
                    alert(response.data.message);
                    loadResellers(); // Refresh the resellers list
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('Error: Could not delete reseller');
            }
        });
    }

    /**
     * Format a date string.
     * 
     * @param {string} dateString Date string to format.
     * @return {string} Formatted date.
     */
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
    }

})( jQuery );
