<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Records\Field;


class Currency extends Base {

	public $symbol = '$';

	public function getBean()
	{

		$bean = parent::getBean();

		$bean->{$this->_('symbol')} = $this->symbol;
		return $bean;
	}
}