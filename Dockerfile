FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends libpq-dev libcurl4-openssl-dev \
    && docker-php-ext-install pdo_pgsql curl \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY . /var/www/html/
COPY docker/apache-site.conf /etc/apache2/sites-available/000-default.conf
COPY docker/uploads.ini /usr/local/etc/php/conf.d/uploads.ini
COPY docker/start.sh /usr/local/bin/start-app
RUN chmod +x /usr/local/bin/start-app \
    && mkdir -p /var/www/html/uploads/contents /var/www/html/uploads/profile /var/lib/php/sessions \
    && chown -R www-data:www-data /var/www/html/uploads /var/lib/php/sessions

ENV PORT=10000
EXPOSE 10000
CMD ["start-app"]
