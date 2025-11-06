<?php
/**
 * Template Name: Forgot Password
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

// Handle password reset request
if ( isset( $_POST['reset_submit'] ) && isset( $_POST['reset_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['reset_nonce'], 'reset_action' ) ) {
        $reset_error = __( 'Security check failed. Please try again.', 'surajx-gii-theme' );
    } else {
        $user_login = sanitize_text_field( $_POST['user_login'] );

        if ( empty( $user_login ) ) {
            $reset_error = __( 'Please enter your username or email.', 'surajx-gii-theme' );
        } else {
            $user_data = get_user_by( 'email', $user_login );

            if ( ! $user_data ) {
                $user_data = get_user_by( 'login', $user_login );
            }

            if ( ! $user_data ) {
                $reset_error = __( 'No user found with that username or email.', 'surajx-gii-theme' );
            } else {
                $user_login = $user_data->user_login;
                $user_email = $user_data->user_email;

                // Generate reset key
                $key = get_password_reset_key( $user_data );

                if ( is_wp_error( $key ) ) {
                    $reset_error = $key->get_error_message();
                } else {
                    // Send reset email
                    $reset_url = network_site_url( "wp-login.php?action=rp&key=$key&login=" . rawurlencode( $user_login ), 'login' );

                    $message = __( 'Someone has requested a password reset for the following account:', 'surajx-gii-theme' ) . "\r\n\r\n";
                    $message .= network_home_url( '/' ) . "\r\n\r\n";
                    $message .= sprintf( __( 'Username: %s', 'surajx-gii-theme' ), $user_login ) . "\r\n\r\n";
                    $message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'surajx-gii-theme' ) . "\r\n\r\n";
                    $message .= __( 'To reset your password, visit the following address:', 'surajx-gii-theme' ) . "\r\n\r\n";
                    $message .= $reset_url . "\r\n";

                    $headers = 'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>' . "\r\n";

                    if ( wp_mail( $user_email, sprintf( __( '[%s] Password Reset', 'surajx-gii-theme' ), get_bloginfo( 'name' ) ), $message, $headers ) ) {
                        $reset_success = true;
                    } else {
                        $reset_error = __( 'Failed to send reset email. Please try again.', 'surajx-gii-theme' );
                    }
                }
            }
        }
    }
}

get_header();
?>

<div class="auth-container">
    <h2><?php esc_html_e( 'Reset Your Password', 'surajx-gii-theme' ); ?></h2>

    <?php if ( isset( $reset_success ) && $reset_success ) : ?>
        <div class="alert alert-success">
            <?php esc_html_e( 'Password reset link has been sent to your email address.', 'surajx-gii-theme' ); ?>
        </div>
        <div class="text-center">
            <a href="<?php echo esc_url( home_url( '/login' ) ); ?>" class="btn">
                <?php esc_html_e( 'Back to Login', 'surajx-gii-theme' ); ?>
            </a>
        </div>
    <?php else : ?>
        <?php if ( isset( $reset_error ) ) : ?>
            <div class="alert alert-error">
                <?php echo esc_html( $reset_error ); ?>
            </div>
        <?php endif; ?>

        <p><?php esc_html_e( 'Enter your username or email address and we will send you a link to reset your password.', 'surajx-gii-theme' ); ?></p>

        <form method="post" action="" id="forgotPasswordForm">
            <?php wp_nonce_field( 'reset_action', 'reset_nonce' ); ?>

            <div class="form-group">
                <label for="user_login"><?php esc_html_e( 'Username or Email', 'surajx-gii-theme' ); ?></label>
                <input type="text" name="user_login" id="user_login" required
                       value="<?php echo isset( $_POST['user_login'] ) ? esc_attr( $_POST['user_login'] ) : ''; ?>">
            </div>

            <button type="submit" name="reset_submit" class="btn" style="width: 100%;">
                <?php esc_html_e( 'Send Reset Link', 'surajx-gii-theme' ); ?>
            </button>
        </form>

        <div class="text-center mt-3">
            <p>
                <a href="<?php echo esc_url( home_url( '/login' ) ); ?>">
                    <?php esc_html_e( 'Back to Login', 'surajx-gii-theme' ); ?>
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php
get_footer();
