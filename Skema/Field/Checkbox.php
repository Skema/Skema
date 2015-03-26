<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Field;

use RedBeanPHP\R;

class Checkbox extends Base {

	public $checkedValue = '';

	public function newBean()
	{
		$bean = parent::newBean();

		if (empty($bean->{$this->_('checkedValue')})) {
			$bean->{$this->_('checkedValue')} = '';
		}
		return $bean;
	}


	public function setCheckedValue($checkedValue)
	{
		if ($this->bean === null) {
			$this->newBean();
		}

		$bean = $this->bean;
		$bean->{$this->_('checkedValue')} = $checkedValue;

		R::store($bean);
		return $this;
	}

	public function getCheckedValue()
	{
		if ($this->bean === null) return [];

		$result = $this->bean->{$this->_('checkedValue')};

		return $result;
	}
}