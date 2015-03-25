<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 1/15/15
 * Time: 11:23 AM
 */

namespace Skema;


class Utility
{
	public static function cleanTableName($str)
	{
		$strNoAccent = self::stripAccents($str);
		$strLower = strtolower($strNoAccent);
		$friendly = preg_replace('/[\W]+/', '', $strLower);
		return $friendly;
	}

	public static function cleanFieldName($str)
	{
		$strNoAccent = self::stripAccents($str);
		$strLower = strtolower($strNoAccent);
		$friendly = preg_replace('/[\W]+/', '', $strLower);
		return $friendly;
	}

	public static function stripAccents($str)
	{
		return strtr(utf8_decode($str), utf8_decode('àáâãäçèéêëìíîïñòóôõöùúûüýÿÀÁÂÃÄÇÈÉÊËÌÍÎÏÑÒÓÔÕÖÙÚÛÜÝ'), 'aaaaaceeeeiiiinooooouuuuyyAAAAACEEEEIIIINOOOOOUUUUY');
	}

	public static function mustachify($value, $fn) {
		return preg_replace_callback('/{{\s*([\w\.]+)\s*}}/', function($matches) use ($fn) {
			return $fn($matches[1]);
		}, $value);
	}
}