<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use \Core\View;


class Panel extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfNotAdmin();
    }

    public function homeAction()
    {
        View::renderTemplate('Backoffice/Initial/index.html');
    }


}
