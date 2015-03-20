<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Directive;

use Skema\Type;
use Skema\Set;

class RecordLink extends Base {
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
		$field = Type::Field($this->field);
		if (!empty($this->value)) {
			$fieldBean = $field->getBean();
			$linkedSetId = $fieldBean->{$field->_('linkedSetId')};

			if (!empty($linkedSetId)) {
				Set::byID($linkedSetId)->getRecord($this->value);
				//TODO: implement
				throw new \Exception('Not yet implemented');
			}
		}
		return '';
	}
}