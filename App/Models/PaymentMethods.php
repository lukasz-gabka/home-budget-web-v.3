<?php

namespace App\Models;

use PDO;

/**
 * Payment methods model
 *
 * PHP version 7.0
 */
class PaymentMethods extends \Core\Model
{
	/**
	 * Saves default payment methods to user's payment methods model
	 * 
	 * @param id The user's id
	 * @param array The array of default payment methods names
	 * 
	 * @return void
	 */
	public static function saveDefault($id, $array) {
		$db = static::getDB();
		
		foreach ($array as $value) {
			$db->query("INSERT INTO payment_methods VALUES (NULL, $id, '{$value['name']}')");
		}
	}
	
	/**
	 * Gets payment methods
	 * 
	 * @param int User's id
	 * 
	 * @return array Array of payment methods names
	 */
	public static function get($id) {
		$db = static::getDB();
		
		$paymentMethods = $db->query("SELECT name FROM payment_methods WHERE user_id = $id");
		
		return $paymentMethods->fetchAll();
	}
	
	/*
	 * Check if payment method's name exists
	 * 
	 * @param string  The payment method's name
	 * 
	 * @return boolean  True if payment method's name exists, false otherwise
	 */
	public static function checkName($name) {
		$sql = "SELECT * FROM payment_methods WHERE user_id = :id AND name = :name";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		
		$stmt->execute();

		return $stmt->fetch();
	}
	
	/*
	 * Edit payment method by changing name
	 * 
	 * @param string  The new name
	 * @param string  The name to be changed
	 * 
	 * @return void
	 */
	public static function edit($name, $category) {
		$name = ucfirst($name);

		$id = static::getID($category);
		
		$sql = "UPDATE payment_methods SET name = :name WHERE id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->execute();
	}
	
	/**
	 * Get ID of the payment method's name
	 * 
	 * @param string  Payment method's name
	 * 
	 * @return int  Payment method's ID
	 */
	protected static function getID($name) {
		$sql = "SELECT id FROM payment_methods WHERE name = :name AND user_id = :user_id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		$stmt->execute();

		return $stmt->fetchColumn();
	}
	
	/**
	 * Add new payment method
	 * 
	 * @param string  New payment method's name
	 * 
	 * @return void
	 */
	public static function add($name) {
		$name = ucfirst($name);
		
		$sql = "INSERT INTO payment_methods VALUES (NULL, :user_id, :name)";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
		$stmt->bindValue(':name', $name, PDO::PARAM_STR);
		
		$stmt->execute();
	}
	
	/**
	 * Delete payment method
	 * 
	 * @param string  Payment method's name to delete
	 * 
	 * @return void
	 */
	public static function delete($name) {
		$id = static::getID($name);

		$sql = "DELETE FROM payment_methods WHERE id = :id";
		
		$db = static::getDB();
		$stmt = $db->prepare($sql);

		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->execute();
	}
}
