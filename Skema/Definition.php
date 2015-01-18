<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:45 PM
 */

namespace Skema;

use R;

class Definition
{
	private static $isSetup = false;

	public $name;
	public $cleanName;
	public $beanTable = null;

	/**
	 * @var FieldDefinition[]
	 */
	public $fields = [];
	public $properties = [];

	/**
	 * @param {String} $name
	 */
	public function __construct($name)
	{
		if (!self::$isSetup) {

			$host = '';
			$db = '';
			$user = '';
			$password = '';

			if (file_exists('../config.php')) {
				require_once('../config.php');
			} else {
				require_once( '../default.config.php' );
			}

			R::setup("mysql:host=$host;dbname=$db", $user, $password);

		}

		$this->name = $name;
		$this->cleanName = Utility::cleanSkemaName($name);
		$this->beanTable = R::inspect('skema' . $this->cleanName);
	}

	/**
	 * @param Skema\Field\* $field
	 */
	public function replaceField($field)
	{
		$def = new FieldDefinition($field);

		$this->fields[] = $def;
	}

	/**
	 * @param Record $record
	 */
	public function save($record)
	{
		if ($this->beanTable === null) {
		}

		$bean = R::dispense('skema' . $this->cleanName);

		foreach($this->fields as $field) {
			$bean->{ $field->cleanName } = '';
		}
	}
}