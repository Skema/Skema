<?php
require_once 'vendor/autoload.php';

use Skema\Set;
use Skema\Records\Field;

(new Set('Projects'))
	->addField((new Field\RecordLink('Client'))
		->link(new Set('Clients')))
	->addField(new Field\Text('Tasks'))
	->addField((new Field\RecordLink('Project Manager'))
		->link(new Set('Project Managers')))
	->addField(new Field\FieldLink('Job #'))
	->addField(new Field\FieldLink('Installer'))
	->addField(new Field\Currency('Income'))
	->addField(new Field\Currency('Expense'))
	->addField(new Field\DateTime('Start Date'))
	->addField(new Field\DateTime('Completion Date'))
	->addField((new Field\RecordLink('Sub Project'))
		->link((new Set('Projects'))));

(new Set('Estimates'))
	->addField((new Field\RecordLink('Project'))
		->link(new Set('Projects')))
	->addField(new Field\Text('Item'))
	->addField((new Field\RecordLink('Requester'))
		->link(new Set('Client Contacts'), function() {
			/*TODO
				traversing?

				this set field 'Project'
				connect to parent set 'Projects'
				connect to field 'Client'
				connect to set 'Clients'
				select from set 'Client Contacts'
			*/
		}))
	->addField(new Field\Checkbox('Create Field for Installer'))
	->addField(new Field\Markup('Description'));


(new Set('Project Managers'))
	->addField(new Field\User('Project Manager'));

(new Set('Installers'))
	->addField(new Field\User('Installer'));

(new Set('Clients'))
	->addField(new Field\Text('Client'));

(new Set('Client Contacts'))
	->addField((new Field\RecordLink('Client'))
		->link(new Set('Clients')))
	->addField(new Field\Text('First Name'))
	->addField(new Field\Text('Last Name'));