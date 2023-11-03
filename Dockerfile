# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_REPO=${SaM_REPO:-ghcr.io/kristianstad/secure_and_minimal}
ARG ALPINE_VERSION=${ALPINE_VERSION:-3.18}
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="2.8.0"
ARG BASEIMAGE="ghcr.io/kristianstad/origo:$ORIGO_VERSION"
#ARG PHP_VERSION="8.1.25"
ARG RUNDEPS="\
        postgresql14 \
        php81-fpm \
        php81-json \
        php81-opcache \
        php81-pgsql"
ARG MAKEDIRS="/etc/php81/conf.d /etc/php81/php-fpm.d /var/log/php81"
ARG FINALCMDS=\
"   cd /usr/local "\
"&& rm -rf share lib "\
"&& ln -s ../lib ../share ./ "\
"&& cd bin "\
"&& find ../../libexec/postgresql14 ! -type l ! -name postgres ! -name ../../libexec/postgresql14 -maxdepth 1 -exec ln -s {} ./ + "\
"&& chmod g+X /usr/bin/* "\
"&& ln -s /www/origo-cities/index1.json /www/origo-cities#1.json "\
"&& ln -s /www/preview/index.json /www/preview.json "
ARG REMOVEFILES="/etc/php81/php-fpm.d/www.conf /origo/index.json"
ARG STARTUPEXECUTABLES="/usr/sbin/php-fpm81 /usr/libexec/postgresql14/postgres"
ARG LINUXUSEROWNED="/var/log/php81 /www/origo-cities /www/origo-cities/index1.json /www/preview /www/preview/index.json"
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

ENV VAR_SOCKET_FILE="/run/php81-fpm/socket" \
    VAR_wwwconf_listen='$VAR_SOCKET_FILE' \
    VAR_wwwconf_pm="dynamic" \
    VAR_wwwconf_pm__max_children="5" \
    VAR_wwwconf_pm__min_spare_servers="1" \
    VAR_wwwconf_pm__max_spare_servers="3" \
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
    VAR_server15_index="index.html manage.php index.php" \
    VAR_serversub02_location="~ \\.php\$ { fastcgi_pass unix:/run/php81-fpm/socket; fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name; fastcgi_param SCRIPT_NAME \$fastcgi_script_name; include fastcgi.conf; }" \
    VAR_FINAL_COMMAND="php-fpm81 --force-stderr && postgres --config_file=\"\$VAR_POSTGRES_CONFIG_FILE\" & nginx -g 'daemon off; user \$VAR_LINUX_USER; error_log stderr \$VAR_LOG_LEVEL; worker_processes \$VAR_WORKER_PROCESSES; worker_rlimit_nofile \$VAR_WORKER_RLIMIT_NOFILE;'"

STOPSIGNAL SIGINT

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>
