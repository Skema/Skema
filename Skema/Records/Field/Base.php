<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Records\Field;

use R;
use Skema;
use Skema\Utility;

abstract class Base
{
	public $name;
	public $cleanName;
	public $directive = null;

	public $bean = null;

	/**
	 * @param $name
	 * @param {Skema\Field\*} $field
	 */
	public function __construct($name, $bean = null)
	{
		$this->name = $name;
		$this->cleanName = Utility::cleanFieldName($name);

		if ($bean !== null) {
			$this->bean = $bean;
		}
	}

	public function fillBean()
	{

	}

	public function fillField()
	{

	}

	public function getBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::findOne('skemafield', ' name = ? ', [$this->name]);

		if (empty($bean)) {
			$bean = R::dispense('skemafield');
			$bean->name = $this->name;
			$bean->cleanName = $this->cleanName;
			$bean->created = R::isoDateTime();
			$bean->type = get_class($this);
		}

		return $this->bean = $bean;
	}

	public function getDirective()
	{
		if ($this->directive === null) {
			$classParts = explode('\\', get_class($this));
			$baseClassName = array_pop($classParts);
			$directiveClass = 'Skema\\Directive\\' . $baseClassName;
			$instantiated = new $directiveClass($this);
			return $instantiated;
		}

		$instantiated = new $this->directive($this);
		return $instantiated;
	}

	public function addToSet(Skema\Set $set)
	{
		$setBean = $set->getBean();
		$set->fields[$this->cleanName] = $this;
		$fieldBean = $this->getBean();
		$setBean->ownSkemafieldList[] = $fieldBean;

		R::storeAll([$fieldBean, $setBean]);

		return $this;
	}
}