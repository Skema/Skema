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
	public $bean = null;

	/**
	 * @param {String} $name
	 * @param $bean
	 */
	public function __construct($name, $bean = null)
	{
		$this->name = $name;
		$this->cleanBaseName = Utility::cleanTableName($name);
		$this->cleanName = 'skemaset' . $this->cleanBaseName;

		if ($bean === null) {
			$this->getBean();
		} else {
			$this->bean = $bean;
		}
	}

	public static function byID($id)
	{
		$bean = R::findOne('skemaset', ' id = ? ', [ $id ]);

		return new self($bean->name, $bean);
	}

	public function getBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::findOne('skemaset', ' name = ? ', [ $this->name ]);

		if (empty($bean)) {
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

	public function each($fn, $fieldFn = '', $useUncleanKeys = false)
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

				$record[$useUncleanKeys ? $field->name : $key] = $value;
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

	public function eachHTMLInput($fn) {
		$this->each($fn, function($directive, $value, $bean) {
			$directive->setValue($value, $bean);

			$result = $directive->renderHTMLInput();

			return $result;
		}, true);
	}

	public function eachJSON($fn)
	{
		$this->each($fn, function($directive, $value, $bean) {
			$directive->setValue($value, $bean);

			$result = $directive->renderJSON();

			return $result;
		});
	}

	public function getField($fieldName, $fn)
	{
		$fieldBean = R::findOne('skemafield', ' name = ? and skemaset_id = ? ', [$fieldName, $this->bean->getID()]);
		if (!empty($fieldBean)) {
			$fn(new $fieldBean->type($fieldBean->name, $fieldBean, $this));
		}
	}

	public function getRecord($id, $useUncleanKeys = false)
	{
		$recordBean = R::findOne('skemarecord' . $this->cleanBaseName, ' id = ? ', [ $id ]);
		$values = [];
		foreach($recordBean as $key => $value) {
			$values[$key] = $value;
		}
		return new Record($values, $this, $recordBean);
	}
}