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

            // Parse all commands
            $menu = $this->_parseControllers(app_path('commands'));

            // Guide
            echo "\n\033[1mThe following commands are available:\033[0m\n";

            // Print routes
            foreach ($menu as $controller => $controllerMenu) {
                // Controller
                echo "\n- \e[33m{$controller}\e[0m  \033[1m{$controllerMenu['comment']}\033[0m\n";
                foreach ($controllerMenu['list'] as $route => $actionMenu) {
                    // Route
                    echo "\e[32m    {$route}\e[0m {$actionMenu['comment']}\n";
                }
            }
            return;
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

    /**
     * Parse controller commands recursively
     *
     * @param string $path
     * @param string $prefix Commands' folders route
     * @return void
     */
    private function _parseControllers($path, $prefix='')
    {
        $menu = [];
        
        // Get all files from a commands folder
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $key => $filename) {
            
            $filePath = $path . DIRECTORY_SEPARATOR . $filename; 
            // Is a folder
            if (is_dir($filePath)) {

                $prefix .= "{$filename}/";
                $menu = array_merge($menu, $this->_parseControllers($filePath, $prefix));

            } 
            // Is a controller
            elseif (strpos($filename, '.')!==false && strpos($filename, 'Controller')!==false) {

                // Get controller class name
                $fileInfo = pathinfo($filename);
                $controllerClass = $fileInfo['filename'];
                // Get controller route name with folder layers
                $controllerRoute = $prefix . $this->_toRoute(substr($controllerClass, 0, -10));
                // Add into route menu
                $menu[$controllerRoute] = ['comment'=>'', 'list'=>[]];
                $selfMenu = &$menu[$controllerRoute];

                // Get controller's namespace and get reflection
                $prefixNS = str_replace("/", "\\", $prefix);
                $class = new \ReflectionClass("app\\commands\\" . $prefixNS . $controllerClass);
                $selfMenu['comment'] = $this->_getCommentTitle($class->getDocComment());
                $methods = $class->getMethods();
                foreach($methods as $method) {
                    // Get actions
                    if ($method->isPublic()) {
                        // Method name to route name
                        $actionRoute = $this->_toRoute($method->name);
                        // Add into self-controller route menu
                        $selfMenu['list']["{$controllerRoute}/$actionRoute"] = ['comment' => $this->_getCommentTitle($method->getDocComment())];
                    }
                }
            }
        }

        return $menu;
    }

    private function _toRoute($namespace)
    {
        // echo 'aa';exit;
        // Case adjustment
        $route = preg_replace_callback('/[A-Z]/', function ($matches) {
            return '-' . strtolower($matches[0]);
        }, lcfirst($namespace));

        return $route;
    }

    private function _toNamespace($route)
    {
        // Route URL conversion
        $route = trim($route, '/');
        // Case adjustment
        $route = preg_replace_callback('!-[a-z]!', function ($matches) {
            return strtoupper($matches[0]);
        }, strtolower($route));

        return $route;
    }

    private function _getCommentTitle($commentString)
    {
        if (!$commentString) {
            return '';
        }
        $pieces = explode("\n", $commentString);
        return ltrim(ltrim($pieces[1], " * "));
    }
}
