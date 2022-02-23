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
        View::renderTemplate('Backoffice/Category/index.html');
    }

    public function addAction()
    {
        View::renderTemplate('Backoffice/Category/add.html');        
    }

    

    private function okAction()
    {
        echo 'ok';
    }


}
