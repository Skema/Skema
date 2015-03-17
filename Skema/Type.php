<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/17/15
 * Time: 11:49 AM
 */

namespace Skema;


use Skema\Records\Record;
use Skema\Records\Field;

class Type {
	/**
	 * @param Directive\Base $directive
	 * @return Directive\Base
	 */
	public static function Directive($directive) { return $directive; }

	/**
	 * @param Set $set
	 * @return Set
	 */
	public static function Set(Set $set) { return $set; }

	/**
	 * @param Record $record
	 * @return Record
	 */
	public static function Record(Record $record) { return $record; }

	/**
	 * @param Field\Base $field
	 * @return Field\Base
	 */
	public static function Field($field) { return $field; }
}