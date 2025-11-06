<?php
/**
 * Front Page Template
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();
?>

<section class="hero-section">
    <div class="container">
        <h1><?php esc_html_e( 'GST Invoice & Inventory Management', 'surajx-gii-theme' ); ?></h1>
        <p><?php esc_html_e( 'Streamline your business with powerful invoicing and inventory tools', 'surajx-gii-theme' ); ?></p>

        <?php if ( is_user_logged_in() ) : ?>
            <a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>" class="btn">
                <?php esc_html_e( 'Go to Dashboard', 'surajx-gii-theme' ); ?>
            </a>
        <?php else : ?>
            <a href="<?php echo esc_url( home_url( '/register' ) ); ?>" class="btn">
                <?php esc_html_e( 'Get Started Free', 'surajx-gii-theme' ); ?>
            </a>
            <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="btn btn-secondary">
                <?php esc_html_e( 'Sign In', 'surajx-gii-theme' ); ?>
            </a>
        <?php endif; ?>
    </div>
</section>

<section class="features-section">
    <div class="container">
        <h2 class="text-center"><?php esc_html_e( 'Powerful Features for Your Business', 'surajx-gii-theme' ); ?></h2>

        <div class="features-grid">
            <div class="feature-card">
                <h3><?php esc_html_e( 'GST Compliant Invoicing', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Create professional GST invoices with automatic tax calculations and compliance.', 'surajx-gii-theme' ); ?></p>
            </div>

            <div class="feature-card">
                <h3><?php esc_html_e( 'Inventory Management', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Track your products, stock levels, and manage your inventory efficiently.', 'surajx-gii-theme' ); ?></p>
            </div>

            <div class="feature-card">
                <h3><?php esc_html_e( 'Cloud-Based Platform', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Access your data anywhere, anytime from any device with internet connection.', 'surajx-gii-theme' ); ?></p>
            </div>

            <div class="feature-card">
                <h3><?php esc_html_e( 'Multi-User Access', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Collaborate with your team with secure multi-user access and permissions.', 'surajx-gii-theme' ); ?></p>
            </div>

            <div class="feature-card">
                <h3><?php esc_html_e( 'Reports & Analytics', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Get insights into your business with comprehensive reports and analytics.', 'surajx-gii-theme' ); ?></p>
            </div>

            <div class="feature-card">
                <h3><?php esc_html_e( 'Secure & Reliable', 'surajx-gii-theme' ); ?></h3>
                <p><?php esc_html_e( 'Your data is encrypted and backed up regularly for maximum security.', 'surajx-gii-theme' ); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section" style="background: #f9fafb; padding: 4rem 0; text-align: center;">
    <div class="container">
        <h2><?php esc_html_e( 'Ready to Transform Your Business?', 'surajx-gii-theme' ); ?></h2>
        <p><?php esc_html_e( 'Join thousands of businesses already using our platform', 'surajx-gii-theme' ); ?></p>
        <a href="<?php echo esc_url( home_url( '/pricing' ) ); ?>" class="btn">
            <?php esc_html_e( 'View Pricing Plans', 'surajx-gii-theme' ); ?>
        </a>
    </div>
</section>

<?php
get_footer();
