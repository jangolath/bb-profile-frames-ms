/**
 * Admin scripts for BuddyBoss Profile Frames MS.
 */
jQuery(document).ready(function($) {
    // Handle form submission for uploading frames
    $('#bbpfms-upload-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'bbpfms_upload_frame');
        formData.append('nonce', BBPFMS.nonce);
        
        $.ajax({
            url: BBPFMS.ajaxurl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $('#upload-response').html('<div class="notice notice-info"><p>Uploading frame...</p></div>');
            },
            success: function(response) {
                if (response.success) {
                    $('#upload-response').html('<div class="notice notice-success"><p>Frame uploaded successfully!</p></div>');
                    $('#bbpfms-upload-form')[0].reset();
                    
                    // Add new frame to the list
                    var newFrame = '<div class="frame-item" data-id="' + response.data.id + '">' +
                        '<div class="frame-preview">' +
                        '<lottie-player src="' + response.data.url + '" background="transparent" speed="1" loop autoplay></lottie-player>' +
                        '</div>' +
                        '<div class="frame-info">' +
                        '<h3>' + response.data.name + '</h3>' +
                        '<div class="frame-actions">' +
                        '<button class="button delete-frame" data-id="' + response.data.id + '">Delete</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';
                    
                    if ($('.frames-grid').length) {
                        $('.frames-grid').append(newFrame);
                    } else {
                        $('.bbpfms-frames-list').html('<h2>Manage Frames</h2><div class="frames-grid">' + newFrame + '</div>');
                    }
                    
                    setTimeout(function() {
                        $('#upload-response').empty();
                    }, 3000);
                } else {
                    $('#upload-response').html('<div class="notice notice-error"><p>' + response.data + '</p></div>');
                }
            },
            error: function() {
                $('#upload-response').html('<div class="notice notice-error"><p>' + BBPFMS.upload_error + '</p></div>');
            }
        });
    });
    
    // Handle frame deletion
    $(document).on('click', '.delete-frame', function() {
        var frameId = $(this).data('id');
        var frameItem = $(this).closest('.frame-item');
        
        if (confirm(BBPFMS.delete_confirm)) {
            $.ajax({
                url: BBPFMS.ajaxurl,
                type: 'POST',
                data: {
                    action: 'bbpfms_delete_frame',
                    nonce: BBPFMS.nonce,
                    frame_id: frameId
                },
                success: function(response) {
                    if (response.success) {
                        frameItem.fadeOut(300, function() {
                            $(this).remove();
                            
                            // If no frames left, show empty message
                            if ($('.frame-item').length === 0) {
                                $('.frames-grid').replaceWith('<p>No frames uploaded yet. Upload your first Lottie frame above.</p>');
                            }
                        });
                    } else {
                        alert(response.data);
                    }
                },
                error: function() {
                    alert('Error deleting frame. Please try again.');
                }
            });
        }
    });
});