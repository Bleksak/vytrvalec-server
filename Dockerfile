FROM php:8.2.28-fpm-alpine3.21

RUN apk update
RUN apk add nodejs~22 npm imagemagick-dev icu-dev libxslt-dev libpng-dev ssmtp zlib-dev libzip-dev oniguruma-dev curl supervisor rabbitmq-c-dev autoconf gcc g++ make jpeg-dev freetype-dev libffi-dev

RUN pecl install imagick && docker-php-ext-enable imagick

RUN docker-php-ext-install pdo_mysql \
  && docker-php-ext-configure intl \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) opcache gd intl zip mbstring ffi xsl \
  && pecl install amqp \
  && docker-php-ext-enable amqp

RUN { \
  echo 'opcache.memory_consumption=128'; \
  echo 'opcache.interned_strings_buffer=8'; \
  echo 'opcache.max_accelerated_files=4000'; \
  echo 'opcache.revalidate_freq=2'; \
  echo 'opcache.fast_shutdown=1'; \
  echo 'opcache.enable_cli=1'; \
  } > /usr/local/etc/php/conf.d/php-opocache-cfg.ini

# RUN { \
#   echo 'APP_ENV=prod'; \
#   echo 'APP_DEBUG=false'; \
#   echo 'APP_SECRET=${APP_SECRET}'; \
#   echo 'JWT_SECRET=${JWT_SECRET}'; \
#   echo 'APP_BASE_URL=https://vytrvalec.kts.zcu.cz'; \
#   echo 'APP_URL=https://vytrvalec.kts.zcu.cz/api'; \
#   echo 'CLIENT_URL=https://vytrvalec.kts.zcu.cz'; \
#   echo 'DATABASE_URL="mysql://db:db@db/db?serverVersion=8.3.0&charset=utf8"'; \
#   echo 'MAILER_DSN=smtp://${SMTP_USER}:${SMTP_PASSWORD}@${SMTP_HOST}:${SMTP_PORT}'; \
#   echo 'CORS_ALLOW_ORIGIN="vytrvalec.kts.zcu.cz"'; \
#   echo 'MESSENGER_TRANSPORT_DSN=amqp://guest:guest@vytrvalec-rmq:5672/%2f/messages'; \
#   } > .env

RUN mkdir -p /etc/supervisor.d/
COPY docker/supervisor.ini /etc/supervisor.d/jobs.ini

RUN rm -rf /var/lib/apt/lists/*

RUN mv /usr/local/etc/php/php.ini-production /usr/local/etc/php/php.ini

RUN {\
  echo 'memory_limit = 256M'; \
  echo 'upload_max_filesize = 100M'; \
  echo 'post_max_size = 100M'; \
  echo 'date.timezone = "Europe/Prague"'; \
  } > /usr/local/etc/php/conf.d/memory_limit.ini


RUN curl -sS https://getcomposer.org/installer | php -- \
  --install-dir=/usr/bin --filename=composer && chmod +x /usr/bin/composer

WORKDIR /app
COPY . .

RUN npm install && npm run build
RUN composer install --no-interaction && composer dump

COPY docker/entrypoint.sh /entrypoint.sh
CMD ["sh", "/entrypoint.sh"]
