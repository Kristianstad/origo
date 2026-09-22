# origo
https://github.com/Kristianstad/origo/pkgs/container/origo

Docker image of Origo (https://github.com/origo-map). The image is built on https://github.com/Kristianstad/nginx/pkgs/container/nginx (check out for webserver settings). Listens on port 8080 internally. Files and directories in the Origo config directory are added to the Origo web directory at startup. Origo with admin-tool is available here: https://github.com/Kristianstad/origo-admin

## Docker run examples
docker run --name origo -d -p 8080:8080 ghcr.io/kristianstad/origo:2.10.0

## Environment variables
### Runtime variables with default value
* VAR_LINUX_USER="nginx" (User running VAR_FINAL_COMMAND)
* VAR_ORIGO_CONFIG_DIR="/etc/origo" (Directory containing configuration files for Origo)
* VAR_CONFIG_DIR="/etc/nginx" (Directory containing configuration files for Nginx)
* VAR_LOG_LEVEL="info"
* VAR_FINAL_COMMAND="nginx -g 'daemon off; error_log stderr \$VAR_LOG_LEVEL;'" (Command run by VAR_LINUX_USER)

### Format of runtime configuration variables (mainly used by the with_php tag)
* VAR_wwwconf_&lt;param name&gt;: Parameter in <span>ww</span>w.conf.
* VAR_phpini_&lt;param name&gt;: Parameter in /etc/php7/conf.d/50-setting.ini (overrides defaults set in php.ini).
* Dot (.) is representated as double underscore (\_\_) in variable names.
* VAR_ldapconf_&lt;param name&gt;: Parameter in /etc/ldap/ldap.conf.

## Capabilities
Can drop all but CHOWN, SETPCAP, SETGID and SETUID.
