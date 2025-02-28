FROM php:8.2.14-fpm

ENV UPLOAD_LIMIT=100M
ENV PHP_MEMORY_LIMIT=256M

RUN curl -sL https://deb.nodesource.com/setup_21.x | bash -
RUN apt-get update -y \
    && apt-get install -y nginx libmagickwand-dev libicu-dev libxslt-dev libpng-dev sendmail zlib1g-dev libzip-dev libonig-dev curl nodejs supervisor

# PHP_CPPFLAGS are used by the docker-php-ext-* scripts
ENV PHP_CPPFLAGS="$PHP_CPPFLAGS -std=c++11"
SHELL ["/bin/bash", "-o", "pipefail", "-c"]

RUN bash -c '[[ -n "$(pecl list | grep imagick)" ]]\
 || (pecl install imagick && docker-php-ext-enable imagick)'

RUN docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure intl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) opcache gd intl zip mbstring ffi xsl
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=4000'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/php-opocache-cfg.ini


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN npm install && npm run build
RUN composer install --no-interaction --no-dev && composer dump

RUN { \
        echo 'APP_ENV=prod'; \
        echo 'APP_DEBUG=false'; \
        echo 'APP_SECRET=${APP_SECRET}'; \
        echo 'JWT_SECRET=${JWT_SECRET}'; \
        echo 'FIREBASE_DSN=firebase://${FIREBASE_EMAIL}:${FIREBASE_PASSWORD}+@default'; \
        echo 'APP_BASE_URL=https://vytrvalec.kts.zcu.cz'; \
        echo 'APP_URL=https://vytrvalec.kts.zcu.cz/api'; \
        echo 'CLIENT_URL=https://vytrvalec.kts.zcu.cz'; \
        echo 'DATABASE_URL="mysql://db:db@db/db?serverVersion=8.3.0&charset=utf8"'; \
        echo 'MAILER_DSN=smtp://${SMTP_USER}:${SMTP_PASSWORD}@${SMTP_HOST}:${SMTP_PORT}'; \
        echo 'CORS_ALLOW_ORIGIN="vytrvalec.kts.zcu.cz"'; \
        echo 'MESSENGER_TRANSPORT_DSN=doctrine://default?auto_setup=0'; \
    } > .env.local

RUN {\
        echo '[program:queue]'; \
        echo 'command=php bin/console messenger:consume async'; \
        echo 'autostart=true'; \
        echo 'autorestart=true'; \
        echo 'stderr_logfile=/var/log/queue.err.log'; \
        echo 'stdout_logfile=/var/log/queue.out.log'; \
    } > /etc/supervisor/conf.d/queue.conf

RUN rm -rf /var/lib/apt/lists/*

RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN {\
    echo 'memory_limit = 256M'; \
    echo 'upload_max_filesize = 100M'; \
    echo 'post_max_size = 100M'; \
    echo 'date.timezone = "Europe/Prague"'; \
} > /usr/local/etc/php/conf.d/memory_limit.ini
