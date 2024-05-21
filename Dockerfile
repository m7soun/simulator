FROM php:8.2-fpm-alpine

WORKDIR /var/www/app

RUN apk --no-cache add \
    nginx \
    supervisor \
    curl \
    && rm -rf /var/cache/apk/*

RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install opcache

RUN docker-php-ext-install pcntl

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY . /var/www/app
COPY ./.docker/fastcgi-php.conf /etc/nginx/snippets/fastcgi-php.conf
COPY ./.docker/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf
COPY ./.docker/nginx.conf /etc/nginx/nginx.conf
COPY ./.docker/nginx-server-block.conf /etc/nginx/conf.d/default.conf
COPY ./.docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chgrp -R www-data storage bootstrap/cache
RUN chmod -R ug+rwx storage bootstrap/cache

RUN composer clearcache
RUN composer install --no-interaction --no-dev
RUN composer dump-autoload --optimize

EXPOSE 80

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]




