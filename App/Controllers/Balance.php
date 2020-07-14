<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;
use \App\Models\Expenses;
use \App\Models\ExpenseCategories;
use \App\Validate;

/**
 * Balance controller
 * 
 * PHP version 7.0
 */
class Balance extends Authenticated {
	/**
	 * Before filter - called before each action method
	 * 
	 * @return void
	 */
	protected function before() {
		parent::before();
		
		$this->user = Auth::getUser();
	}
	
	/**
	 * Displays balance page 
	 * 
	 * @return void
	 */
	public function displayAction() {
		if ($_GET['id'] == 4 && !isset($_POST['firstDate'])) {
			Flash::addMessage('Wybierz zakres bilansu z menu powyżej', FLASH::INFO);
			
			$this->redirect('/');
			exit();
		}
			$date = static::getDate($_GET['id']);
		
			if (!$date) {
				Flash::addMessage('Podane daty mają niepoprawną wartość', FLASH::DANGER);
				
				$this->redirect('/');
			}
			
			$balance['incomes'] = Incomes::get($date, $this->user->id);
			$balance['incomeCategories'] = Incomes::getCategories($date, $this->user->id);
			$balance['expenses'] = Expenses::get($date, $this->user->id);
			$balance['expenseCategories'] = static::getExpenseCategories($date);
			$balance['date'] = $date;
			
			$balance['incomeSum'] = 0;
			$balance['expenseSum'] = 0;

			foreach ($balance['incomes'] as $value) {
				$balance['incomeSum'] += $value['amount'];
			}
			
			foreach ($balance['expenses'] as $value) {
				$balance['expenseSum'] += $value['amount'];
			}
			
			$balance['balance'] = $balance['incomeSum'] - $balance['expenseSum'];
			
			if ($balance['balance'] >= 0) {
				$balance['spanClass'] = 'text-success';
				$balance['balanceText'] = 'Gratulacje! Świetnie zarządzasz finansami!';
			} else {
				$balance['spanClass'] = 'text-danger';
				$balance['balanceText'] = 'Uważaj! Popadasz w długi!';
			}

			View::renderTemplate('Balance/show.html', [
				'balance_active' => 'active',
				'balance' => $balance]);
	}
	
	/**
	 * Gets the date
	 * 
	 * @param int  The user's choice
	 * 
	 * @return mixed  The two dates array if the dates are correct, false otherwise
	 */
	protected static function getDate($choice) {
		switch ($_GET['id']) {
			case 1: {
				return static::getCurrentMonth();
			} break;
			case 2: {
				return static::getPreviousMonth();
			} break;
			case 3: {
				return static::getCurrentYear();
			} break;
			case 4: {
				$date = static::getCustomDate();
				
				if (!$date) {
					return false;
				}
				
				return $date;
			} break;
			default: {
				View::renderTemplate('404.html');
				exit();
			}
		}
	}
	
	/*
	 * Gets two dates of first and last day of current month
	 * 
	 * @return array  The array of dates
	 */
	protected static function getCurrentMonth() {
		$firstDate = date("Y-m-d", strtotime("first day of this month"));
		$lastDate = date("Y-m-d", strtotime("last day of this month"));
		$date = ['firstDate' => $firstDate,
				 'lastDate' => $lastDate];
		
		return $date;
	}
	
	/*
	 * Gets two dates of first and last day of previous month
	 * 
	 * @return array  The array of dates
	 */
	protected static function getPreviousMonth() {
		$firstDate = date("Y-m-d", strtotime("first day of previous month"));
		$lastDate = date("Y-m-d", strtotime("last day of previous month"));
		$date = ['firstDate' => $firstDate,
				 'lastDate' => $lastDate];
		
		return $date;
	}
	
	/*
	 * Gets two dates of first and last day of current year
	 * 
	 * @return array  The array of dates
	 */
	protected static function getCurrentYear() {
		$firstDate = date("Y-m-d", strtotime("first day of January"));
		$lastDate = date("Y-m-d", strtotime("last day of December"));
		$date = ['firstDate' => $firstDate,
				 'lastDate' => $lastDate];
		
		return $date;
	}
	
	/*
	 * Gets user specified dates
	 * 
	 * @return mixed  The array of dates if dates are correct, false otherwise
	 */
	protected static function getCustomDate() {
		if (Validate::validateDateOrder($_POST['firstDate'], $_POST['lastDate']) && !Validate::validate($_POST['firstDate']) && !Validate::validate($_POST['lastDate'])) {
			$date = ['firstDate' => $_POST['firstDate'],
				 'lastDate' => $_POST['lastDate']];
		
			return $date;
		} else {
			return false;
		}
	}
	
	/**
	 * Gets expense categories with expense sum and category limits
	 * 
	 * @param array  The array of dates for showing balance
	 * 
	 * @return array  The arrat of expense categories
	 */
	protected static function getExpenseCategories($date) {
		$categories = Expenses::getCategories($date);
		$limits = ExpenseCategories::getLimits();
		
		return Expenses::getCategoriesAndLimits($categories, $limits);
	}
}

?>
