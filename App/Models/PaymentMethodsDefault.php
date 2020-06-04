<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * Default payment methods model
 *
 * PHP version 7.0
 */
class PaymentMethodsDefault extends \Core\Model
{
	/**
	 * Gets the names of the default payment methods
	 * 
	 * @return array The array of default payment methods names
	 */
	public static function get() {
		$sql = 'SELECT name FROM payment_methods_default';
		
		$db = static::getDB();
		
		$res = $db->query($sql);
		
		return $res->fetchAll();
	}
}
