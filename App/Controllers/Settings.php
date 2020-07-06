<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;
use \App\Models\IncomeCategories;
use \App\Models\ExpenseCategories;
use \App\Models\PaymentMethods;

/**
 * Settings controller
 *
 * PHP version 7.0
 */
class Settings extends \Core\Controller
{

    /**
     * Show the user settings page
     * 
     * @param array  The arguments to display (optional)
     *
     * @return void
     */
    public function newUserAction($arg = 0)
    {
        View::renderTemplate('Settings/user.html', [
			'settings_active' => 'active',
			'settings' => $arg]);
    }
    
    /**
     * Show the categories settings page
     * 
     * @param array  The arguments to display (optional)
     *
     * @return void
     */
    public function newCategoriesAction($arg = 0)
    {
		$incomeCategories = IncomeCategories::get($_SESSION['user_id']);
		$expenseCategories = ExpenseCategories::get($_SESSION['user_id']);
		$paymentMethods = PaymentMethods::get($_SESSION['user_id']);
		
		//var_dump($incomeCategories['name']);
		//exit();
		
        View::renderTemplate('Settings/categories.html', [
			'settings_active' => 'active',
			'settings' => $arg,
			'incomeCategories' => $incomeCategories,
			'expenseCategories' => $expenseCategories,
			'paymentMethods' => $paymentMethods]);
    }
    
    /**
     * Change user's name by user's input
     * 
     * @return void
     */
    public function changeNameAction() {
		$user = User::findByID($_SESSION['user_id']);
		
		$user->name = $_POST['name'];
		
		if($user->changeName()) {
			Flash::addMessage("Imię zmieniono poprawnie");

            $this->redirect('/dane-uzytkownika');
		} else {
			$user->errorString = implode(PHP_EOL, $user->errors);
			
			Flash::addMessage($user->errorString, Flash::DANGER);

			$this->redirect('/dane-uzytkownika');
		}
	}
	
	/**
     * Change user's e-mail by user's input
     * 
     * @return void
     */
    public function changeEmailAction() {
		$user = User::findByID($_SESSION['user_id']);
		
		$user->email = $_POST['email'];
		
		if($user->changeEmail()) {
			Flash::addMessage("e-mail zmieniono poprawnie");

            $this->redirect('/dane-uzytkownika');
		} else {
			$user->errorString = implode(PHP_EOL, $user->errors);
			
			Flash::addMessage($user->errorString, Flash::DANGER);

			$this->redirect('/dane-uzytkownika');
		}
	}
	
	/**
     * Change user's password by user's input
     * 
     * @return void
     */
    public function changePasswordAction() {
		$user = User::findByID($_SESSION['user_id']);
		
		$user->password1 = $_POST['password1'];
		$user->password2 = $_POST['password2'];
		
		if($user->changePassword()) {
			Flash::addMessage("Hasło zmieniono poprawnie");

            $this->redirect('/dane-uzytkownika');
		} else {
			$user->errorString = implode(PHP_EOL, $user->errors);
			
			Flash::addMessage($user->errorString, Flash::DANGER);

			$this->redirect('/dane-uzytkownika');
		}
	}
	
	/**
     * Delete an account
     * 
     * @return void
     */
    public function deleteAccountAction() {
		$user = User::findByID($_SESSION['user_id']);
		
		if($user->deleteAccount()) {
			Auth::logout();
			
			$this->redirect('/settings/show-delete-account');
		} else {
			Flash::addMessage("Wystąpił błąd, spróbuj ponownie później", Flash::DANGER);

			$this->redirect('/dane-uzytkownika');
		}
	}
	
	/**
	 * Shows a flash message after deletion of an account
	 * 
	 * @return void
	 */
	public function showDeleteAccountAction()
    {
        Flash::addMessage('Konto usunięto poprawnie');

        $this->redirect('/');
    }
    
    /*
     * Edit income category
     * 
     * @return void
     */
    public function editIncomeCategoryAction() {
		$newIncomeCategory = $_POST['newIncomeCategory'];
		$currentIncomeCategory = $_POST['hidden'];
		
		if(!IncomeCategories::checkName($newIncomeCategory)) {
			IncomeCategories::edit($newIncomeCategory, $currentIncomeCategory);
			
			Flash::addMessage("Nazwa kategorii zmieniona poprawnie");

			$this->redirect('/ustawienia-kategorii');
		} else {
			Flash::addMessage("Podana kategoria przychodu już istnieje", Flash::DANGER);

			$this->redirect('/ustawienia-kategorii');
		}
	}
}
