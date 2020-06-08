<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\Incomes;

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
	 * @return void
	 */
	public function showAction() {
		View::renderTemplate('Incomes/add.html', [
			//'user' => $this->user,
			'income_active' => 'active']);
		
		unset($_SESSION['amount']);
		unset($_SESSION['date']);
		unset($_SESSION['comment']);
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
			
			$_SESSION['amount'] = $_POST['amount'];
			$_SESSION['date'] = $_POST['date'];
			
			if (isset($_POST['comment'])) {
				$_SESSION['comment'] = $_POST['comment'];
			}

            $this->redirect('/dodaj-przychod');
		}
	}
}

?>
