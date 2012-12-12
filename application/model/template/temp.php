<?php
/**
 * Controls the content output of the site
 * @author Kasper Baagø Jensen
 */
class Temp extends Model {
    private $folders, $title, $titlePrefix, $styles, $libScripts, $headerScripts, $footerScripts, $content;
    private $injectedData = array();
    private $menu = array();
    private $isAdmin = false;
    private $currentUser;
    
    public function __construct() { 
        parent::__construct();
        $conf = $this->getConfig('template');
        $this->titlePrefix = $conf['title_prefix'];
        $this->styles = $conf['css_autoload'];
        $this->headerScripts = $conf['js_autoload'];
        $this->folders = $conf['folderConf'];
        $this->libScripts = $conf['js_lib_autoload'];
    }
    
    public function getTitle() {
        return $this->titlePrefix. " - ". $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }
    
    public function getInjectedData() {
        $data = json_encode($this->injectedData);
        return "<script type='text/javascript'>var tempData = $data</script>";
    }

    public function setInjectedData($injectedData) {
        if(!is_array($injectedData)) throw new Exception("Injected parameter given is not array!");
        $this->injectedData = array_merge($this->injectedData, $injectedData);
    }

        
    /**
     * Returns preformatted list of styles
     * @return string
     */
    public function getStyles() {
        $output = "";
        $folder = $this->getBasePath(). $this->folders['css'];
        foreach($this->styles as $style) {
            $output .= "<link rel='stylesheet' href='". $folder. $style. "' />\n";
        }
        
        return $output;
    }
    
    /**
     * Adds a new style to template
     * @param string $style
     * @return boolean
     */
    public function addStyle($style) {
        if(!is_string($style)) return false;
        $this->styles[] = $style;
        return true;
    }

    public function getHeaderScripts() {
        $out = "";
        $libFolder = $this->getBasePath(). $this->folders['js_lib'];
        $jsFolder = $this->getBasePath(). $this->folders['js'];
        
        if(is_array($this->libScripts)) {
            foreach($this->libScripts as $script) {
                $out .= "<script type='text/javascript' src='". $libFolder. $script. "'></script>\n";
            }
        }
        
        if(is_array($this->headerScripts)) {
            foreach($this->headerScripts as $script) {
                $out .= "<script type='text/javascript' src='". $jsFolder. $script. "'></script>\n";
            }
        }
        
        return $out;
    }

    public function addHeaderScript($headerScripts) {
        $this->headerScripts[] = $headerScripts;
    }

    public function getFooterScripts() {
        $out = "";
        if(is_array($this->footerScripts)) {
            $jsFolder = $this->router->getBasePath(). $this->folders['js'];
            foreach($this->footerScripts as $script) {
                $out .= "<script type='text/javascript' src='". $jsFolder. $script. "'></script>\n";
            }
        }
        
        return $out;
    }

    public function addFooterScripts($footerScripts) {
        $this->footerScripts[] = $footerScripts;
    }

    public function getContent() {
        return $this->content;
    }

    public function setContent($content) {
        $this->content = $content;
    }
    
    /**
     * Sets a new menu
     * 
     * @param type $name
     * @param type $menu
     * @return boolean
     */
    public function setMenu($name = null, $menu = null) {
        if(!isset($name)) return false;
        if(!is_array($menu)) return false;
        $this->menu[$name] = $menu;
    }
    
    /**
     * Returns given menu
     * 
     * @param type $menu
     * @return boolean | string
     */
    public function getMenu($menu = null) {
        if(!isset($menu) || !isset($this->menu[$menu])) return false; 
        return $this->printMenu($this->menu[$menu]);
    }
    
    /**
     * Makes a UL list with menu items
     * @param type $menuList
     */
    private function printMenu($menuList) {
         $html = "<ul>\n";
        if(is_array($menuList)) {
            foreach($menuList as $menuItem) {
                if(isset($menuItem->selected) && $menuItem->selected == true) {
                    $selected = "active_menu";
                } else {
                    $selected = "";
                }
                $link = $menuItem->getLink();
                $name = $menuItem->getName();
                $html .= "<li><a href='$link' class='$selected'>$name</a></li>";
            }
        }
        $html .= "</ul>";
        return $html;
    }
        
    public function render() {
        $this->loadView('template', array('temp' => $this));
    }
    
    public function setIsAdmin($isAdmin) {
        $this->isAdmin = $isAdmin;
    }
    
    public function getIsAdmin() {
        return $this->isAdmin;
    }

    public function setCurrentUser($currentUser)
    {
        $this->currentUser = $currentUser;
    }

    public function getCurrentUser()
    {
        return $this->currentUser;
    }



}

?>