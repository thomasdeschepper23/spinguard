# SpinGuard — PHP 8.2 + Apache image (geschikt voor Render free tier)
FROM php:8.2-apache

# === PHP-extensies: GD voor image_resize.php ===
RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && rm -rf /var/lib/apt/lists/*

# === Apache: mod_rewrite + .htaccess (AllowOverride All) ===
RUN a2enmod rewrite headers expires deflate \
 && sed -i 's!<Directory /var/www/>!<Directory /var/www/>\n    AllowOverride All!' /etc/apache2/apache2.conf

# === Render verwacht dat de container op poort 10000 luistert (gratis tier) ===
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
 && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf

# === PHP runtime tuning voor foto-uploads ===
RUN { \
        echo "upload_max_filesize=10M"; \
        echo "post_max_size=64M"; \
        echo "memory_limit=256M"; \
        echo "max_execution_time=60"; \
        echo "expose_php=Off"; \
    } > /usr/local/etc/php/conf.d/spinguard.ini

# === App-code kopiëren ===
COPY --chown=www-data:www-data . /var/www/html/

# === Schrijfbare directories ===
RUN mkdir -p /var/www/html/uploads /var/www/html/uploads/leads /var/www/html/content/leads \
 && chown -R www-data:www-data /var/www/html/uploads /var/www/html/content \
 && chmod -R 775 /var/www/html/uploads /var/www/html/content/leads

EXPOSE 10000
CMD ["apache2-foreground"]
