<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:40 PM
 */

namespace Skema;

use Skema;

class FieldDefinition
{
	public $name;
	public $cleanName;
	public $field;
	public $type;

	/**
	 * @param {Skema\Field\*} $field
	 */
	public function __construct($field)
	{
		$this->name = $field->name;
		$this->cleanName = Skema\Utility::cleanFieldName($field->name);
		$this->field = $field;
		$this->type = get_class($field);
	}
}