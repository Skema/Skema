<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:50 PM
 */

namespace Skema\Directive;


class Checkbox extends Base {
	public function renderHTMLInput()
	{
		$input = '';

		$checkedValue = $this->field->getCheckedValue();
		$valueUrlSafe = urlencode($checkedValue);
		//$valueHtmlSave = htmlspecialchars($checkedValue);
		$key = $this->key();
		if ($checkedValue === $this->value) {
			$input .= "<input name='$key' type='checkbox' checked value='$valueUrlSafe' />";
		} else {
			$input .= "<input name='$key' type='checkbox' value='$valueUrlSafe' />";
		}


		return $input;
	}
}