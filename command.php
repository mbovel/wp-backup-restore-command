<?php

if ( ! class_exists( 'WP_CLI' ) ) {
	return;
}

require_once __DIR__ . "/php/functions.php";
require_once __DIR__ . "/php/class-Plugin.php";
require_once __DIR__ . "/php/class-Command.php";
require_once __DIR__ . "/php/class-Backup_Command.php";
require_once __DIR__ . "/php/class-Restore_Command.php";

WP_CLI::add_command( 'backup', 'WP_CLI\BR\Backup_Command' );
WP_CLI::add_command( 'restore', 'WP_CLI\BR\Restore_Command' );
