<?php
/**
 * Moduł naprawy – Page Optimizer Pro
 * Dodatkowe narzędzia diagnostyczne i naprawcze.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Sprawdź czy tabele WP są kompletne (wywoływane z kokpitu diagnostycznego)
function wpo_check_db_tables() {
	global $wpdb;
	$required = [
		$wpdb->posts,
		$wpdb->postmeta,
		$wpdb->options,
		$wpdb->users,
		$wpdb->usermeta,
	];

	$missing = [];
	foreach ( $required as $table ) {
		$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
		if ( ! $exists ) {
			$missing[] = $table;
		}
	}
	return $missing;
}
