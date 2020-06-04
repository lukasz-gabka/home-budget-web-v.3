<?php

namespace App\Models;

use PDO;

/**
 * Expense categories model
 *
 * PHP version 7.0
 */
class ExpenseCategories extends \Core\Model
{
	/**
	 * Saves default expense categories to user's expense categories model
	 * 
	 * @param id The user's id
	 * @param array The array of default expense categories names
	 * 
	 * @return void
	 */
	public static function saveDefault($id, $array) {
		$db = static::getDB();
		
		foreach ($array as $value) {
			$db->query("INSERT INTO expense_categories VALUES (NULL, $id, '{$value['name']}')");
		}

	}
}
