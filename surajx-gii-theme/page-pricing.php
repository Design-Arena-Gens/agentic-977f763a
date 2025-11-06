<?php
/**
 * Template Name: Pricing Page
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<section class="pricing-section">
    <div class="container">
        <h1 class="text-center"><?php esc_html_e( 'Choose Your Plan', 'surajx-gii-theme' ); ?></h1>
        <p class="text-center"><?php esc_html_e( 'Select the perfect plan for your business needs', 'surajx-gii-theme' ); ?></p>

        <div class="pricing-grid">
            <!-- Starter Plan -->
            <div class="pricing-card">
                <h3><?php esc_html_e( 'Starter', 'surajx-gii-theme' ); ?></h3>
                <div class="price">
                    ₹499<span style="font-size: 1rem; font-weight: normal;">/<?php esc_html_e( 'month', 'surajx-gii-theme' ); ?></span>
                </div>
                <p><?php esc_html_e( 'Perfect for small businesses', 'surajx-gii-theme' ); ?></p>

                <ul class="pricing-features">
                    <li><?php esc_html_e( '50 Invoices per month', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( '100 Products', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( '1 User', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Basic Reports', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Email Support', 'surajx-gii-theme' ); ?></li>
                </ul>

                <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn">
                    <?php esc_html_e( 'Get Started', 'surajx-gii-theme' ); ?>
                </a>
            </div>

            <!-- Professional Plan -->
            <div class="pricing-card featured">
                <div style="background: #2563eb; color: white; padding: 0.5rem; margin: -2rem -2rem 1rem; border-radius: 8px 8px 0 0; text-align: center; font-weight: bold;">
                    <?php esc_html_e( 'Most Popular', 'surajx-gii-theme' ); ?>
                </div>
                <h3><?php esc_html_e( 'Professional', 'surajx-gii-theme' ); ?></h3>
                <div class="price">
                    ₹999<span style="font-size: 1rem; font-weight: normal;">/<?php esc_html_e( 'month', 'surajx-gii-theme' ); ?></span>
                </div>
                <p><?php esc_html_e( 'Ideal for growing businesses', 'surajx-gii-theme' ); ?></p>

                <ul class="pricing-features">
                    <li><?php esc_html_e( 'Unlimited Invoices', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( '500 Products', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( '3 Users', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Advanced Reports', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Priority Email Support', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'API Access', 'surajx-gii-theme' ); ?></li>
                </ul>

                <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn">
                    <?php esc_html_e( 'Get Started', 'surajx-gii-theme' ); ?>
                </a>
            </div>

            <!-- Enterprise Plan -->
            <div class="pricing-card">
                <h3><?php esc_html_e( 'Enterprise', 'surajx-gii-theme' ); ?></h3>
                <div class="price">
                    ₹2,499<span style="font-size: 1rem; font-weight: normal;">/<?php esc_html_e( 'month', 'surajx-gii-theme' ); ?></span>
                </div>
                <p><?php esc_html_e( 'For large organizations', 'surajx-gii-theme' ); ?></p>

                <ul class="pricing-features">
                    <li><?php esc_html_e( 'Unlimited Invoices', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Unlimited Products', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Unlimited Users', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Custom Reports', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( '24/7 Phone & Email Support', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'API Access', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Custom Integrations', 'surajx-gii-theme' ); ?></li>
                    <li><?php esc_html_e( 'Dedicated Account Manager', 'surajx-gii-theme' ); ?></li>
                </ul>

                <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn">
                    <?php esc_html_e( 'Get Started', 'surajx-gii-theme' ); ?>
                </a>
            </div>
        </div>

        <div style="text-align: center; margin-top: 3rem;">
            <p><?php esc_html_e( 'All plans include:', 'surajx-gii-theme' ); ?></p>
            <p><?php esc_html_e( '✓ GST Compliant Invoicing ✓ Cloud Storage ✓ Data Security ✓ Regular Updates', 'surajx-gii-theme' ); ?></p>
        </div>
    </div>
</section>

<?php
get_footer();
