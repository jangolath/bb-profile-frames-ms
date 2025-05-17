/**
 * Frontend scripts for BuddyBoss Profile Frames MS.
 */
jQuery(document).ready(function($) {
    // Handle frame selection
    $('.bbpfms-frame-selector .frame-item').on('click', function() {
        $('.bbpfms-frame-selector .frame-item').removeClass('selected');
        $(this).addClass('selected');
    });
    
    // Handle save selection
    $('#save-frame-selection').on('click', function() {
        var selectedFrame = $('.bbpfms-frame-selector .frame-item.selected').data('id');
        var button = $(this);
        
        button.prop('disabled', true).text('Saving...');
        
        $.ajax({
            url: BBPFMS.ajaxurl,
            type: 'POST',
            data: {
                action: 'bbpfms_save_user_frame',
                nonce: BBPFMS.nonce,
                frame_id: selectedFrame || ''
            },
            success: function(response) {
                button.prop('disabled', false).text('Save Selection');
                
                if (response.success) {
                    $('.frame-selection-message').html('<div class="success-message">' + BBPFMS.save_success + '</div>');
                    
                    setTimeout(function() {
                        $('.frame-selection-message').empty();
                    }, 3000);
                } else {
                    $('.frame-selection-message').html('<div class="error-message">' + response.data + '</div>');
                }
            },
            error: function() {
                button.prop('disabled', false).text('Save Selection');
                $('.frame-selection-message').html('<div class="error-message">' + BBPFMS.save_error + '</div>');
            }
        });
    });
});