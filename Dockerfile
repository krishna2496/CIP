FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /optimy
RUN rm /optimy/Dockerfile
RUN rm /optimy/Dockerfile.cli
RUN mkdir -p /optimy/database/seeds

RUN apt-get install curl -y
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash -
RUN apt-get install -y nodejs

RUN cd /optimy && composer install
RUN npm install

RUN chown -R www-data:www-data /optimy/storage

#ENTRYPOINT ["apache2ctl", "-D", "FOREGROUND"]

