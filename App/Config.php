<?php

namespace App;

/**
 * Application configuration
 *
 * PHP version 7.0
 */
class Config
{

    /**
     * Database host
     * @var string
     */
    const DB_HOST = '';

    /**
     * Database name
     * @var string
     */
    const DB_NAME = '';

    /**
     * Database user
     * @var string
     */
    const DB_USER = '';

    /**
     * Database password
     * @var string
     */
    const DB_PASSWORD = '';

    /**
     * Show or hide error messages on screen
     * @var boolean
     */
    const SHOW_ERRORS = false;
    
    /**
     * Secret key for hashing
     * @var boolean
     */
    const SECRET_KEY = "tZQ0nMiilQQkwFEvtBMT1XHqVYsasxO0";
    
    /**
     * Mailgun domain
     * @var string
     */
    const MAILGUN_DOMAIN = 'sandbox6506f1169b4446ecaa347bf788497f60.mailgun.org';
    
    /**
     * Mailgun API key
     * @var string
     */
    const MAILGUN_API_KEY = '053083e1ee6f2df2555024790cde33e7-e5e67e3e-ebd3a674';
}
