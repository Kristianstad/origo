# Secure and Minimal image of Origo
# https://hub.docker.com/repository/docker/huggla/sam-origo

# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_VERSION="2.0.5-3.14"
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="2.4.0"
ARG LIGHTTPD2_VERSION="20201125"
ARG PHP_VERSION="7.4.19"
ARG CONTENTIMAGE1="node:16.13.2-alpine3.14"
ARG CONTENTDESTINATION1="/"
ARG BASEIMAGE="huggla/sam-lighttpd2:$LIGHTTPD2_VERSION"
#ARG CLONEGITS="https://github.com/filleg/origo.git -b wfs-qgis"
ARG DOWNLOADS="https://github.com/origo-map/origo/releases/download/v$ORIGO_VERSION/origo-$ORIGO_VERSION.tar.gz"
ARG BUILDDEPS="python2"
ARG BUILDCMDS=\
#"   cd origo "\
" npm install "\
#"&& npm --depth 8 update "\
"&& npm run prebuild-sass "\
"&& npm run build "\
"&& sed -i 's/origo.js/origo.min.js/' build/index.html "\
"&& cp -a build /finalfs/origo"
ARG RUNDEPS="\
postgresql postgresql-contrib libressl3.0-libssl unixodbc \
#        php7 \
#        php7-bcmath \
        php7-dom \
#        php7-ctype \
        php7-curl \
#        php7-fileinfo \
        php7-fpm \
#        php7-gd \
#        php7-iconv \
        php7-intl \
        php7-json \
        php7-mbstring \
        php7-mcrypt \
#        php7-mysqlnd \
        php7-opcache \
        php7-openssl \
#        php7-pdo \
#        php7-pdo_mysql \
#        php7-pdo_pgsql \
#        php7-pdo_sqlite \
#        php7-phar \
#        php7-posix \
        php7-simplexml \
        php7-session \
#        php7-soap \
#        php7-tokenizer \
#        php7-xml \
#        php7-xmlreader \
#        php7-xmlwriter \
#        php7-zip \
        php7-pgsql \
        php7-pecl-imagick \
#        libpng \
#        libpng-static \
#        libpng-utils \
#        libjpeg-turbo \
#        imagemagick \
#        curl \
        php7-pecl-apcu \
        php7-ldap"
#        composer"
ARG MAKEDIRS="/etc/php7/conf.d /etc/php7/php-fpm.d"
ARG REMOVEDIRS="/origo/origo-documentation /origo/examples /usr/include"
ARG REMOVEFILES="/etc/php7/php-fpm.d/www.conf"
ARG STARTUPEXECUTABLES="/usr/sbin/php-fpm7 /usr/bin/postgres"
ARG FINALCMDS=\
"   cd /usr/local "\
"&& rm -rf share "\
"&& ln -s ../lib ../share ./ "\
"&& cd bin "\
"&& find ../../bin ! -type l ! -name postgres ! -name ../../bin -maxdepth 1 -exec ln -s {} ./ + "\
"&& chmod g+X /usr/bin/*"
# ARGs (can be passed to Build/Final) </END>

# Generic template (don't edit) <BEGIN>
FROM ${CONTENTIMAGE1:-scratch} as content1
FROM ${CONTENTIMAGE2:-scratch} as content2
FROM ${CONTENTIMAGE3:-scratch} as content3
FROM ${CONTENTIMAGE4:-scratch} as content4
FROM ${CONTENTIMAGE5:-scratch} as content5
FROM ${BASEIMAGE:-huggla/secure_and_minimal:$SaM_VERSION-base} as base
FROM ${INITIMAGE:-scratch} as init
# Generic template (don't edit) </END>

# =========================================================================
# Build
# =========================================================================
# Generic template (don't edit) <BEGIN>
FROM ${BUILDIMAGE:-huggla/secure_and_minimal:$SaM_VERSION-build} as build
FROM ${BASEIMAGE:-huggla/secure_and_minimal:$SaM_VERSION-base} as final
COPY --from=build /finalfs /
# Generic template (don't edit) </END>

# =========================================================================
# Final
# =========================================================================
ARG POSTGRES_CONFIG_DIR="/etc/postgres"

ENV VAR_FINAL_COMMAND="php-fpm7 --force-stderr && lighttpd2 -c '\$VAR_CONFIG_DIR/angel.conf'" \
    VAR_ORIGO_CONFIG_DIR="/etc/origo" \
    VAR_OPERATION_MODE="dual" \
    VAR_setup1_module_load="[ 'mod_deflate','mod_fastcgi' ]" \
    VAR_WWW_DIR="/origo" \
    VAR_SOCKET_FILE="/run/php7-fpm/socket" \
    VAR_LOG_FILE="/var/log/php7/error.log" \
    VAR_wwwconf_listen='$VAR_SOCKET_FILE' \
    VAR_wwwconf_pm="dynamic" \
    VAR_wwwconf_pm__max_children="5" \
    VAR_wwwconf_pm__min_spare_servers="1" \
    VAR_wwwconf_pm__max_spare_servers="3" \
    VAR_mode_dual=\
"      include '\$VAR_CONFIG_DIR/mimetypes.conf';\n"\
"      docroot '\$VAR_WWW_DIR';\n"\
"      index [ 'index.php', 'index.html', 'index.htm', 'default.htm', 'index.lighttpd.html', '/index.php' ];\n"\
"      if phys.path =$ '.php' {\n"\
"         buffer_request_body false;\n"\
"         strict.post_content_length false;\n"\
"         if req.header['X-Forwarded-Proto'] =^ 'http' and req.header['X-Forwarded-Port'] =~ '[0-9]+' {\n"\
"            env.set 'REQUEST_URI' => '%{req.header[X-Forwarded-Proto]}://%{req.host}:%{req.header[X-Forwarded-Port]}%{req.raw_path}';\n"\
"         }\n"\
"         fastcgi 'unix:\$VAR_SOCKET_FILE';\n"\
"         if request.is_handled { header.remove 'Content-Length'; }\n"\
"      } else {\n"\
"         static;\n"\
"         if request.is_handled {\n"\
"            if response.header['Content-Type'] =~ '^(.*/javascript|text/.*)(;|$)' {\n"\
"               deflate;\n"\
"            }\\n"\
"         }\n"\
"      }" \
    VAR_LINUX_USER="postgres" \
    VAR_INIT_CAPS="cap_chown" \
    VAR_POSTGRES_CONFIG_DIR="$POSTGRES_CONFIG_DIR" \
    VAR_POSTGRES_CONFIG_FILE="$POSTGRES_CONFIG_DIR/postgresql.conf" \
    VAR_LOCALE="en_US.UTF-8" \
    VAR_ENCODING="UTF8" \
    VAR_TEXT_SEARCH_CONFIG="english" \
    VAR_HBA="local all all trust, host all all 127.0.0.1/32 trust, host all all ::1/128 trust, host all all all md5" \
    VAR_param_data_directory="'/pgdata'" \
    VAR_param_hba_file="'$POSTGRES_CONFIG_DIR/pg_hba.conf'" \
    VAR_param_ident_file="'$POSTGRES_CONFIG_DIR/pg_ident.conf'" \
    VAR_param_unix_socket_directories="'/var/run/postgresql'" \
    VAR_param_listen_addresses="'*'" \
    VAR_param_timezone="'UTC'"
#    VAR_FINAL_COMMAND="postgres --config_file=\"\$VAR_POSTGRES_CONFIG_FILE\""

STOPSIGNAL SIGINT

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>
