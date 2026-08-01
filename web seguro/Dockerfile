FROM php:8.2-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev openssl \
    && docker-php-ext-install pdo_pgsql pgsql \
    && a2enmod ssl headers rewrite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

COPY . /var/www/html/
COPY docker/apache-ssl.conf /etc/apache2/sites-available/secure-vhost.conf

RUN mkdir -p /etc/apache2/ssl /var/www/html/storage/uploads \
    && openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
        -keyout /etc/apache2/ssl/secureshop.key \
        -out /etc/apache2/ssl/secureshop.crt \
        -subj "/C=PE/ST=Local/L=Lab/O=SecureShop/OU=Training/CN=localhost" \
        -addext "subjectAltName=DNS:localhost,IP:127.0.0.1" \
    && a2dissite 000-default.conf \
    && a2ensite secure-vhost.conf \
    && chown -R www-data:www-data /var/www/html/storage

EXPOSE 80 443
