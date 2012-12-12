<?php
/**
 * :: APPLICATION CLASS ::
 * Main application class.
 * Contains the main methods to load controller, model and view for application
 * 
 * @author kasp466h
 * @version 0.6b
 */
class Application {
    private $config;
    private $uri;
    private $controller;
    private static $modelFolder = "application/model/";
    private static $controllerFolder = "application/controller/";
    private static $viewFolder = "application/view/";
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
        if(isset($this->$modelname)) return;
        if(!(isset($modelname) && is_string($modelname))) throw new Exception('Given parameter is not a string!');
        $file = Application::$modelFolder. $modelname. ".php";
        $modelname = $this->getClassName($file);  
        $mainModel = Application::$modelFolder. "model.php";
        
        if(file_exists($file) && file_exists($mainModel)) {
            include_once $mainModel;
            include_once $file;
            $this->$modelname = new $modelname();
        } else {
            throw new Exception($file. " does not exist!");
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
                include_once $file;
                return ob_get_clean();
            } else {
                include_once $file;
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
        $file = Application::$controllerFolder. $controllerName. ".php";
        $mainContoller = Application::$controllerFolder. "controller.php";
        
        $controllerName = $this->getClassName($file);
        if(file_exists($file) && file_exists($mainContoller)) {
            include_once $mainContoller;
            include_once $file;
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
            throw new Exception($file. " does not exist!");
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
        $parts = explode("/", $path);
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
