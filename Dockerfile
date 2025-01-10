FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y \
    zip libzip-dev unzip git

RUN docker-php-ext-install pdo pdo_mysql zip

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer global require --dev phpunit/phpunit ^11
RUN composer global require squizlabs/php_codesniffer --no-progress --no-suggest

ENV PATH=/root/.composer/vendor/bin:$PATH

COPY ./src /var/www/html/src
# RUN chown -R www-data:www-data /var/www/html

COPY config.sh /usr/local/bin/config.sh
RUN chmod +x /usr/local/bin/config.sh
# EXPOSE 80
ENTRYPOINT ["config.sh"]
# CMD ["apache2-foreground"]