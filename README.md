# Skema
Data Modeling Platform

Create database tables along with their data types, values, inputs, outputs, json, & relationship with other tables all polymorphically.

Create table, fields, and first record
```php
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
```


Retrieve html inputs
```php
(new Set('Complete Address'))->eachHTMLInput(function($arrayOfInputs) {
	foreach ($arrayOfInputs as $input) {
		echo $input;
	}
});
```


Retrieve record and update
```php
$record = (new Set('Complete Address'))->getRecord(1);
$record->name = 'Former Township of Pelee';
$record->update();
```


Delete record
```php
(new Set('Complete Address'))->getRecord(1)->delete();
```