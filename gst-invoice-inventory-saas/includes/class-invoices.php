<?php
/**
 * Invoices Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Invoices Class
 */
class Invoices {

    /**
     * Instance
     *
     * @var Invoices
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Invoices
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
     * Get user invoices
     *
     * @param int $user_id User ID.
     * @return array
     */
    public static function get_user_invoices( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        $invoices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT id, invoice_number, customer_name, invoice_date as date, total, status
                FROM $table
                WHERE user_id = %d
                ORDER BY created_at DESC",
                $user_id
            ),
            ARRAY_A
        );

        return $invoices ?: array();
    }

    /**
     * Get single invoice
     *
     * @param int $invoice_id Invoice ID.
     * @param int $user_id User ID.
     * @return array|null
     */
    public static function get_invoice( $invoice_id, $user_id ) {
        global $wpdb;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $items_table = $wpdb->prefix . 'gii_invoice_items';

        // Get invoice
        $invoice = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT * FROM $invoices_table WHERE id = %d AND user_id = %d",
                $invoice_id,
                $user_id
            ),
            ARRAY_A
        );

        if ( ! $invoice ) {
            return null;
        }

        // Get invoice items
        $items = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $items_table WHERE invoice_id = %d",
                $invoice_id
            ),
            ARRAY_A
        );

        $invoice['items'] = $items;

        return $invoice;
    }

    /**
     * Create invoice
     *
     * @param array $data Invoice data.
     * @param int   $user_id User ID.
     * @return int|false Invoice ID or false on failure.
     */
    public static function create_invoice( $data, $user_id ) {
        global $wpdb;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $items_table = $wpdb->prefix . 'gii_invoice_items';

        // Generate invoice number
        $invoice_number = self::generate_invoice_number( $user_id );

        // Calculate totals
        $subtotal = 0;
        $gst_amount = 0;

        if ( isset( $data['items'] ) && is_array( $data['items'] ) ) {
            foreach ( $data['items'] as $item ) {
                $qty = floatval( $item['quantity'] ?? 1 );
                $rate = floatval( $item['rate'] ?? 0 );
                $gst_rate = floatval( $item['gst'] ?? 18 );

                $item_subtotal = $qty * $rate;
                $item_gst = ( $item_subtotal * $gst_rate ) / 100;

                $subtotal += $item_subtotal;
                $gst_amount += $item_gst;
            }
        }

        $total = $subtotal + $gst_amount;

        // Insert invoice
        $invoice_data = array(
            'user_id'          => $user_id,
            'invoice_number'   => $invoice_number,
            'customer_name'    => sanitize_text_field( $data['to']['company'] ?? '' ),
            'customer_gstin'   => sanitize_text_field( $data['to']['gstin'] ?? '' ),
            'customer_address' => sanitize_textarea_field( $data['to']['address'] ?? '' ),
            'company_name'     => sanitize_text_field( $data['from']['company'] ?? '' ),
            'company_gstin'    => sanitize_text_field( $data['from']['gstin'] ?? '' ),
            'company_address'  => sanitize_textarea_field( $data['from']['address'] ?? '' ),
            'invoice_date'     => current_time( 'mysql' ),
            'subtotal'         => $subtotal,
            'gst_amount'       => $gst_amount,
            'total'            => $total,
            'status'           => 'generated',
        );

        $inserted = $wpdb->insert( $invoices_table, $invoice_data );

        if ( ! $inserted ) {
            return false;
        }

        $invoice_id = $wpdb->insert_id;

        // Insert invoice items
        if ( isset( $data['items'] ) && is_array( $data['items'] ) ) {
            foreach ( $data['items'] as $item ) {
                $qty = floatval( $item['quantity'] ?? 1 );
                $rate = floatval( $item['rate'] ?? 0 );
                $gst_rate = floatval( $item['gst'] ?? 18 );
                $item_subtotal = $qty * $rate;
                $item_gst = ( $item_subtotal * $gst_rate ) / 100;
                $amount = $item_subtotal + $item_gst;

                $item_data = array(
                    'invoice_id' => $invoice_id,
                    'item_name'  => sanitize_text_field( $item['name'] ?? '' ),
                    'quantity'   => $qty,
                    'rate'       => $rate,
                    'gst_rate'   => $gst_rate,
                    'amount'     => $amount,
                );

                $wpdb->insert( $items_table, $item_data );
            }
        }

        return $invoice_id;
    }

    /**
     * Update invoice
     *
     * @param int   $invoice_id Invoice ID.
     * @param array $data Invoice data.
     * @param int   $user_id User ID.
     * @return bool
     */
    public static function update_invoice( $invoice_id, $data, $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        // Verify ownership
        $invoice = self::get_invoice( $invoice_id, $user_id );
        if ( ! $invoice ) {
            return false;
        }

        $update_data = array();

        if ( isset( $data['status'] ) ) {
            $update_data['status'] = sanitize_text_field( $data['status'] );
        }
        if ( isset( $data['notes'] ) ) {
            $update_data['notes'] = sanitize_textarea_field( $data['notes'] );
        }

        if ( empty( $update_data ) ) {
            return false;
        }

        $updated = $wpdb->update(
            $table,
            $update_data,
            array(
                'id'      => $invoice_id,
                'user_id' => $user_id,
            )
        );

        return false !== $updated;
    }

    /**
     * Delete invoice
     *
     * @param int $invoice_id Invoice ID.
     * @param int $user_id User ID.
     * @return bool
     */
    public static function delete_invoice( $invoice_id, $user_id ) {
        global $wpdb;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $items_table = $wpdb->prefix . 'gii_invoice_items';

        // Verify ownership
        $invoice = self::get_invoice( $invoice_id, $user_id );
        if ( ! $invoice ) {
            return false;
        }

        // Delete items first
        $wpdb->delete( $items_table, array( 'invoice_id' => $invoice_id ) );

        // Delete invoice
        $deleted = $wpdb->delete(
            $invoices_table,
            array(
                'id'      => $invoice_id,
                'user_id' => $user_id,
            )
        );

        return false !== $deleted;
    }

    /**
     * Generate invoice number
     *
     * @param int $user_id User ID.
     * @return string
     */
    private static function generate_invoice_number( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM $table WHERE user_id = %d",
                $user_id
            )
        );

        $prefix = 'INV';
        $year = date( 'Y' );
        $number = str_pad( intval( $count ) + 1, 4, '0', STR_PAD_LEFT );

        return sprintf( '%s-%s-%s', $prefix, $year, $number );
    }
}
