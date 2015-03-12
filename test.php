<?php
require_once ('vendor/autoload.php');

use Testify;
use Skema;

$tf = new Testify\Testify('Skema Test Suite');

$tf->test('Name conversion', function() {
	$set = new Skema\Set('version control');
	$set->replaceField(new Skema\Field\Number('version number'));

	$skema->newRecord([
		"version"
	]);
});