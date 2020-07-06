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
	 * Save default income categories to user's income categories model
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
	 * Get income categories
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
	
	/*
	 * Check if category name exists
	 * 
	 * @param string  The category name
	 * 
	 * @return boolean  True if category name exists, false otherwise
	 */
	public static function checkName($name) {
		$sql = "SELECT * FROM income_categories WHERE user_id = :id AND name = :name";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetch();
	}
	
	/*
	 * Edit category by changing name
	 * 
	 * @param string  The new name
	 * @param string  The name to be changed
	 * 
	 * @return void
	 */
	public static function edit($name, $category) {
		$id = static::getID($category);
		
		$sql = "UPDATE income_categories SET name = :name WHERE id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	/**
	 * Get ID of the category's name
	 * 
	 * @param string  Category's name
	 * 
	 * @return int  Category's ID
	 */
	protected static function getID($name) {
		$sql = "SELECT id FROM income_categories WHERE name = :name";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetchColumn();
	}
}
