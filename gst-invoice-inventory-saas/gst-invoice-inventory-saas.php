<?php
/**
 * Plugin Name: GST Invoice & Inventory SaaS
 * Plugin URI: https://example.com/gst-invoice-inventory-saas
 * Description: Complete SaaS solution for GST-compliant invoice generation and inventory management with REST API, Google OAuth, and multi-language support.
 * Version: 1.0.0
 * Requires at least: 6.0
 * Requires PHP: 8.0
 * Author: Surajx
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: gii-saas
 * Domain Path: /languages
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin constants
define( 'GII_SAAS_VERSION', '1.0.0' );
define( 'GII_SAAS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GII_SAAS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'GII_SAAS_PLUGIN_FILE', __FILE__ );

/**
 * Main Plugin Class
 */
class Plugin {

    /**
     * Plugin instance
     *
     * @var Plugin
     */
    private static $instance = null;

    /**
     * Get plugin instance
     *
     * @return Plugin
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        $this->load_dependencies();
        $this->init_hooks();
    }

    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-database.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-rest-api.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-oauth.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-products.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-invoices.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-customers.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-reports.php';
        require_once GII_SAAS_PLUGIN_DIR . 'includes/class-admin.php';
    }

    /**
     * Initialize hooks
     */
    private function init_hooks() {
        register_activation_hook( GII_SAAS_PLUGIN_FILE, array( $this, 'activate' ) );
        register_deactivation_hook( GII_SAAS_PLUGIN_FILE, array( $this, 'deactivate' ) );

        add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
        add_action( 'init', array( $this, 'init' ) );
    }

    /**
     * Plugin activation
     */
    public function activate() {
        Database::create_tables();
        flush_rewrite_rules();
    }

    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }

    /**
     * Load plugin text domain
     */
    public function load_textdomain() {
        load_plugin_textdomain(
            'gii-saas',
            false,
            dirname( plugin_basename( GII_SAAS_PLUGIN_FILE ) ) . '/languages'
        );
    }

    /**
     * Initialize plugin
     */
    public function init() {
        // Initialize REST API
        REST_API::get_instance();

        // Initialize OAuth
        OAuth::get_instance();

        // Initialize Products
        Products::get_instance();

        // Initialize Invoices
        Invoices::get_instance();

        // Initialize Customers
        Customers::get_instance();

        // Initialize Reports
        Reports::get_instance();

        // Initialize Admin
        if ( is_admin() ) {
            Admin::get_instance();
        }
    }
}

// Initialize plugin
Plugin::get_instance();
