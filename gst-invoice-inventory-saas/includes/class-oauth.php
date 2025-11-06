<?php
/**
 * OAuth Handler
 *
 * @package GII_SaaS
 * @since 1.0.0
 */

namespace GII_SaaS;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * OAuth Class
 */
class OAuth {

    /**
     * Instance
     *
     * @var OAuth
     */
    private static $instance = null;

    /**
     * Get instance
     *
     * @return OAuth
     */
    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action( 'rest_api_init', array( $this, 'register_routes' ) );
    }

    /**
     * Register REST API routes
     */
    public function register_routes() {
        register_rest_route( 'gii-saas/v1', '/auth/google', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'google_auth' ),
            'permission_callback' => '__return_true',
        ) );

        register_rest_route( 'gii-saas/v1', '/auth/google/callback', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => array( $this, 'google_callback' ),
            'permission_callback' => '__return_true',
        ) );
    }

    /**
     * Google OAuth initiation
     *
     * @param \WP_REST_Request $request Request object.
     * @return void
     */
    public function google_auth( $request ) {
        $client_id = get_option( 'gii_google_client_id' );
        $redirect_uri = rest_url( 'gii-saas/v1/auth/google/callback' );

        if ( empty( $client_id ) ) {
            wp_die( esc_html__( 'Google OAuth not configured. Please add Google Client ID in plugin settings.', 'gii-saas' ) );
        }

        $state = wp_create_nonce( 'google_oauth_state' );
        set_transient( 'gii_oauth_state', $state, 600 );

        $redirect_to = $request->get_param( 'redirect' ) ?: home_url( '/dashboard' );
        set_transient( 'gii_oauth_redirect_' . $state, $redirect_to, 600 );

        $auth_url = add_query_arg(
            array(
                'client_id'     => $client_id,
                'redirect_uri'  => $redirect_uri,
                'response_type' => 'code',
                'scope'         => 'email profile',
                'state'         => $state,
                'access_type'   => 'offline',
                'prompt'        => 'consent',
            ),
            'https://accounts.google.com/o/oauth2/v2/auth'
        );

        wp_redirect( $auth_url );
        exit;
    }

    /**
     * Google OAuth callback
     *
     * @param \WP_REST_Request $request Request object.
     * @return void
     */
    public function google_callback( $request ) {
        $code = $request->get_param( 'code' );
        $state = $request->get_param( 'state' );

        // Verify state
        $stored_state = get_transient( 'gii_oauth_state' );
        if ( ! $state || $state !== $stored_state ) {
            wp_die( esc_html__( 'Invalid state parameter', 'gii-saas' ) );
        }

        delete_transient( 'gii_oauth_state' );

        if ( ! $code ) {
            wp_die( esc_html__( 'Authorization failed', 'gii-saas' ) );
        }

        // Exchange code for token
        $token_data = $this->exchange_code_for_token( $code );

        if ( ! $token_data || ! isset( $token_data['access_token'] ) ) {
            wp_die( esc_html__( 'Failed to get access token', 'gii-saas' ) );
        }

        // Get user info from Google
        $user_info = $this->get_google_user_info( $token_data['access_token'] );

        if ( ! $user_info || ! isset( $user_info['email'] ) ) {
            wp_die( esc_html__( 'Failed to get user information', 'gii-saas' ) );
        }

        // Create or login user
        $user = $this->create_or_login_user( $user_info );

        if ( is_wp_error( $user ) ) {
            wp_die( esc_html( $user->get_error_message() ) );
        }

        // Log the user in
        wp_set_auth_cookie( $user->ID );

        // Redirect
        $redirect_to = get_transient( 'gii_oauth_redirect_' . $state ) ?: home_url( '/dashboard' );
        delete_transient( 'gii_oauth_redirect_' . $state );

        wp_redirect( $redirect_to );
        exit;
    }

    /**
     * Exchange authorization code for access token
     *
     * @param string $code Authorization code.
     * @return array|false
     */
    private function exchange_code_for_token( $code ) {
        $client_id = get_option( 'gii_google_client_id' );
        $client_secret = get_option( 'gii_google_client_secret' );
        $redirect_uri = rest_url( 'gii-saas/v1/auth/google/callback' );

        $response = wp_remote_post(
            'https://oauth2.googleapis.com/token',
            array(
                'body' => array(
                    'code'          => $code,
                    'client_id'     => $client_id,
                    'client_secret' => $client_secret,
                    'redirect_uri'  => $redirect_uri,
                    'grant_type'    => 'authorization_code',
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        return json_decode( $body, true );
    }

    /**
     * Get Google user info
     *
     * @param string $access_token Access token.
     * @return array|false
     */
    private function get_google_user_info( $access_token ) {
        $response = wp_remote_get(
            'https://www.googleapis.com/oauth2/v2/userinfo',
            array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $access_token,
                ),
            )
        );

        if ( is_wp_error( $response ) ) {
            return false;
        }

        $body = wp_remote_retrieve_body( $response );
        return json_decode( $body, true );
    }

    /**
     * Create or login user
     *
     * @param array $user_info User information from Google.
     * @return \WP_User|\WP_Error
     */
    private function create_or_login_user( $user_info ) {
        $email = sanitize_email( $user_info['email'] );
        $user = get_user_by( 'email', $email );

        if ( $user ) {
            return $user;
        }

        // Create new user
        $username = sanitize_user( str_replace( '@', '_', $email ) );
        $password = wp_generate_password();

        // Ensure unique username
        $original_username = $username;
        $counter = 1;
        while ( username_exists( $username ) ) {
            $username = $original_username . '_' . $counter;
            $counter++;
        }

        $user_id = wp_create_user( $username, $password, $email );

        if ( is_wp_error( $user_id ) ) {
            return $user_id;
        }

        // Update user meta
        if ( isset( $user_info['name'] ) ) {
            wp_update_user(
                array(
                    'ID'           => $user_id,
                    'display_name' => sanitize_text_field( $user_info['name'] ),
                    'first_name'   => sanitize_text_field( $user_info['given_name'] ?? '' ),
                    'last_name'    => sanitize_text_field( $user_info['family_name'] ?? '' ),
                )
            );
        }

        update_user_meta( $user_id, 'gii_google_id', sanitize_text_field( $user_info['id'] ) );

        return get_user_by( 'id', $user_id );
    }
}
