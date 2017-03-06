<?php

namespace WP_CLI\BR;

use WP_CLI;

class Backup_Command extends \WP_CLI_Command {
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
		load_plugins_api();

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

	/**
	 * Backup WordPress core
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp backup core > core.json
	 *
	 * @when after_wp_load
	 */
	function core() {
		$backup = [
			"version" => WP_CLI::runcommand( "core version", [ "return" => true ] ),
			"locales" => get_locales_list(),
			"path"    => relative_path( $_SERVER["PWD"], $_SERVER['DOCUMENT_ROOT'] )
		];

		WP_CLI::log( json_encode( $backup, JSON_PRETTY_PRINT ) );
	}
}
