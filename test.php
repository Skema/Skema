<?php
require_once ('vendor/autoload.php');

use Skema\Set;
use Skema\Records\Field;
use Testify\Testify;

error_reporting(E_ALL);
ini_set( 'display_errors','1');

R::setup('mysql:host=localhost;dbname=skema', 'skema', 'skema');

$tf = new Testify('Skema Test Suite');

R::nuke();

$tf->test('Add simple record', function(Testify $tf) {
	$number = '1000';
	$date = time();

	(new Set('numbers'))
		->addField(new Field\Number('number'))
		->addField(new Field\DateTime('date'))

		->addRecord([
			'number' => $number,
			'date' => $date
		]);

	(new Set('numbers'))
		->eachHTML(function($record) use ($number, $date, $tf) {
			$tf->assertSame($record['number'], 1000, 'Numbers are correct and converted correctly');
			$tf->assertTrue(strstr($record['date'], getdate($date)['month']), 'Month name is in returned string, which means it parsed correctly');
		});
});


$tf->test('Name conversion', function(Testify $tf) {
	(new Set('version control'))
		->addField(new Field\Number('version number'))

		->addRecord([
			'versionnumber' => 1
		]);

	(new Set('version control'))
		->eachHTML(function($record) use ($tf) {
			$tf->assertEquals($record['versionnumber'], 1, 'Name was correctly converted for keys');
		});
});


$tf->test('Currency', function(Testify $tf) {

	$number = 12389732498723;
	(new Set('Currency'))
		->addField(new Field\Currency('Balance'))

		->addRecord([
			'balance' => $number
		]);

	(new Set('Currency'))
		->eachHTML(function($record) use($number, $tf) {
			$tf->assertEquals($record['balance']{0}, '$', 'Dollar sign is on front');
			$tf->assertEquals($record['balance'], '$' . $number, 'Formatted correctly');
		});
});

$tf->test('Multi Records', function(Testify $tf) {
	$i = 0;
	$text1 = 'This is some text 1';
	$text2 = 'This is some text 2';

	(new Set('Text'))
		->addField(new Field\Text('Text'))

		->addRecord([
			'text' => $text1
		])
		->addRecord([
			'text' => $text2
		]);

	(new Set('Text'))
		->eachHTML(function($record) use (&$i, $text1, $text2, $tf) {
			switch($i++) {
				case 0:
					$tf->assertSame(0, 0, 'Index was 0 when expected');
					$tf->assertSame($record['text'], $text1, 'Text is correct');
					break;
				case 1:
					$tf->assertSame(1, 1, 'Index was 1 when expected');
					$tf->assertSame($record['text'], $text2, 'Text is correct');
					break;
			}
		});

	$tf->assertSame($i, 2, 'Index was 2 at end');
});

$tf->test('Longitude and latitude', function(Testify $tf) {
	$longitude = -81.581211;
	$latitude = 28.418749;

	(new Set('Test Coordinates'))
		->addField(new Field\Longitude('A Location Longitude'))
		->addField(new Field\Latitude('A Location Latitude'))

		->addRecord([
			'alocationlongitude' => $longitude,
			'alocationlatitude' => $latitude
		]);

	(new Set('Test Coordinates'))
		->eachHTML(function($record) use ($longitude, $latitude, $tf) {
			$tf->assertEquals($record['alocationlongitude'], $longitude . '&deg;', 'Correctly rendered longitude');
			$tf->assertEquals($record['alocationlatitude'], $latitude . '&deg;', 'Correctly rendered latitude');
		});
});

$tf->test('Complete Address', function(Testify $tf) {

	(new Set('Complete Address'))
		->addField(new Field\Text('Name'))
		->addField(new Field\StreetAddress('Address1'))
		->addField(new Field\StreetAddress('Address2'))
		->addField(new Field\City('City'))
		->addField(new Field\Province('State'))
		->addField(new Field\Country('Country'))
		->addField(new Field\Zip('Zip'))

		->addRecord([
			'name' => 'Township of Pelee',
			'address1' => '1045 WEST SHORE RD',
			'address2' => '',
			'city' => 'Pelee Island',
			'state' => 'ON',
			'country' => 'Canada',
			'zip' => 'N0R 1M0'
		]);

	(new Set('Complete Address'))
		->eachHTML(function($record) use ($tf) {
			$tf->assertEquals($record['name'], 'Township of Pelee', 'name correct');
			$tf->assertEquals($record['address1'], '1045 WEST SHORE RD', 'address1 correct');
			$tf->assertEquals($record['address2'], '', 'address1 correct');
			$tf->assertEquals($record['city'], 'Pelee Island', 'city correct');
			$tf->assertEquals($record['state'], 'ON', 'state correct');
			$tf->assertEquals($record['country'], 'Canada', 'country correct');
			$tf->assertEquals($record['zip'], 'N0R 1M0', 'zip correct');
		});
});

$tf->test('Markup (wikiLingo)', function(Testify $tf) {

	(new Set('Markup (wikiLingo)'))
		->addField(new Field\Markup('My Markup'))

		->addRecord([
			'mymarkup' => '__hello world!__'
		]);

	(new Set('Markup (wikiLingo)'))
		->eachHTML(function($record) use ($tf) {
			$tf->assertEquals($record['mymarkup'], '<strong>hello world!</strong>', 'html is correct');
		});
});

ob_start();
$tf();