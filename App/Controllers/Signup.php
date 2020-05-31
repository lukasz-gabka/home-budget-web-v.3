<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Flash;

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
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html', [
			'register_active' => 'active']);
		
		unset($_SESSION['name']);
		unset($_SESSION['email']);
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
			
			//$user->sendActivationEmail();
			
			Flash::addMessage("Zarejestrowano poprawnie");

            $this->redirect('/');

        } else {
			
			//($user->errors);
			//exit();
			
			$user->errorString = implode("\n", $user->errors);
			
			Flash::addMessage($user->errorString, Flash::DANGER);
			
			$_SESSION['name'] = $_POST['name'];
			$_SESSION['email'] = $_POST['email'];

            /*View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);*/
            $this->redirect('/rejestracja');
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
}
