<?php

namespace App\Models;

use PDO;
use \App\Validate;

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
		$this->errors = Validate::validate($this->date, $this->amount);
		
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
	
	/**
	 * Gets the incomes based by dates and user's id
	 * 
	 * @param array  The array of dates
	 * @param int  The user's id
	 * 
	 * @return array  The array of incomes
	 */
	public static function get($date, $id) {
		$db = static::getDB();
		
		$stmt = $db->prepare("SELECT amount, date, category, comment FROM incomes WHERE date BETWEEN :firstDate AND :lastDate AND user_id = :id ORDER BY date ASC");
		
		$stmt->bindValue(':firstDate', $date['firstDate'], PDO::PARAM_STR);
		$stmt->bindValue(':lastDate', $date['lastDate'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
	
	/**
	 * Gets the income categories and sum of incomes in each category based by dates and user's id
	 * 
	 * @param array  The array of dates
	 * @param int  The user's id
	 * 
	 * @return array  The array of sum of incomes based by category
	 */
	public static function getCategories($date, $id) {
		$db = static::getDB();
		
		$stmt = $db->prepare("SELECT ROUND(SUM(amount), 2), category FROM incomes WHERE date BETWEEN :firstDate AND :lastDate AND user_id = :id GROUP BY category");
		
		$stmt->bindValue(':firstDate', $date['firstDate'], PDO::PARAM_STR);
		$stmt->bindValue(':lastDate', $date['lastDate'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
}
