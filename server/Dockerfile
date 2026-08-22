FROM php:8.3-apache

RUN docker-php-ext-install pdo_mysql

COPY api/ /var/www/html/

HEALTHCHECK --interval=30s --timeout=5s --start-period=15s \
  CMD php -r "exit(strpos(@file_get_contents('http://127.0.0.1/health.php'),'ok')!==false?0:1);"
