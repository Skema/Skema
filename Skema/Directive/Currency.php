<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Directive;

use Skema\Records\Field;

class Currency extends Base {

	public function __construct(Field\Currency $field) {
		$this->field = $field;
	}

	public function renderHTML()
	{
		$field = $this->field;
		return $field->bean->{$field->_('symbol')} . ($this->value * 1);
	}
}