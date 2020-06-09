<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// Add the routes
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('logowanie', ['controller' => 'Login', 'action' => 'new']);
$router->add('rejestracja', ['controller' => 'Signup', 'action' => 'new']);
$router->add('dodaj-przychod', ['controller' => 'Income', 'action' => 'show']);
$router->add('dodaj-wydatek', ['controller' => 'Expense', 'action' => 'show']);
$router->add('wyloguj', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('{controller}/{action}');

$router->dispatch($_SERVER['QUERY_STRING']);
