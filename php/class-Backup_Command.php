<?php

namespace WP_CLI\BR;

use WP_CLI;

class Backup_Command extends Command {
	/**
	 * Backup plugins
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp backup plugins > plugins.json
	 *
	 * @when after_wp_load
	 */
	function plugins() {
		$backup = [];

		foreach ( get_plugins() as $path => $info ) {
			$plugin          = Plugin::fromWP( $path, $info );
			$backup[ $path ] = $plugin;
		}

		WP_CLI::log( json_encode( $backup, JSON_PRETTY_PRINT ) );
	}

	/**
	 * Backup database
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp backup db > db.sql
	 *
	 * @when after_wp_load
	 */
	function db() {
		WP_CLI::runcommand( "transient delete --all", [ "return" => true ] );
		$result = WP_CLI::runcommand( "db export -", [ "return" => true ] );
		WP_CLI::log( db_backup_replace( $result ) );
	}
}
