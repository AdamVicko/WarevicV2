<?php

class App
{
    public static function start()
    {
    	$route = Request::getroute(); 

        $parts = explode('/',substr($route,1));

        $controller = '';

        if(false === isset($parts[0]) || '' === $parts[0]) {
            $controller='IndexController';
        } else {
            $controller = ucfirst($parts[0]) . 'Controller'; 
        }                                                       
        
        $method='';
        
        if(false === isset($parts[1]) || '' === $parts[1]) {
            $method='index';
        } else {
            $method=$parts[1];
        }

        $parameter='';
        if(false === isset($parts[2]) || '' === $parts[2]) {
            $parameter='';
        } else {
            $parameter=$parts[2];
        }

        if(false === (class_exists($controller) && method_exists($controller,$method))) {
            $view = new View();
            $view->render('notFoundPage', []);
        }
        
        $instance = new $controller();
        if(strlen($parameter)>0) {
            $instance->$method($parameter);
        } else {
            $instance->$method();
        }
    }

    public static function config($key)
    {
        $configFile = BP_APP . 'configuration.php';

        if(false === file_exists($configFile)) {
            return 'The configuration file does not exist!';
        }

        $config = require $configFile;
        
        if(false === isset($config[$key])) {
            return 'Key ' . $key . ' not set in configuration!';
        }
        
        return $config[$key];
    }

    public static function dev()
    {
        return App::config('dev');
    }
}