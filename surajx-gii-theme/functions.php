<?php
/**
 * Surajx GII Theme Functions
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */
function surajx_gii_theme_setup() {
    // Add theme support
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption' ) );

    // Register navigation menus
    register_nav_menus( array(
        'primary' => __( 'Primary Menu', 'surajx-gii-theme' ),
        'footer'  => __( 'Footer Menu', 'surajx-gii-theme' ),
    ) );

    // Load text domain for translations
    load_theme_textdomain( 'surajx-gii-theme', get_template_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'surajx_gii_theme_setup' );

/**
 * Enqueue Scripts and Styles
 */
function surajx_gii_enqueue_scripts() {
    // Enqueue theme stylesheet
    wp_enqueue_style( 'surajx-gii-style', get_stylesheet_uri(), array(), '1.0.0' );

    // Enqueue theme JavaScript
    wp_enqueue_script( 'surajx-gii-main', get_template_directory_uri() . '/assets/js/main.js', array( 'jquery' ), '1.0.0', true );

    // Localize script for AJAX and REST API
    wp_localize_script( 'surajx-gii-main', 'giiTheme', array(
        'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
        'restUrl'   => rest_url( 'gii-saas/v1/' ),
        'nonce'     => wp_create_nonce( 'wp_rest' ),
        'siteUrl'   => home_url(),
    ) );
}
add_action( 'wp_enqueue_scripts', 'surajx_gii_enqueue_scripts' );

/**
 * Customer Dashboard Shortcode
 *
 * Usage: [gii_customer_dashboard]
 */
function surajx_gii_customer_dashboard_shortcode( $atts ) {
    // Check if user is logged in
    if ( ! is_user_logged_in() ) {
        return '<div class="alert alert-info">' .
               esc_html__( 'Please log in to access your dashboard.', 'surajx-gii-theme' ) .
               ' <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' .
               esc_html__( 'Login here', 'surajx-gii-theme' ) . '</a></div>';
    }

    ob_start();
    ?>
    <div class="dashboard-container" id="giiDashboard">
        <div class="dashboard-header">
            <h2><?php esc_html_e( 'Welcome back', 'surajx-gii-theme' ); ?>, <?php echo esc_html( wp_get_current_user()->display_name ); ?>!</h2>
        </div>

        <div class="dashboard-tabs">
            <button class="dashboard-tab active" data-tab="products">
                <?php esc_html_e( 'Products', 'surajx-gii-theme' ); ?>
            </button>
            <button class="dashboard-tab" data-tab="invoices">
                <?php esc_html_e( 'Invoices', 'surajx-gii-theme' ); ?>
            </button>
            <button class="dashboard-tab" data-tab="account">
                <?php esc_html_e( 'Account', 'surajx-gii-theme' ); ?>
            </button>
        </div>

        <div class="dashboard-content">
            <!-- Products Tab -->
            <div class="tab-panel active" id="products-panel">
                <div class="panel-header">
                    <h3><?php esc_html_e( 'Your Products', 'surajx-gii-theme' ); ?></h3>
                    <button class="btn" id="addProductBtn">
                        <?php esc_html_e( 'Add New Product', 'surajx-gii-theme' ); ?>
                    </button>
                </div>
                <div id="productsListContainer">
                    <p><?php esc_html_e( 'Loading products...', 'surajx-gii-theme' ); ?></p>
                </div>
            </div>

            <!-- Invoices Tab -->
            <div class="tab-panel" id="invoices-panel">
                <div class="panel-header">
                    <h3><?php esc_html_e( 'Your Invoices', 'surajx-gii-theme' ); ?></h3>
                    <a href="<?php echo esc_url( home_url( '/invoice-builder' ) ); ?>" class="btn">
                        <?php esc_html_e( 'Create New Invoice', 'surajx-gii-theme' ); ?>
                    </a>
                </div>
                <div id="invoicesListContainer">
                    <p><?php esc_html_e( 'Loading invoices...', 'surajx-gii-theme' ); ?></p>
                </div>
            </div>

            <!-- Account Tab -->
            <div class="tab-panel" id="account-panel">
                <h3><?php esc_html_e( 'Account Settings', 'surajx-gii-theme' ); ?></h3>
                <div id="accountSettingsContainer">
                    <form id="accountSettingsForm">
                        <div class="form-group">
                            <label for="company_name"><?php esc_html_e( 'Company Name', 'surajx-gii-theme' ); ?></label>
                            <input type="text" id="company_name" name="company_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="gstin"><?php esc_html_e( 'GSTIN', 'surajx-gii-theme' ); ?></label>
                            <input type="text" id="gstin" name="gstin" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="company_address"><?php esc_html_e( 'Company Address', 'surajx-gii-theme' ); ?></label>
                            <textarea id="company_address" name="company_address" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn">
                            <?php esc_html_e( 'Save Settings', 'surajx-gii-theme' ); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'gii_customer_dashboard', 'surajx_gii_customer_dashboard_shortcode' );

/**
 * Invoice Builder Shortcode
 *
 * Usage: [gii_invoice_builder]
 */
function surajx_gii_invoice_builder_shortcode( $atts ) {
    // Check if user is logged in
    if ( ! is_user_logged_in() ) {
        return '<div class="alert alert-info">' .
               esc_html__( 'Please log in to create invoices.', 'surajx-gii-theme' ) .
               ' <a href="' . esc_url( wp_login_url( get_permalink() ) ) . '">' .
               esc_html__( 'Login here', 'surajx-gii-theme' ) . '</a></div>';
    }

    ob_start();
    ?>
    <div class="invoice-builder" id="giiInvoiceBuilder">
        <h2><?php esc_html_e( 'Create New Invoice', 'surajx-gii-theme' ); ?></h2>

        <form id="invoiceBuilderForm">
            <div class="invoice-header">
                <div class="invoice-from">
                    <h3><?php esc_html_e( 'From', 'surajx-gii-theme' ); ?></h3>
                    <div class="form-group">
                        <label for="from_company"><?php esc_html_e( 'Company Name', 'surajx-gii-theme' ); ?></label>
                        <input type="text" id="from_company" name="from_company" required>
                    </div>
                    <div class="form-group">
                        <label for="from_gstin"><?php esc_html_e( 'GSTIN', 'surajx-gii-theme' ); ?></label>
                        <input type="text" id="from_gstin" name="from_gstin" required>
                    </div>
                    <div class="form-group">
                        <label for="from_address"><?php esc_html_e( 'Address', 'surajx-gii-theme' ); ?></label>
                        <textarea id="from_address" name="from_address" rows="3" required></textarea>
                    </div>
                </div>

                <div class="invoice-to">
                    <h3><?php esc_html_e( 'To', 'surajx-gii-theme' ); ?></h3>
                    <div class="form-group">
                        <label for="to_company"><?php esc_html_e( 'Customer Name', 'surajx-gii-theme' ); ?></label>
                        <input type="text" id="to_company" name="to_company" required>
                    </div>
                    <div class="form-group">
                        <label for="to_gstin"><?php esc_html_e( 'GSTIN', 'surajx-gii-theme' ); ?></label>
                        <input type="text" id="to_gstin" name="to_gstin">
                    </div>
                    <div class="form-group">
                        <label for="to_address"><?php esc_html_e( 'Address', 'surajx-gii-theme' ); ?></label>
                        <textarea id="to_address" name="to_address" rows="3" required></textarea>
                    </div>
                </div>
            </div>

            <div class="invoice-items">
                <h3><?php esc_html_e( 'Invoice Items', 'surajx-gii-theme' ); ?></h3>
                <table id="invoiceItemsTable">
                    <thead>
                        <tr>
                            <th><?php esc_html_e( 'Item', 'surajx-gii-theme' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'surajx-gii-theme' ); ?></th>
                            <th><?php esc_html_e( 'Rate', 'surajx-gii-theme' ); ?></th>
                            <th><?php esc_html_e( 'GST %', 'surajx-gii-theme' ); ?></th>
                            <th><?php esc_html_e( 'Amount', 'surajx-gii-theme' ); ?></th>
                            <th><?php esc_html_e( 'Action', 'surajx-gii-theme' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="invoiceItemsBody">
                        <tr class="invoice-item-row">
                            <td><input type="text" name="item_name[]" required></td>
                            <td><input type="number" name="item_qty[]" min="1" value="1" required></td>
                            <td><input type="number" name="item_rate[]" min="0" step="0.01" required></td>
                            <td><input type="number" name="item_gst[]" min="0" max="100" step="0.01" value="18" required></td>
                            <td class="item-amount">0.00</td>
                            <td><button type="button" class="btn btn-secondary remove-item"><?php esc_html_e( 'Remove', 'surajx-gii-theme' ); ?></button></td>
                        </tr>
                    </tbody>
                </table>
                <button type="button" id="addItemBtn" class="btn btn-secondary mt-2">
                    <?php esc_html_e( 'Add Item', 'surajx-gii-theme' ); ?>
                </button>
            </div>

            <div class="invoice-total">
                <p><?php esc_html_e( 'Subtotal:', 'surajx-gii-theme' ); ?> ₹<span id="subtotal">0.00</span></p>
                <p><?php esc_html_e( 'GST:', 'surajx-gii-theme' ); ?> ₹<span id="gstAmount">0.00</span></p>
                <p class="total-amount"><?php esc_html_e( 'Total:', 'surajx-gii-theme' ); ?> ₹<span id="totalAmount">0.00</span></p>
            </div>

            <div class="form-actions mt-4">
                <button type="submit" class="btn">
                    <?php esc_html_e( 'Generate Invoice', 'surajx-gii-theme' ); ?>
                </button>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode( 'gii_invoice_builder', 'surajx_gii_invoice_builder_shortcode' );

/**
 * Google Sign-In Button Shortcode
 *
 * Usage: [gii_google_signin]
 */
function surajx_gii_google_signin_shortcode( $atts ) {
    $atts = shortcode_atts( array(
        'redirect' => home_url( '/dashboard' ),
    ), $atts, 'gii_google_signin' );

    ob_start();
    ?>
    <button class="google-signin-btn" id="googleSignInBtn" data-redirect="<?php echo esc_url( $atts['redirect'] ); ?>">
        <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M19.8055 10.2292C19.8055 9.55203 19.7501 8.86654 19.6313 8.19775H10.2V12.0492H15.6014C15.3773 13.2911 14.6571 14.3898 13.6025 15.0879V17.5866H16.8251C18.7175 15.8449 19.8055 13.2728 19.8055 10.2292Z" fill="#4285F4"/>
            <path d="M10.2 20C12.9566 20 15.2721 19.1045 16.8286 17.5866L13.606 15.0879C12.7096 15.6979 11.5587 16.0433 10.2035 16.0433C7.5403 16.0433 5.28053 14.2834 4.48055 11.9167H1.16309V14.4925C2.75618 17.6573 6.30851 20 10.2 20Z" fill="#34A853"/>
            <path d="M4.47705 11.9167C4.05751 10.6748 4.05751 9.32953 4.47705 8.08766V5.51184H1.16309C-0.175529 8.16949 -0.175529 11.8348 1.16309 14.4925L4.47705 11.9167Z" fill="#FBBC04"/>
            <path d="M10.2 3.95671C11.6289 3.93477 13.0063 4.47506 14.0364 5.45845L16.8951 2.60012C15.1829 0.990832 12.9356 0.104492 10.2 0.130431C6.30851 0.130431 2.75618 2.47311 1.16309 5.51181L4.47705 8.08763C5.27352 5.71652 7.53679 3.95671 10.2 3.95671Z" fill="#EA4335"/>
        </svg>
        <?php esc_html_e( 'Sign in with Google', 'surajx-gii-theme' ); ?>
    </button>
    <?php
    return ob_get_clean();
}
add_shortcode( 'gii_google_signin', 'surajx_gii_google_signin_shortcode' );

/**
 * Custom Login URL Redirect
 */
function surajx_gii_login_redirect( $redirect_to, $request, $user ) {
    if ( isset( $user->roles ) && is_array( $user->roles ) ) {
        return home_url( '/dashboard' );
    }
    return $redirect_to;
}
add_filter( 'login_redirect', 'surajx_gii_login_redirect', 10, 3 );

/**
 * Widget Areas
 */
function surajx_gii_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Footer Widget Area', 'surajx-gii-theme' ),
        'id'            => 'footer-widget-area',
        'description'   => __( 'Appears in the footer section', 'surajx-gii-theme' ),
        'before_widget' => '<div class="footer-widget">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );
}
add_action( 'widgets_init', 'surajx_gii_widgets_init' );

/**
 * Customize Login Page
 */
function surajx_gii_login_logo() {
    ?>
    <style type="text/css">
        #login h1 a, .login h1 a {
            background-image: none;
            height: auto;
            width: auto;
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
            text-indent: 0;
        }
        .login form {
            border-radius: 8px;
        }
        .login .button-primary {
            background: #2563eb;
            border-color: #2563eb;
            box-shadow: none;
            text-shadow: none;
        }
    </style>
    <?php
}
add_action( 'login_enqueue_scripts', 'surajx_gii_login_logo' );

/**
 * Change Login Logo URL
 */
function surajx_gii_login_logo_url() {
    return home_url();
}
add_filter( 'login_headerurl', 'surajx_gii_login_logo_url' );

/**
 * Change Login Logo Title
 */
function surajx_gii_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'surajx_gii_login_logo_url_title' );
