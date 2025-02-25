# Vytrvalec Server

## Requirements:
- PHP 8.2
- NodeJS 21.2.0

## Prerequisties
- Install composer (ex. ```paru -S composer```)
- Install PHP required extensions - php-imagick, php-xsl, php-ffi, php-iconv
- Inside php.ini enable extensions:

       extension=ffi
       extension=gd
       extension=gettext
       extension=iconv
       extension=imagick
       extension=xsl
       extension=pdo_mysql (For MySQL database)
    
## How to setup
- ```cp .env .env.local```
- Fill .env.local with your local environment variables (database, jwt token, smtp, firebase token)
- ```composer install```
- ```npm ci```
- ```php bin/console doctrine:database:create```
- ```php bin/console doctrine:migrations:migrate```
- ```php bin/console doctrine:fixtures:load```

## Run in IntelliJ Idea 
To be done


