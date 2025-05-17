<?php
/**
 * Admin functionality for BuddyBoss Profile Frames MS.
 *
 * @package BB_Profile_Frames_MS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Admin class.
 */
class BB_Profile_Frames_MS_Admin {

    /**
     * Initialize the class.
     */
    public function __construct() {
        $this->register_hooks();
    }

    /**
     * Register all admin hooks.
     */
    private function register_hooks() {
        // Add to network admin menu
        add_action('network_admin_menu', array($this, 'add_network_admin_menu'));
        
        // Admin scripts and styles
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        
        // AJAX handlers
        add_action('wp_ajax_bbpfms_upload_frame', array($this, 'ajax_upload_frame'));
        add_action('wp_ajax_bbpfms_delete_frame', array($this, 'ajax_delete_frame'));
    }

    /**
     * Add menu item to the network admin settings menu.
     */
    public function add_network_admin_menu() {
        add_submenu_page(
            'settings.php',  // Parent slug for network settings
            __('Lottie Profile Frames', 'bb-profile-frames-ms'),
            __('Lottie Profile Frames', 'bb-profile-frames-ms'),
            'manage_network_options',  // Network admin capability
            'bb-profile-frames-ms',
            array($this, 'render_admin_page')
        );
    }

    /**
     * Enqueue admin scripts and styles.
     */
    public function enqueue_scripts($hook) {
        if ($hook != 'buddyboss-platform_page_bb-profile-frames-ms') {
            return;
        }
        
        // Lottie player script
        wp_enqueue_script(
            'lottie-player',
            'https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js',
            array(),
            BBPFMS_VERSION,
            true
        );
        
        // Admin scripts
        wp_enqueue_script(
            'bbpfms-admin',
            BBPFMS_PLUGIN_URL . 'assets/js/bbpfms-admin.js',  // Changed from admin.js
            array('jquery', 'lottie-player'),
            BBPFMS_VERSION,
            true
        );

        // Admin styles
        wp_enqueue_style(
            'bbpfms-admin',
            BBPFMS_PLUGIN_URL . 'assets/css/bbpfms-admin.css',  // Changed from admin.css
            array(),
            BBPFMS_VERSION
        );
        
        // Localize script
        wp_localize_script('bbpfms-admin', 'BBPFMS', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('bbpfms-nonce'),
            'upload_error' => __('Error uploading file. Please ensure it is a valid .json Lottie animation file.', 'bb-profile-frames-ms'),
            'delete_confirm' => __('Are you sure you want to delete this frame? This action cannot be undone.', 'bb-profile-frames-ms')
        ));
    }

    /**
     * Render admin page.
     */
    public function render_admin_page() {
        if (!current_user_can('manage_network_options')) {
            wp_die(__('Sorry, you are not allowed to access this page.', 'bb-profile-frames-ms'));
        }
        
        // Include admin page template
        include BBPFMS_PLUGIN_DIR . 'templates/admin-page.php';
    }

    /**
     * AJAX handler for uploading Lottie frames.
     */
    public function ajax_upload_frame() {
        // Check permissions
        if (!current_user_can('manage_network_options')) {
            wp_send_json_error(__('Permission denied.', 'bb-profile-frames-ms'));
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bbpfms-nonce')) {
            wp_send_json_error(__('Security check failed.', 'bb-profile-frames-ms'));
        }
        
        // Check if file uploaded
        if (empty($_FILES['frame_file'])) {
            wp_send_json_error(__('No file uploaded.', 'bb-profile-frames-ms'));
        }
        
        // Check file type
        $file_info = pathinfo($_FILES['frame_file']['name']);
        if ($file_info['extension'] !== 'json') {
            wp_send_json_error(__('Invalid file type. Only JSON Lottie files are allowed.', 'bb-profile-frames-ms'));
        }
        
        // Get frame name
        $frame_name = sanitize_text_field($_POST['frame_name']);
        if (empty($frame_name)) {
            wp_send_json_error(__('Frame name is required.', 'bb-profile-frames-ms'));
        }
        
        // Generate unique filename
        $filename = 'frame_' . uniqid() . '.json';
        $upload_path = BBPFMS_PLUGIN_DIR . 'lottie_assets/' . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($_FILES['frame_file']['tmp_name'], $upload_path)) {
            wp_send_json_error(__('Error saving file.', 'bb-profile-frames-ms'));
        }
        
        // Validate JSON file is a valid Lottie animation
        $json_content = file_get_contents($upload_path);
        $json_data = json_decode($json_content);
        
        if (!$json_data || !isset($json_data->v) || !isset($json_data->layers)) {
            // Not a valid Lottie file, delete it
            unlink($upload_path);
            wp_send_json_error(__('Invalid Lottie animation file.', 'bb-profile-frames-ms'));
        }
        
        // Save frame info
        $frames = BB_Profile_Frames_MS::get_lottie_frames();
        $frame_id = 'frame_' . uniqid();
        $frames[$frame_id] = array(
            'name' => $frame_name,
            'file' => $filename
        );
        
        BB_Profile_Frames_MS::save_lottie_frames($frames);
        
        wp_send_json_success(array(
            'id' => $frame_id,
            'name' => $frame_name,
            'file' => $filename,
            'url' => BBPFMS_PLUGIN_URL . 'lottie_assets/' . $filename
        ));
    }

    /**
     * AJAX handler for deleting Lottie frames.
     */
    public function ajax_delete_frame() {
        // Check permissions
        if (!current_user_can('manage_network_options')) {
            wp_send_json_error(__('Permission denied.', 'bb-profile-frames-ms'));
        }
        
        // Verify nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'bbpfms-nonce')) {
            wp_send_json_error(__('Security check failed.', 'bb-profile-frames-ms'));
        }
        
        // Get frame ID
        $frame_id = sanitize_text_field($_POST['frame_id']);
        if (empty($frame_id)) {
            wp_send_json_error(__('Frame ID is required.', 'bb-profile-frames-ms'));
        }
        
        // Get frames
        $frames = BB_Profile_Frames_MS::get_lottie_frames();
        if (!isset($frames[$frame_id])) {
            wp_send_json_error(__('Frame not found.', 'bb-profile-frames-ms'));
        }
        
        // Delete file
        $file_path = BBPFMS_PLUGIN_DIR . 'lottie_assets/' . $frames[$frame_id]['file'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Remove frame from list
        unset($frames[$frame_id]);
        BB_Profile_Frames_MS::save_lottie_frames($frames);
        
        wp_send_json_success();
    }
}