FROM php:8.2-cli

# Dependensi sistem + driver PostgreSQL (pdo_pgsql/pgsql) untuk Laravel.
RUN apt-get update && apt-get install -y --no-install-recommends \
        git \
        unzip \
        libpq-dev \
    && docker-php-ext-install pdo_pgsql pgsql \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composer (dari image composer resmi).
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

# Sesuaikan limit upload agar fitur file (max 10MB) berfungsi di dalam container.
RUN echo "upload_max_filesize=10M" > /usr/local/etc/php/conf.d/fms-uploads.ini \
    && echo "post_max_size=12M" >> /usr/local/etc/php/conf.d/fms-uploads.ini

WORKDIR /var/www

EXPOSE 8000
