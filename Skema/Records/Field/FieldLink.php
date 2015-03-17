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
use Skema\Records\Record;
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
		$bean = $this->getBean();
		if ($bean === null) {
			$bean = $this->newBean();
		}

		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();
		$bean->{$this->_('linkedFieldId')} = $linkedField->getBean($linkedSet)->getID();

		R::store($bean);
		return $this;
	}

	public function getOptions()
	{
		$bean = $this->getBean();
		if ($bean === null) return [];

		$result = [];
		$set = Set::byID($bean->{$this->_('linkedSetId')});
		$field = self::byID($bean->{$this->_('linkedFieldId')}, $set);
		$set->each(function(Record $record) use (&$result, $field) {
			$result[] = $record->fieldDirective($field)->value;
		});

		return $result;
	}
}