# origo
https://github.com/Kristianstad/origo/pkgs/container/origo

Docker image of Origo (https://github.com/origo-map). The image is built on https://github.com/Kristianstad/nginx/pkgs/container/nginx (check out for webserver settings). Listens on port 8080 internally. Files and directories in the Origo config directory are added to the Origo web directory at startup. There is also an optional management tool for Origo and metadata included in the with_php tag (web path to manage tool: adm/manage.php). Source code for the management tool is available in the with_php branch.

A live demo of the Image can be found here:

https://kartor.kristianstad.se/demo/

https://kartor.kristianstad.se/demo/adm/manage.php 

## Docker run examples
### If you just need Origo
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:2.7.0
### If you also want Kristianstad's management tool for Origo and metadata
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:with_php

## Environment variables
### Runtime variables with default value
* VAR_LINUX_USER="nginx" (User running VAR_FINAL_COMMAND)
* VAR_ORIGO_CONFIG_DIR="/etc/origo" (Directory containing configuration files for Origo)
* VAR_CONFIG_DIR="/etc/nginx" (Directory containing configuration files for Nginx)
* VAR_LOG_LEVEL="info"
* VAR_FINAL_COMMAND="nginx -g 'daemon off; error_log stderr \$VAR_LOG_LEVEL;'" (Command run by VAR_LINUX_USER)

## Capabilities
Can drop all but CHOWN, SETPCAP, SETGID and SETUID.
