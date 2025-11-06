<?php
/**
 * Reports Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Reports Class
 */
class Reports {

    /**
     * Instance
     *
     * @var Reports
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return Reports
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
     * Get sales summary
     *
     * @param int    $user_id User ID.
     * @param string $start_date Start date (Y-m-d format).
     * @param string $end_date End date (Y-m-d format).
     * @return array
     */
    public static function get_sales_summary( $user_id, $start_date = null, $end_date = null ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        $where = array( 'user_id = %d' );
        $params = array( $user_id );

        if ( $start_date ) {
            $where[] = 'invoice_date >= %s';
            $params[] = $start_date;
        }

        if ( $end_date ) {
            $where[] = 'invoice_date <= %s';
            $params[] = $end_date;
        }

        $where_clause = implode( ' AND ', $where );

        $query = "SELECT
            COUNT(*) as total_invoices,
            SUM(total) as total_revenue,
            SUM(subtotal) as total_subtotal,
            SUM(gst_amount) as total_gst,
            AVG(total) as average_invoice_value
            FROM $table
            WHERE $where_clause";

        $result = $wpdb->get_row( $wpdb->prepare( $query, $params ), ARRAY_A );

        return $result ?: array();
    }

    /**
     * Get product sales report
     *
     * @param int    $user_id User ID.
     * @param string $start_date Start date (Y-m-d format).
     * @param string $end_date End date (Y-m-d format).
     * @return array
     */
    public static function get_product_sales( $user_id, $start_date = null, $end_date = null ) {
        global $wpdb;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $items_table = $wpdb->prefix . 'gii_invoice_items';

        $where = array( 'i.user_id = %d' );
        $params = array( $user_id );

        if ( $start_date ) {
            $where[] = 'i.invoice_date >= %s';
            $params[] = $start_date;
        }

        if ( $end_date ) {
            $where[] = 'i.invoice_date <= %s';
            $params[] = $end_date;
        }

        $where_clause = implode( ' AND ', $where );

        $query = "SELECT
            ii.item_name,
            SUM(ii.quantity) as total_quantity,
            SUM(ii.amount) as total_revenue
            FROM $items_table ii
            JOIN $invoices_table i ON ii.invoice_id = i.id
            WHERE $where_clause
            GROUP BY ii.item_name
            ORDER BY total_revenue DESC";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

        return $results ?: array();
    }

    /**
     * Get customer report
     *
     * @param int $user_id User ID.
     * @return array
     */
    public static function get_customer_report( $user_id ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        $query = "SELECT
            customer_name,
            COUNT(*) as total_invoices,
            SUM(total) as total_spent
            FROM $table
            WHERE user_id = %d
            GROUP BY customer_name
            ORDER BY total_spent DESC
            LIMIT 20";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $user_id ), ARRAY_A );

        return $results ?: array();
    }

    /**
     * Get monthly revenue trend
     *
     * @param int $user_id User ID.
     * @param int $months Number of months to retrieve.
     * @return array
     */
    public static function get_monthly_trend( $user_id, $months = 12 ) {
        global $wpdb;
        $table = $wpdb->prefix . 'gii_invoices';

        $query = "SELECT
            DATE_FORMAT(invoice_date, '%%Y-%%m') as month,
            COUNT(*) as invoice_count,
            SUM(total) as revenue
            FROM $table
            WHERE user_id = %d
            AND invoice_date >= DATE_SUB(NOW(), INTERVAL %d MONTH)
            GROUP BY month
            ORDER BY month ASC";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $user_id, $months ), ARRAY_A );

        return $results ?: array();
    }

    /**
     * Get GST report
     *
     * @param int    $user_id User ID.
     * @param string $start_date Start date (Y-m-d format).
     * @param string $end_date End date (Y-m-d format).
     * @return array
     */
    public static function get_gst_report( $user_id, $start_date = null, $end_date = null ) {
        global $wpdb;
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $items_table = $wpdb->prefix . 'gii_invoice_items';

        $where = array( 'i.user_id = %d' );
        $params = array( $user_id );

        if ( $start_date ) {
            $where[] = 'i.invoice_date >= %s';
            $params[] = $start_date;
        }

        if ( $end_date ) {
            $where[] = 'i.invoice_date <= %s';
            $params[] = $end_date;
        }

        $where_clause = implode( ' AND ', $where );

        $query = "SELECT
            ii.gst_rate,
            SUM(ii.quantity * ii.rate) as taxable_amount,
            SUM(ii.quantity * ii.rate * ii.gst_rate / 100) as gst_amount
            FROM $items_table ii
            JOIN $invoices_table i ON ii.invoice_id = i.id
            WHERE $where_clause
            GROUP BY ii.gst_rate
            ORDER BY ii.gst_rate";

        $results = $wpdb->get_results( $wpdb->prepare( $query, $params ), ARRAY_A );

        return $results ?: array();
    }
}
