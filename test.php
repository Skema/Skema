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

$tf->test('DropDown List Inputs', function(Testify $tf) {

	(new Set('DropDown List'))
		->addField((new Field\DropDown('Which?'))
			->setOptions([1,2,3,4,5])
		)

		->addRecord([
			'which' => 5
		]);

	(new Set('DropDown List'))
		->eachHTMLInput(function($record) {
			//print_r($record);
		});
});

$tf->test('Field Link', function(Testify $tf) {

	(new Set('Color Set'))
		->addField(new Field\Text('Color Name'))
		->addField(new Field\Color('Color'))
		->addField(new Field\Text('Emotion'))
		->addField(new Field\Text('Meaning'))

		->addRecord([
			'colorname' => 'blue',
			'color' => 'blue',
			'emotion' => 'trust',
			'meaning' => 'depth & stability'
		])
		->addRecord([
			'colorname' => 'red',
			'color' => 'red',
			'emotion' => 'passion',
			'meaning' => 'energy'
		])
		->addRecord([
			'colorname' => 'orange',
			'color' => 'orange',
			'emotion' => 'joy',
			'meaning' => 'enthusiasm'
		]);

	(new Set('My Favorite Color'))
		->addField(new Field\Text('User'))
		->addField((new Field\FieldLink('Favorite Color'))
			->link(new Set('Color Set'), new Field\Color('Color'))
		)

		->addRecord([
			'user' => 'Charles',
			'favoritecolor' => 'red'
		])

		->getField('Favorite Color', function($field) {
			print_r($field->getOptions());
		});

	(new Set('My Favorite Color'))
		->eachHTMLInput(function($record) {
			//print_r($record);
		});
});

$tf->test('Record Link', function(Testify $tf) {

	(new Set('Color Set 2'))
		->addField(new Field\Text('Color Name 2'))
		->addField(new Field\Color('Color 2'))
		->addField(new Field\Text('Emotion 2'))
		->addField(new Field\Text('Meaning 2'))

		->addRecord([
			'colorname2' => 'blue',
			'color2' => 'blue',
			'emotion2' => 'trust',
			'meaning2' => 'depth & stability'
		])
		->addRecord([
			'colorname2' => 'red',
			'color2' => 'red',
			'emotion2' => 'passion',
			'meaning2' => 'energy'
		])
		->addRecord([
			'colorname2' => 'orange',
			'color2' => 'orange',
			'emotion2' => 'joy',
			'meaning2' => 'enthusiasm'
		]);

	(new Set('My Favorite Color 2'))
		->addField(new Field\Text('User 2'))
		->addField((new Field\RecordLink('Favorite Color 2'))
			->link(new Set('Color Set 2'))
		)

		->addRecord([
			'user2' => 'Charles',
			'favoritecolor2' => 2
		])

		->getField('Favorite Color 2', function($field) {
			//print_r($field->getOptions());
		});

	(new Set('My Favorite Color 2', true))
		->eachHTMLInput(function($array, $id) {
			echo $id . "\n";
			print_r($array);
		});
});
ob_start();
$tf();