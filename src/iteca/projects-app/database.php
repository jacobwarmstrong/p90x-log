<?php
//database.php
//Our Database class does our database dirty work. Selecting, Updating, Deleting, Inserting, all that shit goes down here.
class Database
{
    //props
    private $action;
    private $table;
    private $columns = [];
    private $filters = [];
    private $stmt;
    private $debug = false;
    
    //empty constructor
    function __construct() 
    {
        
    }
    
    //custom constructors
    public static function action($action, $table, $columns, $filters = null) 
    {
        global $db;
        $instance = new self();
        $instance->action = $action;
        $instance->table = $table;
        $instance->columns = $columns;
        $instance->filters = $filters;
        
        $query = $instance->generateQuery();
        
        try {
            $instance->stmt = $db->prepare($query);
            if($instance->action === 'INSERT') {
               $instance->bindParams(); 
            }
            $instance->dump($instance->stmt);
            $instance->stmt->execute();
            if($instance->action === 'SELECT') {
                $results = $instance->stmt->fetchAll(PDO::FETCH_ASSOC);
                $instance->dump($results);
                return $results;
            } else {
                return true;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    //helper functions
    private function generateQuery() 
    {  
        //empty string to start
        $query = "";
        //switch action to generate proper query string
        //create props str
        switch($this->action) {
            case 'INSERT':
                $query .= "INSERT INTO {$this->table} ({$this->getPropsString('regular')}) VALUES ({$this->getPropsString('binded')})";
                break;
            case 'SELECT':
                $query .= "SELECT {$this->getPropsString('regular')} FROM {$this->table}";
                break;
            default:
                die;   
        }
        if($this->filters != null) {
            $query .= ' ' . $this->generateFilterQuery();
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
            $str = implode(', ', array_keys($this->columns));
            return $str;
        }
        //binded properties str
        if($choice == 'binded') {
            $bindedPropsStr = [];
            foreach($this->columns as $column => $value) {
                array_push($bindedPropsStr, ':' . $column);
            }
            return implode(', ', $bindedPropsStr);
        }
    }
    
    private function bindParams() {
        foreach ($this->columns as $column => &$value) {
            if(is_int($value)) {
                $this->stmt->bindParam(':' . $column, $value, PDO::PARAM_INT);
            } else {
                $this->stmt->bindParam(':' . $column, $value);
            }
        }
    }
    
    private function generateFilterQuery() {
        //if we got a string passed, then we assume programmer wrote the query already
        if( is_string($this->filters) ) {
            return $this->filters;
        }
        //otherwise we have an array and need to parse it
        $query = '';
        $filters = $this->filters;
        if ($this->table == 'maintenancecurrent') {
            $days = key_exists('days', $filters) ? $filters['days'] : 'all';
            if($days == 'all') {
                $mondayThisWeek = date( 'Y-m-d', strtotime( "monday this week" ) );
                $sundayThisWeek = date( 'Y-m-d', strtotime( "sunday this week" ) );
                $query .= "expectedStartDate BETWEEN '{$mondayThisWeek}' AND '{$sundayThisWeek}'";
            } elseif($days != null) { 
                $daysDates = [];
                foreach($days as $day) {
                    $date = date( 'Y-m-d', strtotime( "{$day} this week" ) );
                    $date = "'{$date}'";
                    array_push($daysDates, $date);
                }
                $daysDatesStr = implode(', ' , $daysDates);
                $query .= "expectedStartDate IN ({$daysDatesStr})";
            }
            $team = key_exists('days', $filters) ? $this->filters['team'] : 'all';
            if($team != 'all' && $days != 'all') {
                $query .= " AND team = {$team}";
            } elseif($team != 'all' && $days == 'all') {
                $query .= "team = {$team}";
            }
            if(isset($this->filters['limit'])) {
                $query .= " LIMIT {$this->filters['limit']}";
            }
               return "WHERE " . $query ;
        }
    }
    
    private function dump($var)
    {
        if($this->debug == true) {
            return var_dump($var);
        }
    }
}