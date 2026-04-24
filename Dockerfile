FROM php:8.4-fpm-alpine3.20
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    imagemagick-dev \
    geos-dev \
    git
RUN apk add --no-cache imagemagick protoc geos nginx supervisor redis
RUN pecl install imagick protobuf redis && \
    git clone https://git.osgeo.org/gitea/geos/php-geos.git /usr/src/php/ext/geos && cd /usr/src/php/ext/geos && \
        	./autogen.sh && ./configure && make && \
        mv /usr/src/php/ext/geos/modules/geos.so /usr/local/lib/php/extensions/no-debug-non-zts-20240924/geos.so
RUN docker-php-ext-enable imagick geos protobuf redis
RUN apk del -f .build-deps && rm -rf /tmp/* /var/cache/apk/*
COPY docker/nginx /etc/nginx/http.d
COPY docker/supervisor /etc/supervisor.d
COPY docker/php/*.conf /usr/local/etc/php-fpm.d/
COPY --from=composer /usr/bin/composer /usr/bin/composer
WORKDIR /var/www
ADD . /var/www
RUN composer install && mkdir -p /var/run/php && mkdir -p /var/redis
RUN protoc --proto_path=./vendor/heymoon/vector-tile-data-provider/proto --php_out=./vendor/heymoon/vector-tile-data-provider/proto/gen ./vendor/heymoon/vector-tile-data-provider/proto/vector_tile.proto
ENV PHPFPM_MAX_CHILDREN=100
ENV PHPFPM_START_SERVERS=50
ENV PHPFPM_MIN_SPARE_SERVERS=5
ENV PHPFPM_MAX_SPARE_SERVERS=50
CMD ["supervisord", "-c", "/etc/supervisord.conf", "--nodaemon"]
