<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Field;

use R;
use Skema;
use Skema\Set;
use Skema\Utility;

abstract class Base
{
	public $name;
	public $cleanName;
	public $directive = null;
	public $set = null;
	public $prerequisite = false;

	private $bean = null;

	/**
	 * @param $name
	 * @param Set $set
	 * @param \RedBean_SimpleModel $bean
	 */
	public function __construct($name, Set $set = null, $bean = null)
	{
		$this->name = $name;
		$this->cleanName = Utility::cleanFieldName($name);
		$this->set = $set;
		$this->bean = $bean;

		if ($bean !== null) {
			$this->prerequisite = $bean->prerequisite;
		}
	}

	public function setPrerequisite($value)
	{
		$this->prerequisite = $value ? true : false;
		return $this;
	}

	public static function byID($id, Set $set)
	{
		$bean = R::findOne('skemafield', ' id = ? ', [ $id ]);
		$field = new $bean->type($bean->name, $set, $bean);
		return $field;
	}

	public function newBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::dispense('skemafield');
		$bean->name = $this->name;
		$bean->cleanName = $this->cleanName;
		$bean->created = R::isoDateTime();
		$bean->type = get_class($this);
		$bean->prerequisite = $this->prerequisite;

		return $this->bean = $bean;
	}
	public function getBean(Set $set = null)
	{
		if ($this->bean !== null) return $this->bean;

		if ($set !== null) {
			$bean = R::findOne( 'skemafield', ' name = ? and skemaset_id = ? ', [
				$this->name,
				$set->getBean()->getID()
			] );

			if ($bean !== null) {
				$this->prerequisite = $bean->prerequisite;
			}

			return $this->bean = $bean;
		}

		return null;
	}

	public function getDirective()
	{
		if ($this->directive === null) {
			$classParts = explode('\\', get_class($this));
			$baseClassName = array_pop($classParts);
			$directiveClass = 'Skema\\Directive\\' . $baseClassName;
			$this->directive = new $directiveClass($this, $this->set);
			return $this->directive;
		}

		return $this->directive;
	}

	public function addToSet(Set $set)
	{
		$setBean = $set->getBean();

		if (R::count('skemafield', ' name = ? and skemaset_id = ? ', [$this->name, $setBean->getID()]) > 0) {
			throw new \Exception('Already exists on set');
		}

		$fieldBean = $this->newBean();

		$set->fields[$this->cleanName] = $this;
		$this->set = $set;
		$setBean->ownSkemafieldList[] = $fieldBean;

		R::storeAll([$fieldBean, $setBean]);

		return $this;
	}

	public function _($name)
	{
		$className = get_class($this);
		$parts = explode('\\', $className);
		$classBase = array_pop($parts);
		$classBaseName = lcfirst($classBase);
		$prop = $classBaseName . ucfirst($name);
		return $prop;
	}

	public static function exists($name)
	{
		return R::count('skemafield', ' name = ? ', [$name]) > 0;
	}
}