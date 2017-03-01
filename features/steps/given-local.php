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
