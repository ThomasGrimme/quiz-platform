FROM php:8.1-apache

RUN apt-get update && \
    docker-php-ext-install mysqli pdo pdo_mysql

# Point DocumentRoot to /var/www/html/php so /index.php -> app/php/index.php
# Add aliases so /css/* and /js/* resolve to app/css/ and app/js/
RUN sed -i \
    's!DocumentRoot /var/www/html!DocumentRoot /var/www/html/php!' \
    /etc/apache2/sites-available/000-default.conf && \
    sed -i \
    '/<\/VirtualHost>/i\
    Alias /css /var/www/html/css\n\
    Alias /js /var/www/html/js\n\
    <Directory /var/www/html/css>\n\
        Require all granted\n\
    </Directory>\n\
    <Directory /var/www/html/js>\n\
        Require all granted\n\
    </Directory>' \
    /etc/apache2/sites-available/000-default.conf