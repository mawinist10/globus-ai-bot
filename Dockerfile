FROM php:8.1-cli
COPY . /app
WORKDIR /app
RUN apt-get update && apt-get install -y libzip-dev unzip && docker-php-ext-install zip
CMD ["php", "index.php"]