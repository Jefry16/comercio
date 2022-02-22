<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Product as ModelsProduct;
use \Core\View;

class Categories extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfNotAdmin();
    }

    public function homeAction()
    {
        View::renderTemplate('Backoffice/Product/category.html');
    }

    public function addAction()
    {
        $categories = Category::getAll();

        if($this->checkRequestMethod('POST')){
            $product = new ModelsProduct($_POST);
           
            $product->save();
            
            if(!empty($product->errors)){
                var_dump($product->errors);
            }
        } else {
            View::renderTemplate('Backoffice/Product/add.html', [
                'categories' => $categories
            ]);
        }
    }


}
