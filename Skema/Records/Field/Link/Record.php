<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Records\Field\Link;


use Skema\Records\Field\Base;
use Skema\Set;

class Record extends Base {

	public $leftField = null;
	public $rightField = null;

	public function addToSet(Set $set)
	{
		if ($this->leftField === null || $this->rightField === null) {
			throw new \Exception('Fields have not been configured');
		}
	}

}