FROM composer/composer

MAINTAINER Dmitri Lakachauskis <dmitri@devmachine.net>

# mongo
RUN apt-get update && apt-get install -y libssl-dev g++ \
    && pecl install mongo \
    && rm -r /var/lib/apt/lists/* \
    && echo "extension=mongo.so" > $PHP_INI_DIR/conf.d/mongo.ini
