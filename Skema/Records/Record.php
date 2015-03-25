<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:04 PM
 */

namespace Skema\Records;

use R;
use RedBeanPHP\OODBBean;
use Skema\Set;
use Skema\Directive;
use Skema\Records\Field;

class Record {

	/**
	 * @var Set
	 */
	public $set;

	/**
	 * @var Directive\Base[]
	 */
	public $directives;
	public $cleanName;
	public $keyType;

	private $bean = null;

	/**
	 * @param Directive\*[] $directives
	 * @param Set $set
	 * @param OODBBean $bean
	 * @param Number [$keyType]
	 */
	public function __construct($directives, Set $set, OODBBean $bean = null, $keyType = 0)
	{
		$this->directives = $directives;
		$this->cleanName = 'skemarecord' . $set->cleanBaseName;
		$this->set = $set;
		$this->keyType = $keyType;
		$this->bean = $bean;
	}

	public function newBean()
	{
		$bean = R::dispense($this->cleanName);
		return $bean;
	}

	public function addTo(Set $set)
	{
		$setBean = $set->getBean();
		$recordBean = $this->newBean();
		foreach($this->directives as $directive) {
			$recordBean->{$directive->field->cleanName} = $directive->value;
		}
		$setBean->{'own' . ucfirst($this->cleanName) . 'List'}[] = $recordBean;
		R::storeAll([$recordBean, $setBean]);

		return $this;
	}

	/**
	 * @param Field\Base $field
	 * @return Directive\*
	 */
	public function fieldDirective($field)
	{
		switch ($this->keyType) {
			default:
			case Set::$keysID:
				$key = $field->getBean()->getID();
				break;
			case Set::$keysClean:
				$key = $field->cleanName;
				break;
			case Set::$keysDirty:
				$key = $field->name;
				break;
		}

		return $this->directives[$key];
	}

	public function getBean()
	{
		return $this->bean;
	}

	public function update()
	{
		R::store($this->bean);
		return $this;
	}

	public function delete()
	{
		R::trash($this->bean);
	}

	public function __get($key)
	{
		return $this->bean->$key;
	}

	public function __set($key, $value)
	{
		return $this->bean->$key = $value;
	}
}