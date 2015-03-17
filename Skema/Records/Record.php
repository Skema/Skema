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
	 * @var Directive\*[]
	 */
	public $directives;
	public $cleanName;
	public $useUncleanKeys = false;

	private $bean = null;

	/**
	 * @param Directive\*[] $directives
	 * @param Set $set
	 * @param OODBBean $bean
	 * @param Boolean [$useUncleanKeys]
	 */
	public function __construct($directives, Set $set, OODBBean $bean = null, $useUncleanKeys = false)
	{
		$this->directives = $directives;
		$this->cleanName = 'skemarecord' . $set->cleanBaseName;
		$this->set = $set;
		$this->useUncleanKeys = $useUncleanKeys;
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
		foreach($this->directives as $key => $directive) {
			$recordBean->{$key} = $directive->value;
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
		return $this->directives[$this->useUncleanKeys ? $field->name : $field->cleanName];
	}

	public function getValues()
	{
		return R::findAll($this->cleanName);
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
}