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
use RedBeanPHP\R;

class Set
{
	public $name;
	public $cleanName;
	public $description;

	/**
	 * @var Field\Base[]
	 */
	public $fieldsByID = [];
	public $fieldsByCleanName = [];
	public $fieldsByName = [];
    public $fields;

	/**
	 * @var Directive\Base[]
	 */
	public $directivesByID = [];
	public $directivesByCleanName = [];
	public $directivesByName = [];
    public $directives;

	private $bean = null;
	public $keyType = 0;

	public static $keysID = 0;
	public static $keysClean = 1;
	public static $keysDirty = 2;

    public static $strict = false;

	/**
	 * @param String $name
	 * @param Number $keyType
	 * @param $bean
	 */
	public function __construct($name, $keyType = -1, $bean = null)
	{
        if ($keyType < self::$keysID) {
            $keyType = self::$keysDirty;
        }

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

	public static function byID($id)
	{
		$bean = R::findOne('skemaset', ' id = ? ', [ $id ]);

		return new self($bean->name, self::$keysID, $bean);
	}

	public static function byCleanName($cleanName)
	{
		$bean = R::findOne('skemaset', ' clean_name = ? ', [ $cleanName ]);

		return new self($bean->name, self::$keysClean, $bean);
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

			$id = $fieldBean->getID();

			$this->fieldsByID[$id] =
			$this->fieldsByCleanName[$fieldBean->cleanName] =
			$this->fieldsByName[$fieldBean->name] = $field;

			$this->directivesByID[$id] =
			$this->directivesByCleanName[$fieldBean->cleanName] =
			$this->directivesByName[$fieldBean->name] = $field->getDirective();
		}

        switch ($this->keyType) {
            default:
            case self::$keysID:
                $this->directives = $this->directivesByID;
                $this->fields = $this->fieldsByID;
                break;
            case self::$keysClean:
                $this->directives = $this->directivesByCleanName;
                $this->fields = $this->fieldsByCleanName;
                break;
            case self::$keysDirty:
                $this->directives = $this->directivesByName;
                $this->fields = $this->fieldsByName;
                break;
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

		$this->directivesByCleanName[$field->cleanName] = $field->getDirective();

		return $this;
	}

	public function addRecord($values) {
		foreach($values as $key => $value) {
			switch ($this->keyType) {
				default:
				case self::$keysID:
					$directive = $this->directivesByID[$key];
					break;
				case self::$keysClean:
					$directive = $this->directivesByCleanName[$key];
					break;
				case self::$keysDirty:
					$directive = $this->directivesByName[$key];
					break;
			}
			$directive->setValue($value);
		}

		switch ($this->keyType) {
			default:
			case self::$keysID:
			$record = new Record($this->directivesByID, $this, null, $this->keyType);
				break;
			case self::$keysClean:
				$record = new Record($this->directivesByCleanName, $this, null, $this->keyType);
				break;
			case self::$keysDirty:
				$record = new Record($this->directivesByName, $this, null, $this->keyType);
				break;
		}

		$record->addTo($this);

		return $this;
	}

	public function getRecord($id)
	{
		$directives = $this->directivesByCleanName;

		$recordBean = R::findOne('skemarecord' . $this->cleanBaseName, ' id = ? ', [ $id ]);

		foreach($recordBean as $cleanName => $value) {
			if (isset($directives[$cleanName])) {
				$directives[$cleanName]->setValue($value, $recordBean->getID());
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