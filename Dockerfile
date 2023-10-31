# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_REPO=${SaM_REPO:-ghcr.io/kristianstad/secure_and_minimal}
ARG ALPINE_VERSION=${ALPINE_VERSION:-3.18}
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="2.7.0"
ARG PHP_VERSION="8.1.14"
ARG CONTENTIMAGE1="node:alpine$ALPINE_VERSION"
ARG CONTENTDESTINATION1="/"
#ARG CLONEGITS="https://github.com/filleg/origo.git -b wfs-qgis"
ARG DOWNLOADS="https://github.com/origo-map/origo/archive/refs/tags/v$ORIGO_VERSION.zip"
ARG BUILDDEPS="python3"
ARG BUILDCMDS=\
"   cd origo-$ORIGO_VERSION "\
"&& rm -rf node_modules package-lock.json "\
"&& npm install "\
#"&& npm --depth 8 update "\
"&& npm run prebuild-sass "\
"&& npm run build "\
"&& rm -rf build/index.html "\
"&& cp -a build /finalfs/tmp/origo"
ARG RUNDEPS="\
        postgresql14 \
        php81-fpm \
        php81-json \
        php81-opcache \
        php81-pgsql \
        nginx"
ARG MAKEDIRS="/etc/php81/conf.d /etc/php81/php-fpm.d /var/log/nginx /usr/lib/nginx/modules /var/lib/nginx/tmp /run/nginx"
ARG REMOVEDIRS="/origo/origo-documentation /origo/examples /usr/include"
ARG REMOVEFILES="/etc/php81/php-fpm.d/www.conf /origo/index.json /usr/bin/ab /usr/bin/dbmmanage /usr/bin/htdbm /usr/bin/htdigest /usr/bin/httxt2dbm /usr/bin/logresolve /usr/sbin/checkgid /usr/sbin/envvars /usr/sbin/envvars-std /usr/sbin/htcacheclean /usr/sbin/rotatelogs"
ARG STARTUPEXECUTABLES="/usr/sbin/php-fpm81 /usr/libexec/postgresql14/postgres /usr/sbin/nginx"
ARG LINUXUSEROWNED="/var/log/nginx /usr/lib/nginx/modules /var/lib/nginx/tmp /run/nginx /origo /origo/origo-cities /origo/origo-cities/index1.json /origo/preview /origo/preview/index.json"
ARG FINALCMDS=\
"   cp -a /tmp/origo/* /origo/ "\
"&& cd /usr/local "\
"&& rm -rf share lib "\
"&& ln -s ../lib ../share ./ "\
"&& cd bin "\
"&& find ../../libexec/postgresql14 ! -type l ! -name postgres ! -name ../../libexec/postgresql14 -maxdepth 1 -exec ln -s {} ./ + "\
"&& chmod g+X /usr/bin/* "\
"&& ln -s /origo/origo-cities/index1.json /origo/origo-cities#1.json "\
"&& ln -s /origo/preview/index.json /origo/preview.json "\
"&& find /var -user 185 -exec chown 0:0 {} \;"
# ARGs (can be passed to Build/Final) </END>

# Generic template (don't edit) <BEGIN>
FROM ${CONTENTIMAGE1:-scratch} as content1
FROM ${CONTENTIMAGE2:-scratch} as content2
FROM ${CONTENTIMAGE3:-scratch} as content3
FROM ${CONTENTIMAGE4:-scratch} as content4
FROM ${CONTENTIMAGE5:-scratch} as content5
FROM ${BASEIMAGE:-$SaM_REPO:base-$ALPINE_VERSION} as base
FROM ${INITIMAGE:-scratch} as init
# Generic template (don't edit) </END>

# =========================================================================
# Build
# =========================================================================
# Generic template (don't edit) <BEGIN>
FROM ${BUILDIMAGE:-$SaM_REPO:build-$ALPINE_VERSION} as build
FROM ${BASEIMAGE:-$SaM_REPO:base-$ALPINE_VERSION} as final
COPY --from=build /finalfs /
# Generic template (don't edit) </END>

# =========================================================================
# Final
# =========================================================================
ARG POSTGRES_CONFIG_DIR="/etc/postgres"

ENV VAR_FINAL_COMMAND="php-fpm81 --force-stderr && postgres --config_file=\"\$VAR_POSTGRES_CONFIG_FILE\" & nginx -g 'daemon off;'" \
    VAR_ORIGO_CONFIG_DIR="/etc/origo" \
    VAR_OPERATION_MODE="dual" \
    VAR_setup1_module_load="[ 'mod_deflate','mod_fastcgi' ]" \
    VAR_WWW_DIR="/origo" \
    VAR_SOCKET_FILE="/run/php81-fpm/socket" \
    VAR_LOG_FILE="/var/log/php81/error.log" \
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
    VAR_param_timezone="'UTC'" \
    VAR_NGINX_CONFIG_DIR="/etc/nginx"
#    VAR_FINAL_COMMAND="postgres --config_file=\"\$VAR_POSTGRES_CONFIG_FILE\""

STOPSIGNAL SIGINT

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>
