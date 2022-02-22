<?php

namespace App\Models;

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
}