<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Directive;

use Skema\Field;
use Skema\Set;
use Skema\Utility;
use RedBeanPHP;
use Skema;

abstract class Base
{
	/**
	 * @var Field\Base
	 */
	public $field;
	public $value;
	public $set;
	public $recordID;

	public function __construct($field, Set $set) {
		$this->field = $field;
		$this->set = $set;
	}

	public function setValue($value, $recordID = 0)
	{
		$this->value = $value;
		$this->recordID = $recordID;

		return $this;
	}

	public function key()
	{
		$bean = $this->field->getBean($this->set);
		$set = $this->set;

		switch ($set->keyType) {
			default:
			case Set::$keysID:
				$setKey = $set->getBean()->getID();
				$fieldKey = $bean->getID();
				break;
			case Set::$keysClean:
				$setKey = $set->getBean()->cleanName;
				$fieldKey = $bean->cleanName;
				break;
			//case Set::$keysDirty:
			//	$setKey = $set->getBean()->name;
			//	$fieldKey = $bean->name;
			//	break;
		}

		return 'skema[' . $setKey . '][' . $this->recordID . '][' . $fieldKey . ']';

	}

	public function renderPlain()
	{
		return $this->value;
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

	public static function directiveLookup($fieldClassName)
	{
		if ($fieldClassName::$directiveClass !== null) {
			$directiveClass = $fieldClassName::$directiveClass;
		} else {
			$classParts = explode('\\', $fieldClassName);
			$baseClassName = array_pop($classParts);
			$directiveClass = 'Skema\\Directive\\' . $baseClassName;
		}

		return $directiveClass;
	}

	public static function sanitize($value)
	{
		return $value;
	}
}