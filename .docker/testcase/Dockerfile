FROM php:8.2.20-apache

COPY --chmod=777 ./entrypoint.sh /entrypoint.sh
WORKDIR /var/www/html

RUN apt-get update && apt-get install -y git libzip-dev libpq-dev && \
  docker-php-ext-install pdo_pgsql pgsql && \
  docker-php-ext-configure zip

COPY ./config.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite && \
 service apache2 restart


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version \
    && git config --global user.email "stub@email.com" \
    && git config --global user.name "Stub User"
