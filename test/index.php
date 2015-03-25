<?php
require_once '../vendor/autoload.php';

use Skema\Set;
use Skema\Field;
use Testify\Testify;

error_reporting(E_ALL);
ini_set( 'display_errors','1');

R::setup('mysql:host=localhost;dbname=skema', 'skema', 'skema');

$tf = new Testify('Skema Test Suite');

R::nuke();

$tf->test('Add simple record', function(Testify $tf) {
	$number = '1000';
	$date = time();

	(new Set('numbers', Set::$keysClean))
		->addField(new Field\Number('number'))
		->addField(new Field\DateTime('date'))

		->addRecord([
			'number' => $number,
			'date' => $date
		]);

	(new Set('numbers', Set::$keysClean))
		->eachHTML(function($record) use ($number, $date, $tf) {
			$tf->assertSame($record['number'], 1000, 'Numbers are correct and converted correctly');
			$tf->assertTrue(strstr($record['date'], getdate($date)['month']), 'Month name is in returned string, which means it parsed correctly');
		});
});


$tf->test('Name conversion', function(Testify $tf) {
	(new Set('version control', Set::$keysClean))
		->addField(new Field\Number('version number'))

		->addRecord([
			'versionnumber' => 1
		]);

	(new Set('version control', Set::$keysClean))
		->eachHTML(function($record) use ($tf) {
			$tf->assertEquals($record['versionnumber'], 1, 'Name was correctly converted for keys');
		});
});


$tf->test('Currency', function(Testify $tf) {

	$number = 12389732498723;
	(new Set('Currency', Set::$keysClean))
		->addField(new Field\Currency('Balance'))

		->addRecord([
			'balance' => $number
		]);

	(new Set('Currency', Set::$keysClean))
		->eachHTML(function($record) use($number, $tf) {
			$tf->assertEquals($record['balance']{0}, '$', 'Dollar sign is on front');
			$tf->assertEquals($record['balance'], '$' . $number, 'Formatted correctly');
		});
});

$tf->test('Multi Records', function(Testify $tf) {
	$i = 0;
	$text1 = 'This is some text 1';
	$text2 = 'This is some text 2';

	(new Set('Text', Set::$keysClean))
		->addField(new Field\Text('Text'))

		->addRecord([
			'text' => $text1
		])
		->addRecord([
			'text' => $text2
		]);

	(new Set('Text', Set::$keysClean))
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

	require_once '../examples/longitudeLatitude.php';

	(new Set('Coordinates', Set::$keysClean))
		->addRecord([
			'longitude' => $longitude,
			'latitude' => $latitude
		]);

	(new Set('Coordinates', Set::$keysClean))
		->eachHTML(function($record) use ($longitude, $latitude, $tf) {
			$tf->assertEquals($record['longitude'], $longitude . '&deg;', 'Correctly rendered longitude');
			$tf->assertEquals($record['latitude'], $latitude . '&deg;', 'Correctly rendered latitude');
		});
});

$tf->test('Complete Address', function(Testify $tf) {

	require '../examples/simpleAddress.php';

	(new Set('Complete Address', Set::$keysClean))
		->addRecord([
			'name' => 'Township of Pelee',
			'address1' => '1045 WEST SHORE RD',
			'address2' => '',
			'city' => 'Pelee Island',
			'state' => 'ON',
			'country' => 'Canada',
			'zip' => 'N0R 1M0'
		]);

	(new Set('Complete Address', Set::$keysClean))
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

	require_once '../examples/wikiMarkup.php';

	(new Set('Markup (wikiLingo)', Set::$keysClean))
		->addRecord([
			'mymarkup' => '__hello world!__'
		]);

	(new Set('Markup (wikiLingo)', Set::$keysClean))
		->eachHTML(function($record) use ($tf) {
			$tf->assertEquals($record['mymarkup'], '<strong>hello world!</strong>', 'html is correct');
		});
});

$tf->test('DropDown List Inputs', function(Testify $tf) {

	(new Set('DropDown List', Set::$keysClean))
		->addField((new Field\DropDown('Which?'))
			->setOptions([1,2,3,4,5])
		)

		->addRecord([
			'which' => 5
		]);

	(new Set('DropDown List', Set::$keysClean))
		->allHTMLInputs(function($values) use ($tf) {
			$dom = new DOMDocument();
			$dom->loadHTML($values['which']);
			$options = $dom->getElementsByTagName('option');

			foreach($options as $i => $option) {
				switch ($i) {
					case 0:
						$tf->assertEquals($option->textContent, '1');
						$tf->assertNotEquals($option->getAttribute('selected'), 'selected');
						break;
					case 1:
						$tf->assertEquals($option->textContent, '2');
						$tf->assertNotEquals($option->getAttribute('selected'), 'selected');
						break;
					case 2:
						$tf->assertEquals($option->textContent, '3');
						$tf->assertNotEquals($option->getAttribute('selected'), 'selected');
						break;
					case 3:
						$tf->assertEquals($option->textContent, '4');
						$tf->assertNotEquals($option->getAttribute('selected'), 'selected');
						break;
					case 4:
						$tf->assertEquals($option->textContent, '5');
						$tf->assertEquals($option->getAttribute('selected'), 'selected');
						break;
				}
			}
		});
});

$tf->test('Field Link (colors)', function(Testify $tf) {

	(new Set('Color Set', Set::$keysClean))
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

	(new Set('My Favorite Color', Set::$keysClean))
		->addField(new Field\Text('User'))
		->addField((new Field\FieldLink('Favorite Color'))
			->link(new Set('Color Set'), new Field\Color('Color'))
		)

		->addRecord([
			'user' => 'Charles',
			'favoritecolor' => 'red'
		])

		->getField('favoritecolor', function(Field\FieldLink $field) use ($tf) {
			foreach($field->getOptions() as $key => $option) {
				switch ($key) {
					case 0:
						$tf->assertEquals($option, 'blue');
						break;
					case 1:
						$tf->assertEquals($option, 'red');
						break;
					case 2:
						$tf->assertEquals($option, 'orange');
						break;
				}
			}
		});

	(new Set('My Favorite Color', Set::$keysClean))
		->allHTMLInputs(function($values) use ($tf) {
			foreach($values as $key => $value) {
				switch ($key) {
					case 'user':
						$tf->assertEquals(strstr($value, 'Charles'), true, 'Correct value');
						break;
					case 'favoritecolor':
						$selectElement = new SimpleXMLElement($value);
						$tf->assertEquals($selectElement->option[1]['selected'] . '', 'selected', 'Correct item selected');
						$tf->assertEquals($selectElement->option[1] . '', 'red', 'Correct value selected');

						$tf->assertEquals($selectElement->option[0] . '', 'blue', 'Correct items in select element');
						$tf->assertEquals($selectElement->option[2] . '', 'orange', 'Correct items in select element');
						break;
				}
			}
		});
});

$tf->test('Record Link', function(Testify $tf) {

	(new Set('Color Set 2', Set::$keysClean))
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

	(new Set('My Favorite Color 2', Set::$keysClean))
		->addField(new Field\Text('User 2'))
		->addField((new Field\RecordLink('Favorite Color 2'))
			->setHtmlInputTemplate('{{emotion2}} / {{meaning2}}')
			->link(new Set('Color Set 2', Set::$keysClean))
		)

		->addRecord([
			'user2' => 'Charles',
			'favoritecolor2' => 2
		])

		->getField('favoritecolor2', function(Field\RecordLink $field) use ($tf) {
			$totalOptions = 0;
			$field->eachOption(function($directives) use (&$totalOptions) {
				switch ($directives['color2']->value) {
					case 'blue':
					case 'red':
					case 'orange':
						$totalOptions++;
				}
			});

			$tf->assertEquals($totalOptions, 3, 'Correct number of linked records');
		});

	$totalOptions = 0;
	(new Set('My Favorite Color 2', Set::$keysClean))
		->allHTMLInputs(function($array) use (&$totalOptions, $tf) {
			foreach($array as $key => $input) {
				switch ($key) {
					case 'user2':
						$tf->assertEquals(strstr($input, 'Charles'), true, 'Correct value');
						$totalOptions++;
						break;
					case 'favoritecolor2':
						$doc = new DOMDocument();
						$doc->loadHTML($input);
						$children = $doc->getElementsByTagName('option');
						foreach($children as $i => $child) {
							switch ($i) {
								case 0:
									$tf->assertEquals($child->textContent, 'trust / depth & stability', 'Correct items in select element');
									break;
								case 1:
									$tf->assertEquals($child->getAttribute('selected'), 'selected', 'Correct item selected');
									$tf->assertEquals($child->textContent, 'passion / energy', 'Correct value selected');
									break;
								case 2:
									$tf->assertEquals($child->textContent, 'joy / enthusiasm', 'Correct items in select element');
									break;
							}
						}
						$totalOptions++;
						break;
				}
			}
		});

	$tf->assertEquals($totalOptions, 2, 'Correct number of inputs');
});
ob_start();
$tf();