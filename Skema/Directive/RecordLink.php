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
use Skema\Field;
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

		$this->field->eachOption(function($directives, $recordID) use (&$options, $template) {
			$optionText = Utility::mustachify($template, function($match) use ($directives, $recordID) {
				if (isset($directives[$match])) {
					return Type::Directive($directives[$match])->renderPlain();
				} else if ($match === 'key') {
					return $recordID;
				}
				return '';
			});

			$optionTextEncoded = htmlentities($optionText);
			$selected = ($this->value == $recordID ? ' selected="selected"' : '');
			$options .= "<option value='{$recordID}'$selected>$optionTextEncoded</option>";
		});

		$key = $this->key();
		return "<select name='{$key}'>$options</select>";
	}

	public function renderJSON()
	{
		$field = Type::Field($this->field);
        $result = [];
		if (!empty($this->value)) {
			$fieldBean = $field->getBean();
			$linkedSetId = $fieldBean->{$field->_('linkedSetId')};

			if (!empty($linkedSetId)) {
				$record = Set::byID($linkedSetId)->getRecord($this->value);
				//TODO: implement
				throw new \Exception('Not yet implemented');
			}
		}
		return '';
	}
}