<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

/**
 * User model
 *
 * PHP version 7.0
 */
class User extends \Core\Model
{

    /**
     * Error messages
     *
     * @var array
     */
    public $errors = [];

    /**
     * Class constructor
     *
     * @param array $data  Initial property values (optional)
     *
     * @return void
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        };
    }

    /**
     * Save the user model with the current property values
     *
     * @return boolean  True if the user was saved, false otherwise
     */
    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {

            $password_hash = password_hash($this->password1, PASSWORD_DEFAULT);

            $sql = 'INSERT INTO users (name, email, password_hash)
                    VALUES (:name, :email, :password_hash)';

            $db = static::getDB();
            $stmt = $db->prepare($sql);

            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
            $stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    /**
     * Validate current property values, adding valiation error messages to the errors array property
     *
     * @return void
     */
    public function validate()
    {
        // Name
        if ($this->name == '') {
            $this->errors[] = 'Imię jest wymagane';
        }
        
        if (strlen($this->name) < 3) {
            $this->errors[] = 'Imię nie może być krótsze niż 3 znaki';
        }
        
        if (strlen($this->name) > 15) {
            $this->errors[] = 'Imię nie może być dłuższe niż 15 znaków';
        }

        // email address
        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors[] = 'Niepoprawny adres e-mail';
        }
        if (static::emailExists($this->email, $this->id ?? null)) {
            $this->errors[] = 'Adres e-mail jest już zajęty';
        }

        // Password
        if (isset($this->password1)) {
			
			if ($this->password1 != $this->password2) {
				$this->errors[] = 'Hasła muszą być takie same';
			}

            if (strlen($this->password1) < 6) {
                $this->errors[] = 'Hasło nie może być krótsze niż 6 znaków';
            }

            if (preg_match('/.*[a-z]+.*/i', $this->password1) == 0) {
                $this->errors[] = 'Hasło musi się składać z co najmniej jednej litery';
            }

            if (preg_match('/.*\d+.*/i', $this->password1) == 0) {
                $this->errors[] = 'Hasło musi się składać z co najmniej jednej liczby';
            }

        }
    }

    /**
     * See if a user record already exists with the specified email
     *
     * @param string $email email address to search for
     * @param string $ignore_id Return false anyway if the record found has this ID
     *
     * @return boolean  True if a record already exists with the specified email, false otherwise
     */
    public static function emailExists($email, $ignore_id = null)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if ($user->id != $ignore_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find a user model by email address
     *
     * @param string $email email address to search for
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByEmail($email)
    {
        $sql = 'SELECT * FROM users WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Authenticate a user by email and password.
     *
     * @param string $email email address
     * @param string $password password
     *
     * @return object The user object
     */
    public static function authenticate($email, $password)
    {
        $user = static::findByEmail($email);

        if ($user) {
            if (password_verify($password, $user->password_hash)) {
                return $user;
            } else {
				$user->errors[] = "Nieprawidłowe hasło";
				return $user;
			}
        } else {
			$user->errors[] = "Użytkownik o takim adresie e-mail nie istnieje";
			return $user;
		}
    }

    /**
     * Find a user model by ID
     *
     * @param string $id The user ID
     *
     * @return mixed User object if found, false otherwise
     */
    public static function findByID($id)
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->setFetchMode(PDO::FETCH_CLASS, get_called_class());

        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Remember the login by inserting a new unique token into the remembered_logins table
     * for this user record
     *
     * @return boolean  True if the login was remembered successfully, false otherwise
     */
    public function rememberLogin()
    {
        $token = new Token();
        $hashed_token = $token->getHash();
        $this->remember_token = $token->getValue();

        $this->expiry_timestamp = time() + 60 * 60 * 24 * 30;  // 30 days from now

        $sql = 'INSERT INTO remembered_logins (token_hash, user_id, expires_at)
                VALUES (:token_hash, :user_id, :expires_at)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':token_hash', $hashed_token, PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $this->id, PDO::PARAM_INT);
        $stmt->bindValue(':expires_at', date('Y-m-d H:i:s', $this->expiry_timestamp), PDO::PARAM_STR);

        return $stmt->execute();
    }
    
    /**
     * Changes name
     * 
     * @return boolean  True if the name has changed successfully, false otherwise
     */
    public function changeName() {
		$this->validate();
		
		if (empty($this->errors)) {
			$sql = "UPDATE users SET name = :name WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Changes e-mail
     * 
     * @return boolean  True if the e-mail has changed successfully, false otherwise
     */
    public function changeEmail() {
		$this->validate();
		
		if (empty($this->errors)) {
			$sql = "UPDATE users SET email = :email WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':email', $this->email, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		
		return false;
	}
	
	/**
     * Changes password
     * 
     * @return boolean  True if the password has changed successfully, false otherwise
     */
    public function changePassword() {
		$this->validate();
		
		if (empty($this->errors)) {
			$password_hash = password_hash($this->password1, PASSWORD_DEFAULT);
			
			$sql = "UPDATE users SET password_hash = :password_hash WHERE id = :id";
			
			$db = static::getDB();
			$stmt = $db->prepare($sql);

			$stmt->bindValue(':password_hash', $password_hash, PDO::PARAM_STR);
			$stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
			
			return $stmt->execute();
		}
		
		return false;
	}
}
