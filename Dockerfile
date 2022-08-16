FROM rockylinux:9
RUN dnf upgrade --refresh -y; \
    dnf install yum-plugin-copr epel-release -y; \
    dnf install https://dl.fedoraproject.org/pub/epel/epel-release-latest-8.noarch.rpm -y; \
    dnf config-manager --set-enabled crb; \
    dnf install dnf-utils http://rpms.remirepo.net/enterprise/remi-release-9.rpm -y; \
    dnf update -y; \
    dnf module enable php:remi-8.1 -y; \
    dnf install php -y; \
    dnf install php-cli php-curl php-mysqlnd php-gd php-opcache php-zip php-intl php-common php-bcmath php-imap php-imagick php-xmlrpc php-json php-readline php-memcached php-redis php-mbstring php-apcu php-xml php-dom php81-fpm -y; \
    php -v;
RUN dnf upgrade --refresh -y; \
    dnf install httpd -y;
    # https://www.linuxcapable.com/how-to-install-apache-httpd-on-rocky-linux-9/
COPY src /var/www/src/
COPY config /var/www/config/
COPY logs /var/www/logs/
COPY app /var/www/html/
COPY composer.json /var/www/
WORKDIR /var/www/html/
RUN dnf install curl git zip unzip wget jq -y; \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"; \
    php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"; \
    php composer-setup.php install; \
    php -r "unlink('composer-setup.php');"; \
    mv composer.phar ../; \
    cd /var/www/; \
    php composer.phar install;
CMD ["/usr/sbin/httpd", "-DFOREGROUND"]
