<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:51 PM
 */

namespace Skema\Records\Field;

use Skema\Set;
use R;

class DropDown extends Base {

	public function newBean()
	{
		$bean = parent::newBean();

		if (empty($bean->{$this->_('options')})) {
			$bean->{$this->_('options')} = '';
		}
		return $bean;
	}


	public function setOptions($options)
	{
		$bean = $this->getBean();

		if ($bean === null) {
			$bean = $this->newBean();
		}

		$bean->{$this->_('options')} = implode(',', $options);

		R::store($bean);
		return $this;
	}

	public function getOptions()
	{
		$bean = $this->getBean();
		if ($bean === null) return [];

		$result = [];
		$optionsRaw = $bean->{$this->_('options')};
		$options = explode(',', $optionsRaw);
		foreach ($options as $option) {
			$result[] = trim($option);
		}

		return $result;
	}
}