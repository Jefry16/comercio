<?php

namespace App\Controllers\Backoffice;

use App\Models\Admin;
use App\Modules\Auth as ModulesAuth;
use \Core\View;


class Auth extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfAdminLoggedIn();
    }
    public function loginAction()
    {
        if ($this->isPostRequest()) {

            $admin = Admin::authenticate($_POST['email'], $_POST['password']);

            if ($admin) {

                ModulesAuth::login($admin);

                $this->redirect('/admin/panel/home');

            } else {

                View::renderTemplate('Backoffice/Auth/login.html', [
                    'badCredentials' => 'Bad credentials'
                ]);

            }

        } else {

            View::renderTemplate('Backoffice/Auth/login.html');
        }
    }

    public function logoutAction()
    {
        ModulesAuth::logout();
        $this->redirect('/admin/auth/login');
    }
}
