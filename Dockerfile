FROM cimdev/moodle:moodle-5.0-php8.2-apache

# Clean up existing standard Moodle code in the image
RUN rm -rf /var/www/html/*

# Install Redis, APCu, igbinary PHP extensions for caching
RUN pecl install redis apcu igbinary \
    && docker-php-ext-enable redis apcu igbinary

# Copy our patched Moodle codebase (including all plugins, config, and patches)
COPY . /var/www/html/

# Copy PHP performance tuning config
COPY .docker/php.ini /usr/local/etc/php/conf.d/zzzz-99-performance.ini

# Set proper permissions for the web server
RUN chown -R www-data:www-data /var/www/html

# Install custom entrypoint (strip Windows CRLF so Linux can execute it)
COPY docker-entrypoint-custom.sh /usr/local/bin/docker-entrypoint-custom.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint-custom.sh \
    && chmod +x /usr/local/bin/docker-entrypoint-custom.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-custom.sh"]

