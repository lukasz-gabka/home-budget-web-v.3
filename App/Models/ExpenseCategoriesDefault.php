<?php

namespace App\Models;

use PDO;

/**
 * Default expense categories model
 *
 * PHP version 7.0
 */
class ExpenseCategoriesDefault extends \Core\Model
{
	/**
	 * Gets the names of the default expense categories
	 * 
	 * @return array The array of default expense categories names
	 */
	public static function get() {
		$sql = 'SELECT name FROM expense_categories_default';
		
		$db = static::getDB();
		
		$res = $db->query($sql);
		
		return $res->fetchAll();
	}
}
