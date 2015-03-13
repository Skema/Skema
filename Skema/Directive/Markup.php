<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/14/15
 * Time: 3:10 PM
 */

namespace Skema\Directive;


class Markup extends Base {

	public static $parserClass = 'WikiLingo\\Parser';
	public static $parser = null;

	public function renderHTML() {
		if (self::$parser === null) {
			self::$parser = new self::$parserClass();
		}

		return self::$parser->parse($this->value);
	}

	public function renderJSON() {
		if (self::$parser === null) {
			self::$parser = new self::$parserClass();
		}

		return urlencode(self::$parser->parse($this->value));
	}
}