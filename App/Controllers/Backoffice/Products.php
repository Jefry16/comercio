<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Product as ModelsProduct;
use App\Modules\ImageUpload;
use \Core\View;
use Exception;

class Products extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfNotAdmin();
    }

    public function homeAction()
    {
        $paginatedProducts = ModelsProduct::getAllByPage();

        View::renderTemplate('Backoffice/Product/index.html', [
            'pagination' => $paginatedProducts
        ]);
    }

    public function addAction()
    {
        $categories = Category::getAll();

        if($this->postRequest()){

            $this->handleAdd();

        } else {

            View::renderTemplate('Backoffice/Product/add.html', [
                'categories' => $categories
            ]);
        }
    }


    private function handleAdd()
    {
        $product = new ModelsProduct($_POST);
           
            $product->save();
            
            if(!empty($product->errors)){
                var_dump($product->errors);
            }
    }


}
