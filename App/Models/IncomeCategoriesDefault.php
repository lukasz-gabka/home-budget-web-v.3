<?php

namespace App\Models;

use PDO;

/**
 * Default income categories model
 *
 * PHP version 7.0
 */
class IncomeCategoriesDefault extends \Core\Model
{
	/**
	 * Gets the names of the default income categories
	 * 
	 * @return array The array of default income categories names
	 */
	public static function get() {
		$sql = 'SELECT name FROM income_categories_default';
		
		$db = static::getDB();
		
		$res = $db->query($sql);
		
		return $res->fetchAll();
	}
}
