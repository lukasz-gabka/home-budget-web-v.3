<?php

namespace App\Models;

use PDO;

/**
 * Expenses model
 *
 * PHP version 7.0
 */
class Expenses extends \Core\Model
{
	/**
     * Error messages
     *
     * @var array
     */
    public $errors = [];
	
	/**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }
	
	/**
	 * Saves expense to the model
	 * 
	 * @param id The user's id
	 * 
	 * @return boolean True if expense was saved, false otherwise 
	 */
	public function save($id) {
		$this->validate();
		
		if (empty($this->errors)) {
			$db = static::getDB();
		
			$sql = "INSERT INTO expenses VALUES (NULL, :id, :amount, :date, :paymentMethod, :category, :comment)";
			
			$stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':paymentMethod', $this->paymentMethod, PDO::PARAM_STR);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
		}
		
		return false;
	}
	
	/**
	 * Validates the user input
	 * 
	 * @return void
	 */
	protected function validate() {
		// Check amount	
		if (!is_numeric($this->amount) || $this->validateDecimalPlaces() || $this->amount <= 0) {
			$this->errors[] = 'Wpisz poprawną wartość (w zł)';
		}
		
		// Check date
		if (!$this->validateDateFormat() || !$this->validateDate()) {
			$this->errors[] = 'Wpisz poprawną datę w formacie rrrr-mm-dd';
		}
	}
	
	/**
	 * Validates if the number given has more than two decimal places
	 * 
	 * @return boolean True if the number has more than to decimal places, false otherwise
	 */
	protected function validateDecimalPlaces() {
		$number = str_replace(",", ".", $this->amount);
		
		if (strlen(substr(strrchr($number, "."), 1)) > 2) {
			return true;
		}
		
		return false;
	}
	
	/**
	 * Validates the date format
	 * 
	 * @return boolean True if the date formatis correct, false otherwise
	 */
	protected function validateDateFormat() {
		$regex = '/^[\d]{4}-[\d]{2}-[\d]{2}$/';
		return preg_match($regex, $this->date);
	}
	
	/**
	 * Validates if the date is correct
	 * 
	 * @return boolean True if the date formatis correct, false otherwise
	 */
	protected function validateDate() {
		$regex = '/[\d]+/';
		preg_match_all($regex, $this->date, $date);
		
		$date = $date[0];
		
		return checkdate($date[1], $date[2], $date[0]);	
	}
	
	/**
	 * Gets the expenses based by dates and user's id
	 * 
	 * @param array  The array of dates
	 * @param int  The user's id
	 * 
	 * @return array  The array of incomes
	 */
	public static function get($date, $id) {
		$db = static::getDB();
		
		$stmt = $db->prepare("SELECT amount, date, payment_method, category, comment FROM expenses WHERE date BETWEEN :firstDate AND :lastDate AND user_id = :id ORDER BY date ASC");
		
		$stmt->bindValue(':firstDate', $date['firstDate'], PDO::PARAM_STR);
		$stmt->bindValue(':lastDate', $date['lastDate'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
	
	/**
	 * Gets the expense categories and sum of expenses in each category based by dates and user's id
	 * 
	 * @param array  The array of dates
	 * @param int  The user's id
	 * 
	 * @return array  The array of sum of expenses based by category
	 */
	public static function getCategories($date, $id) {
		$db = static::getDB();
		
		$stmt = $db->prepare("SELECT ROUND(SUM(amount), 2), category FROM expenses WHERE date BETWEEN :firstDate AND :lastDate AND user_id = :id GROUP BY category");
		
		$stmt->bindValue(':firstDate', $date['firstDate'], PDO::PARAM_STR);
		$stmt->bindValue(':lastDate', $date['lastDate'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
}
