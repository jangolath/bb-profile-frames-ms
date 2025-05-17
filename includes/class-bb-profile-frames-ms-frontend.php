<?php
/**
 * Frontend functionality for BuddyBoss Profile Frames MS.
 *
 * @package BB_Profile_Frames_MS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Frontend class.
 */
class BB_Profile_Frames_MS_Frontend {

    /**
     * Initialize the class.
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register all frontend hooks.
     */
    private function register_hooks() {
        // Frontend scripts and styles
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // Shortcode
        add_shortcode('bb_profile_frame_selector', array($this, 'frame_selector_shortcode'));
        
        // AJAX handlers
        add_action('wp_ajax_bbpfms_save_user_frame', array($this, 'ajax_save_user_frame'));
        
        // Display hooks for BuddyBoss profile
        add_action('bp_before_member_header_meta', array($this, 'display_lottie_frame'));
    }

    /**
     * Enqueue frontend scripts and styles.
     */
    public function enqueue_scripts() {
        // Only load on profile pages or pages with our shortcode
        global $post;
        $has_shortcode = $post && has_shortcode($post->post_content, 'bb_profile_frame_selector');
        
        if (function_exists('bp_is_user') && bp_is_user() || $has_shortcode) {
            // Lottie player script
            wp_enqueue_script(
                'lottie-player',
                'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
                array(),
                BBPFMS_VERSION,
                true
            );
            
            // Frontend scripts
            wp_enqueue_script(
                'bbpfms-frontend',
                BBPFMS_PLUGIN_URL . 'assets/js/bbpfms-frontend.js',  // Changed from frontend.js
                array('jquery', 'lottie-player'),
                BBPFMS_VERSION,
                true
            );

            // Frontend styles
            wp_enqueue_style(
                'bbpfms-frontend',
                BBPFMS_PLUGIN_URL . 'assets/css/bbpfms-frontend.css',  // Changed from frontend.css
                array(),
                BBPFMS_VERSION
            );
            
            // Localize script
            wp_localize_script('bbpfms-frontend', 'BBPFMS', array(
                'ajaxurl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('bbpfms-nonce'),
                'save_error' => __('Error saving your selection. Please try again.', 'bb-profile-frames-ms'),
                'save_success' => __('Your profile frame has been updated!', 'bb-profile-frames-ms')
            ));
        }
    }

    /**
     * Frame selector shortcode.
     */
    public function frame_selector_shortcode($atts) {
        // Only show to logged in users
        if (!is_user_logged_in()) {
            return '<p>' . __('Please log in to select a profile frame.', 'bb-profile-frames-ms') . '</p>';
        }
        
        $frames = BB_Profile_Frames_MS::get_lottie_frames();
        if (empty($frames)) {
            return '<p>' . __('No profile frames available.', 'bb-profile-frames-ms') . '</p>';
        }
        
        $current_user_id = get_current_user_id();
        $selected_frame = get_user_meta($current_user_id, '_bbpfms_selected_frame', true);
        
        // Set up variables for the template
        $data = array(
            'frames' => $frames,
            'selected_frame' => $selected_frame,
            'plugin_url' => BBPFMS_PLUGIN_URL
        );
        
        // Start output buffering
        ob_start();
        
        // Include the template
        include BBPFMS_PLUGIN_DIR . 'templates/frame-selector.php';
        
        // Return the buffered content
        return ob_get_clean();
    }

    /**
     * AJAX handler for saving user frame selection.
     */
    public function ajax_save_user_frame() {
        // Check if user is logged in
        if (!is_user_logged_in()) {
            wp_send_json_error(__('You must be logged in.', 'bb-profile-frames-ms'));
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bbpfms-nonce')) {
            wp_send_json_error(__('Security check failed.', 'bb-profile-frames-ms'));
        }
        
        // Get selected frame ID
        $frame_id = sanitize_text_field($_POST['frame_id']);
        
        // Get current user ID
        $user_id = get_current_user_id();
        
        // If empty frame ID, remove the selection
        if (empty($frame_id)) {
            delete_user_meta($user_id, '_bbpfms_selected_frame');
            wp_send_json_success(array('message' => __('Frame selection removed.', 'bb-profile-frames-ms')));
        }
        
        // Verify frame exists
        $frames = BB_Profile_Frames_MS::get_lottie_frames();
        if (!isset($frames[$frame_id])) {
            wp_send_json_error(__('Selected frame not found.', 'bb-profile-frames-ms'));
        }
        
        // Save selected frame
        update_user_meta($user_id, '_bbpfms_selected_frame', $frame_id);
        
        wp_send_json_success(array(
            'message' => __('Frame selection saved.', 'bb-profile-frames-ms'),
            'frame' => array(
                'id' => $frame_id,
                'name' => $frames[$frame_id]['name']
            )
        ));
    }

    /**
     * Display Lottie frame on user profile.
     */
    public function display_lottie_frame() {
        // Only show on profile pages
        if (!function_exists('bp_is_user') || !bp_is_user()) {
            return;
        }
        
        $user_id = bp_displayed_user_id();
        $selected_frame = get_user_meta($user_id, '_bbpfms_selected_frame', true);
        
        if (empty($selected_frame)) {
            return;
        }
        
        $frames = BB_Profile_Frames_MS::get_lottie_frames();
        if (!isset($frames[$selected_frame])) {
            return;
        }
        
        $frame = $frames[$selected_frame];
        ?>
        <div class="bbpfms-profile-frame">
            <lottie-player 
                src="<?php echo esc_url(BBPFMS_PLUGIN_URL . 'lottie_assets/' . $frame['file']); ?>" 
                background="transparent" 
                speed="1" 
                loop 
                autoplay>
            </lottie-player>
        </div>
        <?php
    }
}