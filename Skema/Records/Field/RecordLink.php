<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Records\Field;

use Skema\Records\Record;
use Skema\Set;
use R;

class RecordLink extends Base {

	public $htmlInputTemplate = '';

	public function __construct($name, Set $set = null, $bean = null)
	{
		parent::__construct($name, $set, $bean);

		if ($bean !== null) {
			$this->htmlInputTemplate = $bean->{$this->_('htmlInputTemplate')};
		}
	}

	public function setHtmlInputTemplate($htmlInputTemplate){
		$this->htmlInputTemplate = $htmlInputTemplate;

		return $this;
	}

	public function newBean()
	{
		$bean = parent::newBean();

		$bean->{$this->_('htmlInputTemplate')} = $this->htmlInputTemplate;

		return $bean;
	}

	public function getBean(Set $set = null)
	{
		$bean = parent::getBean($set);

		if ($bean !== null) {
			$this->htmlInputTemplate = $bean->{$this->_('htmlInputTemplate')};
		}

		return $bean;
	}

	/**
	 * @param Set $linkedSet
	 * @returns $this
	 */
	public function link(Set $linkedSet)
	{
		$bean = $this->getBean();
		if ($bean === null) {
			$bean = $this->newBean();
		}

		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();

		R::store($bean);
		return $this;
	}

	/**
	 * @return \RedBean_SimpleModel[]
	 */
	public function getOptions()
	{
		$records = [];
		$linkedSetId = $this->getBean()->{$this->_('linkedSetId')};

		if (isset($linkedSetId)) {
			$set = Set::byID($linkedSetId, $this->set->keyType);
			foreach($set->getBean()->{'ownSkemarecord' . $set->cleanBaseName . 'List'} as $id => $recordBean) {
				$records[$id] = $recordBean;
			}
		}

		return $records;
	}

}