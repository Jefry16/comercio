<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;

use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Pages extends \Core\Controller
{
     
    public function before()
    {
        
        $this->redirectIfNotAdmin();
    }
    public function indexAction()
    {

        echo 'index';
    }

}
