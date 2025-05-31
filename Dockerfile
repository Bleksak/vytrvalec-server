FROM bleksak/php-production:8.4.6

COPY docker/supervisor.ini /etc/supervisor.d/supervisor.ini

WORKDIR /app
COPY . .

RUN npm install && npm run build
RUN composer install --no-interaction && composer dump -o
