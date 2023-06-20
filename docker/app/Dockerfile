# Build Stage for AWS CLI v2
FROM amazon/aws-cli:2.4.5 as awscli

FROM php:8.2-fpm

# Allow Composer to be run as root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install dependencies
RUN apt-get update && apt-get install -y \
    supervisor \
    nginx \
    libzip-dev \
    zip \
    less \
    netcat-openbsd \
    procps \
    && docker-php-ext-install zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY ../../ .

# Install xDebug
ADD ./docker/app/xdebug.ini /usr/local/etc/php/conf.d
RUN pecl install xdebug && docker-php-ext-enable xdebug

# Install Composer dependencies
RUN composer install

# Copy AWS CLI from the awscli image
COPY --from=awscli /usr/local/aws-cli /usr/local/aws-cli

# Add AWS CLI binary to PATH
ENV PATH="/usr/local/aws-cli/v2/current/dist:${PATH}"

# Copy the php.ini file into the image
COPY ./docker/app/php.ini /usr/local/etc/php/php.ini

# Copy Nginx configuration
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Copy Supervisor configuration
COPY ./docker/app/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Copy entrypoint script
COPY ./docker/app/entrypoint.sh /entrypoint.sh

# Grant permissions for the entrypoint script
RUN chmod +x /entrypoint.sh

# Use the entrypoint script
ENTRYPOINT ["/entrypoint.sh"]
