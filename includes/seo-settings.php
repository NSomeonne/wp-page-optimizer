<?php
/**
 * Moduł SEO – Page Optimizer Pro
 * Dodatkowe funkcje SEO: Open Graph, Twitter Card.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Open Graph meta tagi
add_action( 'wp_head', 'wpo_add_opengraph_tags', 5 );
function wpo_add_opengraph_tags() {
	if ( is_admin() ) {
		return;
	}

	$title       = get_option( 'wpo_seo_title', get_bloginfo( 'name' ) );
	$description = get_option( 'wpo_seo_description', get_bloginfo( 'description' ) );

	echo '<meta property="og:type" content="website" />' . "\n";
	echo '<meta property="og:url" content="' . esc_url( get_permalink() ?: get_site_url() ) . '" />' . "\n";
	if ( $title ) {
		echo '<meta property="og:title" content="' . esc_attr( $title ) . '" />' . "\n";
	}
	if ( $description ) {
		echo '<meta property="og:description" content="' . esc_attr( $description ) . '" />' . "\n";
	}
}
