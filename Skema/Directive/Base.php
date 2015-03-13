<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Directive;

use Skema\Utility;
use R;
use Skema;

abstract class Base
{
	public $field;
	public $value;
	public $bean;

	public function __construct($field) {
		$this->field = $field;
	}

	public function setValue($value, $bean)
	{
		$this->value = $value;
		$this->bean = $bean;
	}

	public function renderHTML()
	{
		return $this->value;
	}

	public function renderJSON()
	{
		return $this->value;
	}
}