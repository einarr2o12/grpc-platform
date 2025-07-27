FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    autoconf \
    g++ \
    make \
    zlib-dev \
    curl \
    git \
    unzip

# Install gRPC extension
RUN pecl install grpc \
    && docker-php-ext-enable grpc

# Install protobuf extension  
RUN pecl install protobuf \
    && docker-php-ext-enable protobuf

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY php-api-gateway/composer.json ./

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy application code
COPY php-api-gateway/ .

# Create proto generated files directory
RUN mkdir -p src/Generated

# Copy proto files for generation
COPY proto/ ./proto/

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Install nginx
RUN apk add --no-cache nginx

# Copy nginx configuration
COPY php-api-gateway/nginx.conf /etc/nginx/nginx.conf

# Create nginx directories
RUN mkdir -p /var/log/nginx \
    && mkdir -p /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/lib/nginx

# Expose port
EXPOSE 8080

# Start script
COPY php-api-gateway/start.sh /start.sh
RUN chmod +x /start.sh

CMD ["/start.sh"]