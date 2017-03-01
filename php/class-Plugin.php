<?php

namespace WP_CLI\BR;

use WP_CLI,
	WP_CLI\Utils;

class Plugin {
	/**
	 * @var String
	 */
	public $name;

	/**
	 * @var String
	 */
	public $path;

	/**
	 * @var String
	 */
	public $version;

	/**
	 * @var bool
	 */
	public $active;

	/**
	 * @var String
	 */
	public $source;

	/**
	 * @var String
	 */
	public $handle;

	public function install() {
		switch ( $this->source ) {
			case "wordpress.org":
				$assoc_args = \WP_CLI\Utils\assoc_args_to_str( [
					"activate" => (bool) $this->active,
					"version"  => (string) $this->version
				] );

				$cmd = Utils\esc_cmd( "plugin install %s", $this->handle ) . " $assoc_args";

				WP_CLI::runcommand( $cmd );
				break;
			default:
				WP_CLI::warning( "can not automatically restore plugin {$this->name}." );
				break;
		}
	}

	public function uninstall() {
		$wp_cli_handle = \WP_CLI\Utils\get_plugin_name( $this->path );

		$cmd = Utils\esc_cmd( "plugin uninstall %s", $wp_cli_handle );
		WP_CLI::runcommand( $cmd );
	}

	static public function fromWP( $path, $info ) {
		$plugin = new Plugin();

		$plugin->name    = $info["Name"];
		$plugin->path    = $path;
		$plugin->version = $info["Version"];
		$plugin->active  = is_plugin_active( $path );
		$plugin->source  = null;
		$plugin->handle  = get_plugin_wp_org_handle( $path );

		if ( $plugin->handle !== null ) {
			$plugin->source = "wordpress.org";
		}

		if ( $plugin->source === null ) {
			WP_CLI::warning( "`wp restore` will not be able to automatically restore plugin {$plugin->name}." );
		}

		return $plugin;
	}

	static public function fromJSON( $info ) {
		$plugin = new Plugin();

		foreach ( $info as $key => $value ) {
			if ( property_exists( self::class, $key ) ) {
				$plugin->$key = $value;
			}
		}

		return $plugin;
	}
}
