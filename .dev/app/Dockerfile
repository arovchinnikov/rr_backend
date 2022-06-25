FROM spiralscout/roadrunner:2.8.5 as rr

FROM php:8.1-cli-alpine

RUN apk update && apk add --no-cache unzip bash autoconf g++ make yaml-dev libzip-dev

RUN docker-php-ext-install zip sockets

RUN pecl install yaml-2.2.2 && docker-php-ext-enable yaml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY --from=rr /usr/bin/rr /usr/bin/rr
COPY --from=rr /etc/rr.yaml /etc/rr.yaml

WORKDIR /app

CMD ["/usr/bin/rr", "serve", "-d", "-c", "/etc/rr.yaml"]
