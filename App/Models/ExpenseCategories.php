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
			$db->query("INSERT INTO expense_categories VALUES (NULL, $id, '{$value['name']}', NULL)");
		}
	}
	
	/**
	 * Gets expense categories
	 * 
	 * @param int User's id
	 * 
	 * @return array Array of expense categories names and limits
	 */
	public static function get($id) {
		$db = static::getDB();
		
		$expenseCategories = $db->query("SELECT name, category_limit FROM expense_categories WHERE user_id = $id");
		
		return $expenseCategories->fetchAll();
	}
	
	/*
	 * Check if category name exists
	 * 
	 * @param string  The category name
	 * 
	 * @return boolean  True if category name exists, false otherwise
	 */
	public static function checkName($name) {
		$sql = "SELECT * FROM expense_categories WHERE user_id = :id AND name = :name";
		
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
	 * @param int  The category's limit to set
	 * 
	 * @return void
	 */
	public static function edit($name, $category, $limit) {
		$name = ucfirst($name);

		$id = static::getID($category);

		if ($limit) {
			$sql = "UPDATE expense_categories SET name = :name, category_limit = :limit WHERE id = :id";
		} else {
			$sql = "UPDATE expense_categories SET name = :name, category_limit = NULL WHERE id = :id";
		}
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		if ($limit) {
			$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
		}
		
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
		$sql = "SELECT id FROM expense_categories WHERE name = :name AND user_id = :user_id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		$stmt->execute();

		return $stmt->fetchColumn();
	}
	
	/**
	 * Add new category
	 * 
	 * @param string  New category's name
	 * 
	 * @return void
	 */
	public static function add($name) {
		$name = ucfirst($name);
		
		$sql = "INSERT INTO expense_categories VALUES (NULL, :user_id, :name, NULL)";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		
		$stmt->execute();
	}
	
	/**
	 * Delete category
	 * 
	 * @param string  Category's name to delete
	 * 
	 * @return void
	 */
	public static function delete($name) {
		$id = static::getID($name);

		$sql = "DELETE FROM expense_categories WHERE id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	/**
	 * Get category name and limits
	 * 
	 * @return array  The array of categories and category limits
	 */
	public static function getLimits() {
		$sql = "SELECT name, category_limit FROM expense_categories WHERE user_id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
	
	/**
	 * Get expense category limit
	 * 
	 * @param string  The category's name
	 * 
	 * @return mixed  The expense category limit if the category has limit set, false otherwise
	 */
	public static function getLimit($limit) {
		$sql = "SELECT * FROM expense_categories WHERE name = :name AND user_id = :id AND category_limit IS NOT NULL";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $limit, PDO::PARAM_STR);
		$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch();
	}
	
	/**
	 * Set limit parameters
	 * 
	 * @param double  The current expense amount
	 * @param double  The current category's limit
	 * @param double  Sum of expenses from current category
	 * 
	 * @return array  The array of limit-message parameters
	 */
	public static function setLimitParams($expense, $limit, $sum) {
		$params['limit'] = $limit;
		$params['sum'] = $sum + $expense;
		$params['left'] = $params['limit'] - $params['sum'];
		
		$params['left'] = round($params['left'], 2);
		$params['sum'] = round($params['sum'], 2);
		
		return $params;
	}
}
