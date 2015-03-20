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
	 * @returns $this
	 */
	public function link(Set $linkedSet)
	{
		$bean = $this->getBean();
		if ($bean === null) {
			$bean = $this->newBean();
		}

		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();

		R::store($bean);
		return $this;
	}

	public function getOptions()
	{
		$records = [];
		$linkedSetId = $this->getBean()->{$this->_('linkedSetId')};

		if (isset($linkedSetId)) {
			$set = Set::byID($linkedSetId, $this->set->useUncleanKeys);
			foreach($set->getBean()->{'ownSkemarecord' . $set->cleanBaseName . 'List'} as $id => $recordBean) {
				$records[] = new Record($set->directives, $set, $recordBean, $set->useUncleanKeys);
			}
		}

		return $records;
	}

}