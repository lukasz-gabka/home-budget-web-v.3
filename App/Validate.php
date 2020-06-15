<?php

namespace App;

/**
 * Date and amount validation 
 *
 * PHP version 7.0
 */
class Validate
{	
	/**
	 * Validates the user input
	 * 
	 * @param string  The date to validate 
	 * @param double  The number to validate  (optional)
	 * 
	 * @return mixed  Array of errors if validation fails, false otherwise
	 */
	public static function validate($date, $amount = 1) {
		// Check amount	
		if (isset($amount)) {
			if (!is_numeric($amount) || static::validateDecimalPlaces($amount) || $amount <= 0) {
			$errors[] = 'Wpisz poprawną wartość (w zł)';
			}
		}
		
		// Check date
		if (!static::validateDateFormat($date) || !static::validateDate($date)) {
			$errors[] = 'Wpisz poprawną datę w formacie rrrr-mm-dd';
		}

		if (isset($errors)) {
			return $errors;
		}
		return false;
	}
	
    /**
	 * Validates the date format
	 * 
	 * @param string  The date to validate
	 * 
	 * @return boolean True if the date format is correct, false otherwise
	 */
	protected static function validateDateFormat($date) {
		$regex = '/^[\d]{4}-[\d]{2}-[\d]{2}$/';
		return preg_match($regex, $date);
	}
	
	/**
	 * Validates if the date is correct
	 * 
	 * @param string  The date to validate
	 * 
	 * @return boolean True if the date format is correct, false otherwise
	 */
	protected static function validateDate($input) {
		$regex = '/[\d]+/';
		preg_match_all($regex, $input, $date);
		
		$date = $date[0];
		
		return checkdate($date[1], $date[2], $date[0]);	
	}
	
	/**
	 * Validates if the number given has more than two decimal places
	 * 
	 * @param double  The number to validate
	 * 
	 * @return boolean True if the number has more than to decimal places, false otherwise
	 */
	protected static function validateDecimalPlaces($amount) {
		$number = str_replace(",", ".", $amount);
		
		if (strlen(substr(strrchr($number, "."), 1)) > 2) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Validates if the first date is earlier than second date
	 * 
	 * @param string  The first date
	 * @param string  The second date
	 * 
	 * @return boolean True if first date is earlier, false otherwise
	 */
	public static function validateDateOrder($date1, $date2) {
		if ($date1 <= $date2) {
			return true;
		}
		return false;
	}
}
