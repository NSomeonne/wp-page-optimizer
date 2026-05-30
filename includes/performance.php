<?php
/**
 * Moduł wydajności – Page Optimizer Pro
 * Zawiera dodatkowe funkcje optymalizacji (preload, resource hints).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Preload LCP image hint (opcjonalnie – brak konfiguracji w tej wersji)
add_action( 'wp_head', 'wpo_add_dns_prefetch', 2 );
function wpo_add_dns_prefetch() {
	if ( is_admin() ) {
		return;
	}
	// Podstawowe resource hints dla popularnych CDN
	echo '<link rel="dns-prefetch" href="//fonts.googleapis.com" />' . "\n";
	echo '<link rel="dns-prefetch" href="//fonts.gstatic.com" />' . "\n";
}
