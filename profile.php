<?php
/**
     * User profile for WordPress
     *
     * Profile adds user profile page
     *
     * @package   Profile
     * @author    Rahul Aryan <rah12@live.com>
     * @copyright 2014 WP3.in & Rahul Aryan
     * @license   GPL-3.0+ http://www.gnu.org/licenses/gpl-3.0.txt
     * @link      http://wp3.in
     *
     * @wordpress-plugin
     * Plugin Name:       Profile
     * Plugin URI:        http://wp3.in
     * Description:       Simple profile for WordPress
     * Donate link: https://www.paypal.com/cgi-bin/webscr?business=rah12@live.com&cmd=_xclick&item_name=Donation%20to%20Profile%20development
     * Version:           0.0.4
     * Author:            Rahul Aryan
     * Author URI:        http://wp3.in
     * Text Domain:       profile
     * License:           GPL-3.0+
     * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
     * Domain Path:       /languages
     */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

if (!class_exists('Profile')) {

    /**
     * Main Profile class
     * @package Profile
     */
    class Profile
    {

        private $_plugin_version = '0.0.4';

        private $_plugin_path;

        private $_plugin_url;

        private $_text_domain = 'profile';

        public static $instance = null;

        public $_hooks;
        public $profile_ajax;

        /**
         * Filter object
         * @var object
         */
        public $profile_query_filter;

        /**
         * Theme object
         * @var object
         * @since 2.0.1
         */
        public $_theme;

        /**
         * Fields object
         * @var object
         * @since 2.0.1
         */
        public $_fields;
        public $_ajax;
        public $_cpt;
        public $_meta_boxes;

        public $_profile_forms;

        /**
         * Initializes the plugin by setting localization, hooks, filters, and administrative functions.
         * @return instance
         */
        public static function instance()
        {
            if (!isset(self::$instance) && !(self::$instance instanceof Profile)) {
                self::$instance = new Profile();
                self::$instance->_setup_constants();
                
                add_action('init', array(self::$instance, 'load_textdomain'));

                global $profile_classes;
                $profile_classes = array();

                self::$instance->includes();

                self::$instance->_hooks              = new Profile_Hooks();
                self::$instance->_fields             = new Profile_Custom_Fields();
                self::$instance->_profile_forms      = new Profile_Process_Form();
                self::$instance->_ajax               = new Profile_Ajax();
                self::$instance->_cpt                = new Profile_CPT();
                self::$instance->_meta_boxes         = new Profile_Meta_Boxes();
                self::$instance->_theme              = new Profile_Theme();

                /**
                 * ACTION: profile_loaded
                 * Hooks for extension to load their codes after Profile is leaded
                 */
                do_action('profile_loaded');
            }

            return self::$instance;
        }

            /**
             * Setup plugin constants
             *
             * @access private
             * @since  2.0.1
             * @return void
             */
            private function _setup_constants()
            {
                if (!defined('PROFILE_VERSION')) {
                    define('PROFILE_VERSION', $this->_plugin_version);
                }

                if (!defined('PROFILE_DIR')) {
                    define('PROFILE_DIR', plugin_dir_path(__FILE__));
                }

                if (!defined('PROFILE_URL')) {
                    define('PROFILE_URL', plugin_dir_url(__FILE__));
                }

                if (!defined('PROFILE_THEME_DIR')) {
                    define('PROFILE_THEME_DIR', PROFILE_DIR.'/theme');
                }

                if (!defined('PROFILE_THEME_URL')) {
                    define('PROFILE_THEME_URL', PROFILE_URL.'/theme');
                }




            }

        /**
         * Include required files
         *
         * @access private
         * @since 2.0.1
         * @return void
         */
        private function includes()
        {
            global $profile_options;

            require_once PROFILE_DIR.'includes/options.php';
            require_once PROFILE_DIR.'includes/functions.php';
            require_once PROFILE_DIR.'includes/theme-functions.php';
            require_once PROFILE_DIR.'includes/roles-permission.php';
            require_once PROFILE_DIR.'includes/activate.php';
            require_once PROFILE_DIR.'includes/hooks.php';
            require_once PROFILE_DIR.'includes/ajax.php';
            require_once PROFILE_DIR.'includes/process-form.php';
            require_once PROFILE_DIR.'includes/cpt.php';
            require_once PROFILE_DIR.'includes/meta_boxes.php';
            require_once PROFILE_DIR.'includes/class-theme.php';
            require_once PROFILE_DIR.'includes/meta.php';
            require_once PROFILE_DIR.'includes/shortcode-basepage.php';            
            require_once PROFILE_DIR.'includes/class-form.php';
            require_once PROFILE_DIR.'includes/class-validation.php';
            require_once PROFILE_DIR.'includes/fields.php';
            require_once PROFILE_DIR.'includes/class-fields.php';
            require_once PROFILE_DIR.'includes/views.php';
            require_once PROFILE_DIR.'includes/rewrite.php';
        }

        /**
         * Load translations
         *
         * @access private
         * @since 2.0.1
         * @return void
         */
        public function load_textdomain()
        {
            load_plugin_textdomain($this->_text_domain, false, dirname(plugin_basename(__FILE__)).'/languages/');
        }
    }
}

function profile()
{
    Profile::instance();
}

profile();

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */

register_activation_hook(__FILE__, 'profile_activate');

//register_deactivation_hook(__FILE__, array( 'profile', 'deactivate' ));


/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * The code below is intended to to give the lightest footprint possible.
 */

if (is_admin()) {
    require_once plugin_dir_path(__FILE__).'admin/admin.php';
    add_action('plugins_loaded', array('profile_admin', 'get_instance'));
}
