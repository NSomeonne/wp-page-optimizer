# 🚀 wp-page-optimizer

Pomysł na wtyczkę AI dla WordPressa z funkcjami Copilota. Projekt łączy moc sztucznej inteligencji z najpopularniejszym CMS-em na świecie.

## 🎯 O projekcie

**wp-page-optimizer** to innowacyjna wtyczka WordPress, która integruje możliwości AI (Copilot) do:
- 🤖 Automatycznej optymalizacji treści stron
- ✨ Asystowania w tworzeniu i edycji zawartości
- ⚡ Poprawy wydajności i SEO
- 💡 Generowania sugestii dla redaktorów

## 🌟 Dlaczego warto się zaangażować?

- 📚 Projekt jest na wczesnym etapie - **Twoje pomysły mogą zdefiniować jego przyszłość!**
- 🛠️ Nowoczesny stack: PHP + Docker + GitHub Actions
- 👥 Otwarta współpraca - wszystkie umiejętności mile widziane
- 🧪 Dobre praktyki: testy unitowe, analiza statyczna, CI/CD
- 🌍 Potencjał dla społeczności WordPress na całym świecie

## 🚀 Szybki start

### Wymagania
- Docker i Docker Compose
- VS Code z rozszerzeniem Dev Containers (opcjonalnie)
- Podstawowa wiedza o WordPress i PHP

### Uruchomienie lokalne

1. **Otwórz repozytorium w devcontainerze:**
   ```bash
   git clone https://github.com/NSomeonne/wp-page-optimizer.git
   cd wp-page-optimizer
   ```

2. **Devcontainer automatycznie uruchomi:**
   - Usługę `app` (PHP)
   - WordPress
   - MySQL
   - Wszystkie zależności via Composer

3. **Dostęp do WordPressa:**
   ```
   URL: http://localhost:8080
   Użytkownik: admin
   Hasło: admin
   ```

### Instalacja WordPressa (przy pierwszym uruchomieniu)

```bash
wp --path=wordpress --allow-root core download
wp --path=wordpress --allow-root config create \
  --dbname=wordpress \
  --dbuser=wordpress \
  --dbpass=wordpress \
  --dbhost=db:3306
wp --path=wordpress --allow-root core install \
  --url=http://localhost:8080 \
  --title="WPO Dev" \
  --admin_user=admin \
  --admin_password=admin \
  --admin_email=admin@example.com
```

## 📝 Testowanie i jakość kodu

```bash
# Uruchamianie testów jednostkowych
composer test

# Analiza statyczna kodu (PHPStan)
composer lint

# Oba jednocześnie
composer test && composer lint
```

## 🔄 CI/CD

Workflow GitHub Actions automatycznie:
- ✅ Uruchamia testy PHPUnit
- 📊 Sprawdza jakość kodu za pomocą PHPStan
- 📦 Tworzy paczkę ZIP gotową do dystrybucji

## 🤝 Jak się zaangażować?

Zapraszamy do udziału! Oto kilka sposobów, w jakie możesz pomóc:

### Dla początkujących
- 📖 Poprawa dokumentacji
- 🐛 Testowanie i zgłaszanie błędów
- 💬 Sugestie nowych funkcji

### Dla doświadczonych
- 🧬 Rozwój nowych features
- 🔒 Prace nad bezpieczeństwem
- 🚀 Optymalizacja wydajności
- ♿ Accessibility improvements

### Chcesz zacząć?
1. Przeczytaj [CONTRIBUTING.md](CONTRIBUTING.md)
2. Sprawdź [otwarte issues](https://github.com/NSomeonne/wp-page-optimizer/issues) - szukamy `good first issue`
3. Utwórz swój fork i wyślij pull request

## 📚 Struktura projektu

```
wp-page-optimizer/
├── README.md              # Ten plik
├── CONTRIBUTING.md        # Wytyczne dla współtwórców
├── CODE_OF_CONDUCT.md     # Kodeks postępowania
├── composer.json          # Zależności PHP
├── .devcontainer/         # Konfiguracja Dev Container
├── .github/               # Szablony i workflow
├── wp-content/plugins/    # Wtyczka WordPress
└── wordpress/             # Instalacja WordPressa (dev)
```

## 📋 Roadmap

- [ ] Podstawowe funkcje optymalizacji AI
- [ ] Integracja z GitHub Copilot API
- [ ] Panel administracyjny
- [ ] Dokumentacja API
- [ ] Testy end-to-end
- [ ] Wsparcie dla wielu języków

## 📜 Licencja

Projekt jest udostępniany na licencji Other. Szczegóły w pliku [LICENSE](LICENSE).

## 💬 Pytania i dyskusje?

- 📌 [Otwórz issue](https://github.com/NSomeonne/wp-page-optimizer/issues/new/choose) - pytania, sugestie, zgłoszenia błędów
- 💡 [Dyskusje](https://github.com/NSomeonne/wp-page-optimizer/discussions) - ogólne rozmowy

## 👨‍💻 Autor

Projekt stworzony przez [NSomeonne](https://github.com/NSomeonne)

---

⭐ **Podoba Ci się projekt? Daj nam gwiazdkę!** ⭐
