<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Field;

use Skema;
use Skema\Set;

class Currency extends Base {

	public $symbol = '$';

	public function newBean()
	{
		$bean = parent::newBean();

		$bean->{$this->_('symbol')} = $this->symbol;

		return $bean;
	}

	public function getBean(Set $set = null)
	{
		$bean = parent::getBean($set);
		$this->symbol = $bean->{$this->_('symbol')};

		return $bean;
	}
}