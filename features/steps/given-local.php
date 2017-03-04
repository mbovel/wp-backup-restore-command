<?php

use Behat\Gherkin\Node\PyStringNode;

$steps->Given( '/^the following setup:$/',
	function( FeatureContext $world, PyStringNode $script ) {
		$world->variables["SUITE_CACHE_DIR"] = get_wp_cli_test_cache_dir();

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

function get_wp_cli_test_cache_dir() {
	return sys_get_temp_dir() . '/wp-cli-test/cache-dir';
}
