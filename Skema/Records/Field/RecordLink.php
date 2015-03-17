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
	 * @param number $linkedRecordId
	 * @returns $this
	 */
	public function link(Set $linkedSet, $linkedRecordId)
	{
		$bean = $this->getBean();
		if ($bean === null) {
			$bean = $this->newBean();
		}

		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();
		$bean->{$this->_('linkedRecordId')} = $linkedRecordId;

		R::store($bean);
		return $this;
	}

	public function getOptions(Set $set)
	{
		$records = [];
		foreach($set->getBean()->{'ownSkemarecord' . $set->cleanBaseName . 'List'} as $recordBean) {
			$records[] = new Record($set->directives, $set, $recordBean, $set->useUncleanKeys);
		}

		return $records;
	}

}