<?php
//database.php
//Our Database class does our database dirty work. Selecting, Updating, Deleting, Inserting, all that shit goes down here.
class Database
{
    //props
    private $action;
    private $table;
    private $columns = [];
    private $extra = [];
    
    //empty constructor
    function __construct() 
    {
        
    }
    
    //custom constructors
    public static function do($action, $table, $columns, String $extra = null) 
    {
        global $db;
        $instance = new self();
        $instance->action = $action;
        $instance->table = $table;
        $instance->columns = $columns;
        $instance->extra = $extra;
        
        $query = $instance->generateQuery();
        
        try {
            $stmt = $db->prepare($query);
            if($instance->action == 'INSERT') {
               $instance->bindParams($stmt); 
            }
            $stmt->execute();
            if($instance->action = 'SELECT') {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    //helper functions
    private function generateQuery() 
    {  
        //emtry string to start
        $query = "";
        //switch action to generate proper query string
        //create props str
        switch( strtoupper($this->action) )  {
            case 'INSERT':
                $query .= "INSERT INTO {$this->table} ({$this->getPropsString('regular')}) VALUES ({$this->getPropString('binded')})";
                break;
            case 'SELECT':
                $query .= "SELECT {$this->getPropsString('regular')} FROM {$this->table}";
                break;
            default:
                die;   
        }
        if($this->extra != null) {
            $query .= ' ' . $this->extra;
        }
        return $query;
    }
    
    private function getPropsString(String $choice = 'regular') 
    {
        //if * was passed then pass a * to return all columns
        if($this->columns === "*") {
            return '*';
        }
        //regular properties str
        if($choice == 'regular') {
            return implode(',',$this->columns);
        }
        //binded properties str
        if($choice == 'binded') {
            $bindedPropsStr = [];
            foreach($this->columns as $column) {
                array_push($bindedPropsStr, ':' . $column);
            }
            return $bindedPropsStr;
        }
    }
    
    private function bindParams($stmt) {
        foreach ($this->columns as $column) {
            $stmt->bindParam(':' . $column, $column);
        }
    }
    
    

}