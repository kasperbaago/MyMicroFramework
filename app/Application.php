<?php
namespace app;
use \Exception;

/*
 *  Copyright (c) 2013 Kasper Baagø Jensen
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

/**
 * :: APPLICATION CLASS ::
 * Main app class.
 * Contains the main methods to load controller, model and view for app
 * 
 * @author kasp466h
 * @version 0.6b
 */
class Application {
    private $config;
    private $uri;
    private $controller;
    private static $modelFolder = "app/model/";
    private static $controllerFolder = "app/controller/";
    private static $viewFolder = "app/view/";
    private $baseDir;

    public function __construct() {
        $this->loadConfiguration();
        $this->uri = $this->readUrl();
        $this->configurerBaseDir();
    }
    
    public function init() {        
        if($this->uri == false || $this->controllerExists($this->uri[0]) == false) {
            $this->loadMainController();
        } elseif(isset($this->uri[0]) && !isset($this->uri[1])) {
            $this->loadController($this->uri[0]);
        } else {
            $this->loadController($this->uri[0], $this->uri[1]);
        }
    }
    
    /**
     * Loads main controller
     * @throws Exception
     */
    private function loadMainController() {
        if(isset($this->config['mainController'])) {
            $this->loadController($this->config['mainController']);
        } else {
            throw new Exception('No main controller set in config file!');
        }
    }
    
    /**
     * Loads configura
     * @throws Exception
     */
    private function loadConfiguration() {
        if(!file_exists('conf.php')) throw new Exception('No config file found :(');
        require 'conf.php';
        if(isset($conf) && is_array($conf)) {
            $this->config = $conf;
        } else {
            throw new Exception('Conf variable could not be found in config libray');
        }
    }
    
    /**
     * Loads a new model into context
     * @param type $modelname
     */
    public function loadModel($modelname) {
        $class = $this->getClassName($modelname);
        if(isset($this->$class)) return;
        if(!(isset($modelname) && is_string($modelname))) throw new Exception('Given parameter is not a string!');
        $modelname = "app\model\\". $modelname;

        if(class_exists($modelname)) {
            $this->$class = new $modelname();
        } else {
            throw new Exception($modelname. " does not exist!");
        }
    }
    
    /**
     * Loads a new view into context
     * @param type $modelname
     */
    public function loadView($viewName, $input = array(), $output = false) {
        if(!(isset($viewName) && is_string($viewName))) throw new Exception('Given parameter is not a string!');
        $file = Application::$viewFolder. $viewName. ".php";
        if(file_exists($file)) {
            extract($input);
            
            if($output == true) {
                ob_start();
                include $file;
                return ob_get_clean();
            } else {
                include $file;
            }
            
        } else {
            throw new Exception($file. " does not exist!");
        }
    }
    
    /**
     * Loads a new controller into space
     * @param type $controllerName
     * @throws Exception
     */
    public function loadController($controllerName, $method = null) {
        if(!(isset($controllerName) && is_string($controllerName))) throw new Exception('Given parameter is not a string!');
        $controllerName = "app\controller\\". $controllerName;

        if(class_exists($controllerName)) {
            $this->controller = new $controllerName();
            
            if(is_string($method) && strlen($method) && method_exists($this->controller, $method)) {
                if(isset($this->uri[2])) {
                    $this->controller->$method($this->uri[2]);
                } else {
                    $this->controller->$method();
                }
            } else {
                $this->controller->index();
            }
        } else {
            throw new Exception($controllerName. " does not exist!");
        }
    }
    
    /**
     * Checks to see if a specific controller exists
     * 
     * @param String $controllerName
     * @return boolean
     * @throws Exception
     */
    private function controllerExists($controllerName) {
        if(!isset($controllerName)) throw new Exception('Given controllerName parameter is not string!');
        $file = Application::$controllerFolder. $controllerName. ".php";
        return file_exists($file);
    }
    
    /**
     * Returns filename for a path
     * 
     * @param string $path
     * @return string
     * @throws Exception
     */
    private function getClassName($path) {
        if(!is_string($path)) throw new Exception('Path given is not a string!');
        $parts = explode("\\", $path);
        if(count($parts) > 0) {
            $ret = $parts[count($parts) - 1];
        } else {
            $ret = $parts;
        }
        
        return str_replace(".php", "", $ret);
    }
    
    /**
     * Reads URL from path
     * @return boolean|null
     */
    private function readUrl() {
        if(!isset($_SERVER['PATH_INFO'])) return false;
        $info = $_SERVER['PATH_INFO'];
        if(strlen($info) <= 0) return false;
        $parts = explode("/", $info);
        if(strlen($parts[1]) <= 0) return false;
        array_shift($parts);
        
        $output = array();
        
        if(count($parts) > 0) {            
            foreach($parts as $part) {
                if(strlen($part) < 1) continue;
                $output[] = $part;
            }
        } else {
           return false;
        }

        return $output;
    }
    
    /**
     * Returns URI
     * @return type
     */
    public function getUri() {
        return $this->uri;
    }
    
    public function startSession() {
        if(!session_id()) {
            session_start();
        }
    }
    
    public function destroySession() {
        session_unset();
        session_destroy();
    }
    
    private function configurerBaseDir() {
        if(isset($this->config['baseAddress'])) {
            $this->baseDir = $this->config['baseAddress'];
        } else {
            $this->baseDir = $_SERVER['SERVER_NAME'];
        }
    }
    
    public function getBasePath() {
        return $this->baseDir;
    }
    
    public function getBaseDir() {
        return $this->baseDir. "index.php/";
    }

    public function getConfig($item = null)
    {
        if(isset($item)) {
            if(!isset($this->config[$item])) throw new Exception($item. " could not be found in config array!");
            return $this->config[$item];
        }

        return $this->config;
    }


    
}

?>
