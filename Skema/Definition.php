<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 2:45 PM
 */

namespace Skema;


class Definition
{
	public $name;
	public $fields = [];

	public function __construct($name)
	{
		$this->name = $name;
	}

	public function addField($field)
	{

	}
}