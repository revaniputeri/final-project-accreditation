# Use PHP 8.2 FPM Alpine for smaller image size
FROM php:8.2-fpm-alpine

# Install system dependencies
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    nginx \
    supervisor \
    postgresql-dev \
    oniguruma-dev \
    libxml2-dev

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    dom \
    xml

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies with optimizations for production
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction --prefer-dist \
    && composer clear-cache

# Copy application files
COPY . .

# Create required Laravel directories
RUN mkdir -p storage/framework/views \
    && mkdir -p storage/framework/cache/data \
    && mkdir -p storage/framework/sessions \
    && mkdir -p storage/logs \
    && mkdir -p bootstrap/cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Create nginx configuration
RUN echo 'worker_processes auto;\n\
error_log /var/log/nginx/error.log warn;\n\
pid /var/run/nginx.pid;\n\
\n\
events {\n\
    worker_connections 1024;\n\
}\n\
\n\
http {\n\
    include /etc/nginx/mime.types;\n\
    default_type application/octet-stream;\n\
    \n\
    log_format main '"'"'$remote_addr - $remote_user [$time_local] "$request" '"'"'\n\
                    '"'"'$status $body_bytes_sent "$http_referer" '"'"'\n\
                    '"'"'"$http_user_agent" "$http_x_forwarded_for"'"'"';\n\
    \n\
    access_log /var/log/nginx/access.log main;\n\
    sendfile on;\n\
    keepalive_timeout 65;\n\
    client_max_body_size 20M;\n\
    \n\
    server {\n\
        listen 8080;\n\
        server_name _;\n\
        root /var/www/html/public;\n\
        index index.php index.html;\n\
        \n\
        location / {\n\
            try_files $uri $uri/ /index.php?$query_string;\n\
        }\n\
        \n\
        location ~ \.php$ {\n\
            fastcgi_pass 127.0.0.1:9000;\n\
            fastcgi_index index.php;\n\
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
            include fastcgi_params;\n\
        }\n\
        \n\
        location ~ /\.ht {\n\
            deny all;\n\
        }\n\
    }\n\
}' > /etc/nginx/nginx.conf

# Create supervisor configuration
RUN echo '[supervisord]\n\
nodaemon=true\n\
user=root\n\
logfile=/var/log/supervisor/supervisord.log\n\
pidfile=/var/run/supervisord.pid\n\
\n\
[program:php-fpm]\n\
command=php-fpm\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0\n\
\n\
[program:nginx]\n\
command=nginx -g '"'"'daemon off;'"'"'\n\
autostart=true\n\
autorestart=true\n\
stdout_logfile=/dev/stdout\n\
stdout_logfile_maxbytes=0\n\
stderr_logfile=/dev/stderr\n\
stderr_logfile_maxbytes=0' > /etc/supervisord.conf

# Create startup script using printf for better compatibility
RUN printf '#!/bin/sh\nset -e\n\n# Create required directories if they do not exist\nmkdir -p /var/www/html/storage/framework/views\nmkdir -p /var/www/html/storage/framework/cache\nmkdir -p /var/www/html/storage/framework/sessions\nmkdir -p /var/www/html/bootstrap/cache\n\n# Set proper permissions\nchown -R www-data:www-data /var/www/html/storage\nchown -R www-data:www-data /var/www/html/bootstrap/cache\n\n# Run Laravel optimizations\nphp artisan config:cache\nphp artisan route:cache\n\n# Only cache views if views directory exists\nif [ -d "/var/www/html/resources/views" ]; then\n    php artisan view:cache\nfi\n\n# Run migrations (optional - uncomment if needed)\n# php artisan migrate --force\n\n# Start supervisor\nexec /usr/bin/supervisord -c /etc/supervisord.conf\n' > /usr/local/bin/start.sh && \
    chmod +x /usr/local/bin/start.sh

# Expose port (Railway will inject PORT env variable)
EXPOSE 8080

# Start services
CMD ["/usr/local/bin/start.sh"]
