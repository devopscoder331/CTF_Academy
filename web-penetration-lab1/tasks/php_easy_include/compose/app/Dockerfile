FROM ubuntu:20.04
LABEL org.opencontainers.image.authors="devopscoder331"

# instal dependencies
RUN set -xe && \
    apt-get update && apt-get upgrade -y && \
    apt-get install -y nginx-light php7.4-fpm supervisor htop curl

RUN apt-get install -y vim

# copy config files
COPY src/configs/nginx /etc/nginx
COPY src/configs/php/php.ini /etc/php/7.4/fpm/php.ini
COPY src/configs/supervisor /etc/supervisor/conf.d

# copy flag
ADD src/flag.txt /

# copy application files
ADD src/html /var/www/html

# Expose
EXPOSE 80

# Start init process
CMD supervisord -n -c /etc/supervisor/conf.d/supervisord.conf


