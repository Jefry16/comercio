<?php

namespace App\Modules;

use App\Config;
use App\Models\Admin;
use App\Models\Login;
use App\Models\User;


class Auth
{
    public static function login($admin)
    {
        $_SESSION['adminType'] = $admin->type;
        $_SESSION['id'] = $admin->id;

        session_regenerate_id(true);
    }

    public static function getUser()
    {
        return Admin::findById($_SESSION['id'] ?? '');
    }

    public static function getAdminType()
    {
        return $_SESSION['adminType'] ?? false;
    }
    public static function logout()
    {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }



}