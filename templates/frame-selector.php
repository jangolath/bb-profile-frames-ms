<?php
/**
 * Frame selector template.
 *
 * @package BB_Profile_Frames_MS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Variables should be passed to this template
// $frames, $selected_frame, $plugin_url
?>
<div class="bbpfms-frame-selector">
    <h2><?php _e('Select Your Profile Frame', 'bb-profile-frames-ms'); ?></h2>
    
    <div class="frame-selection-message"></div>
    
    <div class="frames-grid">
        <div class="frame-item <?php echo empty($selected_frame) ? 'selected' : ''; ?>" data-id="">
            <div class="frame-preview">
                <div class="no-frame"><?php _e('No Frame', 'bb-profile-frames-ms'); ?></div>
            </div>
            <div class="frame-info">
                <h3><?php _e('None', 'bb-profile-frames-ms'); ?></h3>
            </div>
        </div>
        
        <?php foreach ($frames as $frame_id => $frame): ?>
            <div class="frame-item <?php echo $selected_frame === $frame_id ? 'selected' : ''; ?>" data-id="<?php echo esc_attr($frame_id); ?>">
                <div class="frame-preview">
                    <lottie-player 
                        src="<?php echo esc_url($plugin_url . 'lottie_assets/' . $frame['file']); ?>" 
                        background="transparent" 
                        speed="1" 
                        loop 
                        autoplay>
                    </lottie-player>
                </div>
                <div class="frame-info">
                    <h3><?php echo esc_html($frame['name']); ?></h3>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <div class="frame-selection-actions">
        <button id="save-frame-selection" class="button button-primary"><?php _e('Save Selection', 'bb-profile-frames-ms'); ?></button>
    </div>
</div>