<?php

namespace App\Controllers\Backoffice;

use \Core\View;


class Auth extends \Core\Controller
{
    
    public function loginAction()
    {
        View::renderTemplate('Backoffice/Auth/login.html');
    }
}
