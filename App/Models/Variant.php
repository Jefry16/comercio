<?php

namespace App\Models;

use PDO;

class Variant extends \Core\Model
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
        $stmt = $db->query('SELECT * FROM optiongroups');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save()
    {
        $this->validateName();

        if (empty($this->errors)) {
            $sql = "INSERT INTO optiongroups (OptionGroupName)VALUES(:name)";
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);

            return $stmt->execute();
        }
        return false;
    }

    private function validateName()
    {

        $this->name = trim($this->name);

        if ($this->name == '') {
            $this->errors['name'] = 'The name of the variant can\'t be empty';
            return;
        }

        if (strlen($this->name) > 50) {
            $this->errors['name'] = 'The name of the variant can\'t be longer than 50 characters';
            return;
        }

        if ($this->findByName($this->name)) {
            $this->errors['name'] = 'The name of the variant already exists';
            return;
        }
    }

    public function findByName($name)
    {

        $name = trim($name);

        $sql = 'SELECT * from optiongroups WHERE OptionGroupName = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Variant');
        $stmt->execute();

        return $stmt->fetch();
    }
}
