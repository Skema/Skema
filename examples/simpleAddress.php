<?php

use Skema\Set;
use Skema\Field;

(new Set('Complete Address'))
	->addField(new Field\Text('Name'))
	->addField(new Field\StreetAddress('Address1'))
	->addField(new Field\StreetAddress('Address2'))
	->addField(new Field\City('City'))
	->addField(new Field\Province('State'))
	->addField(new Field\Country('Country'))
	->addField(new Field\Zip('Zip'));