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

}