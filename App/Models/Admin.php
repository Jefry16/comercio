<?php

namespace App\Models;

use PDO;
use App\Modules\Token;

use function PHPSTORM_META\type;

/**
 * Example user model
 *
 * PHP version 7.0
 */
class Admin extends \Core\Model
{
    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT id, name FROM users');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //Validation functions 
    private function validate($username, $firstName, $lastName, $email, $password)
    {
        $this->validateUsername($username);
        $this->validateFirstOrLastName($firstName, 'firstname');
        $this->validateFirstOrLastName($lastName, 'lastname');
        $this->validateEmail($email);
        $this->validatePassword($password);

    }

    private function validateUsername($username)
    {
        if($username == '') {
            $this->errors['username'] = 'Username is required *';
            return;
        }

        if (!preg_match('/^[A-Z0-9]{2,45}$/i', $username)) {
            $this->errors['username'] = 'Please enter a desired name using only letters and numbers! *';
            return;
        }

        if($this->usernameExist($username)){
            $this->errors['username'] = 'This username is in use already';
            return;
        }
    }

    private function validateFirstOrLastName($name, $type)
    {
        if (preg_match('~[0-9]+~', $name)) {
            $this->errors[$type] = "Inavalid $type";
            return;
        }
        if (!preg_match("/^[\p{L} ,.'-]{2,45}+$/u",$name)) {
            $this->errors[$type] = "Inavalid $type";
        }
    }

    private function validateEmail($email)
    {

        if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->errors['email'] = 'Email is invalid';

            return;
        }

        if ($this->emailExist($email)) {
            $this->errors['email'] = 'Email is in used already';
        }
    }

    private function validatePassword($password)
    {
        if (strlen($password) < 6) {
            $this->errors['password'] = 'Password length must be 6 caracters at least';
        }
    }

    public static function emailExist($email)
    {
        return static::findByEmail($email) !== false;
    }

    public static function findByEmail($email)
    {
        $sql = 'SELECT email, password, id, type from admin WHERE email = :email';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Admin');
        $stmt->execute();

        return $stmt->fetch();
    }

    public function usernameExist($username)
    {
        $sql = 'SELECT username from users WHERE username = :un';
        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':un', $username, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\User');
        $stmt->execute();

        return $stmt->fetch();
    }

    public static function authenticate($email, $password)
    {
        $admin = static::findByEmail($email);

        if ($admin && password_verify($password, $admin->password)) {
            return $admin;
        }

        return false;
    }

    public static function findById($id)
    {
        $sql = 'SELECT id from admin WHERE id = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Admin');
        $stmt->execute();

        return $stmt->fetch();
    }
}