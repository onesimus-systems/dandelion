#!/bin/bash
set -euo pipefail

# usage: file_env VAR [DEFAULT]
#    ie: file_env 'XYZ_DB_PASSWORD' 'example'
# (will allow for "$XYZ_DB_PASSWORD_FILE" to fill in the value of
#  "$XYZ_DB_PASSWORD" from a file, especially for Docker's secrets feature)
# This function is used throughout the Docker official images:
#       https://github.com/docker-library
file_env() {
	local var="$1"
	local fileVar="${var}_FILE"
	local def="${2:-}"
	if [ "${!var:-}" ] && [ "${!fileVar:-}" ]; then
		echo >&2 "error: both $var and $fileVar are set (but are exclusive)"
		exit 1
	fi
	local val="$def"
	if [ "${!var:-}" ]; then
		val="${!var}"
	elif [ "${!fileVar:-}" ]; then
		val="$(< "${!fileVar}")"
	fi
	export "$var"="$val"
	unset "$fileVar"
}

if [[ "$1" == apache2* ]]; then
    file_env 'DANDELION_DB_HOST' 'mysql'
	# if we're linked to MySQL and thus have credentials already, let's use them
	file_env 'DANDELION_DB_USER' "${MYSQL_ENV_MYSQL_USER:-root}"
	if [ "$DANDELION_DB_USER" = 'root' ]; then
		file_env 'DANDELION_DB_PASSWORD' "${MYSQL_ENV_MYSQL_ROOT_PASSWORD:-}"
	else
		file_env 'DANDELION_DB_PASSWORD' "${MYSQL_ENV_MYSQL_PASSWORD:-}"
	fi
	file_env 'DANDELION_DB_NAME' "${MYSQL_ENV_MYSQL_DATABASE:-dandelion}"
	if [ -z "$DANDELION_DB_PASSWORD" ]; then
		echo >&2 'error: missing required DANDELION_DB_PASSWORD environment variable'
		echo >&2 '  Did you forget to -e DANDELION_DB_PASSWORD=... ?'
		echo >&2
		echo >&2 '  (Also of interest might be DANDELION_DB_USER and DANDELION_DB_NAME.)'
		exit 1
	fi

    cd ..

    if ! [ -d dandelion ]; then
        mkdir dandelion
    fi

    cd dandelion

    if ! [ -e public/index.php ]; then
		echo >&2 "Dandelion not found in $(pwd) - copying now..."
		if [ "$(ls -A)" ]; then
			echo >&2 "WARNING: $(pwd) is not empty - press Ctrl+C now if this is an error!"
			( set -x; ls -A; sleep 10 )
		fi
		tar cf - --one-file-system -C /usr/src/dandelion . | tar xf -
		echo >&2 "Complete! Dandelion has been successfully copied to $(pwd)"
		if [ ! -e public/.htaccess ]; then
			# NOTE: The "Indexes" option is disabled in the php:apache base image
			cat > public/.htaccess <<-'EOF'
				# BEGIN Dandelion
				<IfModule mod_rewrite.c>
				RewriteEngine On
				RewriteBase /
				RewriteRule ^index\.php$ - [L]
				RewriteCond %{REQUEST_FILENAME} !-f
				RewriteCond %{REQUEST_FILENAME} !-d
				RewriteRule . /index.php [L]
				</IfModule>
				# END Dandelion
			EOF
			chown www-data:www-data public/.htaccess
		fi
	fi

    if ! [ -e app/config/config.php ]; then
        cp app/config/config.sample.php app/config/config.php
        cat << 'EOPHP' >> app/config/config.php
// If we're behind a proxy server and using HTTPS, we need to alert Wordpress of that fact
// see also http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
	$_SERVER['HTTPS'] = 'on';
}
EOPHP
        chown www-data:www-data app/config/config.php
    fi

    set_config() {
        key="$1"
        value="$2"

        echo "\$config${key} = \"${value}\";" >> app/config/config.php
    }

    set_config '["db"]["hostname"]' "$DANDELION_DB_HOST"
	set_config '["db"]["username"]' "$DANDELION_DB_USER"
	set_config '["db"]["password"]' "$DANDELION_DB_PASSWORD"
	set_config '["db"]["dbname"]' "$DANDELION_DB_NAME"
fi

exec "$@"
