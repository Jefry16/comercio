<?php

namespace App\Models;

use App\Modules\ImageUpload;
use App\Modules\Paginator;
use App\Modules\Token;
use DateTime;
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

    public static function getAllByPage()
    {
        $db = static::getDB();
        
        return new Paginator($_GET, 10, $db, 'products');
    }

    public function save()
    {
        $this->validateBeforeSaving();

        if (empty($this->errors)) {
            $sql = "INSERT INTO products 
            (sku, 
            name, 
            active, 
            shortDescription, 
            longDescription,
            cartDescription, 
            thumbnail,
            slug) 
            VALUES
            (:sku, 
            :name, 
            :active, 
            :shortDescription, 
            :longDescription, 
            :cartDescription, 
            :thumbnail,
            :slug)";
            $db = static::getDB();
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':sku', $this->createSku(), PDO::PARAM_STR);
            $stmt->bindValue(':name', $this->name, PDO::PARAM_STR);
            $stmt->bindValue(':active', $this->active, PDO::PARAM_BOOL);
            $stmt->bindValue(':shortDescription', $this->shortDescription, PDO::PARAM_STR);
            $stmt->bindValue(':longDescription', $this->longDescription, PDO::PARAM_STR);
            $stmt->bindValue(':cartDescription', $this->cartDescription, PDO::PARAM_STR);
            $stmt->bindValue(':thumbnail', 'thumbnail', PDO::PARAM_STR);
            $stmt->bindValue(':slug', $this->slug($this->name), PDO::PARAM_STR);

            return $stmt->execute();
        }

        return false;
    }

    public static function findByName($name)
    {
        $name = trim($name);

        $sql = 'SELECT * from products WHERE name = :name';

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
        $this->validateThumbnail();
        $this->validateIsActive($this->active);
        $this->validateCartDesc($this->cartDescription);
        $this->validateShortDesc($this->shortDescription);
        $this->validateLongDescription($this->longDescription);
        $this->validatePrice($this->price);
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
            $this->errors['cartDescription'] = 'The length of the cart description can\'t be longer than 100 characters.';
            return false;
        }
    }

    private function validateShortDesc($desc)
    {
        $desc = trim($desc);
        $lengthOfDesc = intval(strlen($desc), 10);

        if ($lengthOfDesc > 200) {
            $this->errors['shortDescription'] = 'The length of the short description can\'t be longer than 200 characters.';
            return false;
        }
    }

    private function validateLongDescription($desc)
    {
        $desc = trim($desc);
        $lengthOfDesc = intval(strlen($desc), 10);

        if ($lengthOfDesc > 500) {
            $this->errors['shortDescription'] = 'The length of the short description can\'t be longer than 500 characters.';
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
        if (!Category::findById($category)) {
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
            $this->errors['thumbnail'] = $uploadResult[0];
            return;
        }

        $this->thumb = $uploadResult;
    }

    private function createSku()
    {
        $now = new DateTime();
        return $now->getTimestamp();
    }

    public static function getProductVariants($id)
    {
        $sql = "SELECT * FROM productoptions INNER JOIN options on productoptions.OptionID = options.OptionID INNER JOIN optiongroups
        ON productoptions.OptionGroupID = optiongroups.OptionGroupID WHERE productoptions.ProductID = :id";
        
        $db = static::getDB();

        $stmt = $db->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $variantsToBeTreated = [];
        $variantsArray = [];

        foreach ($rows as $key => $one) {

            $data[] = $one['OptionGroupName'];

            foreach ($one as $key => $two) {

                foreach ($data as $three) {

                    if ($three == $two) {
                        $variantsToBeTreated[$two][] = $one['OptionName'];
                    }
                }
            }
        }

        foreach ($variantsToBeTreated as $key => $val) {
            $variantsArray[$key] = array_unique($val);
        }

        return $variantsArray;
    }

    public static function findBySlug($slug)
    {
        $slug = trim($slug);

        $sql = 'SELECT * from products WHERE ProductSlug = :slug';

        $db = static::getDB();
        $stmt = $db->prepare($sql);

        $stmt->bindValue(':slug', $slug, PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS, '\App\Models\Product');
        $stmt->execute();

        return $stmt->fetch();
    }
}
