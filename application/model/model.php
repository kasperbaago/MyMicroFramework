<?php
/**
 * ::: MAIN MODEL CLASS :::
 * Main model class, which all models extends from
 * 
 * @version 1.0
 * @author Kasper Baagø Jensen <kapper14@gmail.com>
 */
class Model {
    public $context;
    
    /**
     * Main constructor for Model class
     * @param type $context
     * @return boolean
     */
    public function __construct($context) {
       if(isset($this->context)) return;
       $this->context = $context; 
    }
}

?>
