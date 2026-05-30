# Security Policy

## Obsługiwane wersje

| Wersja | Wsparcie bezpieczeństwa |
|--------|------------------------|
| 2.0.x  | ✅ Aktywne             |
| 1.0.x  | ❌ Zakończone          |

## Zgłaszanie luk bezpieczeństwa

Prosimy **nie** zgłaszać luk bezpieczeństwa publicznie przez GitHub Issues.

Wyślij raport na adres autora lub utwórz **prywatny** Security Advisory w repozytorium GitHub.

Raport powinien zawierać:
- Opis podatności
- Kroki do odtworzenia
- Potencjalny wpływ
- (opcjonalnie) Propozycję naprawy

## Wdrożone zabezpieczenia

- Wszystkie dane `$_POST` są sanitizowane przez `sanitize_text_field()` / `sanitize_textarea_field()` oraz `wp_unslash()`
- Formularze chronione przez nonce WordPress (`wp_nonce_field`, `wp_verify_nonce`)
- Zapytania SQL wykonywane przez `$wpdb->prepare()`
- Klucze API przechowywane w bazie WP options; wyświetlane wyłącznie w zamaskowanej formie
- Walidacja whitelist dla pola wyboru dostawcy AI
- Dostęp do panelu ograniczony do `manage_options`
- Zabezpieczenie przed bezpośrednim dostępem (`ABSPATH` check)
