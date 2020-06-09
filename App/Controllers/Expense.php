<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Expenses;

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
	 * @return void
	 */
	public function showAction() {
		View::renderTemplate('Expenses/add.html', [
			'expense_active' => 'active']);
		
		unset($_SESSION['amount']);
		unset($_SESSION['date']);
		unset($_SESSION['comment']);
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
			
			$_SESSION['amount'] = $_POST['amount'];
			$_SESSION['date'] = $_POST['date'];
			
			if (isset($_POST['comment'])) {
				$_SESSION['comment'] = $_POST['comment'];
			}

            $this->redirect('/dodaj-wydatek');
		}
	}
}

?>
