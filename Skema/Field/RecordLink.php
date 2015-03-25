<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:05 PM
 */

namespace Skema\Field;

use Skema\Record;
use Skema\Set;
use R;

class RecordLink extends Base {

	public $htmlInputTemplate = '';
	public $linkedSetId = 0;

	public function __construct($name, Set $set = null, $bean = null)
	{
		parent::__construct($name, $set, $bean);

		if ($bean !== null) {
			$this->htmlInputTemplate = $bean->{$this->_('htmlInputTemplate')};
			$this->linkedSetId = $bean->{$this->_('linkedSetId')};
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
		$bean->{$this->_('linkedSetId')} = $this->linkedSetId;

		return $bean;
	}

	public function getBean(Set $set = null)
	{
		$bean = parent::getBean($set);

		if ($bean !== null) {
			$this->htmlInputTemplate = $bean->{$this->_('htmlInputTemplate')};
			$this->linkedSetId = $bean->{$this->_('linkedSetId')};
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

		$this->linkedSetId =
		$bean->{$this->_('linkedSetId')} = $linkedSet->getBean()->getID();

		R::store($bean);
		return $this;
	}

	public function linkedSet()
	{
		if ($this->linkedSetId > 0) {
			return Set::byID($this->linkedSetId, $this->set->keyType);
		}

		return null;
	}

	/**
	 * @param \Closure $fn
	 * @return $this
	 */
	public function eachOption($fn)
	{
		$linkedSet = $this->linkedSet();

		if ($linkedSet !== null) {
			$linkedSet->each($fn);
		}

		return $this;
	}

	public function eachOptionHTML($fn)
	{
		$linkedSet = $this->linkedSet();

		if ($linkedSet !== null) {
			$linkedSet->eachHTML($fn);
		}

		return $this;
	}

	public function eachHTMLInput($fn) {
		$linkedSet = $this->linkedSet();

		if ($linkedSet !== null) {
			$linkedSet->eachHTMLInput($fn);
		}

		return $this;
	}

	public function eachJSON($fn)
	{
		$linkedSet = $this->linkedSet();

		if ($linkedSet !== null) {
			$linkedSet->eachJSON($fn);
		}

		return $this;
	}
}