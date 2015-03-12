<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Records\Field;


class Currency extends Base {

	public $currencyType = '$';

	public function getBean()
	{
		$bean = parent::getBean();
		$bean->currencyType = $this->currencyType;
		return $bean;
	}
}