FROM registry.kubernetes.infra.optimy.net/tools/web-ci:7.3

COPY . /tmp

RUN cd /tmp && mv vue-prod.config.js vue.config.js

RUN apt-get install curl -y
RUN curl -sL https://deb.nodesource.com/setup_13.x | bash - 
RUN apt-get install -y nodejs 
RUN cd /tmp && npm install && npm rebuild node-sass && npm run build

RUN mkdir /optimy
RUN mkdir /optimy/public

RUN cp -R /tmp/dist/* /optimy/public/
RUN cp /tmp/htaccess /optimy/public/.htaccess

RUN rm -rf /tmp/*

RUN chown -R www-data:www-data /optimy/public




