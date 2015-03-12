<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:07 PM
 */

namespace Skema\Directive;


class Number extends Base {

	public function render()
	{
		return $this->value * 1;
	}
}