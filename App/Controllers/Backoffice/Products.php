<?php

namespace App\Controllers\Backoffice;

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
        View::renderTemplate('Backoffice/Product/index.html');
    }

    public function addAction()
    {
        if($this->checkRequestMethod('POST')){
            $product = new ModelsProduct($_POST);
           
            $product->save();
            
            if(!empty($product->errors)){
                var_dump($product->errors);
            }
        } else {
            
            View::renderTemplate('Backoffice/Product/add.html');
        }
    }


}
