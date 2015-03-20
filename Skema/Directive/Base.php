<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Directive;

use Skema\Set;
use Skema\Utility;
use R;
use Skema;

abstract class Base
{
	public $field;
	public $value;
	public $set;

	public function __construct($field, Set $set) {
		$this->field = $field;
		$this->set = $set;
	}

	public function setValue($value)
	{
		$this->value = $value;

		return $this;
	}

	public function key()
	{
		$bean = $this->field->getBean($this->set);
		return $bean->cleanName . '[' . $bean->skemasetID . ']';
	}

	public function renderHTML()
	{
		return $this->value;
	}

	public function renderHTMLInput()
	{
		$key = $this->key();
		return "<input type='text' name='{$key}' value='{$this->value}'>";
	}

	public function renderJSON()
	{
		return $this->value;
	}
}