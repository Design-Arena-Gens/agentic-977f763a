<?php
/**
 * REST API Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * REST API Class
 */
class REST_API {

    /**
     * Instance
     *
     * @var REST_API
     */
    private static $instance = null;

    /**
     * Namespace
     *
     * @var string
     */
    private $namespace = 'gii-saas/v1';

    /**
     * Get instance
     *
     * @return REST_API
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
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        // Products endpoints
        register_rest_route( $this->namespace, '/products', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_products' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_product' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/products/(?P<id>\d+)', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_product' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_product' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_product' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );

        // Invoices endpoints
        register_rest_route( $this->namespace, '/invoices', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_invoices' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_invoice' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );

        register_rest_route( $this->namespace, '/invoices/(?P<id>\d+)', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_invoice' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::EDITABLE,
                'callback'            => array( $this, 'update_invoice' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::DELETABLE,
                'callback'            => array( $this, 'delete_invoice' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );

        // Customers endpoints
        register_rest_route( $this->namespace, '/customers', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_customers' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'create_customer' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );

        // Account settings endpoints
        register_rest_route( $this->namespace, '/account/settings', array(
            array(
                'methods'             => \WP_REST_Server::READABLE,
                'callback'            => array( $this, 'get_account_settings' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
            array(
                'methods'             => \WP_REST_Server::CREATABLE,
                'callback'            => array( $this, 'update_account_settings' ),
                'permission_callback' => array( $this, 'check_user_permission' ),
            ),
        ) );
    }

    /**
     * Check user permission
     *
     * @param \WP_REST_Request $request Request object.
     * @return bool
     */
    public function check_user_permission( $request ) {
        return is_user_logged_in();
    }

    /**
     * Get products
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_products( $request ) {
        $products = Products::get_user_products( get_current_user_id() );
        return new \WP_REST_Response( $products, 200 );
    }

    /**
     * Get single product
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_product( $request ) {
        $product_id = (int) $request['id'];
        $product = Products::get_product( $product_id, get_current_user_id() );

        if ( ! $product ) {
            return new \WP_REST_Response( array( 'error' => 'Product not found' ), 404 );
        }

        return new \WP_REST_Response( $product, 200 );
    }

    /**
     * Create product
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function create_product( $request ) {
        $data = $request->get_json_params();
        $product_id = Products::create_product( $data, get_current_user_id() );

        if ( ! $product_id ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to create product' ), 500 );
        }

        return new \WP_REST_Response( array( 'id' => $product_id, 'message' => 'Product created' ), 201 );
    }

    /**
     * Update product
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function update_product( $request ) {
        $product_id = (int) $request['id'];
        $data = $request->get_json_params();
        $updated = Products::update_product( $product_id, $data, get_current_user_id() );

        if ( ! $updated ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to update product' ), 500 );
        }

        return new \WP_REST_Response( array( 'message' => 'Product updated' ), 200 );
    }

    /**
     * Delete product
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function delete_product( $request ) {
        $product_id = (int) $request['id'];
        $deleted = Products::delete_product( $product_id, get_current_user_id() );

        if ( ! $deleted ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to delete product' ), 500 );
        }

        return new \WP_REST_Response( array( 'message' => 'Product deleted' ), 200 );
    }

    /**
     * Get invoices
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_invoices( $request ) {
        $invoices = Invoices::get_user_invoices( get_current_user_id() );
        return new \WP_REST_Response( $invoices, 200 );
    }

    /**
     * Get single invoice
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_invoice( $request ) {
        $invoice_id = (int) $request['id'];
        $invoice = Invoices::get_invoice( $invoice_id, get_current_user_id() );

        if ( ! $invoice ) {
            return new \WP_REST_Response( array( 'error' => 'Invoice not found' ), 404 );
        }

        return new \WP_REST_Response( $invoice, 200 );
    }

    /**
     * Create invoice
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function create_invoice( $request ) {
        $data = $request->get_json_params();
        $invoice_id = Invoices::create_invoice( $data, get_current_user_id() );

        if ( ! $invoice_id ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to create invoice' ), 500 );
        }

        return new \WP_REST_Response( array( 'id' => $invoice_id, 'message' => 'Invoice created' ), 201 );
    }

    /**
     * Update invoice
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function update_invoice( $request ) {
        $invoice_id = (int) $request['id'];
        $data = $request->get_json_params();
        $updated = Invoices::update_invoice( $invoice_id, $data, get_current_user_id() );

        if ( ! $updated ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to update invoice' ), 500 );
        }

        return new \WP_REST_Response( array( 'message' => 'Invoice updated' ), 200 );
    }

    /**
     * Delete invoice
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function delete_invoice( $request ) {
        $invoice_id = (int) $request['id'];
        $deleted = Invoices::delete_invoice( $invoice_id, get_current_user_id() );

        if ( ! $deleted ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to delete invoice' ), 500 );
        }

        return new \WP_REST_Response( array( 'message' => 'Invoice deleted' ), 200 );
    }

    /**
     * Get customers
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_customers( $request ) {
        $customers = Customers::get_user_customers( get_current_user_id() );
        return new \WP_REST_Response( $customers, 200 );
    }

    /**
     * Create customer
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function create_customer( $request ) {
        $data = $request->get_json_params();
        $customer_id = Customers::create_customer( $data, get_current_user_id() );

        if ( ! $customer_id ) {
            return new \WP_REST_Response( array( 'error' => 'Failed to create customer' ), 500 );
        }

        return new \WP_REST_Response( array( 'id' => $customer_id, 'message' => 'Customer created' ), 201 );
    }

    /**
     * Get account settings
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function get_account_settings( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_user_settings';
        $user_id = get_current_user_id();

        $settings = $wpdb->get_results(
            $wpdb->prepare( "SELECT setting_key, setting_value FROM $table WHERE user_id = %d", $user_id ),
            ARRAY_A
        );

        $formatted = array();
        foreach ( $settings as $setting ) {
            $formatted[ $setting['setting_key'] ] = maybe_unserialize( $setting['setting_value'] );
        }

        return new \WP_REST_Response( $formatted, 200 );
    }

    /**
     * Update account settings
     *
     * @param \WP_REST_Request $request Request object.
     * @return \WP_REST_Response
     */
    public function update_account_settings( $request ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_user_settings';
        $user_id = get_current_user_id();
        $data = $request->get_json_params();

        foreach ( $data as $key => $value ) {
            $key = sanitize_key( $key );
            $value = maybe_serialize( sanitize_text_field( $value ) );

            $wpdb->replace(
                $table,
                array(
                    'user_id'       => $user_id,
                    'setting_key'   => $key,
                    'setting_value' => $value,
                ),
                array( '%d', '%s', '%s' )
            );
        }

        return new \WP_REST_Response( array( 'message' => 'Settings updated' ), 200 );
    }
}
