<?php

namespace App\Controllers\Backoffice;

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
        View::renderTemplate('Backoffice/Product/add.html');
    }


}
