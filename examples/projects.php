<?php

use Skema\Set;
use Skema\Field;

(new Set('Projects'))
	->addField((new Field\RecordLink('Client'))
		->setPrerequisite(true)
		->link(new Set('Clients')))
	->addField(new Field\Text('Tasks'))
	->addField((new Field\RecordLink('Project Manager'))
		->link(new Set('Project Managers')))
	->addField(new Field\FieldLink('Job #'))
	->addField(new Field\Currency('Income'))
	->addField(new Field\Currency('Expense'))
	->addField(new Field\DateTime('Start Date'))
	->addField(new Field\DateTime('Completion Date'))
	->addField((new Field\RecordLink('Sub Project'))
		->link((new Set('Projects'))));


(new Set('Project Installers'))
	->addField((new Field\RecordLink('Project'))
		->link(new Set('Projects')))
	->addField((new Field\RecordLink('Installer'))
		->link(new Set('Installers')));


(new Set('Estimates'))
	->addField((new Field\RecordLink('Project'))
		->setPrerequisite(true)
		->link(new Set('Projects')))
	->addField((new Field\RecordLink('Client'))
		->setPrerequisite(true)
		->link(new Set('Clients')))
	->addField(new Field\Text('Item'))
	->addField((new Field\RecordLink('Requester'))
		->link(new Set('Client Contacts')))
	->addField(new Field\Checkbox('Create Field for Installer'))
	->addField(new Field\Markup('Description'));


(new Set('Project Managers'))
	->addField(new Field\User('User'))
	->addField(new Field\Text('First Name'))
	->addField(new Field\Text('Last Name'))
	->addField((new Field\RecordLink('Contact'))
		->link(new Set('Contacts')));


(new Set('Installers'))
	->addField(new Field\User('User'))
	->addField(new Field\Text('First Name'))
	->addField(new Field\Text('Last Name'))
	->addField((new Field\RecordLink('Contact'))
		->link(new Set('Contacts')));


(new Set('Clients'))
	->addField(new Field\Text('Name'))
	->addField(new Field\Text('Client'));


(new Set('Client Contacts'))
	->addField((new Field\RecordLink('Client'))
		->setPrerequisite(true)
		->link(new Set('Clients')))
	->addField((new Field\RecordLink('Contact'))
		->setPrerequisite(true)
		->link(new Set('Contacts')))
	->addField(new Field\Text('First Name'))
	->addField(new Field\Text('Last Name'));


(new Set('Contacts'))
	->addField(new Field\Text('Type'))
	->addField(new Field\StreetAddress('Address 1'))
	->addField(new Field\StreetAddress('Address 2'))
	->addField(new Field\City('City'))
	->addField(new Field\Province('State'))
	->addField(new Field\Country('Country'))
	->addField(new Field\Zip('Zip'))
	->addField(new Field\Longitude('Longitude'))
	->addField(new Field\Longitude('Latitude'));


(new Set('Phone Numbers'))
	->addField(new Field\Text('Type'))
	->addField(new Field\Phone('Phone'))
	->addField((new Field\RecordLink('Contact'))
		->setPrerequisite(true)
		->link(new Set('Contacts')));


(new Set('Email Addresses'))
	->addField(new Field\Text('Type'))
	->addField(new Field\Email('Email'))
	->addField((new Field\RecordLink('Contact'))
		->setPrerequisite(true)
		->link(new Set('Contacts')));