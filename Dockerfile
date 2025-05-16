FROM php:8.2-cli
RUN apt-get update && apt-get install -y libzip-dev zip unzip libpng-dev libjpeg-dev libfreetype6-dev git curl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && docker-php-ext-install gd zip
COPY . /var/www
WORKDIR /var/www
EXPOSE 8080
CMD php -S 0.0.0.0:${PORT:-8080} index.php