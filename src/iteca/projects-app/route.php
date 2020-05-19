<?php
//the purpose of the route class is point us to the proper view from index.php, content is retrived by the renderer object 
class Route
{ 
    /*
    --------------------
    PROPERTIES
    --------------------
    */
    public $uriString = '/';
    public $className = '';
    public $view = 'sidebar';
    public $query = '';
    
    /*
    CONSTRUCTOR METHOD
    */
    function __construct() 
    {

    }
    
    /*
    -----------------------
    CUSTOM CONSTRUCTOR METHODS
    -----------------------
    */
    //initalizes router with info from URI request on index.php
    public static function withURI($string) 
    {
        $instance = new self();
        $instance->fillPropertiesWith($string);
        //$instance->checkUser();
        return $instance;
    }
    
    //methods
    public function fillPropertiesWith($string) 
    {
        $this->uriString = $string;
        $this->className = trim($string, '/');
        $this->view = $this->getView($this->className);
        $this->query = $this->getQuery();
    }
    
    public function checkUser()
    {
        $authenticator = new Authenticator();
        if ($this->className != 'login' && $this->className != 'register') {
            $authenticator->requireAuth();
        }
    }
    
    public function getQuery()
    {
        $properties = [
            "className" => $this->className,
            "view" => $this->view
        ];
        $query = http_build_query($properties);
        return $query;
    }
    
    public function getView($className) 
    {
        switch ($className) {
            case 'workouts':
            case 'workout':
            case 'my-account':
                return 'sidebar';
                break;
            default:
                return 'sidebar';
        } 
    }

}
