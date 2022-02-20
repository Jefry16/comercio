<?php

namespace App\Controllers\Backoffice;

use App\Models\Product as ModelsProduct;
use \Core\View;


class Product extends \Core\Controller
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

        }else{
        View::renderTemplate('Backoffice/Product/add.html');
        }
    }


}
