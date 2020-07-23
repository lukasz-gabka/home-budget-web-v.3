<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;
use \App\Models\PaymentMethodsDefault;
use \App\Models\PaymentMethods;
use \App\Models\IncomeCategoriesDefault;
use \App\Models\IncomeCategories;
use \App\Models\ExpenseCategoriesDefault;
use \App\Models\ExpenseCategories;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
class Signup extends \Core\Controller
{

    /**
     * Show the signup page
     * 
     * @param array  The arguments to display (optional)
     *
     * @return void
     */
    public function newAction($args = 0)
    {
		if (empty($_SESSION['user_id'])) {
			View::renderTemplate('Signup/new.html', [
			'register_active' => 'active',
			'signup' => $args]);
		} else {
			Flash::addMessage("Strona niedostępna dla zalogowanych użytkowników", Flash::INFO);
			
			$this->redirect('/');
		}
        
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        if ($user->save()) {

			$user = User::findByEmail($user->email);
			
			static::saveDefaultPaymentMethods($user->id);
			static::saveDefaultIncomeCategories($user->id);
			static::saveDefaultExpenseCategories($user->id);

			Flash::addMessage("Zarejestrowano poprawnie");

            $this->redirect('/');

        } else {
			
			$user->errorString = implode(PHP_EOL, $user->errors);
			
			Flash::addMessage($user->errorString, Flash::DANGER);
			
			$signup['name'] = $_POST['name'];
			$signup['email'] = $_POST['email'];
			
			$this->newAction($signup);
        }
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }
    
    /**
     * Activate a new account
     * 
     * @return void
     */
    public function activateAction() {
		User::activate($this->route_params['token']);
		
		$this->redirect('/signup/activated');
	}
	
	/**
	 * Show the activation success page
	 * 
	 * @return void
	 */
	public function activatedAction() {
		View::renderTemplate('Signup/activated.html');
	}
	
	/**
	 * Saves default payment methods to payment methods model 
	 * 
	 * @param int The user's id
	 * 
	 * @return void
	 */
	protected static function saveDefaultPaymentMethods($id) {
		$paymentMethods = PaymentMethodsDefault::get();
		
		PaymentMethods::saveDefault($id, $paymentMethods);
	}
	
	/**
	 * Saves default income categories to income categories model 
	 * 
	 * @param int The user's id
	 * 
	 * @return void
	 */
	protected static function saveDefaultIncomeCategories($id) {
		$categories = IncomeCategoriesDefault::get();
		
		IncomeCategories::saveDefault($id, $categories);
	}
	
	/**
	 * Saves default expense categories to expense categories model 
	 * 
	 * @param int The user's id
	 * 
	 * @return void
	 */
	protected static function saveDefaultExpenseCategories($id) {
		$categories = ExpenseCategoriesDefault::get();
		
		ExpenseCategories::saveDefault($id, $categories);
	}
}
