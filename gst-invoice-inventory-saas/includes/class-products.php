<?php
/**
 * Products Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Products Class
 */
class Products {

    /**
     * Instance
     *
     * @var Products
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Products
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
     * Get user products
     *
     * @param int $user_id User ID.
     * @return array
     */
    public static function get_user_products( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        $products = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d ORDER BY created_at DESC",
                $user_id
            ),
            ARRAY_A
        );

        return $products ?: array();
    }

    /**
     * Get single product
     *
     * @param int $product_id Product ID.
     * @param int $user_id User ID.
     * @return array|null
     */
    public static function get_product( $product_id, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        $product = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE id = %d AND user_id = %d",
                $product_id,
                $user_id
            ),
            ARRAY_A
        );

        return $product ?: null;
    }

    /**
     * Create product
     *
     * @param array $data Product data.
     * @param int   $user_id User ID.
     * @return int|false Product ID or false on failure.
     */
    public static function create_product( $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        $insert_data = array(
            'user_id'     => $user_id,
            'name'        => sanitize_text_field( $data['name'] ?? '' ),
            'sku'         => sanitize_text_field( $data['sku'] ?? '' ),
            'description' => sanitize_textarea_field( $data['description'] ?? '' ),
            'price'       => floatval( $data['price'] ?? 0 ),
            'cost'        => floatval( $data['cost'] ?? 0 ),
            'stock'       => intval( $data['stock'] ?? 0 ),
            'hsn_code'    => sanitize_text_field( $data['hsn_code'] ?? '' ),
            'gst_rate'    => floatval( $data['gst_rate'] ?? 18 ),
            'unit'        => sanitize_text_field( $data['unit'] ?? 'pcs' ),
        );

        $inserted = $wpdb->insert( $table, $insert_data );

        return $inserted ? $wpdb->insert_id : false;
    }

    /**
     * Update product
     *
     * @param int   $product_id Product ID.
     * @param array $data Product data.
     * @param int   $user_id User ID.
     * @return bool
     */
    public static function update_product( $product_id, $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        // Verify ownership
        $product = self::get_product( $product_id, $user_id );
        if ( ! $product ) {
            return false;
        }

        $update_data = array();

        if ( isset( $data['name'] ) ) {
            $update_data['name'] = sanitize_text_field( $data['name'] );
        }
        if ( isset( $data['sku'] ) ) {
            $update_data['sku'] = sanitize_text_field( $data['sku'] );
        }
        if ( isset( $data['description'] ) ) {
            $update_data['description'] = sanitize_textarea_field( $data['description'] );
        }
        if ( isset( $data['price'] ) ) {
            $update_data['price'] = floatval( $data['price'] );
        }
        if ( isset( $data['cost'] ) ) {
            $update_data['cost'] = floatval( $data['cost'] );
        }
        if ( isset( $data['stock'] ) ) {
            $update_data['stock'] = intval( $data['stock'] );
        }
        if ( isset( $data['hsn_code'] ) ) {
            $update_data['hsn_code'] = sanitize_text_field( $data['hsn_code'] );
        }
        if ( isset( $data['gst_rate'] ) ) {
            $update_data['gst_rate'] = floatval( $data['gst_rate'] );
        }
        if ( isset( $data['unit'] ) ) {
            $update_data['unit'] = sanitize_text_field( $data['unit'] );
        }

        if ( empty( $update_data ) ) {
            return false;
        }

        $updated = $wpdb->update(
            $table,
            $update_data,
            array(
                'id'      => $product_id,
                'user_id' => $user_id,
            )
        );

        return false !== $updated;
    }

    /**
     * Delete product
     *
     * @param int $product_id Product ID.
     * @param int $user_id User ID.
     * @return bool
     */
    public static function delete_product( $product_id, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        $deleted = $wpdb->delete(
            $table,
            array(
                'id'      => $product_id,
                'user_id' => $user_id,
            )
        );

        return false !== $deleted;
    }

    /**
     * Update product stock
     *
     * @param int $product_id Product ID.
     * @param int $quantity Quantity to add/subtract.
     * @param int $user_id User ID.
     * @return bool
     */
    public static function update_stock( $product_id, $quantity, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_products';

        $product = self::get_product( $product_id, $user_id );
        if ( ! $product ) {
            return false;
        }

        $new_stock = max( 0, intval( $product['stock'] ) + intval( $quantity ) );

        return self::update_product( $product_id, array( 'stock' => $new_stock ), $user_id );
    }
}
