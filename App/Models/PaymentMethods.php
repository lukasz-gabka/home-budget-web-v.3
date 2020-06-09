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
}
