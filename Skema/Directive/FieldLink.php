<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:06 PM
 */

namespace Skema\Directive;

class FieldLink extends Base {
	public function renderHTMLInput()
	{
		$options = '';

		foreach ($this->field->getOptions() as $option) {
			$valueUrlSafe = urlencode($option);
			$valueHtmlSave = htmlspecialchars($option);
			if ($option === $this->value) {
				$options .= "<option selected='selected' value='$valueUrlSafe'>$valueHtmlSave</option>";
			} else {
				$options .= "<option value='$valueUrlSafe'>$valueHtmlSave</option>";
			}
		}

		$key = $this->key();
		return "<select name='{$key}'>$options</select>";
	}
}