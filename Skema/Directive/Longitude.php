<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:57 PM
 */

namespace Skema\Directive;

use Skema\Records\Field;

class Longitude extends Base {

	public function renderHTML()
	{
		return $this->value . '&deg;';
	}
}