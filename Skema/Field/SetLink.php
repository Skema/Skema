<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Field;

use RedBeanPHP;
use Skema\Set;

class SetLink extends Base {
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

		RedBeanPHP::store($bean);
		return $this;
	}

	public function getOptions()
	{
		$sets = [];

		foreach(RedBeanPHP::findAll('skemaset') as $setBean) {
			$sets[$setBean->getID()] = $setBean->name;
		}

		return $sets;
	}
}