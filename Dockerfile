FROM node:alpine as js-build
COPY . /build

FROM registry.gitlab.com/codific/docker/php81:latest
COPY . /var/www
COPY --from=js-build /build/public/back/dependency /var/www/public/back/dependency
COPY --from=js-build /build/public/front/dependency /var/www/public/front/dependency
WORKDIR /var/www
RUN APP_ENV=prod APP_DEBUG=0 COMPOSER_MEMORY_LIMIT=-1 composer install --prefer-dist --no-ansi --no-dev --no-interaction --no-progress --no-scripts --no-suggest --optimize-autoloader
RUN rm -rf /root/.composer

COPY docker-build/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker-build/codific.ini /usr/local/etc/php/conf.d/codific.ini
COPY docker-build/cron /etc/cron.d/cron
RUN chmod 644 /etc/cron.d/cron