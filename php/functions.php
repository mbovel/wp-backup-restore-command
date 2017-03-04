<?php

namespace WP_CLI\BR;

use WP_CLI,
	WP_CLI\Utils;

/***
 * Use the wordpress.org API to get plugin handle.
 *
 * WP-CLI get_plugin_name() is not reliable because a plugin folder and its
 * main file can have any names.
 *
 * @param String $path Path of the plugin main file (file containing header).
 *
 * @return WordPress.org plugin handle if found, or null otherwise.
 */
function get_plugin_wp_org_handle( $path ) {
	static $plugins_updates = null;

	if ( $plugins_updates === null ) {
		wp_update_plugins();
		$plugins_updates = get_site_transient( 'update_plugins' );
	}

	if ( isset( $plugins_updates->no_update[ $path ] ) ) {
		return $plugins_updates->no_update[ $path ]->slug;
	}
	if ( isset( $plugins_updates->response[ $path ] ) ) {
		return $plugins_updates->response[ $path ]->slug;
	}

	return null;
}

/***
 * Load a minimal part of WordPress so that URLs functions work.
 *
 * This enables functions like `site_url` or `content_url` to work even if WordPress
 * has not yet been installed.
 *
 * @param String[] $assoc_args Array of associative arguments passed to the command.
 */
function load_wp_urls_constants( $assoc_args ) {
	define( "SHORTINIT", true );
	define( 'WP_INSTALLING', true );
	define( 'WP_SETUP_CONFIG', true );

	require_once( Utils\locate_wp_config() );
	require( ABSPATH . WPINC . '/link-template.php' );

	add_filter( "pre_option_home", function() use ( $assoc_args ) {
		if ( ! empty( $assoc_args["homeurl"] ) ) {
			return $assoc_args["homeurl"];
		}

		if ( ! empty( WP_CLI::get_config()["url"] ) ) {
			return WP_CLI::get_config( "url" );
		}

		if ( defined( "WP_HOME" ) ) {
			return WP_HOME;
		}

		WP_CLI::error( "you must either provide --homeurl parameter, or set WP_HOME in wp-config.php." );
	} );

	add_filter( "pre_option_siteurl", function() use ( $assoc_args ) {
		if ( ! empty( $assoc_args["siteurl"] ) ) {
			return $assoc_args["siteurl"];
		}

		if ( ! empty( WP_CLI::get_config()["url"] ) ) {
			return WP_CLI::get_config( "url" );
		}

		if ( defined( "WP_URL" ) ) {
			return WP_URL;
		}

		WP_CLI::error( "you must either provide --siteurl parameter, or set WP_URL in wp-config.php." );
	} );

	wp_plugin_directory_constants();
}

/***
 * Array of expression to replace in db dump.
 *
 * @return array
 */
function get_db_replacements() {
	return [
		// Dump HOME_URL for homeurl option even if HOME_URL === SITE_URL
		"'home','HOME_URL" => "'home','" . home_url(),

		// Replace urls with named constants
		"UPLOADS_URL"      => wp_upload_dir()['baseurl'],
		"PLUGINS_URL"      => plugins_url(),
		"CONTENT_URL"      => content_url(),
		"SITE_URL"         => site_url(),
		"HOME_URL"         => home_url(),

		// Add line breaks for a more git-friendly dump.
		// Should maybe use --skip-extended-insert instead, which would
		// need a change in `wp export`.
		// See http://stackoverflow.com/q/15750535
		"),\n("               => "),("
	];
}

function db_backup_replace( $dump ) {
	$replacements = get_db_replacements();

	return str_replace(
		array_values( $replacements ),
		array_keys( $replacements ),
		$dump );
}

function db_restore_replace( $dump ) {
	$replacements = get_db_replacements();

	return str_replace(
		array_keys( $replacements ),
		array_values( $replacements ),
		$dump );
}
