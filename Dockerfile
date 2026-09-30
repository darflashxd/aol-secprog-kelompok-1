FROM php:8.2-apache

# Install PDO MySQL extension and enable Apache mod_rewrite
RUN docker-php-ext-install pdo pdo_mysql \
    && a2enmod rewrite

# Secure PHP configurations for development & secure file upload
RUN echo "file_uploads = On" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "upload_max_filesize = 5M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "post_max_size = 8M" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "session.cookie_httponly = 1" >> /usr/local/etc/php/conf.d/custom.ini \
    && echo "session.use_only_cookies = 1" >> /usr/local/etc/php/conf.d/custom.ini

WORKDIR /var/www/html

# Serve only the public directory; application code stays outside the web root.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf
