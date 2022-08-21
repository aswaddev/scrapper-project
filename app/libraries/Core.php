<?php

/*
 * App Core Class
 * Creates URL & loads core controller
 * URL FORMAT - /controller/method/params
 */

class Core
{
    protected $currentController = 'Pages';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct()
    {
        $url = Core::getUrl();

        $parts = count($url);

        $controllerName = $methodName = '';

        if ($parts) {
            if (is_numeric($url[$parts - 1])) {
                $controllerName = $url[$parts - 3];
                $methodName = $url[$parts - 2];
            } else {
                $controllerName = $url[$parts - 2];
                $methodName = $url[$parts - 1];
            }
        }

        // Look in controllers for first value
        if ($parts && file_exists('../app/controllers/' . ucwords($controllerName) . '.php')) {

            // If exists, set as controller
            $this->currentController = ucwords($controllerName);
            // Unset 0 Index
            unset($controllerName);
            // Require the controller
            require_once '../app/controllers/' . $this->currentController . '.php';

            // Instantiate controller class
            $this->currentController = new $this->currentController;

            //Check for second part of URL
            if (isset($methodName)) {
                //Check to see if method exists in controller
                if (method_exists($this->currentController, $methodName)) {
                    $this->currentMethod = $methodName;
                    // Unset 1 index
                    unset($methodName);

                    // Get params
                    $params = array_values($url);
                    $this->params = $url ? [end($params)] : [];

                    //Call a callback with array of params
                    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
                } else {
                    // Get params
                    $params = array_values($url);
                    $this->params = $url ? [end($params)] : [];

                    //Call a callback with array of params
                    call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
                }
            } else {
                // Get params
                $params = array_values($url);
                $this->params = $url ? [end($params)] : [];
                //Call a callback with array of params
                call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
            }
        } else {
            // Require the controller
            require_once '../app/controllers/' . $this->currentController . '.php';
            // Instantiate controller class
            $this->currentController = new $this->currentController;
            // Get params
            $params = array_values($url);
            $this->params = $url ? [end($params)] : [];
            //Call a callback with array of params
            call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
        }
    }

    public static function getUrl()
    {
        if (isset($_SERVER['REDIRECT_URL'])) {
            $url = rtrim($_SERVER['REDIRECT_URL'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }
}
