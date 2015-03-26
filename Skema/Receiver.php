<?php
/**
 * Created by PhpStorm.
 * User: robert
 * Date: 3/26/15
 * Time: 10:12 AM
 */

namespace Skema;


use Slim\Slim;
use RedBeanPHP\R;

class Receiver {
	public static function receive()
	{

		$slim = new Slim();
		$skemaKeys = $slim->request->get('skema');
		$sets = [];

		foreach($skemaKeys as $skemaKey => $records) {
			if (!isset($sets[$skemaKey])) {
				if (is_numeric($skemaKey)) {
					$id = $skemaKey;
					$sets[$skemaKey] = Set::byID($id);
				} else {
					$cleanName = $skemaKey;
					$sets[$skemaKey] = Set::byCleanName($cleanName);
				}
			}
			$set = Type::Set($sets[$skemaKey]);

			foreach($records as $recordID => $fields) {
				$recordChanged = false;
				$record = $set->getRecord($recordID);

				foreach($fields as $fieldKey => $fieldValue) {
					if (is_numeric($fieldKey)) {
						$id = $fieldKey;
						$field = $set->fieldsByID[$id];
					} else {
						$cleanName = $fieldKey;
						$field = $set->fieldsByCleanName[$cleanName];
					}

					$directive = $field->getDirective();
					$sanitized = $directive::sanitize($fieldValue);
					if ($record->{$field->cleanName} !== $sanitized) {
						$record->{$field->cleanName} = $sanitized;
						$recordChanged = true;
					}
				}

				if ($recordChanged) {
					$record->update();
				}
			}
		}
	}
}