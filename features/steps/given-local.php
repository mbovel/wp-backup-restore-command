<?php

use Behat\Gherkin\Node\PyStringNode;

$steps->Given( '/^the following setup:$/',
	function( FeatureContext $world, PyStringNode $script ) {
		foreach ( $script->getLines() as $line ) {
			$cmd    = escapeshellarg( $line );
			$result = $world->proc( "bash -c $cmd" )->run_check();
			echo "$ {$line}\n{$result->stdout}\n";
		}
	}
);

$steps->Given( '/^the file (.+)$/',
	function( FeatureContext $world, $name ) {
		$srcpath  = $world->replace_variables( "{SRC_DIR}/features/files/$name" );
		$destpath = $world->replace_variables( "{RUN_DIR}/$name" );
		file_put_contents( $destpath, file_get_contents( $srcpath ) );
	}
);

$steps->Given( '/^a WP install at version ([0-9.]+)$/',
	function( FeatureContext $world, $version ) {
		install_wp( $world, $version );
	}
);

$steps->Given( "/^a WP install at version ([0-9.]+) in '([^\s]+)'$/",
	function( FeatureContext $world, $version, $path ) {
		install_wp( $world, $version, $path );
	}
);

function install_wp( FeatureContext $world, $version, $path = '' ) {
	$world->create_db();
	$world->create_run_dir();
	$world->proc( "wp core download", compact( 'version', 'path' ) )->run_check();

	$world->create_config( $path );

	$install_args = [
		'url'            => 'http://example.com',
		'title'          => 'WP CLI Site',
		'admin_user'     => 'admin',
		'admin_email'    => 'admin@example.com',
		'admin_password' => 'password1'
	];

	$world->proc( 'wp core install', $install_args, $path )->run_check();
}
