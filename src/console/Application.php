<?php

namespace mere\console;

use Exception;

class Application
{
    function __construct($config) 
    {
        // Constructor
    }
    
    public function run()
    {
        // Get route string
        $route = isset($_SERVER['argv'][1]) ? strtolower($_SERVER['argv'][1]) : null;
        // Get CLI command parameter except route
        $parameters = isset($_SERVER['argv'][2]) ? array_slice($_SERVER['argv'], 2) : [];

        // Show menu if there has no route input
        if (!$route) {
            return 'List Menu';
        }

        // Route URL conversion
        $route = trim($route, '/');
        // Case adjustment
        $route = preg_replace_callback('!-[a-z]!', function ($matches) {
            return strtoupper($matches[0]);
        }, strtolower($route));
        //
        $route = str_replace('-', '', $route);
        $routeArray = explode('/', $route);

        // Full route pattern guessing
        if (count($routeArray) >= 2) {

            $controllerClassName = ucfirst($routeArray[count($routeArray)-2]) . 'Controller';
            
            // Extract controller's path
            $controllerPath = substr($route, 0, strrpos($route, '/', 0));
            $controllerPath = (strrpos($controllerPath, '/', 0)!==false) 
                ? substr($controllerPath, 0, strrpos($controllerPath, '/', 0) + 1)
                : '';
            $controllerClass = "app\\commands\\" . str_replace("/", "\\", $controllerPath) . $controllerClassName;

            // Route check
            $action = end($routeArray);
            if (class_exists($controllerClass)) {
                $controller = new $controllerClass();
                if (method_exists($controller, $action)) {
                    return $this->runController($controller, $action, $parameters);
                }
            }
        }

        // No action input guessing
        $controllerClassName = ucfirst($routeArray[count($routeArray)-1]) . 'Controller';
        $controllerPath = $route;
        $controllerPath = (strrpos($controllerPath, '/', 0)!==false) 
            ? substr($controllerPath, 0, strrpos($controllerPath, '/', 0) + 1)
            : '';
        $controllerClass = "app\\commands\\" . str_replace("/", "\\", $controllerPath) . $controllerClassName;

        // Route check
        $action = 'index';
        if (class_exists($controllerClass)) {
            $controller = new $controllerClass();
            if (method_exists($controller, $action)) {
                return $this->runController($controller, $action, $parameters);
            }
        }
        
        throw new UnknownCommandException("Giving route `{$route}` not found", 1);
        
        return 0;
    }

    public function runController($controller, $action, $parameters=[])
    {
        return call_user_func_array([$controller, $action], $parameters);
    }
}
