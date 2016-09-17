FROM php:7.0.9-alpine
MAINTAINER Ryuichi Tanaka <mapserver2007@gmail.com>

RUN apk add --update git build-base autoconf \
  && rm -rf /var/cache/apk/*

RUN pecl install xdebug-2.4.1 \
  && echo "zend_extension=$(find /usr/local/lib/php/extensions/ -name xdebug.so)" > /usr/local/etc/php/conf.d/xdebug.ini \
  && echo "xdebug.remote_enable=on" >> /usr/local/etc/php/conf.d/xdebug.ini \
  && docker-php-ext-enable xdebug

RUN curl -s http://getcomposer.org/installer | php && mv -f composer.phar /usr/bin/composer \
  && chmod +x /usr/bin/composer \
  && composer self-update

RUN apk del git autoconf build-base \
  && rm -rf /var/cache/apk/*

WORKDIR /workspace
