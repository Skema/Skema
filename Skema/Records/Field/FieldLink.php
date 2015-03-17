<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:06 PM
 */

namespace Skema\Records\Field;

use Skema;
use Skema\Set;
use R;

class FieldLink extends Base {

	public $linkedSetId;
	public $linkedFieldId;

	/**
	 * @param Set $linkedSet
	 * @param Skema\Records\Field\* $linkedField
	 * @returns $this
	 */
	public function link(Set $linkedSet, $linkedField)
	{
		if ($this->bean === null) {
			$this->newBean();
		}

		$bean = $this->bean;
		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();
		$bean->{$this->_('linkedFieldId')} = $linkedField->getBean($linkedSet)->getID();

		R::store($bean);
		return $this;
	}

	public function getOptions()
	{
		if ($this->bean === null) return [];

		$result = [];
		$set = Set::byID($this->bean->{$this->_('linkedSetId')});
		$field = self::byID($this->bean->{$this->_('linkedFieldId')});
		$set->each(function($record) use (&$result, $field) {
			$result[] = $record[$field->cleanName];
		});

		return $result;
	}
}