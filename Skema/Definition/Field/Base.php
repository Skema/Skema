<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Definition\Field;

use Skema\Utility;

abstract class Base
{
	public $name;
	public $cleanName;

	/**
	 * @param $name
	 * @param {Skema\Field\*} $field
	 */
	public function __construct($name, $field)
	{
		$this->name = $name;
		$this->cleanName = Utility::cleanFieldName($name);
	}
}