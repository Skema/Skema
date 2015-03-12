<?php
require_once ('vendor/autoload.php');

use Skema\Set;
use Skema\Records\Field;

error_reporting(E_ALL);
ini_set( 'display_errors','1');

R::setup('mysql:host=localhost;dbname=skema', 'skema', 'skema');

$tf = new \Testify\Testify('Skema Test Suite');

R::nuke();

$tf->test('Add simple record', function() {

	$field1 = new Field\Number('number');
	$field2 = new Field\DateTime('date');

	(new Set('numbers'))

		->addField($field1)
		->addField($field2)

		->addRecord([
			$field1->cleanName => rand(0, 1000000000),
			$field2->cleanName => time()
		]);

	$set = new Set('numbers');

	$set->eachRendered(function($record) {
		print_r($record);
	});
});
/*
$tf->test('Name conversion', function() {
	$field = new Field\Number('version number');

	(new Set('version control'))
		->addField($field)

		->addRecord([
			$field->cleanName => 1
		]);
});
*/


$tf->test('Currency', function() {
	$field = new Field\Currency('Balance');

	(new Set('Currency'))
		->addField($field)

		->addRecord([
			$field->cleanName => 12389732498723
		]);

	(new Set('Currency'))
		->eachRendered(function($record) {
			print_r($record);
		});
});

$tf->test('Multi Records', function() {
	$field = new Field\Text('Text');

	(new Set('Text'))
		->addField($field)

		->addRecord([
			$field->cleanName => 'This is some text 1'
		])
		->addRecord([
			$field->cleanName => 'This is some text 2'
		]);

	(new Set('Text'))
		->eachRendered(function($record) {
			print_r($record);
		});
});

ob_start();
$tf();