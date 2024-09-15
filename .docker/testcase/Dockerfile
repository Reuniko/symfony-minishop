FROM php:8.2.20-apache

COPY --chmod=777 ./entrypoint.sh /var/www/html/entrypoint.sh

RUN apt-get update && apt-get install -y git libzip-dev && docker-php-ext-configure zip

COPY ./config.conf /etc/apache2/sites-enabled/000-default.conf
RUN a2enmod rewrite && \
 service apache2 restart


RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer --version \
    && git config --global user.email "stub@email.com" \
    && git config --global user.name "Stub User"
