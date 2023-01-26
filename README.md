# origo
https://github.com/Kristianstad/origo/pkgs/container/origo

Origo from Github master, image based on https://github.com/Kristianstad/lighttpd2/pkgs/container/lighttpd2 (check out for webserver settings). Listens on port 8080 internally. Files and directories in the Origo config directory are added to the Origo web directory at startup. There is also an optional management tool for Origo included in a separate tag (path to manage tool: adm/manage.php). Source code for the management tool is available in the with_php branch.

## Docker run examples
### If you just need Origo
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:2.6.0
### If you also want Kristianstads management tool for Origo
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:with_php

## Environment variables
### Runtime variables with default value
* VAR_LINUX_USER="www-user" (User running VAR_FINAL_COMMAND)
* VAR_ORIGO_CONFIG_DIR="/etc/origo" (Directory containing configuration files for Origo)
* VAR_CONFIG_DIR="/etc/lighttpd2" (Directory containing configuration files for Lighttpd2)
* VAR_FINAL_COMMAND="lighttpd2 -c '\$VAR_CONFIG_DIR/angel.conf'" (Command run by VAR_LINUX_USER)

## Capabilities
Can drop all but SETPCAP, SETGID and SETUID.
