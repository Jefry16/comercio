<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Modules\Message;
use \Core\View;

/**
 * Home controller
 *
 * PHP version 7.0
 */
class Helper extends \Core\Controller
{
     
    public function before()
    {
        $this->redirectIfNotAdmin();
    }
    public function indexAction()
    {
        Message::set("A new $_GET[entityname] was added.", Message::SUCCESS);
        header('Location:'. $_SERVER['HTTP_REFERER'] , true, 303);
    }

}
