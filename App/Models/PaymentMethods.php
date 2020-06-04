<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * Payment methods model
 *
 * PHP version 7.0
 */
class PaymentMethods extends \Core\Model
{
	/**
	 * Saves default payment methods to user's payment methods 
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
}
