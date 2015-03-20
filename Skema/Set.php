<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/11/15
 * Time: 11:33 PM
 */

namespace Skema;

use R;
use Skema\Records\Field;
use Skema\Directive;
use Skema\Records\Record;

class Set
{
	public $name;
	public $cleanName;
	public $description;

	/**
	 * @var Field\Base[]
	 */
	public $fields = [];

	/**
	 * @var Directive\Base[]
	 */
	public $directives = [];
	private $bean = null;
	public $useUncleanKeys = false;

	/**
	 * @param String $name
	 * @param Boolean $useUncleanKeys
	 * @param $bean
	 */
	public function __construct($name, $useUncleanKeys = false, $bean = null)
	{
		$this->name = $name;
		$this->cleanBaseName = Utility::cleanTableName($name);
		$this->cleanName = 'skemaset' . $this->cleanBaseName;
		$this->useUncleanKeys = $useUncleanKeys;

		if ($bean === null) {
			$bean = $this->getBean();
		} else {
			$this->bean = $bean;
		}

		if ($bean !== null) {
			$this->setFieldsAndDirectives();
		}
	}

	public static function byID($id, $useUncleanKeys = false)
	{
		$bean = R::findOne('skemaset', ' id = ? ', [ $id ]);

		return new self($bean->name, $useUncleanKeys, $bean);
	}

	public function getBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = R::findOne('skemaset', ' name = ? ', [ $this->name ]);

		if (empty($bean)) {
			$bean = R::dispense('skemaset');
			$bean->name = $this->name;
			$bean->cleanName = Utility::cleanTableName($this->name);
			$bean->created = R::isoDateTime();
			$bean->description = '';
			$bean->ownFieldList;
			$bean->{'ownSkemarecord' . $this->cleanBaseName . 'List'};
		}

		return $this->bean = $bean;
	}

	public function setFieldsAndDirectives() {
		foreach($this->getBean()->ownSkemafieldList as $fieldBean) {
			$field = Type::Field(new $fieldBean->type($fieldBean->name, $this, $fieldBean));

			$this->fields[$this->useUncleanKeys ? $fieldBean->name : $fieldBean->cleanName] = $field;
			$this->directives[$this->useUncleanKeys ? $fieldBean->name : $fieldBean->cleanName] = $field->getDirective();
		}

		return $this;
	}

	/**
	 * @param Field\Base $field
	 * @return $this
	 */
	public function addField($field)
	{
		$field->addToSet($this);
		$this->directives[$this->useUncleanKeys ? $field->name : $field->cleanName] = $field->getDirective();

		return $this;
	}

	public function addRecord($values) {
		foreach($values as $key => $value) {
			$this->directives[$key]->setValue($value);
		}
		$record = new Record($this->directives, $this, null, $this->useUncleanKeys);

		$record->addTo($this);

		return $this;
	}

	public function getRecord($id)
	{
		$fields = $this->fields;
		$directives = $this->directives;

		$recordBean = R::findOne('skemarecord' . $this->cleanBaseName, ' id = ? ', [ $id ]);

		foreach($recordBean as $key => $value) {
			if (isset($directives[$this->useUncleanKeys ? $fields[$key]->name : $key])) {
				$directives[$this->useUncleanKeys ? $fields[$key]->name : $key]->setValue($value);
			}
		}

		return new Record($directives, $this, $recordBean, $this->useUncleanKeys);
	}

	/**
	 * @param callback $fn
	 * @param callback [$fieldFn]
	 *
	 * @return $this
	 */
	public function each($fn, $fieldFn = null)
	{
		$setBean = $this->getBean();
		$fields = $this->fields;
		$directives = $this->directives;

		if ($fieldFn === null) {
			foreach ( $setBean->{'ownSkemarecord' . $this->cleanBaseName . 'List'} as $id => $recordBean ) {
				$values          = [ ];

				foreach ( $fields as $key => $field ) {
					$directive = Type::Directive($directives[ $key ])
						->setValue( $recordBean->{$key} );

					$values[ $this->useUncleanKeys ? $field->name : $key ] = $directive;
				}

				$fn( new Record( $values, $this, $recordBean, $this->useUncleanKeys ) );
			}
		} else {
			foreach ( $setBean->{'ownSkemarecord' . $this->cleanBaseName . 'List'} as $id => $recordBean ) {
				$processedValues = [ ];

				foreach ( $fields as $key => $field ) {
					$directive = Type::Directive($directives[ $key ])
						->setValue( $recordBean->{$key} );

					$processedValues[ $this->useUncleanKeys ? $field->name : $key ] = $fieldFn( $directive );
				}

				$fn( $processedValues, $recordBean->getID() );
			}
		}

		return $this;
	}

	public function eachHTML($fn)
	{
		$this->each($fn, function($directive) {

			$result = $directive->renderHTML();

			return $result;
		});

		return $this;
	}

	public function eachHTMLInput($fn) {
		$this->each($fn, function($directive) {

			$result = $directive->renderHTMLInput();

			return $result;
		});

		return $this;
	}

	public function eachJSON($fn)
	{
		$this->each($fn, function($directive) {

			$result = $directive->renderJSON();

			return $result;
		});

		return $this;
	}

	public function getField($fieldName, $fn)
	{
		$fieldBean = R::findOne('skemafield', ' name = ? and skemaset_id = ? ', [$fieldName, $this->bean->getID()]);
		if (!empty($fieldBean)) {
			$fn(new $fieldBean->type($fieldBean->name, $this, $fieldBean));
		}

		return $this;
	}
}