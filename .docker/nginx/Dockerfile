FROM nginx:alpine

ARG APP_ENV

COPY ./nginx.conf /etc/nginx/nginx.conf
RUN rm /etc/nginx/conf.d/default.conf
COPY ./conf.d/${APP_ENV}/ /etc/nginx/conf.d/

CMD ["nginx", "-g", "daemon off;"]
