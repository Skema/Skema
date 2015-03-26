<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/11/15
 * Time: 11:33 PM
 */

namespace Skema;

use RedBeanPHP;
use Skema\Field;
use Skema\Directive;
use Skema\Record;

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
	public $keyType = 0;

	public static $keysID = 0;
	public static $keysClean = 1;
	public static $keysDirty = 2;

	/**
	 * @param String $name
	 * @param Number $keyType
	 * @param $bean
	 */
	public function __construct($name, $keyType = 0, $bean = null)
	{
		$this->name = $name;
		$this->cleanBaseName = Utility::cleanTableName($name);
		$this->cleanName = 'skemaset' . $this->cleanBaseName;
		$this->keyType = $keyType;

		if ($bean === null) {
			$this->bean = $bean = $this->getBean();
		} else {
			$this->bean = $bean;
		}

		if ($bean !== null) {
			$this->setFieldsAndDirectives();
		}
	}

	public static function byID($id, $keyType = 0)
	{
		$bean = RedBeanPHP::findOne('skemaset', ' id = ? ', [ $id ]);

		return new self($bean->name, $keyType, $bean);
	}

	public function getBean()
	{
		if ($this->bean !== null) return $this->bean;

		$bean = RedBeanPHP::findOne('skemaset', ' name = ? ', [ $this->name ]);

		if (empty($bean)) {
			$bean = RedBeanPHP::dispense('skemaset');
			$bean->name = $this->name;
			$bean->cleanName = Utility::cleanTableName($this->name);
			$bean->created = RedBeanPHP::isoDateTime();
			$bean->description = '';
			$bean->ownFieldList;
			$bean->{'ownSkemarecord' . $this->cleanBaseName . 'List'};
		}

		return $this->bean = $bean;
	}

	public function setFieldsAndDirectives() {
		foreach($this->getBean()->ownSkemafieldList as $fieldBean) {
			$field = Type::Field(new $fieldBean->type($fieldBean->name, $this, $fieldBean));

			switch ($this->keyType) {
				default:
				case self::$keysID:
					$key = $fieldBean->getID();
					break;
				case self::$keysClean:
					$key = $fieldBean->cleanName;
					break;
				case self::$keysDirty:
					$key = $fieldBean->name;
					break;
			}

			$this->fields[$key] = $field;
			$this->directives[$key] = $field->getDirective();
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

		switch ($this->keyType) {
			default:
			case self::$keysID:
				$key = $field->getBean()->getID();
				break;
			case self::$keysClean:
				$key = $field->cleanName;
				break;
			case self::$keysDirty:
				$key = $field->name;
				break;
		}

		$this->directives[$key] = $field->getDirective();

		return $this;
	}

	public function addRecord($values) {
		foreach($values as $key => $value) {
			$this->directives[$key]->setValue($value);
		}
		$record = new Record($this->directives, $this, null, $this->keyType);

		$record->addTo($this);

		return $this;
	}

	public function getRecord($id)
	{
		$fields = $this->fields;
		$directives = $this->directives;

		$recordBean = RedBeanPHP::findOne('skemarecord' . $this->cleanBaseName, ' id = ? ', [ $id ]);

		foreach($recordBean as $cleanName => $value) {
			switch ($this->keyType) {
				default:
				case self::$keysID:
					$key = $fields[$cleanName]->getBean()->getID();
					break;
				case self::$keysClean:
					$key = $cleanName;
					break;
				case self::$keysDirty:
					$key = $fields[$cleanName]->name;
					break;
			}

			if (isset($directives[$key])) {
				$directives[$key]->setValue($value, $recordBean->getID());
			}
		}

		return new Record($directives, $this, $recordBean, $this->keyType);
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

				$recordID = $recordBean->getID();

				foreach ( $fields as $cleanName => $field ) {
					$directive = Type::Directive($directives[ $cleanName ])
						->setValue( $recordBean->{$cleanName}, $recordID );

					switch ($this->keyType) {
						default:
						case self::$keysID:
							$key = $fields[$cleanName]->getBean()->getID();
							break;
						case self::$keysClean:
							$key = $cleanName;
							break;
						case self::$keysDirty:
							$key = $fields[$cleanName]->name;
							break;
					}

					$values[ $key ] = $directive;
				}

				$fn( $values, $recordID, $this, $recordBean, $this->keyType );
			}
		} else {
			foreach ( $setBean->{'ownSkemarecord' . $this->cleanBaseName . 'List'} as $id => $recordBean ) {
				$processedValues = [ ];

				foreach ( $fields as $cleanName => $field ) {
					$directive = Type::Directive($directives[ $cleanName ])
						->setValue( $recordBean->{$cleanName}, $recordBean->getID() );

					switch ($this->keyType) {
						default:
						case self::$keysID:
							$key = $fields[$cleanName]->getBean()->getID();
							break;
						case self::$keysClean:
							$key = $cleanName;
							break;
						case self::$keysDirty:
							$key = $fields[$cleanName]->name;
							break;
					}

					$processedValues[ $key ] = $fieldFn( $directive );
				}

				$fn( $processedValues );
			}
		}

		return $this;
	}

	public function eachHTML($fn)
	{
		$this->each($fn, function($directive) {

			$result = Type::Directive($directive)->renderHTML();

			return $result;
		});

		return $this;
	}

	public function allHTMLInputs($fn) {
		$this->each($fn, function($directive) {

			$result = Type::Directive($directive)->renderHTMLInput();

			return $result;
		});

		return $this;
	}

	public function eachJSON($fn)
	{
		$this->each($fn, function($directive) {

			$result = Type::Directive($directive)->renderJSON();

			return $result;
		});

		return $this;
	}

	public function getField($key, $fn)
	{
		if (!empty($this->fields[$key])) {
			$fn($this->fields[$key]);
		}

		return $this;
	}
}