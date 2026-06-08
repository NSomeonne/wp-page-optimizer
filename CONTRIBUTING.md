# 🤝 Wytyczne dla współtwórców

Dziękujemy za zainteresowanie projektem wp-page-optimizer! Poniżej znajdziesz wszystko, co musisz wiedzieć, aby zacząć wspierać ten projekt.

## 📋 Kod postępowania

Wszyscy współtwórcy zobowiązują się do:
- 👥 Traktowania wszystkich z szacunkiem i godnie
- 🤐 Niedyskryminowania ze względu na pochodzenie, płeć, orientację, religię czy inne cechy
- 📌 Skupiania się na merytorycznym dyskusje
- 🙏 Konstruktywnego udzielania i przyjmowania feedbacku

## 🚀 Jak zacząć?

### 1. Przygotuj środowisko

```bash
# Skopiuj repozytorium
git clone https://github.com/NSomeonne/wp-page-optimizer.git
cd wp-page-optimizer

# Otwórz w Dev Container (VS Code)
# lub uruchom Docker Compose samodzielnie
docker-compose up -d
```

### 2. Zainstaluj zależności

```bash
composer install
```

### 3. Uruchom testy

```bash
composer test
composer lint
```

## 🎯 Proces pracy

### Dla małych zmian (dokumentacja, małe poprawki)

1. **Fork** repozytorium
2. **Utwórz branch** z opisową nazwą:
   ```bash
   git checkout -b fix/nazwa-poprawki
   # lub
   git checkout -b feature/nazwa-funkcji
   ```
3. **Dokonaj zmian** i commituj z jasnymi wiadomościami
4. **Push** do swojego forka
5. **Utwórz Pull Request** do głównego repozytorium

### Dla dużych zmian lub nowych funkcji

1. **Otwórz Issue** z etykietą `enhancement`
   - Opisz co chcesz dodać i dlaczego
   - Czekaj na feedback
   - Dyskusja pomoże upewnić się, że kierunek się zgadza

2. **Po zatwierdzeniu koncepcji:**
   - Postępuj jak wyżej (fork → branch → PR)
   - Odwołuj się do Issue w opisie PR

## 📝 Commits i Pull Requesty

### Wiadomości commitu

Używamy konwencji Conventional Commits:

```
type(scope): subject

body

footer
```

**Typy:**
- `feat:` - nowa funkcja
- `fix:` - naprawa błędu
- `docs:` - zmiany dokumentacji
- `test:` - dodanie/zmiana testów
- `refactor:` - zmiana kodu bez nowych funkcji
- `style:` - formatowanie, brakujące semicolony itp.
- `ci:` - zmiany CI/CD

**Przykłady:**
```
feat(api): add copilot integration endpoints
fix(security): prevent XSS in content editor
docs: update installation instructions
test: add unit tests for optimizer class
```

### Pull Request

1. **Tytuł PR:**
   - Jasny, opisowy
   - Zgodny z konwencją Conventional Commits
   - Przykład: `feat: add AI content suggestions feature`

2. **Opis PR:**
   ```markdown
   ## Opis zmian
   Krótko opisz co robisz

   ## Linked Issue
   Closes #123

   ## Typ zmiany
   - [ ] Bug fix
   - [x] New feature
   - [ ] Documentation update

   ## Checklist
   - [x] Kod został otestowany lokalnie
   - [x] Dodane testy dla nowych features
   - [x] PHPStan nie wykazuje błędów
   - [x] README zaktualizowany (jeśli potrzebne)
   ```

3. **Czekaj na review:**
   - Mogą pojawić się pytania lub sugestie
   - Odpowiadaj konstruktywnie
   - Dokonaj zmian jeśli są wskazane

## 🧪 Testowanie

Wszystkie nowe features **muszą** mieć testy.

### Uruchamianie testów

```bash
# Wszystkie testy
composer test

# Tylko testy z konkretnym frazą w nazwie
composer test -- --filter testName

# Coverage report
composer test -- --coverage-html coverage
```

### Jakość kodu

```bash
# PHPStan - statyczna analiza kodu
composer lint

# Powinno zakończyć się bez błędów!
```

### Przed wysłaniem PR

```bash
# Upewnij się, że wszystko przechodzi:
composer test && composer lint
```

## 📚 Struktura kodu

### Organizacja plików

```
wp-page-optimizer/
├── src/                   # Główny kod wtyczki
│   ├── Admin/            # Strony administracyjne
│   ├── Public/           # Funkcje frontendowe
│   └── Core/             # Logika podstawowa
├── tests/                # Testy PHPUnit
├── plugin-file.php       # Główny plik wtyczki
└── composer.json
```

### Standardy kodowania

- **Indentation**: 4 spacje
- **Coding Standard**: [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/wordpress-coding-standards/)
- **Naming**: camelCase dla funkcji/zmiennych, snake_case dla akcji WordPress

## 🐛 Zgłaszanie błędów

### Przed zgłoszeniem

- ✅ Sprawdź czy bug nie został już zgłoszony
- ✅ Czytaj najnowszą dokumentację
- ✅ Spróbuj reprodukować błąd na czystej instalacji

### Jak zgłosić

1. **Tytuł:**
   ```
   [BUG] Krótki opis problemu
   ```

2. **Opis:**
   ```markdown
   ## Opis
   Czym jest problem?

   ## Kroki do reprodukcji
   1. Zrób to...
   2. Potem to...
   3. Zaobserwuj to...

   ## Oczekiwane zachowanie
   Co powinno się stać?

   ## Rzeczywiste zachowanie
   Co się stało?

   ## Środowisko
   - PHP version: 8.1
   - WordPress version: 6.0
   - System: Ubuntu 22.04
   ```

## 📖 Dokumentacja

Dla nowych funkcji lub zmian w API:

1. **PHPDoc komentarze** - dla wszystkich public methods
2. **README.md** - jeśli zmienia się instalacja/setup
3. **Inline comments** - dla skomplikowanej logiki

Przykład PHPDoc:

```php
/**
 * Optymalizuje zawartość strony za pomocą AI
 *
 * @param int   $post_id    ID posta do optymalizacji
 * @param array $options    Opcje optymalizacji
 *
 * @return string|WP_Error Zoptymalizowana zawartość lub błąd
 *
 * @since 1.0.0
 */
public function optimize_content( $post_id, $options = [] ) {
    // ...
}
```

## 🚀 Releases

- Trzymamy się [Semantic Versioning](https://semver.org/)
- Format: `MAJOR.MINOR.PATCH`
- Release notes zawierają zmiany dla użytkowników

## 📞 Pytania?

- 📌 Sprawdź [Issues](https://github.com/NSomeonne/wp-page-optimizer/issues)
- 💬 Piszemy w [Discussions](https://github.com/NSomeonne/wp-page-optimizer/discussions)

## ⭐ Dziękujemy!

Każdy pull request, issue czy sugestia naprawdę nam pomaga. Dziękujemy za bycie częścią tego projektu!

---

**Happy coding! 🎉**
