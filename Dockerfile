FROM bleksak/php-production:8.4.6

COPY docker/queue.ini /etc/supervisor.d/queue.ini

WORKDIR /app
COPY . .

RUN npm install && npm run build
RUN composer install --no-interaction && composer dump
