<?php
/**
 * The header template
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
    <div class="container">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="site-title">
            <?php bloginfo( 'name' ); ?>
        </a>

        <nav class="main-navigation">
            <?php
            if ( has_nav_menu( 'primary' ) ) {
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => '',
                    'fallback_cb'    => false,
                ) );
            } else {
                // Default menu if no menu is assigned
                echo '<ul>';
                echo '<li><a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'surajx-gii-theme' ) . '</a></li>';
                echo '<li><a href="' . esc_url( home_url( '/pricing' ) ) . '">' . esc_html__( 'Pricing', 'surajx-gii-theme' ) . '</a></li>';

                if ( is_user_logged_in() ) {
                    echo '<li><a href="' . esc_url( home_url( '/dashboard' ) ) . '">' . esc_html__( 'Dashboard', 'surajx-gii-theme' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( wp_logout_url( home_url() ) ) . '">' . esc_html__( 'Logout', 'surajx-gii-theme' ) . '</a></li>';
                } else {
                    echo '<li><a href="' . esc_url( home_url( '/login' ) ) . '">' . esc_html__( 'Login', 'surajx-gii-theme' ) . '</a></li>';
                    echo '<li><a href="' . esc_url( home_url( '/register' ) ) . '">' . esc_html__( 'Register', 'surajx-gii-theme' ) . '</a></li>';
                }

                echo '</ul>';
            }
            ?>
        </nav>
    </div>
</header>

<main class="site-main">
