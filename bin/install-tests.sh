#!/usr/bin/env bash

set -ex

WP_CLI_BIN_DIR=${WP_CLI_BIN_DIR-/tmp/wp-cli-phar}
PKG_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )"/../ && pwd )"
BEHAT_DIR=${PKG_DIR}/features

download() {
	if [ `which curl` ]; then
		curl -s "$1" > "$2";
	elif [ `which wget` ]; then
		wget -nv -O "$2" "$1"
	fi
}

install_composer_packages() {
	cd $PKG_DIR
	composer update
}

install_tests_includes() {
	mkdir -p ${BEHAT_DIR}/bootstrap
	mkdir -p ${BEHAT_DIR}/steps
	mkdir -p ${BEHAT_DIR}/extra

	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/bootstrap/FeatureContext.php ${BEHAT_DIR}/bootstrap
	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/bootstrap/support.php ${BEHAT_DIR}/bootstrap
	cp $PKG_DIR/vendor/wp-cli/wp-cli/php/WP_CLI/Process.php ${BEHAT_DIR}/bootstrap
	cp $PKG_DIR/vendor/wp-cli/wp-cli/php/WP_CLI/ProcessRun.php ${BEHAT_DIR}/bootstrap
	cp $PKG_DIR/vendor/wp-cli/wp-cli/php/utils.php ${BEHAT_DIR}/bootstrap
	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/steps/given.php ${BEHAT_DIR}/steps
	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/steps/when.php ${BEHAT_DIR}/steps
	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/steps/then.php ${BEHAT_DIR}/steps
	cp $PKG_DIR/vendor/wp-cli/wp-cli/features/extra/no-mail.php ${BEHAT_DIR}/extra
}

install_db() {
	mysql -h 127.0.0.1 -e 'CREATE DATABASE IF NOT EXISTS wp_cli_test; GRANT ALL PRIVILEGES ON wp_cli_test.* TO "wp_cli_test" IDENTIFIED BY "password1"' -uroot -p
}

install_composer_packages
install_tests_includes
install_db
