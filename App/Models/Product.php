<?php

namespace App\Models;

use App\Modules\ImageUpload;
use App\Modules\Token;
use PDO;



class Product extends \Core\Model
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
        $this->validateBeforeSaving();

        if (empty($this->errors)) {
            $sql = "INSERT INTO products 
            (ProductSKU, 
            ProductName, 
            ProductPrice, 
            ProductWeight, 
            ProductCartDesc, 
            ProductShortDesc, 
            ProductLongDesc,
            ProductThumb, 
            ProductImage, 
            ProductCategoryID, 
            ProductStock, 
            ProductLive, 
            ProductUnlimited, 
            ProductLocation) 
            VALUES
            (:SKU, 
            :name, 
            :price, 
            :weight, 
            :cartDesc,
            :shortDesc, 
            :longDesc, 
            :thumb, 
            :images, 
            :category, 
            :stock, 
            :active, 
            :unlimited, 
            :location)";
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $SKU = new Token();
            $stmt->bindValue(':SKU', $SKU->getValue(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':price', $this->price, PDO::PARAM_INT);
            $stmt->bindValue(':weight', $this->weight, PDO::PARAM_INT);
            $stmt->bindValue(':cartDesc', $this->cartDesc, PDO::PARAM_STR);
            $stmt->bindValue(':shortDesc', $this->shortDesc, PDO::PARAM_STR);
            $stmt->bindValue(':longDesc', $this->longDesc, PDO::PARAM_STR);
            $stmt->bindValue(':thumb', $this->thumb, PDO::PARAM_STR);
            $stmt->bindValue(':images', ImageUpload::imagesFromNewProduct(), PDO::PARAM_STR);
            $stmt->bindValue(':category', $this->category, PDO::PARAM_INT);
            $stmt->bindValue(':stock', $this->stock, PDO::PARAM_INT);
            $stmt->bindValue(':active', $this->active, PDO::PARAM_INT);
            $stmt->bindValue(':unlimited', $this->unlimited, PDO::PARAM_INT);
            $stmt->bindValue(':location', $this->location, PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public static function findByName($name)
    {
        $name = trim($name);

        $sql = 'SELECT * from products WHERE ProductName = :name';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Product');
        $stmt->execute();

        return $stmt->fetch();
    }

    private function validateBeforeSaving()
    {
        $this->validateName($this->name);
        $this->validatePrice($this->price);
        $this->validateThumbnail();
        $this->validateIsUnlimited($this->unlimited);
        $this->validateIsActive($this->active);
        $this->validateCartDesc($this->cartDesc);
        $this->validateWeight($this->weight);
        $this->validateShortDesc($this->shortDesc);
        $this->validateLongDescription($this->longDesc);
        $this->validateStock($this->stock);
        $this->validateCategory($this->category);
    }

    private function validateName($name)
    {
        $name = trim($name);
        $lengthOfName = intval(strlen($name), 10);

        if ($lengthOfName > 100) {
            $this->errors['name'] = 'The length of the name can\'t be longer than 100 characters.';
            return false;
        }

        if ($lengthOfName < 1) {
            $this->errors['name'] = 'The name can\'t be empty.';
            return false;
        }

        if (static::findByName($name)) {
            $this->errors['name'] = 'This name is being used already.';
            return false;
        }
    }

    private function validatePrice($price)
    {
        $price = trim($price);

        if (!is_numeric($price)) {
            $this->errors['price'] = 'The price format is invalid.';
            return false;
        }

        if ($price < 0) {
            $this->errors['price'] = 'The price can\'t be less than zero.';
            return false;
        }
    }

    private function validateWeight($weight)
    {
        $weight = trim($weight);

        if (!is_numeric($weight)) {
            $this->errors['weight'] = 'The weight format is invalid.';
            return false;
        }

        if ($weight < 0) {
            $this->errors['weight'] = 'The weight can\'t be less than zero.';
            return false;
        }
    }

    private function validateCartDesc($desc)
    {
        $desc = trim($desc);
        $lengthOfDesc = intval(strlen($desc), 10);

        if ($lengthOfDesc > 100) {
            $this->errors['cartDesc'] = 'The length of the cart description can\'t be longer than 100 characters.';
            return false;
        }
    }

    private function validateShortDesc($desc)
    {
        $desc = trim($desc);
        $lengthOfDesc = intval(strlen($desc), 10);

        if ($lengthOfDesc > 200) {
            $this->errors['shortDesc'] = 'The length of the short description can\'t be longer than 200 characters.';
            return false;
        }
    }

    private function validateLongDescription($desc)
    {
        $desc = trim($desc);
        $lengthOfDesc = intval(strlen($desc), 10);

        if ($lengthOfDesc > 500) {
            $this->errors['shortDesc'] = 'The length of the short description can\'t be longer than 500 characters.';
            return false;
        }
    }

    private function validateStock($stock)
    {
        $stock = trim($stock);

        if (!is_numeric($stock)) {
            $this->errors['stock'] = 'The stock format is invalid.';
            return false;
        }
    }

    private function validateCategory($category)
    {
        if(!Category::findById($category)) {
            $this->errors['category'] = 'This category is invalid';
            return;
        }
    }

    private function validateIsActive($value)
    {
        $value = intval(strlen($value), 10);

        if ($value !== 0 && $value !== 1) {
            $this->errors['active'] = 'Invalid value for active.';
        }
    }

    private function validateIsUnlimited($value)
    {
        $value = intval(strlen($value), 10);

        if ($value !== 0 && $value !== 1) {
            $this->errors['unlimited'] = 'Invalid value for unlimited.';
        }
    }

    private function validateThumbnail()
    {
        $uploadResult = ImageUpload::singleImageNewProduct();

        if ($uploadResult === 0) {
            $this->thumb = null;
            return;
        }

        if (is_array($uploadResult)) {
            $this->errors['thumb'] = $uploadResult[0];
            return;
        }

        $this->thumb = $uploadResult;
    }

    //todo: validate location in the future
    private function validateLocation($location)
    {
        $location = trim($location);

        if ($location) {
            
        }
    }
}
