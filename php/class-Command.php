<?php

namespace WP_CLI\BR;

use WP_CLI;

class Command extends \WP_CLI_Command {
	public function __construct() {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
	}
}
