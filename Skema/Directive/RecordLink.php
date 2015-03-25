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
use Skema\Records\Field;
use Skema\Utility;

class RecordLink extends Base {

	/**
	 * @var Field\RecordLink
	 */
	public $field;

	public function renderHTML()
	{
		return $this->value;
	}

	public function renderHTMLInput()
	{

		$template = $this->field->htmlInputTemplate;
		$options = '';

		foreach( $this->field->getOptions() as $key => $values) {
			$optionText = Utility::mustachify($template, function($match) use ($key, $values) {
				if (isset($values[$match])) {
					return $values[$match];
				} else if ($match === 'key') {
					return $key;
				}
				return '';
			});

			$optionTextEncoded = htmlspecialchars($optionText);
			$selected = ($this->value == $key ? 'selected' : '');
			$options .= "<option value='{$key}' $selected>$optionTextEncoded</option>";
		}

		$key = $this->key();
		return "<select name='{$key}'>$options</select>";
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