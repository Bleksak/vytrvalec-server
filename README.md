# Vytrvalec Server

## Requirements:

PHP 8.2
NodeJS 21.2.0

## How to setup:

```cp .env .env.local```

Fill .env.local with your local environment variables (database, jwt token, smtp, firebase token)

```composer install```
```npm ci```

```php bin/console doctrine:database:create```
```php bin/console doctrine:migrations:migrate```
```php bin/console doctrine:fixtures:load```

