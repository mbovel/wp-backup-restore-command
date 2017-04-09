<?php

namespace WP_CLI\BR;

use WP_CLI,
	WP_CLI\Utils;

class Restore_Command extends \WP_CLI_Command {
	/**
	 * Restore plugins previously backed up with `wp backup plugins`.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp restore plugins < plugins.json
	 *
	 * @when after_wp_load
	 */
	function plugins() {
		$plugins_to_impose = json_decode( file_get_contents( "php://stdin" ) );

		foreach ( $plugins_to_impose as &$plugin ) {
			$plugin = Plugin::fromJSON( $plugin );
		}

		/** @var Plugin[] $plugins_to_impose */

		foreach ( get_plugins() as $path => $info ) {
			$plugin = Plugin::fromWP( $path, $info );

			if ( $plugin != $plugins_to_impose[ $path ] ) {
				$plugin->uninstall();
			}
		}

		foreach ( $plugins_to_impose as $plugin ) {
			$plugin->install();
		}
	}

	/**
	 * Restore a WordPress database previously backed up with `wp backup db`.
	 *
	 * ## OPTIONS
	 *
	 * [--siteurl=<url>]
	 * : the site url (where the WordPress core files reside).
	 *
	 * [--homeurl=<url>]
	 * : the home url.
	 *
	 * ## EXAMPLES
	 *
	 *     # Different siteurl and homeurl parameters
	 *     $ wp restore db --siteurl=http://example.com/wp --homeurl=http://example.com < db.sql
	 *
	 *     # Same URL for siteurl and homeurl
	 *     $ wp restore db --url=http://example.com < db.sql
	 *
	 *     # WP_URL and WP_HOME defined in wp-config.php
	 *     $ wp restore db < db.sql
	 *
	 * @when before_wp_load
	 */
	function db( $args, $assoc_args ) {
		load_wp_urls_constants( $assoc_args );

		$dump = db_restore_replace( file_get_contents( "php://stdin" ) );

		// Because there is no way to pass a custom stdin to WP_CLI::runcommand()
		require( ABSPATH . WPINC . '/formatting.php' );
		$tmp_file = get_temp_dir() . "tmp.sql";
		file_put_contents( $tmp_file, $dump );

		$cmd = Utils\esc_cmd( "db import %s", $tmp_file );
		WP_CLI::runcommand( $cmd );
	}

	/**
	 * Restore WordPress core previously backed up with `wp backup core`.
	 *
	 * ## EXAMPLES
	 *
	 *     $ wp restore core < core.json
	 *
	 * @when before_wp_load
	 */
	function core() {
		$core = json_decode( file_get_contents( "php://stdin" ) );

		$cmd = Utils\esc_cmd( "core download --version=%s --path=%s --force", $core->version, $core->path );

		WP_CLI::runcommand( $cmd );
	}
}
