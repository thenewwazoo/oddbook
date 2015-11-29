FROM php:5.6-apache
# Install modules
RUN apt-get update && apt-get install -y \
        php5-mysql \
    && rm -rf /var/lib/apt/lists/* \
    && docker-php-ext-install mysql \
