<?php
/**
 * Plugin Name: Page Optimizer Pro
 * Plugin URI:  https://github.com/norbertsiedlecki-prog/wp-page-optimizer
 * Description: Optymalizuje wydajność WordPress + AI + SEO + Naprawy + Integracje
 * Version:     2.0.2
 * Author:      Norbert Siedlecki
 * Author URI:  https://github.com/norbertsiedlecki-prog
 * License:     GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-page-optimizer
 */

// Zabezpieczenie przed bezpośrednim dostępem
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WPO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'WPO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'WPO_VERSION', '2.0.2' );

// Bezpieczne ładowanie modułów
$wpo_modules = [
	'includes/ai-integration.php',
	'includes/seo-settings.php',
	'includes/site-repair.php',
	'includes/performance.php',
];

foreach ( $wpo_modules as $module ) {
	$path = WPO_PLUGIN_DIR . $module;
	if ( file_exists( $path ) ) {
		require_once $path;
	}
}

// ─────────────────────────────────────────────
// MENU ADMINA
// ─────────────────────────────────────────────
add_action( 'admin_menu', 'wpo_add_admin_menu' );
function wpo_add_admin_menu() {
	add_menu_page(
		'Page Optimizer Pro',
		'Page Optimizer',
		'manage_options',
		'wpo-settings',
		'wpo_render_settings_page',
		'dashicons-rocket',
		99
	);

	$subpages = [
		[ 'Wydajność',     'wpo-performance', 'wpo_render_performance_page' ],
		[ 'SEO',           'wpo-seo',         'wpo_render_seo_page'         ],
		[ 'AI Integracja', 'wpo-ai',          'wpo_render_ai_page'          ],
		[ 'Naprawa',       'wpo-repair',       'wpo_render_repair_page'      ],
	];

	foreach ( $subpages as $sp ) {
		add_submenu_page( 'wpo-settings', $sp[0], $sp[0], 'manage_options', $sp[1], $sp[2] );
	}
}

// ─────────────────────────────────────────────
// STRONA GŁÓWNA
// ─────────────────────────────────────────────
function wpo_render_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Obsługa formularza
	if (
		isset( $_POST['submit_wpo'], $_POST['_wpnonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpo_nonce' )
	) {
		update_option( 'wpo_minify_css',  isset( $_POST['wpo_minify_css'] ) );
		update_option( 'wpo_minify_js',   isset( $_POST['wpo_minify_js'] ) );
		update_option( 'wpo_lazy_loading', isset( $_POST['wpo_lazy_loading'] ) );
		update_option( 'wpo_defer_js',    isset( $_POST['wpo_defer_js'] ) );
		update_option( 'wpo_cache_time',  intval( $_POST['wpo_cache_time'] ?? 3600 ) );
		echo '<div class="notice notice-success"><p>✅ Ustawienia zapisane pomyślnie!</p></div>';
	}

	$minify_css   = get_option( 'wpo_minify_css' );
	$minify_js    = get_option( 'wpo_minify_js' );
	$lazy_loading = get_option( 'wpo_lazy_loading' );
	$defer_js     = get_option( 'wpo_defer_js' );
	$cache_time   = get_option( 'wpo_cache_time', 3600 );
	?>
	<div class="wrap">
		<h1>🚀 Page Optimizer Pro v<?php echo esc_html( WPO_VERSION ); ?></h1>
		<p style="font-size:16px;color:#666;">Kompleksowa optymalizacja WordPress + AI + SEO</p>

		<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin:28px 0;">
			<?php
			$cards = [
				[ '⚡', 'Wydajność',     'Minifikacja, Cache, Lazy Loading', 'wpo-performance', '#667eea,#764ba2' ],
				[ '🔍', 'SEO',           'Meta tagi, Schema.org, Sitemap',   'wpo-seo',         '#f093fb,#f5576c' ],
				[ '🤖', 'AI Integracja', 'ChatGPT, Claude, Gemini',          'wpo-ai',          '#4facfe,#00f2fe' ],
				[ '🔧', 'Naprawa',       'Usuwanie błędów, Clean DB',        'wpo-repair',      '#fa709a,#fee140' ],
			];
			foreach ( $cards as $c ) {
				printf(
					'<div style="background:linear-gradient(135deg,%s);padding:20px;border-radius:10px;color:#fff;">
						<h3 style="margin-top:0">%s %s</h3>
						<p style="margin-bottom:12px;opacity:.9">%s</p>
						<a href="%s" style="color:#fff;font-weight:600;text-decoration:underline;">Konfiguruj →</a>
					</div>',
					esc_attr( $c[4] ),
					esc_html( $c[0] ),
					esc_html( $c[1] ),
					esc_html( $c[2] ),
					esc_url( admin_url( 'admin.php?page=' . $c[3] ) )
				);
			}
			?>
		</div>

		<form method="POST" style="max-width:700px;background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);margin-top:20px;">
			<?php wp_nonce_field( 'wpo_nonce' ); ?>
			<h2>⚙️ Ustawienia Wydajności</h2>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="wpo_minify_css">📦 Asynchroniczny CSS</label></th>
					<td>
						<input type="checkbox" name="wpo_minify_css" id="wpo_minify_css" value="1" <?php checked( $minify_css ); ?> />
						<p class="description">Ładuje CSS asynchronicznie, przyspieszając renderowanie (FCP)</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_minify_js">📦 Minifikacja JavaScript</label></th>
					<td>
						<input type="checkbox" name="wpo_minify_js" id="wpo_minify_js" value="1" <?php checked( $minify_js ); ?> />
						<p class="description">Włącza wsparcie dla minimalizowania kodu JS</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_defer_js">⏳ Defer JavaScript</label></th>
					<td>
						<input type="checkbox" name="wpo_defer_js" id="wpo_defer_js" value="1" <?php checked( $defer_js ); ?> />
						<p class="description">Skrypty ładują się z atrybutem <code>defer</code> – bez blokowania renderowania. jQuery jest chroniony.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_lazy_loading">🖼️ Lazy Loading obrazów</label></th>
					<td>
						<input type="checkbox" name="wpo_lazy_loading" id="wpo_lazy_loading" value="1" <?php checked( $lazy_loading ); ?> />
						<p class="description">Obrazy ładują się dopiero przy przewijaniu</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_cache_time">💾 Cache (sekundy)</label></th>
					<td>
						<input type="number" name="wpo_cache_time" id="wpo_cache_time"
							value="<?php echo esc_attr( $cache_time ); ?>" min="300" max="604800"
							style="width:150px;" />
						<p class="description">Domyślnie 3600 s (1 godzina). Max 604800 s (7 dni).</p>
					</td>
				</tr>
			</table>
			<?php submit_button( '💾 Zapisz ustawienia', 'primary', 'submit_wpo' ); ?>
		</form>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// STRONA: WYDAJNOŚĆ (placeholder)
// ─────────────────────────────────────────────
function wpo_render_performance_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1>⚡ Ustawienia Wydajności</h1>
		<p>Wszystkie opcje optymalizacji wydajności są na stronie głównej wtyczki.</p>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpo-settings' ) ); ?>" class="button button-primary">← Wróć do ustawień</a>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// STRONA: SEO
// ─────────────────────────────────────────────
function wpo_render_seo_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['submit_seo'], $_POST['_wpnonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpo_seo_nonce' )
	) {
		update_option( 'wpo_seo_title',       sanitize_text_field( wp_unslash( $_POST['wpo_seo_title'] ?? '' ) ) );
		update_option( 'wpo_seo_description', sanitize_textarea_field( wp_unslash( $_POST['wpo_seo_description'] ?? '' ) ) );
		update_option( 'wpo_seo_keywords',    sanitize_text_field( wp_unslash( $_POST['wpo_seo_keywords'] ?? '' ) ) );
		update_option( 'wpo_enable_schema',   isset( $_POST['wpo_enable_schema'] ) );
		update_option( 'wpo_generate_sitemap', isset( $_POST['wpo_generate_sitemap'] ) );
		echo '<div class="notice notice-success"><p>✅ Ustawienia SEO zapisane!</p></div>';
	}

	$seo_title       = get_option( 'wpo_seo_title', get_bloginfo( 'name' ) );
	$seo_description = get_option( 'wpo_seo_description', get_bloginfo( 'description' ) );
	$seo_keywords    = get_option( 'wpo_seo_keywords', '' );
	$enable_schema   = get_option( 'wpo_enable_schema' );
	$generate_sitemap = get_option( 'wpo_generate_sitemap' );
	?>
	<div class="wrap">
		<h1>🔍 Ustawienia SEO</h1>
		<form method="POST" style="max-width:700px;background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);">
			<?php wp_nonce_field( 'wpo_seo_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="wpo_seo_title">📝 Title Tag</label></th>
					<td><input type="text" name="wpo_seo_title" id="wpo_seo_title"
						value="<?php echo esc_attr( $seo_title ); ?>"
						style="width:100%;max-width:500px;" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_seo_description">📋 Meta description</label></th>
					<td>
						<textarea name="wpo_seo_description" id="wpo_seo_description"
							style="width:100%;max-width:500px;height:100px;"
							maxlength="320"><?php echo esc_textarea( $seo_description ); ?></textarea>
						<p class="description">Zalecana długość: 120–160 znaków.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_seo_keywords">🏷️ Słowa kluczowe</label></th>
					<td><input type="text" name="wpo_seo_keywords" id="wpo_seo_keywords"
						value="<?php echo esc_attr( $seo_keywords ); ?>"
						style="width:100%;max-width:500px;" /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_enable_schema">🔗 Schema.org (JSON-LD)</label></th>
					<td><input type="checkbox" name="wpo_enable_schema" id="wpo_enable_schema" value="1" <?php checked( $enable_schema ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_generate_sitemap">🗺️ Generuj Sitemap</label></th>
					<td>
						<input type="checkbox" name="wpo_generate_sitemap" id="wpo_generate_sitemap" value="1" <?php checked( $generate_sitemap ); ?> />
						<p class="description">Sitemap dostępny pod adresem <code><?php echo esc_url( site_url( '/sitemap.xml' ) ); ?></code></p>
					</td>
				</tr>
			</table>
			<?php submit_button( '💾 Zapisz ustawienia SEO', 'primary', 'submit_seo' ); ?>
		</form>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// STRONA: AI INTEGRACJA
// ─────────────────────────────────────────────
function wpo_render_ai_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	if (
		isset( $_POST['submit_ai'], $_POST['_wpnonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpo_ai_nonce' )
	) {
		$provider = sanitize_text_field( wp_unslash( $_POST['wpo_ai_provider'] ?? 'none' ) );
		$allowed  = [ 'none', 'openai', 'claude', 'google', 'cohere' ];
		if ( ! in_array( $provider, $allowed, true ) ) {
			$provider = 'none';
		}
		update_option( 'wpo_ai_provider', $provider );

		// Klucz API: zapisuj tylko jeżeli użytkownik nie zostawił zamaskowanej wartości
		$raw_key = sanitize_text_field( wp_unslash( $_POST['wpo_ai_api_key'] ?? '' ) );
		if ( $raw_key && strpos( $raw_key, '***' ) === false ) {
			update_option( 'wpo_ai_api_key', $raw_key );
		}

		update_option( 'wpo_ai_auto_content', isset( $_POST['wpo_ai_auto_content'] ) );
		update_option( 'wpo_ai_seo_optimize', isset( $_POST['wpo_ai_seo_optimize'] ) );
		echo '<div class="notice notice-success"><p>✅ Ustawienia AI zapisane!</p></div>';
	}

	$ai_provider     = get_option( 'wpo_ai_provider', 'none' );
	$ai_api_key      = get_option( 'wpo_ai_api_key', '' );
	$ai_auto_content = get_option( 'wpo_ai_auto_content' );
	$ai_seo_optimize = get_option( 'wpo_ai_seo_optimize' );
	$masked_key      = $ai_api_key ? substr( $ai_api_key, 0, 8 ) . '***' : '';
	?>
	<div class="wrap">
		<h1>🤖 Integracja AI</h1>
		<form method="POST" style="max-width:700px;background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);">
			<?php wp_nonce_field( 'wpo_ai_nonce' ); ?>
			<table class="form-table">
				<tr>
					<th scope="row"><label for="wpo_ai_provider">🤖 Dostawca AI</label></th>
					<td>
						<select name="wpo_ai_provider" id="wpo_ai_provider" style="width:100%;max-width:300px;">
							<option value="none"   <?php selected( $ai_provider, 'none' ); ?>>— Żaden —</option>
							<option value="openai" <?php selected( $ai_provider, 'openai' ); ?>>OpenAI (ChatGPT)</option>
							<option value="claude" <?php selected( $ai_provider, 'claude' ); ?>>Anthropic (Claude)</option>
							<option value="google" <?php selected( $ai_provider, 'google' ); ?>>Google (Gemini)</option>
							<option value="cohere" <?php selected( $ai_provider, 'cohere' ); ?>>Cohere</option>
						</select>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_ai_api_key">🔑 API Key</label></th>
					<td>
						<input type="password" name="wpo_ai_api_key" id="wpo_ai_api_key"
							value="<?php echo esc_attr( $masked_key ); ?>"
							autocomplete="new-password"
							style="width:100%;max-width:300px;" />
						<p class="description">Zostaw to pole bez zmian, jeśli nie chcesz aktualizować klucza.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_ai_auto_content">📝 Auto-generowanie treści</label></th>
					<td><input type="checkbox" name="wpo_ai_auto_content" id="wpo_ai_auto_content" value="1" <?php checked( $ai_auto_content ); ?> /></td>
				</tr>
				<tr>
					<th scope="row"><label for="wpo_ai_seo_optimize">🔍 Optymalizacja SEO przez AI</label></th>
					<td><input type="checkbox" name="wpo_ai_seo_optimize" id="wpo_ai_seo_optimize" value="1" <?php checked( $ai_seo_optimize ); ?> /></td>
				</tr>
			</table>
			<?php submit_button( '💾 Zapisz ustawienia AI', 'primary', 'submit_ai' ); ?>
		</form>

		<?php do_action( 'wpo_after_ai_form' ); ?>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// STRONA: NAPRAWA
// ─────────────────────────────────────────────
function wpo_render_repair_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$repair_message = '';
	$repair_type    = 'success';

	if (
		isset( $_POST['repair_action'], $_POST['_wpnonce'] ) &&
		wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'wpo_repair_nonce' )
	) {
		global $wpdb;
		$action = sanitize_text_field( wp_unslash( $_POST['repair_action'] ) );

		switch ( $action ) {
			case 'cleanup_db':
				// Usuwanie meta kluczy blokad edycji
				$wpdb->query(
					$wpdb->prepare(
						"DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s OR meta_key = %s",
						'_edit_lock',
						'_edit_last'
					)
				);
				$repair_message = '✅ Usunięto blokady edycji z bazy danych!';
				break;

			case 'clear_cache':
				wp_cache_flush();
				$repair_message = '✅ Cache wyczyszczony!';
				break;

			case 'remove_orphaned':
				// Bezpieczna podzapytanie przez tymczasową tabelę, aby uniknąć problemu MySQL z DELETE + subquery na tej samej tabeli
				$orphaned = $wpdb->get_col(
					"SELECT pm.meta_id FROM {$wpdb->postmeta} pm
					LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
					WHERE p.ID IS NULL
					LIMIT 500"
				);
				$deleted = 0;
				if ( ! empty( $orphaned ) ) {
					$ids_placeholder = implode( ',', array_map( 'intval', $orphaned ) );
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
					$deleted = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_id IN ({$ids_placeholder})" );
				}
				$repair_message = sprintf( '✅ Usunięto %d sierocych wpisów postmeta.', (int) $deleted );
				break;

			default:
				$repair_message = '⚠️ Nieznana akcja.';
				$repair_type    = 'warning';
		}
	}

	$db_size_mb = wpo_get_db_size();
	$db_size    = wpo_format_db_size( $db_size_mb );
	$wp_errors  = wpo_get_debug_errors();
	$error_count = is_array( $wp_errors ) ? count( $wp_errors ) : 0;
	?>
	<div class="wrap">
		<h1>🔧 Naprawa Strony</h1>
		<?php if ( $repair_message ) : ?>
			<div class="notice notice-<?php echo esc_attr( $repair_type ); ?> is-dismissible"><p><?php echo esc_html( $repair_message ); ?></p></div>
		<?php endif; ?>

		<div style="background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);margin-bottom:20px;">
			<h2>🩺 Diagnostyka</h2>
			<table class="widefat striped">
				<thead>
					<tr><th>Parametr</th><th>Wartość</th><th>Status</th></tr>
				</thead>
				<tbody>
					<tr>
						<td>📊 Rozmiar bazy danych</td>
						<td><?php echo esc_html( $db_size ); ?></td>
						<td><?php echo ( $db_size_mb > 100 ) ? '⚠️ Duża' : '✅ OK'; ?></td>
					</tr>
					<tr>
						<td>🚨 Błędy w debug.log</td>
						<td><?php echo esc_html( $error_count ); ?> wpisów</td>
						<td><?php echo $error_count > 10 ? '❌ Wymaga uwagi' : '✅ Czysto'; ?></td>
					</tr>
					<tr>
						<td>🐘 Wersja PHP</td>
						<td><?php echo esc_html( PHP_VERSION ); ?></td>
						<td><?php echo version_compare( PHP_VERSION, '7.4', '>=' ) ? '✅ OK' : '⚠️ Przestarzała'; ?></td>
					</tr>
					<tr>
						<td>📝 Wersja WordPress</td>
						<td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
						<td>✅</td>
					</tr>
				</tbody>
			</table>
		</div>

		<div style="background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);">
			<h2>🛠️ Akcje naprawcze</h2>
			<p style="color:#666;">Każdy przycisk wysyła osobny formularz – operacje są bezpieczne i odwracalne z poziomu backupu.</p>
			<div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:16px;">
				<?php
				$actions = [
					[ 'cleanup_db',      '🗑️ Wyczyść blokady DB',    'Usuwa klucze _edit_lock i _edit_last' ],
					[ 'clear_cache',     '⚡ Zrzuć cache',            'Wywołuje wp_cache_flush()' ],
					[ 'remove_orphaned', '🧹 Usuń sieroty (do 500)', 'Usuwa postmeta bez powiązanego posta' ],
				];
				foreach ( $actions as $a ) :
					?>
					<form method="POST" style="display:inline;">
						<?php wp_nonce_field( 'wpo_repair_nonce' ); ?>
						<button type="submit" name="repair_action" value="<?php echo esc_attr( $a[0] ); ?>"
							class="button button-primary"
							title="<?php echo esc_attr( $a[2] ); ?>">
							<?php echo esc_html( $a[1] ); ?>
						</button>
					</form>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php
}

// ─────────────────────────────────────────────
// HELPER: rozmiar bazy danych
// ─────────────────────────────────────────────
function wpo_get_db_size() {
	global $wpdb;
	$size = $wpdb->get_var(
		$wpdb->prepare(
			'SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2)
			 FROM information_schema.tables
			 WHERE table_schema = %s',
			DB_NAME
		)
	);
	return $size !== null ? (float) $size : 0.0;
}

/**
 * Formatuje wynik wpo_get_db_size() do postaci czytelnej dla użytkownika.
 */
function wpo_format_db_size( float $size_mb ): string {
	return number_format( $size_mb, 2 ) . ' MB';
}

// ─────────────────────────────────────────────
// HELPER: ostatnie błędy z debug.log
// ─────────────────────────────────────────────
function wpo_get_debug_errors() {
	if ( ! defined( 'WP_DEBUG_LOG' ) || ! WP_DEBUG_LOG ) {
		return [];
	}

	$log_file = is_string( WP_DEBUG_LOG ) ? WP_DEBUG_LOG : WP_CONTENT_DIR . '/debug.log';

	if ( ! file_exists( $log_file ) || ! is_readable( $log_file ) ) {
		return [];
	}

	$file_size = filesize( $log_file );
	if ( ! $file_size ) {
		return [];
	}

	// Czytamy tylko ostatnie 10 KB – unikamy błędu 'Allowed memory size exhausted'
	$read_bytes = min( 10240, $file_size );
	$fh = @fopen( $log_file, 'r' );
	if ( ! $fh ) {
		return [];
	}

	fseek( $fh, -$read_bytes, SEEK_END );
	$content = fread( $fh, $read_bytes );
	fclose( $fh );

	$lines = explode( "\n", trim( $content ) );
	return array_slice( array_filter( $lines ), -20 );
}

// ─────────────────────────────────────────────
// HOOK: SEO Title Tag (document_title_parts)
// ─────────────────────────────────────────────
add_filter( 'document_title_parts', 'wpo_filter_document_title' );
function wpo_filter_document_title( $title ) {
	if ( is_admin() ) {
		return $title;
	}
	$seo_title = get_option( 'wpo_seo_title', '' );
	if ( $seo_title ) {
		$title['title'] = $seo_title;
		// Usuń sufiks nazwy serwisu żeby uniknąć duplikatu
		unset( $title['site'] );
	}
	return $title;
}

// ─────────────────────────────────────────────
// HOOK: Defer JS (chroni jQuery i jquery-migrate)
// ─────────────────────────────────────────────
add_filter( 'script_loader_tag', 'wpo_defer_js_tags', 10, 3 );
function wpo_defer_js_tags( $tag, $handle, $src ) {
	if ( is_admin() || ! get_option( 'wpo_defer_js' ) ) {
		return $tag;
	}

	// Chronione handlery – nie dodajemy defer
	$protected = [ 'jquery', 'jquery-core', 'jquery-migrate' ];
	if ( in_array( $handle, $protected, true ) ) {
		return $tag;
	}

	// Dodaj defer tylko jeśli jeszcze go nie ma
	if ( strpos( $tag, ' defer' ) !== false ) {
		return $tag;
	}

	return str_replace( '<script ', '<script defer ', $tag );
}

// ─────────────────────────────────────────────
// HOOK: Lazy Loading obrazów
// ─────────────────────────────────────────────
add_filter( 'wp_get_attachment_image_attributes', 'wpo_add_lazy_loading' );
function wpo_add_lazy_loading( $attr ) {
	if ( get_option( 'wpo_lazy_loading' ) && ! is_admin() ) {
		$attr['loading'] = 'lazy';
	}
	return $attr;
}

// ─────────────────────────────────────────────
// HOOK: Asynchroniczny CSS
// ─────────────────────────────────────────────
add_filter( 'style_loader_tag', 'wpo_async_css_tags', 10, 4 );
function wpo_async_css_tags( $tag, $handle, $src, $media ) {
	if ( ! get_option( 'wpo_minify_css' ) || is_admin() ) {
		return $tag;
	}

	// Pomiń własne arkusze wtyczki
	if ( strpos( $handle, 'wpo-' ) === 0 ) {
		return $tag;
	}

	// Nie przetwarzaj ponownie jeśli tag już zawiera onload (np. wywołanie filtra dwa razy)
	if ( strpos( $tag, 'onload=' ) !== false ) {
		return $tag;
	}

	$escaped_media = esc_attr( $media );

	// Buduj noscript z oryginalnego tagu (przed modyfikacją)
	$noscript = '<noscript>' . $tag . '</noscript>';

	$tag = str_replace(
		"media='" . $escaped_media . "'",
		"media='print' onload=\"this.media='" . $escaped_media . "'\"",
		$tag
	);

	return $tag . "\n" . $noscript;
}

// ─────────────────────────────────────────────
// HOOK: Nagłówki cache + bezpieczeństwa
// ─────────────────────────────────────────────
add_action( 'send_headers', 'wpo_add_cache_headers' );
function wpo_add_cache_headers() {
	if ( is_admin() || headers_sent() ) {
		return;
	}

	$cache_time = intval( get_option( 'wpo_cache_time', 3600 ) );
	header( 'Cache-Control: public, max-age=' . $cache_time );
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
}

// ─────────────────────────────────────────────
// HOOK: SEO tagi w <head>
// ─────────────────────────────────────────────
add_action( 'wp_head', 'wpo_add_seo_tags', 1 );
function wpo_add_seo_tags() {
	if ( is_admin() ) {
		return;
	}

	$seo_description = get_option( 'wpo_seo_description', '' );
	$seo_keywords    = get_option( 'wpo_seo_keywords', '' );

	if ( $seo_description ) {
		echo '<meta name="description" content="' . esc_attr( $seo_description ) . '" />' . "\n";
	}
	if ( $seo_keywords ) {
		echo '<meta name="keywords" content="' . esc_attr( $seo_keywords ) . '" />' . "\n";
	}
	if ( get_option( 'wpo_enable_schema' ) ) {
		wpo_add_schema_org();
	}
}

function wpo_add_schema_org() {
	$schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'WebSite',
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'url'         => get_site_url(),
	];
	$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES );
	if ( $json ) {
		echo '<script type="application/ld+json">' . $json . '</script>' . "\n";
	}
}

// ─────────────────────────────────────────────
// HOOK: Aktywacja / flush rewrite rules dla sitemapy
// ─────────────────────────────────────────────
add_action( 'init', 'wpo_register_sitemap_rewrite' );
function wpo_register_sitemap_rewrite() {
	if ( ! get_option( 'wpo_generate_sitemap' ) ) {
		return;
	}
	add_rewrite_rule( '^sitemap\.xml$', 'index.php?wpo_sitemap=1', 'top' );
	add_filter( 'query_vars', static function ( $vars ) {
		$vars[] = 'wpo_sitemap';
		return $vars;
	} );
}

add_action( 'template_redirect', 'wpo_deliver_sitemap' );
function wpo_deliver_sitemap() {
	if ( ! get_query_var( 'wpo_sitemap' ) ) {
		return;
	}

	// Zbierz opublikowane posty i strony
	$query = new WP_Query( [
		'post_type'      => [ 'post', 'page' ],
		'post_status'    => 'publish',
		'posts_per_page' => 1000,
		'no_found_rows'  => true,
		'fields'         => 'ids',
	] );

	header( 'Content-Type: application/xml; charset=UTF-8' );
	echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
	echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

	// Strona główna
	echo "\t<url>\n\t\t<loc>" . esc_url( home_url( '/' ) ) . "</loc>\n\t\t<changefreq>daily</changefreq>\n\t\t<priority>1.0</priority>\n\t</url>\n";

	foreach ( $query->posts as $post_id ) {
		$permalink = get_permalink( $post_id );
		$modified  = get_post_modified_time( 'Y-m-d', false, $post_id, false );
		if ( ! $permalink ) {
			continue;
		}
		echo "\t<url>\n";
		echo "\t\t<loc>" . esc_url( $permalink ) . "</loc>\n";
		echo "\t\t<lastmod>" . esc_html( $modified ) . "</lastmod>\n";
		echo "\t\t<changefreq>weekly</changefreq>\n";
		echo "\t\t<priority>0.8</priority>\n";
		echo "\t</url>\n";
	}

	echo '</urlset>';
	exit;
}

// Flush rewrite rules when sitemap option changes
add_action( 'update_option_wpo_generate_sitemap', 'wpo_flush_sitemap_rules', 10, 0 );
function wpo_flush_sitemap_rules() {
	flush_rewrite_rules( false );
}

// ─────────────────────────────────────────────
// AKTYWACJA / DEAKTYWACJA
// ─────────────────────────────────────────────
register_activation_hook( __FILE__, 'wpo_activation' );
function wpo_activation() {
	// Ustaw domyślne wartości tylko przy pierwszej instalacji
	if ( false === get_option( 'wpo_cache_time' ) ) {
		$defaults = [
			'wpo_minify_css'    => 1,
			'wpo_minify_js'     => 1,
			'wpo_lazy_loading'  => 1,
			'wpo_defer_js'      => 1,
			'wpo_cache_time'    => 3600,
			'wpo_enable_schema' => 1,
		];
		foreach ( $defaults as $key => $value ) {
			update_option( $key, $value );
		}
	}
	// Zarejestruj reguły przepisywania (dla sitemapy)
	wpo_register_sitemap_rewrite();
	flush_rewrite_rules( false );
}

register_deactivation_hook( __FILE__, 'wpo_deactivation' );
function wpo_deactivation() {
	// Oczyść cache przy deaktywacji
	wp_cache_flush();
	// Usuń reguły przepisywania wtyczki
	flush_rewrite_rules( false );
}
