<?php

namespace Core;

use App\Config;

use App\Modules\Auth;
use App\Modules\Message;

/**
 * Base controller
 *
 * PHP version 7.0
 */
abstract class Controller
{

    /**
     * Parameters from the matched route
     * @var array
     */
    protected $route_params = [];

    /**
     * Class constructor
     *
     * @param array $route_params  Parameters from the route
     *
     * @return void
     */
    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    /**
     * Magic method called when a non-existent or inaccessible method is
     * called on an object of this class. Used to execute before and after
     * filter methods on action methods. Action methods need to be named
     * with an "Action" suffix, e.g. indexAction, showAction etc.
     *
     * @param string $name  Method name
     * @param array $args Arguments passed to the method
     *
     * @return void
     */
    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {

            if ($this->before() !== false) {

                call_user_func_array([$this, $method], $args);
                
                $this->after();
            }
        } else {

            throw new \Exception("Method $method not found in controller " . get_class($this));
        
        }
    }

    /**
     * Before filter - called before an action method.
     *
     * @return void
     */
    protected function before()
    {
    }

    /**
     * After filter - called after an action method.
     *
     * @return void
     */
    protected function after()
    {
    }

    protected function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
    }

    protected function redirectIfNotAdmin()
    {
         if (!isset($_SESSION['adminType'])) {
            $this->redirect('/admin/auth/login');
         }
    }

    protected function redirectIfAdminLoggedIn()
    {
        if (isset($_SESSION['adminType'])) {
            $this->redirect('/admin/panel/home');
         }
    }

    protected function redirectIfNotRequestMethod($method, $url)
    {
        if ($_SERVER['REQUEST_METHOD'] != $method) {
            
            $this->redirect($url);
            
            exit;
        }
    }

    protected function postRequest()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST';
    }


    protected function checkRequestMethod($method)
    {
        return $_SERVER['REQUEST_METHOD'] === $method;
    }

    protected function handleAdd($model, $entityName)
    {
        if ($this->postRequest()) {
            $entity = new $model($_POST);

            if ($entity->save()) {
                $_POST = [];
                $this->redirect("/admin/helper/index?entityname=$entityName");
            }
            return count($entity->errors) > 0 ? $entity->errors : null;
        }
    }

}
