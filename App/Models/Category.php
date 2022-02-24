<?php

namespace App\Models;

use App\Modules\Paginator;
use PDO;

class Category extends \Core\Model
{
    public $errors = [];

    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function save()
    {
        $this->validate();

        if (empty($this->errors)) {
            $sql = "INSERT INTO productcategories ( CategoryName ) VALUES ( :name )";

            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            return $stmt->execute();
        }
        return false;
    }

    public static function getAllByPage()
    {
        $db = static::getDB();

        return new Paginator($_GET, 2, $db, 'productcategories');
    }
    public static function findById($id)
    {
        $sql = 'SELECT * from productcategories WHERE CategoryId = :id';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Category');

        $stmt->execute();

        return $stmt->fetch();
    }

    public static function getAll()
    {
        $db = static::getDB();
        $stmt = $db->query('SELECT * FROM productcategories');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function findByName($name)
    {
        $sql = 'SELECT * from productcategories WHERE CategoryName = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Category');

        $stmt->execute();

        return $stmt->fetch();
    }

    private function validate()
    {
        $this->validateName($this->name);
    }
    private function validateName($name)
    {
        $name = trim($name);

        if (static::findByName($name)) {
            $this->errors['name'] = 'This category already exists.';
            return;
        }

        if (empty($name)) {
            $this->errors['name'] = 'The category name can\'t be empty.';
            return;
        }
    }
}
