<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:07 PM
 */

namespace Skema\Directive;


class Number extends Base {

	public function renderHTML()
	{
		return $this->value * 1;
	}

	public function renderJSON()
	{
		return $this->value * 1;
	}
}