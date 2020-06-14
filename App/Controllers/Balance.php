<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;
use \App\Models\Expenses;

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
	 * Show the balance page if balance range is specified, otherwise show the homepage
	 * 
	 * @return void
	 */
	public function showAction() {
		if (isset($_SESSION['incomes'])) {
			View::renderTemplate('Balance/show.html', [
			'balance_active' => 'active']);
		} else {
			Flash::addMessage('Wybierz zakres bilansu z menu powyżej', FLASH::INFO);
			
			$this->redirect('/');
		}
		
		unset($_SESSION['incomes']);
		unset($_SESSION['incomeCategories']);
		unset($_SESSION['expenses']);
		unset($_SESSION['expenseCategories']);
		unset($_SESSION['date']);
		unset($_SESSION['incomeSum']);
		unset($_SESSION['expenseSum']);
		unset($_SESSION['balance']);
		unset($_SESSION['spanClass']);
		unset($_SESSION['balanceText']);
	}
	
	/**
	 * Displays balance page 
	 * 
	 * @return void
	 */
	public function displayAction() {
		switch ($_GET['id']) {
			case 1: {
				$date = static::getCurrentMonth();
			} break;
			case 2: {
				$date = static::getPreviousMonth();
			} break;
			case 3: {
				$date = static::getCurrentYear();
			} break;
			case 4: {
				$date = static::getCustom();
			} break;
			default: {
				View::renderTemplate('404.html');
				exit();
			}
		}
		
		$_SESSION['incomes'] = Incomes::get($date, $this->user->id);
		$_SESSION['incomeCategories'] = Incomes::getCategories($date, $this->user->id);
		$_SESSION['expenses'] = Expenses::get($date, $this->user->id);
		$_SESSION['expenseCategories'] = Expenses::getCategories($date, $this->user->id);
		$_SESSION['date'] = $date;
		
		$_SESSION['incomeSum'] = 0;
		$_SESSION['expenseSum'] = 0;

		foreach ($_SESSION['incomes'] as $value) {
			$_SESSION['incomeSum'] += $value['amount'];
		}
		
		foreach ($_SESSION['expenses'] as $value) {
			$_SESSION['expenseSum'] += $value['amount'];
		}
		
		$_SESSION['balance'] = $_SESSION['incomeSum'] - $_SESSION['expenseSum'];
		
		if ($_SESSION['balance'] >= 0) {
			$_SESSION['spanClass'] = 'text-success';
			$_SESSION['balanceText'] = 'Gratulacje! Świetnie zarządzasz finansami!';
		} else {
			$_SESSION['spanClass'] = 'text-danger';
			$_SESSION['balanceText'] = 'Uważaj! Popadasz w długi!';
		}

		$this->redirect('/bilans');
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
	 * @return array  The array of dates
	 */
	protected static function getCustom() {
		$date = ['firstDate' => $_POST['firstDate'],
				 'lastDate' => $_POST['lastDate']];
		
		return $date;
	}
}

?>
