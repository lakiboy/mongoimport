FROM composer/composer

MAINTAINER Dmitri Lakachauskis <dmitri@devmachine.net>

# mongo
RUN apt-get update && apt-get install -y libssl-dev g++ \
    && pecl install mongo \
    && rm -rf /tmp/pear \
    && rm -r /var/lib/apt/lists/* \
    && echo "extension=mongo.so" > $PHP_INI_DIR/conf.d/mongo.ini

# xdebug
RUN pecl install xdebug \
    && rm -rf /tmp/pear \
    && echo "zend_extension=xdebug.so" > $PHP_INI_DIR/conf.d/xdebug.ini
