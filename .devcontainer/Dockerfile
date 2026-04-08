FROM mcr.microsoft.com/devcontainers/php:3-8.5-trixie

# Enable Apache modules and .htaccess support
RUN a2enmod rewrite && a2enmod headers

# Set Apache DocumentRoot to the public folder
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/sitecounter/public|g' /etc/apache2/sites-available/000-default.conf
RUN sed -i 's|<Directory /var/www/html>|<Directory /var/www/html/sitecounter/public>|g' /etc/apache2/apache2.conf

# Allow .htaccess overrides in public folder
RUN echo '<Directory /var/www/html/sitecounter/public>' >> /etc/apache2/apache2.conf && \
    echo '  AllowOverride All' >> /etc/apache2/apache2.conf && \
    echo '  Require all granted' >> /etc/apache2/apache2.conf && \
    echo '</Directory>' >> /etc/apache2/apache2.conf

RUN usermod -a -G www-data root

# Install ICU development files and MySQL client tools
RUN apt-get update && apt-get install -y \
    libicu-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

# Install and enable the intl extension
RUN docker-php-ext-install intl