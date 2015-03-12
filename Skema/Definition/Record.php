<?php

namespace Skema\Definition;

use R;
use Skema\Utility;

class Record
{
	public $name;
	public $cleanName;
	public $beanTable = null;

	/**
	 * @var Field[]
	 */
	public $fields = [];
	public $properties = [];

	/**
	 * @param {String} $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->cleanName = Utility::cleanTableName($name);
		$this->bean = R::inspect('skema' . $this->cleanName);
	}

	/**
	 * @param Skema\Field\* $field
	 */
	public function replaceField($field)
	{
		$def = new Field($field);

		$this->fields[] = $def;
	}

	/**
	 * @param Record $record
	 */
	public function save($record)
	{
		$bean = R::dispense('skema_' . $this->cleanName);

		foreach($this->fields as $field) {
			$bean->{ $field->cleanName } = '';
		}
	}
}