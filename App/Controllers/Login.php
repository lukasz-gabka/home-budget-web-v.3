<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

/**
 * Login controller
 *
 * PHP version 7.0
 */
class Login extends \Core\Controller
{

    /**
     * Show the login page
     * 
     * @param array  The arguments to display (optional)
     *
     * @return void
     */
    public function newAction($arg = 0)
    {
		if (empty($_SESSION['user_id'])) {
			View::renderTemplate('Login/new.html', [
				'login_active' => 'active',
				'login' => $arg]);
		} else {
			Flash::addMessage("Strona niedostępna dla zalogowanych użytkowników", Flash::INFO);
			
			$this->redirect('/');
		}
    }

    /**
     * Log in a user
     *
     * @return void
     */
    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $remember_me = isset($_POST['remember_me']);

        if ($user && empty($user->errors)) {

            Auth::login($user, $remember_me);
			
            Flash::addMessage("Zalogowano poprawnie");

            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage($user->errors[0], Flash::DANGER);
            
            $login['email'] = $_POST['email'];
            $login['remember_me'] = $remember_me;

            $this->newAction($login);
            
        }
    }

    /**
     * Log out a user
     *
     * @return void
     */
    public function destroyAction()
    {
        Auth::logout();

        $this->redirect('/login/show-logout-message');
    }

    /**
     * Show a "logged out" flash message and redirect to the homepage. Necessary to use the flash messages
     * as they use the session and at the end of the logout method (destroyAction) the session is destroyed
     * so a new action needs to be called in order to use the session.
     *
     * @return void
     */
    public function showLogoutMessageAction()
    {
        Flash::addMessage('Wylogowano poprawnie');

        $this->redirect('/');
    }
}
