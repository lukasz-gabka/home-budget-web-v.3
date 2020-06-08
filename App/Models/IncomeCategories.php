<?php

namespace App\Models;

use PDO;

/**
 * Income categories model
 *
 * PHP version 7.0
 */
class IncomeCategories extends \Core\Model
{
	/**
	 * Saves default income categories to user's income categories model
	 * 
	 * @param id The user's id
	 * @param array The array of default income categories names
	 * 
	 * @return void
	 */
	public static function saveDefault($id, $array) {
		$db = static::getDB();
		
		foreach ($array as $value) {
			$db->query("INSERT INTO income_categories VALUES (NULL, $id, '{$value['name']}')");
		}
	}
	
	/**
	 * Gets income categories
	 * 
	 * @param int User's id
	 * 
	 * @return array Array of income categories names
	 */
	public static function get($id) {
		$db = static::getDB();
		
		$incomeCategories = $db->query("SELECT name FROM income_categories WHERE user_id = $id");
		
		return $incomeCategories->fetchAll();
	}
}
