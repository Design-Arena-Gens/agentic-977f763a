<?php
/**
 * The footer template
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>

</main><!-- .site-main -->

<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3><?php bloginfo( 'name' ); ?></h3>
                <p><?php bloginfo( 'description' ); ?></p>
            </div>

            <div class="footer-section">
                <h3><?php esc_html_e( 'Quick Links', 'surajx-gii-theme' ); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Home', 'surajx-gii-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/pricing' ) ); ?>"><?php esc_html_e( 'Pricing', 'surajx-gii-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/dashboard' ) ); ?>"><?php esc_html_e( 'Dashboard', 'surajx-gii-theme' ); ?></a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h3><?php esc_html_e( 'Support', 'surajx-gii-theme' ); ?></h3>
                <ul>
                    <li><a href="<?php echo esc_url( home_url( '/contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'surajx-gii-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/faq' ) ); ?>"><?php esc_html_e( 'FAQ', 'surajx-gii-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/terms' ) ); ?>"><?php esc_html_e( 'Terms of Service', 'surajx-gii-theme' ); ?></a></li>
                    <li><a href="<?php echo esc_url( home_url( '/privacy' ) ); ?>"><?php esc_html_e( 'Privacy Policy', 'surajx-gii-theme' ); ?></a></li>
                </ul>
            </div>

            <?php if ( is_active_sidebar( 'footer-widget-area' ) ) : ?>
                <div class="footer-section">
                    <?php dynamic_sidebar( 'footer-widget-area' ); ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo esc_html( date( 'Y' ) ); ?> <?php bloginfo( 'name' ); ?>. <?php esc_html_e( 'All rights reserved.', 'surajx-gii-theme' ); ?></p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
