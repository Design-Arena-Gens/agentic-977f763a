<?php
/**
 * Database Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Database Class
 */
class Database {

    /**
     * Create database tables
     */
    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Products table
        $products_table = $wpdb->prefix . 'gii_products';
        $products_sql = "CREATE TABLE IF NOT EXISTS $products_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            sku varchar(100) NOT NULL,
            description text,
            price decimal(10,2) NOT NULL DEFAULT 0.00,
            cost decimal(10,2) NOT NULL DEFAULT 0.00,
            stock int(11) NOT NULL DEFAULT 0,
            hsn_code varchar(50),
            gst_rate decimal(5,2) NOT NULL DEFAULT 18.00,
            unit varchar(50) DEFAULT 'pcs',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY sku (sku)
        ) $charset_collate;";

        // Invoices table
        $invoices_table = $wpdb->prefix . 'gii_invoices';
        $invoices_sql = "CREATE TABLE IF NOT EXISTS $invoices_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            invoice_number varchar(100) NOT NULL,
            customer_id bigint(20),
            customer_name varchar(255) NOT NULL,
            customer_gstin varchar(50),
            customer_address text,
            company_name varchar(255),
            company_gstin varchar(50),
            company_address text,
            invoice_date date NOT NULL,
            due_date date,
            subtotal decimal(10,2) NOT NULL DEFAULT 0.00,
            gst_amount decimal(10,2) NOT NULL DEFAULT 0.00,
            total decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(50) DEFAULT 'draft',
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY invoice_number (invoice_number),
            KEY customer_id (customer_id)
        ) $charset_collate;";

        // Invoice items table
        $invoice_items_table = $wpdb->prefix . 'gii_invoice_items';
        $invoice_items_sql = "CREATE TABLE IF NOT EXISTS $invoice_items_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            invoice_id bigint(20) NOT NULL,
            product_id bigint(20),
            item_name varchar(255) NOT NULL,
            description text,
            quantity decimal(10,2) NOT NULL DEFAULT 1.00,
            unit varchar(50) DEFAULT 'pcs',
            rate decimal(10,2) NOT NULL DEFAULT 0.00,
            gst_rate decimal(5,2) NOT NULL DEFAULT 18.00,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY invoice_id (invoice_id),
            KEY product_id (product_id)
        ) $charset_collate;";

        // Customers table
        $customers_table = $wpdb->prefix . 'gii_customers';
        $customers_sql = "CREATE TABLE IF NOT EXISTS $customers_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            name varchar(255) NOT NULL,
            email varchar(255),
            phone varchar(50),
            gstin varchar(50),
            address text,
            city varchar(100),
            state varchar(100),
            pincode varchar(20),
            country varchar(100) DEFAULT 'India',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY email (email)
        ) $charset_collate;";

        // User settings table
        $settings_table = $wpdb->prefix . 'gii_user_settings';
        $settings_sql = "CREATE TABLE IF NOT EXISTS $settings_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            setting_key varchar(100) NOT NULL,
            setting_value longtext,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY user_setting (user_id, setting_key)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $products_sql );
        dbDelta( $invoices_sql );
        dbDelta( $invoice_items_sql );
        dbDelta( $customers_sql );
        dbDelta( $settings_sql );
    }

    /**
     * Drop database tables (use with caution)
     */
    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'gii_products',
            $wpdb->prefix . 'gii_invoices',
            $wpdb->prefix . 'gii_invoice_items',
            $wpdb->prefix . 'gii_customers',
            $wpdb->prefix . 'gii_user_settings',
        );

        foreach ( $tables as $table ) {
            $wpdb->query( "DROP TABLE IF EXISTS $table" );
        }
    }
}
