<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:51 PM
 */

namespace Skema\Directive;


class DateTime extends Base {

	public function render()
	{
		return date('l jS \of F Y h:i:s A', $this->value);
	}
}