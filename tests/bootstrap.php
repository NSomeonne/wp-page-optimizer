<?php

if ( ! defined( 'ABSPATH' ) ) {
    define( 'ABSPATH', __DIR__ . '/../' );
}

$GLOBALS['wpo_test_options'] = [];

function add_action( $hook, $callback ) {
    return true;
}

function add_menu_page( ...$args ) {
    return true;
}

function add_submenu_page( ...$args ) {
    return true;
}

function current_user_can( $capability ) {
    return true;
}

function wp_verify_nonce( $nonce, $action ) {
    return true;
}

function sanitize_text_field( $value ) {
    return is_string( $value ) ? trim( $value ) : $value;
}

function wp_unslash( $value ) {
    return $value;
}

function update_option( $name, $value ) {
    $GLOBALS['wpo_test_options'][ $name ] = $value;
    return true;
}

function get_option( $name, $default = false ) {
    return $GLOBALS['wpo_test_options'][ $name ] ?? $default;
}

function esc_html( $text ) {
    return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_attr( $text ) {
    return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
}

function esc_url( $url ) {
    return filter_var( $url, FILTER_SANITIZE_URL );
}

function admin_url( $path = '' ) {
    return 'https://example.org/wp-admin/' . ltrim( $path, '/' );
}

require_once dirname( __DIR__ ) . '/wp-page-optimizer.php';
