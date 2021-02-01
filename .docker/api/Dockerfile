FROM php:7.4-fpm

RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        supervisor \
        nginx \
        postgresql-server-dev-11 \
        libcurl4-gnutls-dev \
        curl && \
    rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install -j$(nproc) \
    pdo_pgsql \
    pdo \
    curl \
    json

COPY ./.docker/api/php.ini /usr/local/etc/php/php.ini
COPY ./.docker/api/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY ./.docker/api/nginx.conf /etc/nginx/nginx.conf
COPY ./src /opt/src/
COPY ./bin /opt/bin/

EXPOSE 80
WORKDIR /opt
CMD ["/usr/bin/supervisord"]
