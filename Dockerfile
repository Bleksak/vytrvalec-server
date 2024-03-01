FROM php:8.2.14-fpm

RUN apt-get update -y \
    && apt-get install -y nginx libmagickwand-dev libicu-dev libxslt-dev libpng-dev sendmail zlib1g-dev libzip-dev libonig-dev curl

# PHP_CPPFLAGS are used by the docker-php-ext-* scripts
ENV PHP_CPPFLAGS="$PHP_CPPFLAGS -std=c++11"
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

RUN bash -c '[[ -n "$(pecl list | grep imagick)" ]]\
 || (pecl install imagick && docker-php-ext-enable imagick)'

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-install opcache \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd \
    && docker-php-ext-install intl \
    && docker-php-ext-install zip \
    && docker-php-ext-install mbstring \
    && docker-php-ext-install ffi \
    && docker-php-ext-install xsl \
    && rm -rf /var/lib/apt/lists/*
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/php-opocache-cfg.ini


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN curl -sL https://deb.nodesource.com/setup_21.x | bash - \
    && apt-get install -y nodejs

WORKDIR /app
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN npm install && npm run build
RUN composer install --no-interaction && composer dump

RUN { \
        echo 'APP_ENV=prod'; \
        echo 'APP_DEBUG=false'; \
        echo 'APP_SECRET=$APP_SECRET'; \
        echo 'JWT_SECRET=$JWT_SECRET'; \
        echo 'FIREBASE_DSN=firebase://$FIREBASE_EMAIL:$FIREBASE_PASSWORD+@default'; \
        echo 'APP_URL=https://vytrvalec.kts.zcu.cz/api'; \
        echo 'CLIENT_URL=https://vytrvalec.kts.zcu.cz'; \
        echo 'DATABASE_URL="mysql://db:db@db/db?serverVersion=8.3.0&charset=utf8"'; \
        echo 'MAILER_DSN=smtp://$SMTP_USER:$SMTP_PASSWORD@$SMTP_HOST:$SMTP_PORT'; \
        echo 'CORS_ALLOW_ORIGIN="vytrvalec.kts.zcu.cz"'; \
        echo 'MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0'; \
    } > .env.local
