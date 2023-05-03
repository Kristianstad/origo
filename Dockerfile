# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_REPO=${SaM_REPO:-ghcr.io/kristianstad/secure_and_minimal}
ARG ALPINE_VERSION=${ALPINE_VERSION:-3.17}
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="search-result-infowindow2"
ARG LIGHTTPD2_VERSION="230118"
ARG CONTENTIMAGE1="node:alpine$ALPINE_VERSION"
ARG CONTENTDESTINATION1="/"
ARG BASEIMAGE="ghcr.io/kristianstad/lighttpd2:$LIGHTTPD2_VERSION"
#ARG CLONEGITS="https://github.com/filleg/origo.git -b wfs-qgis"
ARG DOWNLOADS="https://github.com/origo-map/origo/archive/refs/tags/$ORIGO_VERSION.zip"
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
    VAR_OPERATION_MODE="normal" \
    VAR_setup1_module_load="[ 'mod_deflate' ]" \
    VAR_WWW_DIR="/origo"

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>
