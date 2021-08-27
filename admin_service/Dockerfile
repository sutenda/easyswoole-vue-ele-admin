FROM centos:8

#version defined
ENV SWOOLE_VERSION 4.4.23
ENV EASYSWOOLE_VERSION 3.4.x

#install libs
RUN yum install -y curl zip unzip  wget openssl-devel gcc-c++ make autoconf git
#install php
RUN yum install -y php-devel php-openssl php-mbstring php-json php-simplexml php-gmp
# composer
RUN curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
# use aliyun composer 由于最近阿里云镜像不稳定，废弃使用
# RUN composer config -g repo.packagist composer https://mirrors.aliyun.com/composer/

# swoole ext
RUN wget https://github.com/swoole/swoole-src/archive/v${SWOOLE_VERSION}.tar.gz -O swoole.tar.gz \
    && mkdir -p swoole \
    && tar -xf swoole.tar.gz -C swoole --strip-components=1 \
    && rm swoole.tar.gz \
    && ( \
    cd swoole \
    && phpize \
    && ./configure --enable-openssl \
    && make \
    && make install \
    ) \
    && sed -i "2i extension=swoole.so" /etc/php.ini \
    && rm -r swoole

# Dir
WORKDIR /easyswoole

COPY ./ /easyswoole
# install easyswoole
RUN cd /easyswoole \
#    && composer require easyswoole/easyswoole=${EASYSWOOLE_VERSION} \
    && php vendor/easyswoole/easyswoole/bin/easyswoole install

#测试配置
CMD ["php","easyswoole","server", "start"]
#正式配置
#CMD ["php","easyswoole","server", "start","-mode=produce"]

EXPOSE 9501