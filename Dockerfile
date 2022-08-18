# We use Rocky Linux 9 as defined in requirement spec
FROM rockylinux:9
# We install all the necessary repositories and software, in this order:
# 1. Update cache and software
# 2. Instal copr, epel-release 9, and epel-release 8; Remi's repository has this as a dependency.
# 3. Enable crb
# 4. Install the remi repository
# 5. Enable the repository for PHP8.0; The reason we use 8.0 is because PHP-FPM is not available for aarch64 in PHP8.1.
# 6. Install the necessary software
# 7. Print out the PHP version, just to make sure
RUN dnf upgrade --refresh -y; \
    dnf install yum-plugin-copr epel-release https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm -y; \
    dnf config-manager --set-enabled crb; \
    dnf install http://rpms.remirepo.net/enterprise/remi-release-9.rpm -y; \
    dnf update -y; \
    dnf module enable php:remi-8.0 -y; \
    dnf install php php-cli php-curl php-mysqlnd php-gd php-opcache php-zip php-intl php-common php-bcmath php-imap php-imagick php-xmlrpc php-json php-readline php-memcached php-redis php-mbstring php-apcu php-xml php-dom php-fpm vim-enhanced httpd curl git zip unzip wget jq -y; \
    php -v;
# Copy the backend source files
COPY ./src/* /var/www/src/
# Copy the configuration files
COPY ./config/* /var/www/config/
# Copy the app files
COPY ./app/* /var/www/html/
# Copy composer.json
COPY composer.json /var/www/
COPY ./certs /var/www/certs
# Define the working directory
WORKDIR /var/www/
# Do the following:
# 1. Download composer-setup.php
# 2. Verify its checksum
# 3. Install composer.phar
# 4. Remove composer-setup.php
# 5. Setup composer.phar so it is ready to be used
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"; \
    php composer-setup.php install; \
    php -r "unlink('composer-setup.php');"; \
    php composer.phar install;
# Ensure /run/php-fpm and /var/www/tmp directories exists.
RUN mkdir -p /run/php-fpm; \
    mkdir -p /var/www/tmp;
# Clean dnf caches to save space
RUN dnf clean all;
# php-fpm runs in the background, while httpd runs in the foreground, successfully creating a PHP/APACHE image based on Rocky Linux
CMD php-fpm; /usr/sbin/httpd -DFOREGROUND
