<?php
require_once ('vendor/autoload.php');

$tf = new \Testify\Testify('Skema Test Suite');

$tf->test('Name conversion', function() {
	$skema = new Skema\Definition('version control');
	$skema->replaceField(new Skema\Field\Number('version number'));

	$skema->newRecord([
		"version"
	]);
});