<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:06 PM
 */

namespace Skema\Field;

use Skema;
use Skema\Set;
use Skema\Record;
use R;

class FieldLink extends Base {

	public $linkedSetId;
	public $linkedFieldId;

	/**
	 * @param Set $linkedSet
	 * @param Skema\Field\* $linkedField
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
		$set = Set::byID($bean->{$this->_('linkedSetId')}, $this->set->keyType);
		$field = self::byID($bean->{$this->_('linkedFieldId')}, $set);
		$set->each(function($values, $recordID, $set, $recordBean, $keyType) use (&$result, $field) {
			switch ($keyType) {
				default:
				case Set::$keysID:
					$key = $field->getBean()->getID();
					break;
				case Set::$keysClean:
					$key = $field->cleanName;
					break;
				case Set::$keysDirty:
					$key = $field->name;
					break;
			}

			$result[] = $values[$key]->value;
		});

		return $result;
	}
}