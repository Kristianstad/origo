# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_REPO=${SaM_REPO:-ghcr.io/kristianstad/secure_and_minimal}
ARG ALPINE_VERSION=${ALPINE_VERSION:-3.18}
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="2.7.0"
ARG CONTENTIMAGE1="node:alpine$ALPINE_VERSION"
ARG CONTENTDESTINATION1="/"
#ARG CLONEGITS="https://github.com/filleg/origo.git -b wfs-qgis"
ARG DOWNLOADS="https://github.com/origo-map/origo/archive/refs/tags/v$ORIGO_VERSION.zip"
ARG RUNDEPS="nginx"
ARG STARTUPEXECUTABLES="/usr/sbin/nginx"
ARG MAKEDIRS="/var/log/nginx /usr/lib/nginx/modules /var/lib/nginx/tmp /run/nginx"
ARG LINUXUSEROWNED="/var/log/nginx /usr/lib/nginx/modules /var/lib/nginx/tmp /run/nginx"
#ARG REMOVEDIRS="/etc/nginx/http.d"
ARG REMOVEFILES="/usr/bin/ab /usr/bin/dbmmanage /usr/bin/htdbm /usr/bin/htdigest /usr/bin/httxt2dbm /usr/bin/logresolve /usr/sbin/checkgid /usr/sbin/envvars /usr/sbin/envvars-std /usr/sbin/htcacheclean /usr/sbin/rotatelogs"
ARG FINALCMDS="find /var -user 185 -exec chown 0:0 {} \;"
ARG BUILDDEPS="python3"
ARG BUILDCMDS=\
"   cd origo-$ORIGO_VERSION "\
"&& rm -rf node_modules package-lock.json "\
"&& npm install "\
#"&& npm --depth 8 update "\
"&& npm run prebuild-sass "\
"&& npm run build "\
"&& sed -i 's/origo.js/origo.min.js/' build/index.html "\
"&& cp -a build /finalfs/origo"
ARG REMOVEDIRS="/origo/origo-documentation /origo/examples"
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
ENV VAR_ORIGO_CONFIG_DIR="/etc/origo" \
    VAR_CONFIG_DIR="/etc/nginx" \
    VAR_LINUX_USER="nginx" \
    VAR_FINAL_COMMAND="nginx -g 'daemon off;'"

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>
