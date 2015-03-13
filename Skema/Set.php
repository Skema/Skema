<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/11/15
 * Time: 11:33 PM
 */

namespace Skema;

use R;
use Skema\Records\Field\Base;
use Skema\Records\Record;

class Set
{
	public $name;
	public $cleanName;
	public $description;
	public $fields = [];

	/**
	 * @param {String} $name
	 */
	public function __construct($name)
	{
		$this->name = $name;
		$this->cleanBaseName = Utility::cleanTableName($name);
		$this->cleanName = 'skemaset' . $this->cleanBaseName;

		$this->getBean();
	}

	public function getBean()
	{
		$bean = R::findOne('skemaset', ' name = ? ', [ $this->name ]);

		if ($bean === null) {
			$bean = R::dispense('skemaset');
			$bean->name = $this->name;
			$bean->created = R::isoDateTime();
			$bean->description = '';
			$bean->ownFieldList;
			$bean->{'ownSkemarecord' . $this->cleanBaseName . 'List'};
		}

		return $this->bean = $bean;
	}

	/**
	 * @param Base $field
	 * @return $this
	 */
	public function addField($field)
	{
		$field->addToSet($this);

		return $this;
	}

	public function addRecord($array) {
		$record = new Record($array, $this);

		$record->add($this->getBean());

		return $this;
	}

	public function each($fn, $fieldFn = '')
	{
		$setBean = $this->getBean();
		$fields = [];
		$directives = [];

		foreach($setBean->ownSkemafieldList as $fieldBean) {
			$fields[$fieldBean->cleanName] = $field = new $fieldBean->type($fieldBean->name, $fieldBean);
			$directives[$fieldBean->cleanName] = $field->getDirective();
		}

		foreach($this->getBean()->{'ownSkemarecord' . $this->cleanBaseName . 'List'} as $id => $recordBean) {
			foreach($fields as $key => $field) {
				if ($fieldFn === '') {
					$value = $recordBean->{$key};
				}

				else {
					$value = $fieldFn($directives[$key], $recordBean->{$key}, $recordBean);
				}

				$record[$key] = $value;
			}

			$fn($record);
		}

		return $this;
	}

	public function eachHTML($fn)
	{
		$this->each($fn, function($directive, $value, $bean) {
			$directive->setValue($value, $bean);

			$result = $directive->renderHTML();

			return $result;
		});
	}

	public function eachJSON($fn)
	{
		$this->each($fn, function($directive, $value, $bean) {
			$directive->setValue($value, $bean);

			$result = $directive->renderJSON();

			return $result;
		});
	}
}