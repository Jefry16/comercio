<?php

namespace App\Models;

use PDO;

class Option extends \Core\Model
{

    public static function save($id, $name)
    {
        $sql = 'INSERT INTO options (OptionGroupID, OptionName) VALUES (:optionGroupId, :optionName)';

        $db = static::getDB();
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':optionGroupId', $id, PDO::PARAM_INT);
        $stmt->bindValue(':optionName', $name, PDO::PARAM_STR);

        $stmt->execute();
    }

    public static function getAllOptionsById($id)
    {
        $optionNames = [];
        $db = static::getDB();
        $stmt = $db->prepare('SELECT OptionName FROM options WHERE OptionGroupID = :id');
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $options = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($options as $key => $value) {
            $optionNames[]  = $value['OptionName'];
        }

        return $optionNames;
    }

    public static function addMultipleOptions($options, $id)
    {
        $optionsCount = 0;

        foreach ($options as $value) {

            $optionsCount++;

            static::save($id, $value);
        }

        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            $url = "https://";
        } else {
            $url = "http://";
        }
        header("Location: $url$_SERVER[HTTP_HOST]/admin/helper/optionsAdd?qt=$optionsCount", false, 303);
    }
}
