<?php
/**
 * Moduł integracji AI – Page Optimizer Pro
 *
 * Obsługuje wywołania do: OpenAI (GPT-4o), Anthropic (Claude), Google (Gemini), Cohere.
 * Każdy provider implementuje ten sam interfejs: wpo_ai_request( string $prompt ): string|WP_Error
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ─────────────────────────────────────────────
// GŁÓWNA FUNKCJA – wysyła zapytanie do aktywnego providera
// ─────────────────────────────────────────────

/**
 * Wysyła prompt do aktywnego dostawcy AI.
 *
 * @param  string $prompt   Treść zapytania.
 * @param  int    $max_tokens Maksymalna liczba tokenów odpowiedzi (domyślnie 1024).
 * @return string|WP_Error  Treść odpowiedzi lub obiekt błędu.
 */
function wpo_ai_request( string $prompt, int $max_tokens = 1024 ) {
	$provider = get_option( 'wpo_ai_provider', 'none' );
	$api_key  = get_option( 'wpo_ai_api_key', '' );

	if ( 'none' === $provider || empty( $api_key ) ) {
		return new WP_Error( 'wpo_ai_no_config', __( 'Brak skonfigurowanego dostawcy AI lub klucza API.', 'wp-page-optimizer' ) );
	}

	switch ( $provider ) {
		case 'openai':
			return wpo_ai_openai( $prompt, $api_key, $max_tokens );
		case 'claude':
			return wpo_ai_claude( $prompt, $api_key, $max_tokens );
		case 'google':
			return wpo_ai_gemini( $prompt, $api_key, $max_tokens );
		case 'cohere':
			return wpo_ai_cohere( $prompt, $api_key, $max_tokens );
		default:
			return new WP_Error( 'wpo_ai_unknown_provider', sprintf( __( 'Nieznany dostawca AI: %s', 'wp-page-optimizer' ), esc_html( $provider ) ) );
	}
}

// ─────────────────────────────────────────────
// HELPER: wspólna obsługa odpowiedzi wp_remote_post
// ─────────────────────────────────────────────

/**
 * Wykonuje żądanie HTTP POST i zwraca zdekodowane ciało JSON lub WP_Error.
 *
 * @param  string $url     Endpoint API.
 * @param  array  $headers Nagłówki HTTP.
 * @param  array  $body    Dane do wysłania (zostaną zakodowane do JSON).
 * @return array|WP_Error  Zdekodowana tablica lub błąd.
 */
function wpo_ai_http_post( string $url, array $headers, array $body ) {
	$response = wp_remote_post(
		$url,
		[
			'headers' => array_merge( [ 'Content-Type' => 'application/json' ], $headers ),
			'body'    => wp_json_encode( $body ),
			'timeout' => 30,
		]
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$code = wp_remote_retrieve_response_code( $response );
	$raw  = wp_remote_retrieve_body( $response );
	$data = json_decode( $raw, true );

	if ( $code < 200 || $code >= 300 ) {
		$message = isset( $data['error']['message'] )
			? $data['error']['message']
			: ( isset( $data['message'] ) ? $data['message'] : "HTTP {$code}" );
		return new WP_Error( 'wpo_ai_http_error', $message, [ 'status' => $code ] );
	}

	if ( json_last_error() !== JSON_ERROR_NONE ) {
		return new WP_Error( 'wpo_ai_json_error', __( 'Nieprawidłowa odpowiedź JSON od API.', 'wp-page-optimizer' ) );
	}

	return $data;
}

// ─────────────────────────────────────────────
// PROVIDER: OpenAI
// ─────────────────────────────────────────────

/**
 * Wywołuje OpenAI Chat Completions API (model gpt-4o-mini).
 */
function wpo_ai_openai( string $prompt, string $api_key, int $max_tokens ): string|WP_Error {
	$data = wpo_ai_http_post(
		'https://api.openai.com/v1/chat/completions',
		[ 'Authorization' => 'Bearer ' . $api_key ],
		[
			'model'      => 'gpt-4o-mini',
			'max_tokens' => $max_tokens,
			'messages'   => [
				[ 'role' => 'system', 'content' => 'Jesteś pomocnym asystentem SEO i copywritingu dla WordPressa.' ],
				[ 'role' => 'user',   'content' => $prompt ],
			],
		]
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$text = $data['choices'][0]['message']['content'] ?? null;
	if ( null === $text ) {
		return new WP_Error( 'wpo_ai_parse', __( 'Nie udało się odczytać odpowiedzi OpenAI.', 'wp-page-optimizer' ) );
	}

	return trim( $text );
}

// ─────────────────────────────────────────────
// PROVIDER: Anthropic (Claude)
// ─────────────────────────────────────────────

/**
 * Wywołuje Anthropic Messages API (model claude-haiku-4-5).
 */
function wpo_ai_claude( string $prompt, string $api_key, int $max_tokens ): string|WP_Error {
	$data = wpo_ai_http_post(
		'https://api.anthropic.com/v1/messages',
		[
			'x-api-key'         => $api_key,
			'anthropic-version' => '2023-06-01',
		],
		[
			'model'      => 'claude-haiku-4-5-20251001',
			'max_tokens' => $max_tokens,
			'system'     => 'Jesteś pomocnym asystentem SEO i copywritingu dla WordPressa.',
			'messages'   => [
				[ 'role' => 'user', 'content' => $prompt ],
			],
		]
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$text = $data['content'][0]['text'] ?? null;
	if ( null === $text ) {
		return new WP_Error( 'wpo_ai_parse', __( 'Nie udało się odczytać odpowiedzi Claude.', 'wp-page-optimizer' ) );
	}

	return trim( $text );
}

// ─────────────────────────────────────────────
// PROVIDER: Google Gemini
// ─────────────────────────────────────────────

/**
 * Wywołuje Google Gemini API (model gemini-1.5-flash).
 */
function wpo_ai_gemini( string $prompt, string $api_key, int $max_tokens ): string|WP_Error {
	$url = add_query_arg(
		'key',
		$api_key,
		'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent'
	);

	$data = wpo_ai_http_post(
		$url,
		[],
		[
			'systemInstruction' => [
				'parts' => [ [ 'text' => 'Jesteś pomocnym asystentem SEO i copywritingu dla WordPressa.' ] ],
			],
			'contents'         => [
				[
					'parts' => [ [ 'text' => $prompt ] ],
				],
			],
			'generationConfig' => [ 'maxOutputTokens' => $max_tokens ],
		]
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
	if ( null === $text ) {
		return new WP_Error( 'wpo_ai_parse', __( 'Nie udało się odczytać odpowiedzi Gemini.', 'wp-page-optimizer' ) );
	}

	return trim( $text );
}

// ─────────────────────────────────────────────
// PROVIDER: Cohere
// ─────────────────────────────────────────────

/**
 * Wywołuje Cohere Chat API (model command-r).
 */
function wpo_ai_cohere( string $prompt, string $api_key, int $max_tokens ): string|WP_Error {
	$data = wpo_ai_http_post(
		'https://api.cohere.com/v2/chat',
		[ 'Authorization' => 'Bearer ' . $api_key ],
		[
			'model'      => 'command-r',
			'max_tokens' => $max_tokens,
			'messages'   => [
				[ 'role' => 'system', 'content' => 'Jesteś pomocnym asystentem SEO i copywritingu dla WordPressa.' ],
				[ 'role' => 'user',   'content' => $prompt ],
			],
		]
	);

	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$text = $data['message']['content'][0]['text'] ?? ( $data['text'] ?? null );
	if ( null === $text ) {
		return new WP_Error( 'wpo_ai_parse', __( 'Nie udało się odczytać odpowiedzi Cohere.', 'wp-page-optimizer' ) );
	}

	return trim( $text );
}

// ─────────────────────────────────────────────
// GOTOWE SKRÓTY – pomocnicze funkcje wyższego poziomu
// ─────────────────────────────────────────────

/**
 * Generuje meta description dla podanego tytułu lub treści.
 *
 * @param  string $content Tytuł lub fragment tekstu.
 * @return string|WP_Error
 */
function wpo_ai_generate_meta_description( string $content ): string|WP_Error {
	$prompt = sprintf(
		'Napisz zwięzłą meta description (max 155 znaków) dla strony o tytule/treści: "%s". Odpowiedz TYLKO samą meta description, bez cudzysłowów.',
		wp_strip_all_tags( $content )
	);
	return wpo_ai_request( $prompt, 200 );
}

/**
 * Sugeruje słowa kluczowe dla podanego tekstu.
 *
 * @param  string $content Treść strony lub wpisu.
 * @return string|WP_Error Słowa kluczowe oddzielone przecinkami.
 */
function wpo_ai_suggest_keywords( string $content ): string|WP_Error {
	$prompt = sprintf(
		'Na podstawie poniższego tekstu zaproponuj 5–8 słów kluczowych SEO, oddzielonych przecinkami. Odpowiedz TYLKO listą słów kluczowych. Tekst: "%s"',
		wp_strip_all_tags( substr( $content, 0, 1500 ) )
	);
	return wpo_ai_request( $prompt, 150 );
}

/**
 * Generuje krótkie wprowadzenie do wpisu na podstawie tytułu.
 *
 * @param  string $title Tytuł wpisu.
 * @return string|WP_Error
 */
function wpo_ai_generate_intro( string $title ): string|WP_Error {
	$prompt = sprintf(
		'Napisz angażujące wprowadzenie (2–3 zdania) do artykułu o tytule: "%s". Styl: profesjonalny, przyjazny dla czytelnika.',
		sanitize_text_field( $title )
	);
	return wpo_ai_request( $prompt, 300 );
}

// ─────────────────────────────────────────────
// AJAX – test połączenia z API (wywoływany ze strony admina)
// ─────────────────────────────────────────────

add_action( 'wp_ajax_wpo_test_ai', 'wpo_ajax_test_ai' );
function wpo_ajax_test_ai() {
	check_ajax_referer( 'wpo_ai_test_nonce', 'nonce' );

	if ( ! current_user_can( 'manage_options' ) ) {
		wp_send_json_error( [ 'message' => 'Brak uprawnień.' ], 403 );
		wp_die();
	}

	$result = wpo_ai_request( 'Odpowiedz jednym zdaniem po polsku: "Połączenie działa poprawnie."', 100 );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		wp_die();
	}

	wp_send_json_success( [ 'message' => $result ] );
	wp_die();
}

// ─────────────────────────────────────────────
// AJAX – generowanie meta description dla posta (edytor WP)
// ─────────────────────────────────────────────

add_action( 'wp_ajax_wpo_generate_meta', 'wpo_ajax_generate_meta' );
function wpo_ajax_generate_meta() {
	check_ajax_referer( 'wpo_ai_meta_nonce', 'nonce' );

	if ( ! current_user_can( 'edit_posts' ) ) {
		wp_send_json_error( [ 'message' => 'Brak uprawnień.' ], 403 );
		wp_die();
	}

	$content = isset( $_POST['content'] ) ? sanitize_textarea_field( wp_unslash( $_POST['content'] ) ) : '';

	if ( empty( $content ) ) {
		wp_send_json_error( [ 'message' => 'Brak treści do analizy.' ] );
		wp_die();
	}

	$result = wpo_ai_generate_meta_description( $content );

	if ( is_wp_error( $result ) ) {
		wp_send_json_error( [ 'message' => $result->get_error_message() ] );
		wp_die();
	}

	wp_send_json_success( [ 'meta_description' => $result ] );
	wp_die();
}

// ─────────────────────────────────────────────
// STRONA ADMINA – zakładka AI (dodatkowy UI testowania)
// Renderowana przez wpo_render_ai_page() z głównego pliku,
// tutaj tylko enqueue skryptów i lokalizacja zmiennych JS.
// ─────────────────────────────────────────────

add_action( 'admin_enqueue_scripts', 'wpo_ai_enqueue_admin_scripts' );
function wpo_ai_enqueue_admin_scripts( $hook ) {
	// Ładuj tylko na stronach wtyczki
	if ( strpos( $hook, 'wpo-' ) === false && strpos( $hook, 'page_optimizer' ) === false ) {
		return;
	}

	wp_localize_script(
		'jquery',
		'wpoAI',
		[
			'ajaxUrl'       => admin_url( 'admin-ajax.php' ),
			'testNonce'     => wp_create_nonce( 'wpo_ai_test_nonce' ),
			'metaNonce'     => wp_create_nonce( 'wpo_ai_meta_nonce' ),
			'testingLabel'  => __( 'Testowanie…', 'wp-page-optimizer' ),
			'testBtnLabel'  => __( '🔌 Testuj połączenie', 'wp-page-optimizer' ),
		]
	);

	// Inline JS dla przycisku testowania – bez zewnętrznego pliku
	$inline_js = <<<'JS'
jQuery(function($) {
    $(document).on('click', '#wpo-test-ai-btn', function() {
        var $btn = $(this);
        var $result = $('#wpo-ai-test-result');
        $btn.prop('disabled', true).text(wpoAI.testingLabel);
        $result.html('').removeClass('notice-success notice-error').addClass('notice notice-info').show().text('Łączenie z API…');

        $.post(wpoAI.ajaxUrl, {
            action: 'wpo_test_ai',
            nonce:  wpoAI.testNonce
        })
        .done(function(res) {
            if (res.success) {
                $result.removeClass('notice-info notice-error').addClass('notice-success').html('✅ ' + $('<span>').text(res.data.message).html());
            } else {
                $result.removeClass('notice-info notice-success').addClass('notice-error').html('❌ ' + $('<span>').text(res.data.message).html());
            }
        })
        .fail(function() {
            $result.removeClass('notice-info notice-success').addClass('notice-error').html('❌ Błąd połączenia HTTP.');
        })
        .always(function() {
            $btn.prop('disabled', false).text(wpoAI.testBtnLabel);
        });
    });
});
JS;

	wp_add_inline_script( 'jquery', $inline_js );
}

// ─────────────────────────────────────────────
// HOOK: Dodaj przycisk "Testuj połączenie" do strony AI
// Podpina się pod wpo_render_ai_page() przez filtr
// ─────────────────────────────────────────────

add_action( 'wpo_after_ai_form', 'wpo_render_ai_test_section' );
function wpo_render_ai_test_section() {
	?>
	<div style="max-width:700px;background:#fff;padding:30px;border-radius:10px;box-shadow:0 1px 4px rgba(0,0,0,.12);margin-top:20px;">
		<h2>🔌 Test połączenia z API</h2>
		<p style="color:#666;">Upewnij się, że klucz API jest zapisany, a następnie kliknij przycisk.</p>
		<button type="button" id="wpo-test-ai-btn" class="button button-secondary" style="font-size:14px;">
			🔌 Testuj połączenie
		</button>
		<div id="wpo-ai-test-result" style="margin-top:16px;padding:12px;border-radius:6px;display:none;"></div>

		<hr style="margin:24px 0;" />

		<h3>⚡ Szybkie generowanie</h3>
		<p style="color:#666;">Wpisz tytuł lub fragment treści, a AI wygeneruje meta description.</p>
		<textarea id="wpo-ai-quick-content" rows="4"
			style="width:100%;max-width:600px;margin-bottom:10px;"
			placeholder="np. Jak wybrać najlepszy hosting WordPress?"></textarea>
		<br />
		<button type="button" id="wpo-generate-meta-btn" class="button button-primary">
			✨ Generuj meta description
		</button>
		<div id="wpo-ai-meta-result" style="margin-top:12px;padding:12px;background:#f0f6fc;border-left:4px solid #2271b1;border-radius:4px;display:none;"></div>
	</div>

	<script>
	jQuery(function($) {
	    $('#wpo-generate-meta-btn').on('click', function() {
	        var $btn = $(this);
	        var content = $('#wpo-ai-quick-content').val().trim();
	        var $result = $('#wpo-ai-meta-result');

	        if (!content) {
	            $result.show().html('⚠️ Wpisz treść do analizy.');
	            return;
	        }

	        $btn.prop('disabled', true).text('Generowanie…');
	        $result.show().html('Przetwarzanie przez AI…');

	        $.post(wpoAI.ajaxUrl, {
	            action:  'wpo_generate_meta',
	            nonce:   wpoAI.metaNonce,
	            content: content
	        })
	        .done(function(res) {
	            if (res.success) {
	                $result.html(
	                    '<strong>Meta description:</strong><br>' +
	                    $('<span>').text(res.data.meta_description).html() +
	                    '<br><small style="color:#666;">Znaki: ' + res.data.meta_description.length + '/155</small>'
	                );
	            } else {
	                $result.html('❌ ' + $('<span>').text(res.data.message).html());
	            }
	        })
	        .fail(function() {
	            $result.html('❌ Błąd połączenia HTTP.');
	        })
	        .always(function() {
	            $btn.prop('disabled', false).text('✨ Generuj meta description');
	        });
	    });
	});
	</script>
	<?php
}
