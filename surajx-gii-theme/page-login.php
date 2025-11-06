<?php
/**
 * Template Name: Login Page
 *
 * @package Surajx_GII_Theme
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Redirect if already logged in
if ( is_user_logged_in() ) {
    wp_redirect( home_url( '/dashboard' ) );
    exit;
}

get_header();
?>

<div class="auth-container">
    <h2><?php esc_html_e( 'Sign In to Your Account', 'surajx-gii-theme' ); ?></h2>

    <?php if ( isset( $_GET['login'] ) && $_GET['login'] === 'failed' ) : ?>
        <div class="alert alert-error">
            <?php esc_html_e( 'Invalid username or password. Please try again.', 'surajx-gii-theme' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['registered'] ) && $_GET['registered'] === 'true' ) : ?>
        <div class="alert alert-success">
            <?php esc_html_e( 'Registration successful! Please log in.', 'surajx-gii-theme' ); ?>
        </div>
    <?php endif; ?>

    <?php if ( isset( $_GET['password'] ) && $_GET['password'] === 'reset' ) : ?>
        <div class="alert alert-success">
            <?php esc_html_e( 'Password reset successful! Please log in with your new password.', 'surajx-gii-theme' ); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="<?php echo esc_url( wp_login_url() ); ?>" id="loginForm">
        <div class="form-group">
            <label for="user_login"><?php esc_html_e( 'Username or Email', 'surajx-gii-theme' ); ?></label>
            <input type="text" name="log" id="user_login" required>
        </div>

        <div class="form-group">
            <label for="user_pass"><?php esc_html_e( 'Password', 'surajx-gii-theme' ); ?></label>
            <input type="password" name="pwd" id="user_pass" required>
        </div>

        <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" name="rememberme" id="rememberme" value="forever">
            <label for="rememberme" style="margin: 0;"><?php esc_html_e( 'Remember Me', 'surajx-gii-theme' ); ?></label>
        </div>

        <input type="hidden" name="redirect_to" value="<?php echo esc_url( home_url( '/dashboard' ) ); ?>">
        <input type="hidden" name="testcookie" value="1">

        <button type="submit" class="btn" style="width: 100%;">
            <?php esc_html_e( 'Sign In', 'surajx-gii-theme' ); ?>
        </button>
    </form>

    <div class="divider">
        <p><?php esc_html_e( 'or', 'surajx-gii-theme' ); ?></p>
    </div>

    <?php echo do_shortcode( '[gii_google_signin redirect="' . home_url( '/dashboard' ) . '"]' ); ?>

    <div class="text-center mt-3">
        <p>
            <a href="<?php echo esc_url( home_url( '/forgot-password' ) ); ?>">
                <?php esc_html_e( 'Forgot Password?', 'surajx-gii-theme' ); ?>
            </a>
        </p>
        <p>
            <?php esc_html_e( "Don't have an account?", 'surajx-gii-theme' ); ?>
            <a href="<?php echo esc_url( home_url( '/register' ) ); ?>">
                <?php esc_html_e( 'Sign Up', 'surajx-gii-theme' ); ?>
            </a>
        </p>
    </div>
</div>

<?php
get_footer();
