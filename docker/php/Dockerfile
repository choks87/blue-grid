FROM php:8.3-cli-bullseye AS base

# SYS: Install required packages
RUN apt-get update
RUN apt-get install --no-install-recommends -y bash
RUN apt-get install --no-install-recommends -y git
RUN apt-get install --no-install-recommends -y sudo
RUN apt-get install --no-install-recommends -y autoconf
RUN apt-get install --no-install-recommends -y gcc
RUN apt-get install --no-install-recommends -y g++
RUN apt-get install --no-install-recommends -y make
RUN apt-get install --no-install-recommends -y gettext
RUN apt-get install --no-install-recommends -y nano
RUN apt-get install --no-install-recommends -y zip
RUN apt-get install --no-install-recommends -y unzip
RUN apt-get install --no-install-recommends -y libzip-dev
RUN apt-get install --no-install-recommends -y libxml2-dev
RUN apt-get install --no-install-recommends -y procps

COPY docker/php/files/bin                    /usr/local/bin/
COPY docker/php/files/ini/php.ini            /usr/local/etc/php/conf.d

# PHP Extensions, via docker-php-ext-install and PECL :)
RUN docker-php-ext-install iconv
RUN docker-php-ext-install session
RUN docker-php-ext-install pdo_mysql
RUN docker-php-ext-install ctype
RUN docker-php-ext-install xml
RUN docker-php-ext-install pcntl
RUN pecl install zip
RUN pecl install inotify
RUN pecl install redis
RUN pecl install openswoole

# Disabling / Eanbling PHP Extension Xdebug because we don't need it by default
RUN php-ext-enable opcache
RUN php-ext-enable openswoole
RUN php-ext-enable redis

# Install composer
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && \
   mv composer.phar /usr/local/bin/composer

# Symfony Binary (used only first time project is created, but probably can stay)s
RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
RUN apt install symfony-cli

# Setting Working Directory
WORKDIR /app

FROM base AS development

COPY docker/php/files/ini/20-xdebug.ini      /usr/local/etc/php/available-ini/

# Excensions
RUN pecl install pcov
RUN pecl install xdebug
RUN php-ext-enable pcov
RUN php-ext-enable xdebug


FROM base AS production
RUN echo -n "APP_ENV=prod\n" >> .env
RUN echo -n "APP_SECRET=" >> .env
RUN echo -n "$(head -c 16 /dev/urandom | xxd -p)" | sha1sum | awk '{print $1}' >> .env

COPY bin /app/bin
COPY config /app/config
COPY public /app/public
COPY src /app/src
COPY translations /app/translations
COPY composer.json /app/composer.json
COPY composer.lock /app/composer.lock
COPY symfony.lock /app/symfony.lock

RUN composer install --no-dev --optimize-autoloader
RUN rm -rf \
      composer.lock \
      phpmd_ruleset.xml \
      symfony.lock

CMD ["bin/console", "swoole:server:run"]



