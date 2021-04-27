ARG DRUPAL_BASE_IMAGE=geerlingguy/drupal:latest

# PHP Dependency install via Composer.
FROM composer as vendor

COPY composer.json composer.json
COPY composer.lock composer.lock
COPY web/ web/
COPY GoodKey.gpg GoodKey.gpg

# RUN composer install \
#    --ignore-platform-reqs \
#    --no-interaction \
#    --no-dev \ 
#    --prefer-dist

# Build the Docker image for Drupal.
FROM $DRUPAL_BASE_IMAGE

# TODO: Change this.
ENV DRUPAL_MD5 aedc6598b71c5393d30242b8e14385e5

# Copy precompiled codebase into the container.
COPY --from=vendor /app/ /var/www/html/


# TODO: For production, copy config in place.
# Copy other required configuration into the container.
# COPY config/ /var/www/html/config/
# COPY load.environment.php /var/www/html/load.environment.php
# COPY jeffgeerling.settings.php /var/www/html/web/sites/default/settings.php

# Make sure file ownership is correct on the document root.
RUN chown -R www-data:www-data /var/www/html/web

# Install xdebug and apps needed to support it

RUN apt-key del B188E2B695BD4743 \
  && apt-key add GoodKey.gpg \
  && apt-get update -yqq \
  && apt-get install php7.4-xdebug\
 ;
RUN apt-get install -yqq \
    # install sshd
    openssh-server \
    # install ping and netcat (for debugging xdebug connectivity)
    iputils-ping netcat \
    # install vim editor
    vim \
    # fix ssh start up bug
    # @see https://github.com/ansible/ansible-container/issues/141
    && mkdir /var/run/sshd \
;

# put xdebug configuration information in xdebug.ini file

RUN echo "xdebug.mode=debug" >> /etc/php7.4/mods-available/xdebug.ini \
 &&  echo "xdebug.idekey=PHPSTORM" >> /etc/php/7.4/mods-available/xdebug.ini \
 && echo "xdebug.client_host=host.docker.internal" >> /etc/php/7.4/mods-available/xdebug.ini \
 && echo "xdebug.client_port=10000" >> /etc/php/7.4/mods-available/xdebug.ini \
;

# Add ssh public key information to authorized_keys

ADD id_rsa.pub .
RUN mkdir /root/.ssh \
 && cat ./id_rsa.pub >> /root/.ssh/authorized_keys \
;

# Add Drush Launcher.
RUN curl -OL https://github.com/drush-ops/drush-launcher/releases/download/0.6.0/drush.phar \
 && chmod +x drush.phar \
 && mv drush.phar /usr/local/bin/drush

# Adjust the Apache docroot.
ENV APACHE_DOCUMENT_ROOT=/var/www/html/web


CMD ["/usr/sbin/apache2ctl", "-D", "FOREGROUND"]


