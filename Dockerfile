FROM php:8.3-fpm-bookworm

# Install required system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    curl \
    unzip \
    libzip-dev \
    libicu-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/lists/*

# Install official extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions required by Symfony & modern PHP applications
RUN install-php-extensions \
    intl \
    opcache \
    zip \
    apcu

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Install Node.js & npm (LTS)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - && \
    apt-get install -y --no-install-recommends nodejs && \
    rm -rf /var/lib/apt/lists/*

# Install Symfony CLI inside the container
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

# Copy custom PHP configuration
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Set working directory
WORKDIR /var/www/prismix-status-monitor

# Match Linux UID/GID (1000:1000) to prevent root permission conflicts on host
ARG USER_ID=1000
ARG GROUP_ID=1000

RUN groupadd -g ${GROUP_ID} prismix && \
    useradd -u ${USER_ID} -g prismix -m -s /bin/bash prismix && \
    chown -R prismix:prismix /var/www/prismix-status-monitor

USER prismix

EXPOSE 9000
CMD ["php-fpm"]
