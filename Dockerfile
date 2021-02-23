FROM phpdockerio/php72-fpm:latest
ENV ACCEPT_EULA=Y

# Microsoft SQL Server Prerequisites
RUN apt-get update \
    && apt-get install -y apt-transport-https \
    && apt-get install -y --no-install-recommends curl libc6 g++ libssl1.0 \
    && curl -s https://packages.microsoft.com/keys/microsoft.asc | apt-key add - \
    && curl -s https://packages.microsoft.com/config/ubuntu/18.04/prod.list > /etc/apt/sources.list.d/mssql-release.list \
    && apt-get update \
    && apt-get -y install msodbcsql17 mssql-tools unixodbc-dev unixodbc

RUN apt-get -y install gcc g++ make autoconf libc-dev pkg-config php-pear php-common php7.2-dev \
    && pecl install sqlsrv pdo_sqlsrv xdebug

# Enable the extensions
RUN echo extension=sqlsrv.so > /etc/php/7.2/fpm/conf.d/30-sqlsrv.ini \
    && echo extension=sqlsrv.so > /etc/php/7.2/cli/conf.d/30-sqlsrv.ini \
    && echo extension=pdo_sqlsrv.so > /etc/php/7.2/fpm/conf.d/35-pdo_sqlsrv.ini \
    && echo extension=pdo_sqlsrv.so > /etc/php/7.2/cli/conf.d/35-pdo_sqlsrv.ini \
    && service php7.2-fpm restart

# Install selected extensions and other stuff
RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        git \
        php7.2-bcmath \
        php7.2-curl \
        php7.2-gd \
        php7.2-intl \
        php7.2-json \
        php7.2-mbstring \
        php7.2-mysql \
        php7.2-odbc \
        php7.2-opcache \
        php7.2-sqlite \
        php7.2-xdebug \
        php7.2-xml \
        php7.2-xmlrpc \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/* \
    # Create the /var/www folder and ensure we have the appropriate
    # permissions on it because we'll be running as www-data, not root
    && mkdir -p /var/www \
    && chown www-data:www-data /var/www

# This is necessary to let php-fpm run as www-data instead of root
RUN usermod -u 1000 www-data

COPY ./build/docker/php.ini /etc/php/7.2/fpm/conf.d/z-overrides.ini
COPY ./build/docker/php.ini /etc/php/7.2/cli/conf.d/z-overrides.ini
COPY ./build/docker/php.pool.ini /etc/php/7.2/fpm/pool.d/z-overrides.conf

WORKDIR /var/www/wallet.casino.test/
