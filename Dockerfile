FROM php:8.2-apache

# Enable Apache mod_rewrite (common for frameworks like Laravel or WordPress)
RUN a2enmod rewrite

# Copy all files to the web root
COPY . /var/www/html/

# Expose port 80 (Render expects a web service to run on this port)
EXPOSE 80
