<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Records\Field;

use Skema;

class Currency extends Base {

	public $symbol = '$';

	public function newBean()
	{
		$bean = parent::newBean();

		$bean->{$this->_('symbol')} = $this->symbol;

		return $bean;
	}
}