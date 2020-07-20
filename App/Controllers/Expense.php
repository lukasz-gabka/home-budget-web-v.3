<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;
use \App\Models\ExpenseCategories;
use \App\Models\PaymentMethods;

/**
 * Expense controller
 * 
 * PHP version 7.0
 */
class Expense extends Authenticated {
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
	 * Show the expense page
	 * 
	 * @param array  The arguments to display (optional)
	 * 
	 * @return void
	 */
	public function showAction($arg = 0) {
		$display['userExpenseCategories'] = ExpenseCategories::get($_SESSION['user_id']);
        $display['userPaymentMethods'] = PaymentMethods::get($_SESSION['user_id']);
		
		View::renderTemplate('Expenses/add.html', [
			'expense_active' => 'active',
			'expense' => $arg,
			'display' => $display]);
	}
		
	/**
	 * Add a new expense
	 * 
	 * @return void
	 */
	public function addAction() {		
		$expenses = new Expenses($_POST);
		
		if ($expenses->save($this->user->id)) {
			Flash::addMessage('Dodano wydatek');
			
			$this->redirect('/dodaj-wydatek');
		} else {
			$expenses->errorString = implode("\n", $expenses->errors);
			
			Flash::addMessage($expenses->errorString, Flash::DANGER);
			
			$expense['amount'] = $_POST['amount'];
			$expense['date'] = $_POST['date'];
			
			if (isset($_POST['comment'])) {
				$expense['comment'] = $_POST['comment'];
			}
			
            $this->showAction($expense);
		}
	}
	
	/**
	 * Get expense category limit parameters
	 * 
	 * @param array  The array of expense-category and expense-amount
	 * 
	 * @return mixed  The array of limit parameters if limit found, false otherwise
	 */
	public function getLimitAction() {
		$expense = $_POST['expense'];
		
		$limit = ExpenseCategories::getLimit($expense['category']);
		
		if ($limit) {
			$dateRange = static::getDateRange($expense['date']);
			
			$expenseSum = Expenses::getSum($expense['category'], $dateRange);
			
			$result = ExpenseCategories::setLimitParams($expense['amount'], $limit['category_limit'], $expenseSum['ROUND(SUM(amount), 2)']);
		} 
		
		echo json_encode($result);
		exit();	
	}
	
	/**
	 * Get month from date provided and convert to date range (1st day of this month - last day of this month)
	 * 
	 * @param string  The date
	 * 
	 * @return array  The array of date range
	 */
	protected static function getDateRange($date) {
		$month = date("F", strtotime($date));
		
		$dateRange['firstDate'] = date("Y-m-d", strtotime("first day of ".$month));
		$dateRange['lastDate'] = date("Y-m-d", strtotime("last day of ".$month));
		
		return $dateRange;
	}
}

?>
