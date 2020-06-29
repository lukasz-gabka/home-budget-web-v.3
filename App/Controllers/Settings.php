<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Settings controller
 *
 * PHP version 7.0
 */
class Settings extends \Core\Controller
{

    /**
     * Show the settings page
     * 
     * @param array  The arguments to display (optional)
     *
     * @return void
     */
    public function newAction($arg = 0)
    {
        View::renderTemplate('Settings/user.html', [
			'settings_active' => 'active',
			'settings' => $arg]);
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

			$this->newAction();
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

			$this->newAction();
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

			$this->newAction();
		}
	}
}
