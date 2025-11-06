<?php
/**
 * Template Name: Register Page
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

// Handle registration
if ( isset( $_POST['register_submit'] ) && isset( $_POST['register_nonce'] ) ) {
    if ( ! wp_verify_nonce( $_POST['register_nonce'], 'register_action' ) ) {
        $registration_error = __( 'Security check failed. Please try again.', 'surajx-gii-theme' );
    } else {
        $username = sanitize_user( $_POST['username'] );
        $email    = sanitize_email( $_POST['email'] );
        $password = $_POST['password'];

        if ( empty( $username ) || empty( $email ) || empty( $password ) ) {
            $registration_error = __( 'All fields are required.', 'surajx-gii-theme' );
        } elseif ( username_exists( $username ) ) {
            $registration_error = __( 'Username already exists.', 'surajx-gii-theme' );
        } elseif ( email_exists( $email ) ) {
            $registration_error = __( 'Email already exists.', 'surajx-gii-theme' );
        } else {
            $user_id = wp_create_user( $username, $password, $email );

            if ( is_wp_error( $user_id ) ) {
                $registration_error = $user_id->get_error_message();
            } else {
                wp_redirect( home_url( '/login?registered=true' ) );
                exit;
            }
        }
    }
}

get_header();
?>

<div class="auth-container">
    <h2><?php esc_html_e( 'Create Your Account', 'surajx-gii-theme' ); ?></h2>

    <?php if ( isset( $registration_error ) ) : ?>
        <div class="alert alert-error">
            <?php echo esc_html( $registration_error ); ?>
        </div>
    <?php endif; ?>

    <form method="post" action="" id="registerForm">
        <?php wp_nonce_field( 'register_action', 'register_nonce' ); ?>

        <div class="form-group">
            <label for="username"><?php esc_html_e( 'Username', 'surajx-gii-theme' ); ?></label>
            <input type="text" name="username" id="username" required
                   value="<?php echo isset( $_POST['username'] ) ? esc_attr( $_POST['username'] ) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="email"><?php esc_html_e( 'Email Address', 'surajx-gii-theme' ); ?></label>
            <input type="email" name="email" id="email" required
                   value="<?php echo isset( $_POST['email'] ) ? esc_attr( $_POST['email'] ) : ''; ?>">
        </div>

        <div class="form-group">
            <label for="password"><?php esc_html_e( 'Password', 'surajx-gii-theme' ); ?></label>
            <input type="password" name="password" id="password" required minlength="8">
            <small><?php esc_html_e( 'Minimum 8 characters', 'surajx-gii-theme' ); ?></small>
        </div>

        <div class="form-group">
            <label for="confirm_password"><?php esc_html_e( 'Confirm Password', 'surajx-gii-theme' ); ?></label>
            <input type="password" name="confirm_password" id="confirm_password" required minlength="8">
        </div>

        <button type="submit" name="register_submit" class="btn" style="width: 100%;">
            <?php esc_html_e( 'Create Account', 'surajx-gii-theme' ); ?>
        </button>
    </form>

    <div class="divider">
        <p><?php esc_html_e( 'or', 'surajx-gii-theme' ); ?></p>
    </div>

    <?php echo do_shortcode( '[gii_google_signin redirect="' . home_url( '/dashboard' ) . '"]' ); ?>

    <div class="text-center mt-3">
        <p>
            <?php esc_html_e( 'Already have an account?', 'surajx-gii-theme' ); ?>
            <a href="<?php echo esc_url( home_url( '/login' ) ); ?>">
                <?php esc_html_e( 'Sign In', 'surajx-gii-theme' ); ?>
            </a>
        </p>
    </div>
</div>

<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;

    if (password !== confirmPassword) {
        e.preventDefault();
        alert('<?php echo esc_js( __( 'Passwords do not match!', 'surajx-gii-theme' ) ); ?>');
    }
});
</script>

<?php
get_footer();
