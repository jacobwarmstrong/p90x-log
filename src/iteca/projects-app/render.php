<?php
//the purpose of the render class is to take out route data and display the appropriate HTML templates 
class Render
{ 
    /*
    --------------------
    PROPERTIES
    --------------------
    */
    public $className = '';
    public $view = 'sidebar';
    
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
    public static function theContentFrom(Route $data) {
        $instance = new self();
        $instance->fill($data);
        $instance->getContent($instance->className);
    }
    
    //methods
    public function fill($data) 
    {
        $this->className = $data->className;
        $this->layout = $data->view;
    }
    
    public function getContent($className) 
    {
        switch ($className) {
            case 'projects':
                require BASE_URL . '/layout/projects.php';
                break;
            case 'maintenance':
                require BASE_URL . '/layout/maintenance.php';
                break;
            case 'login':
                require BASE_URL . '/layout/login.php';
                break;
            case 'users':
                require BASE_URL . '/layout/table.php';
            case 'register':
                require BASE_URL . '/layout/register.php';
            default:
                require BASE_URL . '/layout/content.php';
        } 
    }
}
