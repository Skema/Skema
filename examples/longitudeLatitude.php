<?php

use Skema\Set;
use Skema\Records\Field;

(new Set('Coordinates'))
	->addField(new Field\Longitude('Longitude'))
	->addField(new Field\Latitude('Latitude'));