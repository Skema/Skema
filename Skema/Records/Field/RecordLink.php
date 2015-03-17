<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Records\Field;

use Skema\Records\Record;
use Skema\Set;
use R;

class RecordLink extends Base {

	/**
	 * @param Set $linkedSet
	 * @param Record $linkedRecord
	 * @returns $this
	 */
	public function link(Set $linkedSet, Record $linkedRecord)
	{
		if ($this->bean === null) {
			$this->newBean();
		}

		$bean = $this->bean;
		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();
		$bean->{$this->_('linkedRecordId')} = $linkedRecord->getBean()->getID();

		R::store($bean);
		return $this;
	}

	public function getOptions()
	{
		if ($this->bean === null) return [];

		$set = Set::byID($this->bean->{$this->_('linkedSetId')});
		$record = $set->getRecord($this->bean->{$this->_('linkedRecordId')});

		return $record->values;
	}

}