<?php
/**
 * Plugin Name: BuddyBoss Profile Frames MS
 * Plugin URI: https://bluespringsweb.com/bb-profile-frames-ms
 * Description: Add Lottie animation frames to user profiles in BuddyBoss multisite.
 * Version: 1.0.1
 * Author: Jason Wood
 * Author URI: https://bluespringsweb.com
 * License: GPL-2.0+
 * Text Domain: bb-profile-frames-ms
 * Domain Path: /languages
 * 
 * @package BB_Profile_Frames_MS
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('BBPFMS_VERSION', '1.0.1');
define('BBPFMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('BBPFMS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BBPFMS_PLUGIN_BASENAME', plugin_basename(__FILE__));

/**
 * Main plugin class.
 */
class BB_Profile_Frames_MS {

    /**
     * Instance of this class.
     *
     * @var object
     */
    protected static $instance = null;

    /**
     * Initialize the plugin.
     */
    private function __construct() {
        $this->load_dependencies();
        $this->define_hooks();
    }

    /**
     * Return an instance of this class.
     *
     * @return object A single instance of this class.
     */
    public static function get_instance() {
        if (null == self::$instance) {
            self::$instance = new self;
        }
        
        return self::$instance;
    }

    /**
     * Load required dependencies.
     */
    private function load_dependencies() {
        // Core functionality
        require_once BBPFMS_PLUGIN_DIR . 'includes/class-bb-profile-frames-ms-admin.php';
        require_once BBPFMS_PLUGIN_DIR . 'includes/class-bb-profile-frames-ms-frontend.php';
    }

    /**
     * Define hooks for the plugin.
     */
    private function define_hooks() {
        // Setup lottie assets directory
        $this->setup_assets_directory();
        
        // Initialize classes
        $admin = new BB_Profile_Frames_MS_Admin();
        $frontend = new BB_Profile_Frames_MS_Frontend();
        
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
    }

    /**
     * Setup lottie assets directory.
     */
    private function setup_assets_directory() {
        $lottie_assets_dir = BBPFMS_PLUGIN_DIR . 'lottie_assets/';
        
        if (!file_exists($lottie_assets_dir)) {
            wp_mkdir_p($lottie_assets_dir);
        }
    }

    /**
     * Plugin activation.
     */
    public function activate() {
        // Create necessary directories
        $this->setup_assets_directory();
        
        // Create default option for frames
        if (!get_site_option('bbpfms_lottie_frames')) {
            update_site_option('bbpfms_lottie_frames', array());
        }
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation.
     */
    public function deactivate() {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    /**
     * Get the lottie frames.
     *
     * @return array Array of frames.
     */
    public static function get_lottie_frames() {
        $frames = get_site_option('bbpfms_lottie_frames', array());
        return is_array($frames) ? $frames : array();
    }

    /**
     * Save the lottie frames.
     *
     * @param array $frames Array of frames to save.
     */
    public static function save_lottie_frames($frames) {
        update_site_option('bbpfms_lottie_frames', $frames);
    }
}

// Initialize the plugin
function bbpfms_init() {
    BB_Profile_Frames_MS::get_instance();
}
add_action('plugins_loaded', 'bbpfms_init');