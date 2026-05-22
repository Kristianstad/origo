# =========================================================================
# Init
# =========================================================================
# ARGs (can be passed to Build/Final) <BEGIN>
ARG SaM_REPO=${SaM_REPO:-ghcr.io/kristianstad/secure_and_minimal}
ARG ALPINE_VERSION=${ALPINE_VERSION:-3.23}
ARG IMAGETYPE="application"
ARG ORIGO_VERSION="2.9.0"
ARG NGINX_VERSION="1.28.3"
ARG BASEIMAGE="ghcr.io/kristianstad/nginx:$NGINX_VERSION"
ARG CONTENTIMAGE1="node:alpine$ALPINE_VERSION"
ARG CONTENTDESTINATION1="/"
#ARG CLONEGITS="https://github.com/origo-map/origo.git"
ARG CLONEGITS="https://github.com/huggla/origo.git -b new-ikarta"
#ARG DOWNLOADS="https://github.com/origo-map/origo/archive/refs/tags/v$ORIGO_VERSION.zip"
ARG BUILDDEPS="python3 py3-libsass"
ARG BUILDCMDS=\
#"   cd origo-$ORIGO_VERSION "\
"   cd origo "\
"&& rm -rf node_modules package-lock.json "\
"&& npm install "\
#"&& npm --depth 8 update "\
"&& npm run prebuild-sass "\
"&& npm run build "\
"&& sed -i 's/origo.js/origo.min.js/' build/index.html "\
"&& cp -a build /finalfs/www"
ARG REMOVEDIRS="/www/origo-documentation /www/examples /usr/include"
ARG LINUXUSEROWNED="/www"
# ARGs (can be passed to Build/Final) </END>

# Generic template (don't edit) <BEGIN>
FROM ${CONTENTIMAGE1:-scratch} AS content1
FROM ${CONTENTIMAGE2:-scratch} AS content2
FROM ${CONTENTIMAGE3:-scratch} AS content3
FROM ${CONTENTIMAGE4:-scratch} AS content4
FROM ${CONTENTIMAGE5:-scratch} AS content5
FROM ${BASEIMAGE:-$SaM_REPO:base-$ALPINE_VERSION} AS base
FROM ${INITIMAGE:-scratch} AS init
# Generic template (don't edit) </END>

# =========================================================================
# Build
# =========================================================================
# Generic template (don't edit) <BEGIN>
FROM ${BUILDIMAGE:-$SaM_REPO:build-$ALPINE_VERSION} AS build
FROM ${BASEIMAGE:-$SaM_REPO:base-$ALPINE_VERSION} AS final
COPY --from=build /finalfs /
# Generic template (don't edit) </END>

# =========================================================================
# Final
# =========================================================================
# Re-declare ARGs
ARG ALPINE_VERSION
ARG ORIGO_VERSION
ARG NGINX_VERSION

ENV VAR_ORIGO_CONFIG_DIR="/etc/origo"

# Generic template (don't edit) <BEGIN>
USER starter
ONBUILD USER root
# Generic template (don't edit) </END>

LABEL org.opencontainers.image.version="${ORIGO_VERSION}" \
      org.opencontainers.image.title="origo" \
      org.opencontainers.image.description="Origo ${ORIGO_VERSION} based on secure_and_minimal ${ALPINE_VERSION} + nginx ${NGINX_VERSION}"
