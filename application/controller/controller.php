<?php
/**
 * ::: MAIN CONTROLLER CLASS :::
 * Main controller class, which all classes extends from
 * 
 * @version 1.0
 * @author Kasper Baagø Jensen <kapper14@gmail.com>
 */

class Controller {
    public $context;
    
    /**
     * Sets application context
     * @param Application $c
     */
    public function setContext($c) {
        if(isset($this->context)) return;
        $this->context = $c;
    }
    
     
     public function index() {
         
     }
}

?>
