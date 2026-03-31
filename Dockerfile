FROM php:8.1-apache

# Enable Apache modules
RUN a2enmod rewrite headers

# Install MySQLi extension
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install other recommended extensions
RUN docker-php-ext-install pdo pdo_mysql zip

# Set working directory
WORKDIR /var/www/html

# Copy application
COPY . .

# Set appropriate permissions
RUN chown -R www-data:www-data /var/www/html

# Create .htaccess for routing if needed
RUN echo 'RewriteEngine On\nRewriteBase /\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteCond %{REQUEST_FILENAME} !-d\nRewriteRule ^(.*)$ index.php?path=$1 [QSA,L]' > /var/www/html/.htaccess

# Expose port
EXPOSE 80

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost/ || exit 1

# Start Apache
CMD ["apache2-foreground"]
