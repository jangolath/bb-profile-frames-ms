<?php
/**
 * Admin page template.
 *
 * @package BB_Profile_Frames_MS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get all frames
$frames = BB_Profile_Frames_MS::get_lottie_frames();
?>
<div class="wrap bbpfms-admin">
    <h1><?php _e('Lottie Profile Frames', 'bb-profile-frames-ms'); ?></h1>
    
    <div class="bbpfms-upload-section">
        <h2><?php _e('Upload New Frame', 'bb-profile-frames-ms'); ?></h2>
        <p><?php _e('Upload a Lottie animation file (.json) to use as a profile frame.', 'bb-profile-frames-ms'); ?></p>
        
        <form id="bbpfms-upload-form" method="post" enctype="multipart/form-data">
            <div class="form-field">
                <label for="frame_name"><?php _e('Frame Name', 'bb-profile-frames-ms'); ?></label>
                <input type="text" id="frame_name" name="frame_name" required>
            </div>
            
            <div class="form-field">
                <label for="frame_file"><?php _e('Lottie JSON File', 'bb-profile-frames-ms'); ?></label>
                <input type="file" id="frame_file" name="frame_file" accept=".json" required>
            </div>
            
            <div class="form-field">
                <button type="submit" class="button button-primary"><?php _e('Upload Frame', 'bb-profile-frames-ms'); ?></button>
            </div>
            
            <div id="upload-response"></div>
        </form>
    </div>
    
    <div class="bbpfms-frames-list">
        <h2><?php _e('Manage Frames', 'bb-profile-frames-ms'); ?></h2>
        
        <?php if (empty($frames)): ?>
            <p><?php _e('No frames uploaded yet. Upload your first Lottie frame above.', 'bb-profile-frames-ms'); ?></p>
        <?php else: ?>
            <div class="frames-grid">
                <?php foreach ($frames as $frame_id => $frame): ?>
                    <div class="frame-item" data-id="<?php echo esc_attr($frame_id); ?>">
                        <div class="frame-preview">
                            <lottie-player 
                                src="<?php echo esc_url(BBPFMS_PLUGIN_URL . 'lottie_assets/' . $frame['file']); ?>" 
                                background="transparent" 
                                speed="1" 
                                loop 
                                autoplay>
                            </lottie-player>
                        </div>
                        <div class="frame-info">
                            <h3><?php echo esc_html($frame['name']); ?></h3>
                            <div class="frame-actions">
                                <button class="button delete-frame" data-id="<?php echo esc_attr($frame_id); ?>"><?php _e('Delete', 'bb-profile-frames-ms'); ?></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>