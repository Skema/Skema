<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:04 PM
 */

namespace Skema\Records;

use R;
use Skema\Set;

class Record {
	public $set;
	public $values;
	public $cleanName;
	public $bean = null;

	public function __construct($values, Set $set, $bean = null)
	{
		$this->values = $values;
		$this->cleanName = 'skemarecord' . $set->cleanBaseName;
		$this->set = $set;
		if ($bean !== null) {
			$this->bean = $bean;
		}
	}

	public function newBean()
	{
		$bean = R::dispense($this->cleanName);
		return $bean;
	}

	public function add($setBean)
	{
		$recordBean = $this->newBean();
		foreach($this->values as $key => $value) {
			$recordBean->{$key} = $value;
		}
		$setBean->{'own' . ucfirst($this->cleanName) . 'List'}[] = $recordBean;
		R::storeAll([$recordBean, $setBean]);
	}

	public function getValues()
	{
		return R::findAll($this->cleanName);
	}

	public function getBean()
	{
		return $this->bean;
	}
}