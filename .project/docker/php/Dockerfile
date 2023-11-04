FROM in2code/php-dev:8.2-fpm

USER root

RUN apt update && \
    apt install -y gettext && \
    docker-php-ext-install gettext && \
    pecl install excimer && \
    docker-php-ext-enable excimer

USER 1000:1000
