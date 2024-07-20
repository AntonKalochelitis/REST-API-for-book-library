FROM php:8.2-fpm

# Fix debconf warnings upon .build
ARG DEBIAN_FRONTEND=noninteractive
ARG APP_ENV

RUN apt-get update && apt-get install -y \
        locales \
        librabbitmq-dev \
        libimage-exiftool-perl \
        libzip-dev \
        libpng-dev \
        libjpeg-dev \
        zip \
        unzip \
        p7zip-full \
        libfreetype6-dev \
        supervisor \
    && pecl install amqp \
    && docker-php-ext-enable amqp \
    && docker-php-ext-install zip mysqli pdo pdo_mysql opcache

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
       postgresql-client libpq-dev \
    && docker-php-ext-install pdo_pgsql

RUN docker-php-ext-configure gd --with-freetype=/usr/include/ --with-jpeg=/usr/include/ \
    && docker-php-ext-install gd

# Install PHP Redis extension
RUN pecl install -o -f redis \
  &&  rm -rf /tmp/pear \
  &&  docker-php-ext-enable redis

# Install systemd extension
RUN apt-get install -y \
    systemd

# Install ssh client extension
RUN apt-get install -y \
        openssh-client

# Configure non-root user.
ARG PUID=1000
ARG PGID=1000
RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

# Install composer
ENV COMPOSER_ALLOW_SUPERUSER=1
RUN curl -sS https://getcomposer.org/installer | php -- \
        --filename=composer \
        --install-dir=/usr/local/bin && \
        echo "alias composer='COMPOSER_MEMORY_LIMIT=-1 composer'" >> /root/.bashrc && \
        composer

RUN apt install -y libxslt-dev \
    && docker-php-ext-install xsl

# Node and NPM install
#RUN apt install -y nodejs
#RUN apt install -y npm
#RUN npm cache clean -f
#RUN npm install -g n
#RUN n stable

# Set the locale
RUN sed -i '/en_US.UTF-8/s/^# //g' /etc/locale.gen && \
    locale-gen
ENV LANG en_US.UTF-8
ENV LANGUAGE en_US:en
ENV LC_ALL en_US.UTF-8

COPY www.conf /usr/local/etc/php-fpm.d
COPY ${APP_ENV}/opcache.ini "$PHP_INI_DIR"/conf.d
COPY ${APP_ENV}/php.ini "$PHP_INI_DIR"/conf.d

WORKDIR "/app"
