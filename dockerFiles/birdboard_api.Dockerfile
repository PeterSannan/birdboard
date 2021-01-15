FROM php:7.4-fpm-alpine

WORKDIR /var/www/birdboard

RUN docker-php-ext-install pdo pdo_mysql
 
RUN docker-php-ext-install sockets
RUN apk add libzip-dev
RUN apk add libsodium-dev
RUN apk add git
RUN apk add curl 
RUN apk add libpng-dev 
RUN apk add libxml2-dev  
RUN apk add libmcrypt-dev 

RUN docker-php-ext-install zip sodium
RUN curl -sS https://getcomposer.org/installer | php -- \
     --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install gd 