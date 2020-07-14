<?php

namespace App\Models;

use PDO;
use \App\Validate;

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
		$this->errors = Validate::validate($this->date, $this->amount);
		
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
	 * 
	 * @return array  The array of sum of expenses based by category
	 */
	public static function getCategories($date) {
		$db = static::getDB();
		
		$stmt = $db->prepare("SELECT ROUND(SUM(amount), 2), category FROM expenses WHERE date BETWEEN :firstDate AND :lastDate AND user_id = :id GROUP BY category");
		
		$stmt->bindValue(':firstDate', $date['firstDate'], PDO::PARAM_STR);
		$stmt->bindValue(':lastDate', $date['lastDate'], PDO::PARAM_STR);
		$stmt->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_INT);
		
		$stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
	}
	
	/**
	 * Join together array of expenses with array of expense categories
	 * 
	 * @param array  The array of expenses with expense sum
	 * @param array  The array of expense categories with limits
	 * 
	 * @return array  The array of expenses with expense categories
	 */
	public static function getCategoriesAndLimits($categories, $limits) {
		for ($i = 0; $i < sizeof($categories); $i++) {
			for ($j = 0; $j < sizeof($limits); $j++) {
				if ($categories[$i]['category'] == $limits[$j]['name'] ) {
					$categories[$i]['limit'] = $limits[$j]['category_limit'];
				}
			}
		}
		return $categories;
	}
}
