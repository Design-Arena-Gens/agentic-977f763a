<?php
/**
 * Template Name: Account Dashboard
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect to login if not logged in
if ( ! is_user_logged_in() ) {
    wp_redirect( wp_login_url( get_permalink() ) );
    exit;
}

get_header();
?>

<section class="dashboard-container">
    <div class="container">
        <?php echo do_shortcode( '[gii_customer_dashboard]' ); ?>
    </div>
</section>

<?php
get_footer();
