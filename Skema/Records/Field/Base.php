<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:46 PM
 */

namespace Skema\Records\Field;


abstract class Base
{
	public $name;

	public function __construct($name)
	{
		$this->name = $name;
	}
}