<?php
/**
 * Customers Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Customers Class
 */
class Customers {

    /**
     * Instance
     *
     * @var Customers
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Customers
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
        // Initialization hooks can be added here
    }

    /**
     * Get user customers
     *
     * @param int $user_id User ID.
     * @return array
     */
    public static function get_user_customers( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_customers';

        $customers = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            ),
            ARRAY_A
        );

        return $customers ?: array();
    }

    /**
     * Get single customer
     *
     * @param int $customer_id Customer ID.
     * @param int $user_id User ID.
     * @return array|null
     */
    public static function get_customer( $customer_id, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_customers';

        $customer = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d AND user_id = %d",
                $customer_id,
                $user_id
            ),
            ARRAY_A
        );

        return $customer ?: null;
    }

    /**
     * Create customer
     *
     * @param array $data Customer data.
     * @param int   $user_id User ID.
     * @return int|false Customer ID or false on failure.
     */
    public static function create_customer( $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_customers';

        $insert_data = array(
            'user_id' => $user_id,
            'name'    => sanitize_text_field( $data['name'] ?? '' ),
            'email'   => sanitize_email( $data['email'] ?? '' ),
            'phone'   => sanitize_text_field( $data['phone'] ?? '' ),
            'gstin'   => sanitize_text_field( $data['gstin'] ?? '' ),
            'address' => sanitize_textarea_field( $data['address'] ?? '' ),
            'city'    => sanitize_text_field( $data['city'] ?? '' ),
            'state'   => sanitize_text_field( $data['state'] ?? '' ),
            'pincode' => sanitize_text_field( $data['pincode'] ?? '' ),
            'country' => sanitize_text_field( $data['country'] ?? 'India' ),
        );

        $inserted = $wpdb->insert( $table, $insert_data );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Update customer
     *
     * @param int   $customer_id Customer ID.
     * @param array $data Customer data.
     * @param int   $user_id User ID.
     * @return bool
     */
    public static function update_customer( $customer_id, $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_customers';

        // Verify ownership
        $customer = self::get_customer( $customer_id, $user_id );
        if ( ! $customer ) {
            return false;
        }

        $update_data = array();

        if ( isset( $data['name'] ) ) {
            $update_data['name'] = sanitize_text_field( $data['name'] );
        }
        if ( isset( $data['email'] ) ) {
            $update_data['email'] = sanitize_email( $data['email'] );
        }
        if ( isset( $data['phone'] ) ) {
            $update_data['phone'] = sanitize_text_field( $data['phone'] );
        }
        if ( isset( $data['gstin'] ) ) {
            $update_data['gstin'] = sanitize_text_field( $data['gstin'] );
        }
        if ( isset( $data['address'] ) ) {
            $update_data['address'] = sanitize_textarea_field( $data['address'] );
        }
        if ( isset( $data['city'] ) ) {
            $update_data['city'] = sanitize_text_field( $data['city'] );
        }
        if ( isset( $data['state'] ) ) {
            $update_data['state'] = sanitize_text_field( $data['state'] );
        }
        if ( isset( $data['pincode'] ) ) {
            $update_data['pincode'] = sanitize_text_field( $data['pincode'] );
        }
        if ( isset( $data['country'] ) ) {
            $update_data['country'] = sanitize_text_field( $data['country'] );
        }

        if ( empty( $update_data ) ) {
            return false;
        }

        $updated = $wpdb->update(
            $table,
            $update_data,
            array(
                'id'      => $customer_id,
                'user_id' => $user_id,
            )
        );

        return false !== $updated;
    }

    /**
     * Delete customer
     *
     * @param int $customer_id Customer ID.
     * @param int $user_id User ID.
     * @return bool
     */
    public static function delete_customer( $customer_id, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_customers';

        $deleted = $wpdb->delete(
            $table,
            array(
                'id'      => $customer_id,
                'user_id' => $user_id,
            )
        );

        return false !== $deleted;
    }
}
