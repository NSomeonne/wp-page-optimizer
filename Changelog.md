# Changelog – Page Optimizer Pro

## 2.0.2 (2026-05-30) – Bugfix release

### Naprawione błędy
- **SEO Title Tag** – opcja `wpo_seo_title` podpięta do filtra `document_title_parts`; tytuł jest teraz faktycznie stosowany na stronie.
- **Async CSS (`wpo_async_css_tags`)** – naprawiono błąd podwójnego przetwarzania tagu (jeśli filtr był wywoływany wielokrotnie, `onload` był zagnieżdżany rekurencyjnie); `<noscript>` jest teraz budowany z oryginalnego, niezmienionego tagu.
- **Gemini API** – system prompt przeniesiony do pola `systemInstruction` zgodnie ze specyfikacją Gemini 1.5; poprzednia implementacja wysyłała go jako element `parts`, co powodowało błędy lub ignorowanie instrukcji przez model.
- **Cohere API v2** – poprawiona obsługa odpowiedzi (fallback na pole `text` obok `message.content[0].text`).
- **Diagnostyka DB** – `wpo_get_db_size()` zwraca teraz `float` zamiast string `"X MB"`; dodana osobna funkcja `wpo_format_db_size()`; usunięte kruche rzutowanie `(float)"X MB"`.
- **Brakujące pliki modułów** – dodane: `includes/performance.php` (DNS prefetch), `includes/seo-settings.php` (Open Graph / Twitter Card), `includes/site-repair.php` (diagnostyka tabel DB).

## 2.0.1 (2026-05-28)

- Dodano obsługę Cohere jako dostawcy AI
- Poprawki bezpieczeństwa (nonce na wszystkich formularzach)
- Asynchroniczny CSS z fallbackiem `<noscript>`

## 2.0.0 (2026-05-20)

- Pełny przepisanie pluginu
- Integracja AI: OpenAI, Claude, Gemini
- Panel SEO, Wydajność, Naprawa
- Defer JS z ochroną jQuery
