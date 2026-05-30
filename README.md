# wp-page-optimizer
Pomysł na wtyczkę AI dla WordPressa z funkcjami Copilot

## Uruchomienie lokalne

1. Otwórz repozytorium w devcontainerze.
2. Devcontainer uruchomi usługę `app` oraz WordPress + MySQL z `docker-compose`.
3. WordPress będzie dostępny pod adresem: `http://localhost:8080`.
4. Plugin znajduje się w katalogu głównym repozytorium i jest zamontowany jako `wp-page-optimizer` w katalogu `wp-content/plugins`.

## WP-CLI

Po uruchomieniu devcontianera możesz zainstalować WordPress i użyć WP-CLI z katalogu repozytorium:

```bash
wp --path=wordpress --allow-root core download
wp --path=wordpress --allow-root config create --dbname=wordpress --dbuser=wordpress --dbpass=wordpress --dbhost=db:3306
wp --path=wordpress --allow-root core install --url=http://localhost:8080 --title="WPO Dev" --admin_user=admin --admin_password=admin --admin_email=admin@example.com
```

Plugin jest dostępny w katalogu `wp-content/plugins/wp-page-optimizer` w środowisku WordPress.

## Testy i lint

- `composer test` — uruchamia PHPUnit
- `composer lint` — uruchamia PHPStan

## CI

Workflow GitHub Actions uruchamia testy PHPUnit, PHPStan oraz tworzy paczkę ZIP pluginu.
