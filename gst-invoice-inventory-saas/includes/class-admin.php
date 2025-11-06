<?php
/**
 * Admin Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Admin Class
 */
class Admin {

    /**
     * Instance
     *
     * @var Admin
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Admin
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
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'GST Invoice SaaS', 'gii-saas' ),
            __( 'GST Invoice', 'gii-saas' ),
            'manage_options',
            'gii-saas',
            array( $this, 'admin_page' ),
            'dashicons-text-page',
            30
        );

        add_submenu_page(
            'gii-saas',
            __( 'Settings', 'gii-saas' ),
            __( 'Settings', 'gii-saas' ),
            'manage_options',
            'gii-saas-settings',
            array( $this, 'settings_page' )
        );
    }

    /**
     * Register settings
     */
    public function register_settings() {
        register_setting( 'gii_saas_settings', 'gii_google_client_id' );
        register_setting( 'gii_saas_settings', 'gii_google_client_secret' );
        register_setting( 'gii_saas_settings', 'gii_default_gst_rate' );
        register_setting( 'gii_saas_settings', 'gii_invoice_prefix' );
    }

    /**
     * Admin page
     */
    public function admin_page() {
        global $wpdb;
        $users_table = $wpdb->users;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $products_table = $wpdb->prefix . 'gii_products';
        $customers_table = $wpdb->prefix . 'gii_customers';

        // Get statistics
        $total_users = $wpdb->get_var( "SELECT COUNT(*) FROM $users_table" );
        $total_invoices = $wpdb->get_var( "SELECT COUNT(*) FROM $invoices_table" );
        $total_products = $wpdb->get_var( "SELECT COUNT(*) FROM $products_table" );
        $total_customers = $wpdb->get_var( "SELECT COUNT(*) FROM $customers_table" );
        $total_revenue = $wpdb->get_var( "SELECT SUM(total) FROM $invoices_table" );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'GST Invoice & Inventory SaaS', 'gii-saas' ); ?></h1>

            <div class="gii-dashboard" style="margin-top: 20px;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3><?php esc_html_e( 'Total Users', 'gii-saas' ); ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;"><?php echo esc_html( number_format( $total_users ) ); ?></p>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3><?php esc_html_e( 'Total Invoices', 'gii-saas' ); ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;"><?php echo esc_html( number_format( $total_invoices ) ); ?></p>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3><?php esc_html_e( 'Total Products', 'gii-saas' ); ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;"><?php echo esc_html( number_format( $total_products ) ); ?></p>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3><?php esc_html_e( 'Total Customers', 'gii-saas' ); ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;"><?php echo esc_html( number_format( $total_customers ) ); ?></p>
                    </div>

                    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <h3><?php esc_html_e( 'Total Revenue', 'gii-saas' ); ?></h3>
                        <p style="font-size: 2rem; font-weight: bold; color: #2563eb;">₹<?php echo esc_html( number_format( $total_revenue, 2 ) ); ?></p>
                    </div>
                </div>

                <div style="margin-top: 40px; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <h2><?php esc_html_e( 'Quick Links', 'gii-saas' ); ?></h2>
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin: 10px 0;">
                            <a href="<?php echo esc_url( admin_url( 'admin.php?page=gii-saas-settings' ) ); ?>" class="button button-primary">
                                <?php esc_html_e( 'Configure Settings', 'gii-saas' ); ?>
                            </a>
                        </li>
                        <li style="margin: 10px 0;">
                            <a href="<?php echo esc_url( rest_url( 'gii-saas/v1/' ) ); ?>" class="button" target="_blank">
                                <?php esc_html_e( 'View REST API Documentation', 'gii-saas' ); ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Settings page
     */
    public function settings_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        // Handle form submission
        if ( isset( $_POST['gii_save_settings'] ) ) {
            check_admin_referer( 'gii_settings_action', 'gii_settings_nonce' );

            update_option( 'gii_google_client_id', sanitize_text_field( $_POST['gii_google_client_id'] ) );
            update_option( 'gii_google_client_secret', sanitize_text_field( $_POST['gii_google_client_secret'] ) );
            update_option( 'gii_default_gst_rate', floatval( $_POST['gii_default_gst_rate'] ) );
            update_option( 'gii_invoice_prefix', sanitize_text_field( $_POST['gii_invoice_prefix'] ) );

            echo '<div class="notice notice-success"><p>' . esc_html__( 'Settings saved successfully!', 'gii-saas' ) . '</p></div>';
        }

        $google_client_id = get_option( 'gii_google_client_id', '' );
        $google_client_secret = get_option( 'gii_google_client_secret', '' );
        $default_gst_rate = get_option( 'gii_default_gst_rate', '18' );
        $invoice_prefix = get_option( 'gii_invoice_prefix', 'INV' );

        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'GST Invoice SaaS Settings', 'gii-saas' ); ?></h1>

            <form method="post" action="">
                <?php wp_nonce_field( 'gii_settings_action', 'gii_settings_nonce' ); ?>

                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="gii_google_client_id"><?php esc_html_e( 'Google Client ID', 'gii-saas' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="gii_google_client_id" name="gii_google_client_id" value="<?php echo esc_attr( $google_client_id ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Enter your Google OAuth Client ID for Google Sign-In integration.', 'gii-saas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="gii_google_client_secret"><?php esc_html_e( 'Google Client Secret', 'gii-saas' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="gii_google_client_secret" name="gii_google_client_secret" value="<?php echo esc_attr( $google_client_secret ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Enter your Google OAuth Client Secret.', 'gii-saas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="gii_default_gst_rate"><?php esc_html_e( 'Default GST Rate (%)', 'gii-saas' ); ?></label>
                        </th>
                        <td>
                            <input type="number" id="gii_default_gst_rate" name="gii_default_gst_rate" value="<?php echo esc_attr( $default_gst_rate ); ?>" step="0.01" min="0" max="100">
                            <p class="description"><?php esc_html_e( 'Default GST rate for new products and invoices.', 'gii-saas' ); ?></p>
                        </td>
                    </tr>

                    <tr>
                        <th scope="row">
                            <label for="gii_invoice_prefix"><?php esc_html_e( 'Invoice Prefix', 'gii-saas' ); ?></label>
                        </th>
                        <td>
                            <input type="text" id="gii_invoice_prefix" name="gii_invoice_prefix" value="<?php echo esc_attr( $invoice_prefix ); ?>" class="regular-text">
                            <p class="description"><?php esc_html_e( 'Prefix for invoice numbers (e.g., INV, BILL).', 'gii-saas' ); ?></p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="submit" name="gii_save_settings" class="button button-primary">
                        <?php esc_html_e( 'Save Settings', 'gii-saas' ); ?>
                    </button>
                </p>
            </form>

            <hr>

            <h2><?php esc_html_e( 'REST API Endpoints', 'gii-saas' ); ?></h2>
            <p><?php esc_html_e( 'Base URL:', 'gii-saas' ); ?> <code><?php echo esc_html( rest_url( 'gii-saas/v1/' ) ); ?></code></p>

            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Endpoint', 'gii-saas' ); ?></th>
                        <th><?php esc_html_e( 'Methods', 'gii-saas' ); ?></th>
                        <th><?php esc_html_e( 'Description', 'gii-saas' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>/products</code></td>
                        <td>GET, POST</td>
                        <td><?php esc_html_e( 'List or create products', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/products/{id}</code></td>
                        <td>GET, PUT, DELETE</td>
                        <td><?php esc_html_e( 'Get, update, or delete a product', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/invoices</code></td>
                        <td>GET, POST</td>
                        <td><?php esc_html_e( 'List or create invoices', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/invoices/{id}</code></td>
                        <td>GET, PUT, DELETE</td>
                        <td><?php esc_html_e( 'Get, update, or delete an invoice', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/customers</code></td>
                        <td>GET, POST</td>
                        <td><?php esc_html_e( 'List or create customers', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/account/settings</code></td>
                        <td>GET, POST</td>
                        <td><?php esc_html_e( 'Get or update account settings', 'gii-saas' ); ?></td>
                    </tr>
                    <tr>
                        <td><code>/auth/google</code></td>
                        <td>GET</td>
                        <td><?php esc_html_e( 'Initiate Google OAuth flow', 'gii-saas' ); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <?php
    }
}
