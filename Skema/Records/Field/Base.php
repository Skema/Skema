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
use Skema\Set;
use Skema\Utility;

class Base
{
	public $name;
	public $cleanName;
	public $directive = null;
	public $set = null;

	public $bean = null;

	/**
	 * @param $name
	 * @param {Skema\Field\*} $field
	 * @param {Set} $set
	 */
	public function __construct($name, $bean = null, Set $set = null)
	{
		$this->name = $name;
		$this->cleanName = Utility::cleanFieldName($name);

		$this->bean = $bean;
		$this->set = $set;
	}

	public static function byID($id)
	{
		$bean = R::findOne('skemafield', ' id = ? ', [ $id ]);
		$field = new $bean->type($bean->name, $bean);
		return $field;
	}

	public function fillBean()
	{

	}

	public function fillField()
	{

	}

	public function newBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::dispense('skemafield');
		$bean->name = $this->name;
		$bean->cleanName = $this->cleanName;
		$bean->created = R::isoDateTime();
		$bean->type = get_class($this);

		return $this->bean = $bean;
	}
	public function getBean(Set $set)
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::findOne('skemafield', ' name = ? and skemaset_id = ? ', [$this->name, $set->getBean()->getID()]);

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

	public function addToSet(Set $set)
	{
		$setBean = $set->getBean();

		if (R::count('skemafield', ' name = ? and skemaset_id = ? ', [$this->name, $setBean->getID()]) > 0) {
			throw new \Exception('Already exists on set');
		}

		$fieldBean = $this->newBean();

		$set->fields[$this->cleanName] = $this;
		$setBean->ownSkemafieldList[] = $fieldBean;

		R::storeAll([$fieldBean, $setBean]);

		return $this;
	}

	public function _($name)
	{
		$className = get_class($this);
		$parts = explode('\\', $className);
		$classBase = array_pop($parts);
		$prop = lcfirst($classBase) . ucfirst($name);
		return $prop;
	}

	public static function exists($name)
	{
		return R::count('skemafield', ' name = ? ', [$name]) > 0;
	}
}