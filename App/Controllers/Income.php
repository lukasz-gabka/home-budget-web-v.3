<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;
use \App\Models\IncomeCategories;

/**
 * Income controller
 * 
 * PHP version 7.0
 */
class Income extends Authenticated {
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
	 * Show the income page
	 * 
	 * @param array  The arguments to display (optional)
	 * 
	 * @return void
	 */
	public function showAction($arg = 0) {
		$display['userIncomeCategories'] = IncomeCategories::get($_SESSION['user_id']);
		
		View::renderTemplate('Incomes/add.html', [
			//'user' => $this->user,
			'income_active' => 'active',
			'income' => $arg,
			'display' => $display]);
	}
	
	/**
	 * Add a new income
	 * 
	 * @return void
	 */
	public function addAction() {		
		$incomes = new Incomes($_POST);
		
		if ($incomes->save($this->user->id)) {
			Flash::addMessage('Dodano przychód');
			
			$this->redirect('/dodaj-przychod');
		} else {
			$incomes->errorString = implode("\n", $incomes->errors);
			
			Flash::addMessage($incomes->errorString, Flash::DANGER);
			
			$income['amount'] = $_POST['amount'];
			$income['date'] = $_POST['date'];
			
			if (isset($_POST['comment'])) {
				$income['comment'] = $_POST['comment'];
			}

            $this->showAction($income);
		}
	}
}

?>
