<?php

namespace App\Models;

use PDO;

/**
 * Incomes model
 *
 * PHP version 7.0
 */
class Incomes extends \Core\Model
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
	 * Saves income to the model
	 * 
	 * @param id The user's id
	 * 
	 * @return boolean True if income was saved, false otherwise 
	 */
	public function save($id) {
		$this->validate();
		
		if (empty($this->errors)) {
			$db = static::getDB();
		
			$sql = "INSERT INTO incomes VALUES (NULL, :id, :amount, :date, :category, :comment)";
			
			$stmt = $db->prepare($sql);

            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':amount', $this->amount, PDO::PARAM_STR);
            $stmt->bindValue(':date', $this->date, PDO::PARAM_STR);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_STR);
            $stmt->bindValue(':comment', $this->comment, PDO::PARAM_STR);

            return $stmt->execute();
		}
		
		return false;
	}
	
	protected function validate() {
		// Check amount		
		if (!is_numeric($this->amount) || fmod($this->amount * 100, 1) || $this->amount <= 0) /* 1st statement checks if amount has more than two decimals */{
			$this->errors[] = 'Wpisz poprawną wartość (w zł)';
		}
		
		// Check date
		if (!$this->validateDateFormat() || !$this->validateDate()) {
			$this->errors[] = 'Wpisz poprawną datę w formacie rrrr-mm-dd';
		}
	}
	
	protected function validateDateFormat() {
		$regex = '/^[\d]{4}-[\d]{2}-[\d]{2}$/';
		return preg_match($regex, $this->date);
	}
	
	protected function validateDate() {
		$regex = '/[\d]+/';
		preg_match_all($regex, $this->date, $date);
		
		$date = $date[0];
		
		return checkdate($date[1], $date[2], $date[0]);	
	}
}
