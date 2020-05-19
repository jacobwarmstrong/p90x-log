<?php

//Project
class Project {
    
    //Project Properties
    private $projectId = null;
    private $projectName = "Untitled Project";
    private $path = 'project.php?projectId=';
    private $projectDescription = "";
    private $location = "Unknown Location";
    private $latitude = 0.0;
    private $longitude = 0.0;
    private $street = "";
    private $city = "";
    private $state = "";
    private $zip = "";
    private $vendors = [];
    private $uploadDate = 0;
    private $deadline;
    private $projectManagerId = 1;
    private $clientId = null;
    private $clientName = null;
    //private $employees = [];
    private $tasks = [];
    private $orders = [];
    //private $media = [];
    
    //Project Functions
    
    public function setProjectId($id) {
        $this->projectId = $id;
    }
    
    public function isComplete() {
        if ($this->isComplete == 1) {
            return true;
        } else {
            return false;
        }
    }
    
    public function getProjectTasks() {
        global $db;
        
        try {
            $query = 'SELECT * FROM tasks WHERE projectId = :projectId';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->execute();
            $this->tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            throw $e;
        }
        
    }
    
    public function getProjectOrders() {
        global $db;
        
        try {
            $query = 'SELECT * FROM delivery WHERE projectId = :projectId';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->execute();
            $this->orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->orders;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getProjectDayCount() {
        if( $this->isComplete ) {
            return null;
        } else {
            return "DAY " . round((time() - strtotime($this->uploadDate)) / (60 * 60 * 24)) . " | ";
        }
        
    }
    
    public function getOpenTasksCount() {
        $count = 0;
        for($i=0;$i<count($this->tasks);$i++) {
            if ($this->tasks[$i]['isComplete'] == 0) {
                $count ++;
            }
        }
        
        return $count;
        
    }
    
    public function getCompleteTasksCount() {
        $count = 0;
        for($i=0;$i<count($this->tasks);$i++) {
            if ($this->tasks[$i]['isComplete'] == 1) {
                $count ++;
            }
        }
        
        return $count;
        
    }
    
    public function getDueTodayTasksCount() {
        $count = 0;
        $today = date("Y-m-d", time());
        for($i=0;$i<count($this->tasks);$i++) {
            if ($this->tasks[$i]['deadline'] == date("Y-m-d", time()) ) {
                $count ++;
            }
        }
        
        return $count;
        
    }
    
    public function dumpTasksArray() {
        var_dump($this->tasks);
    }
    
    public function setClientId($id) {
        $this->clientId = $id;
    }
    
    public function setClientName($name) {
        $this->clientName = $name;
    }
    
    public function setProjectDescription($description) {
        $this->projectDescription = $description;
    }
    
    public function getAllToolIds() {
        global $db;
        
        $query = 'SELECT toolId FROM projectTools WHERE projectId = ?';
        $stmt = $db->prepare($query);
        $stmt->bindParam(1, $this->projectId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getAllTasksWithFilters($status = "incomplete", $priority) {
        global $db;
        
        $query = 'SELECT taskId FROM tasks WHERE projectId = ?'; //basic query request
        
        
        $now = date("Y-m-d"); //today's date      
        $oneWeek = date("Y-m-d", strtotime("+1 week")); //date 1 week from now
        
        //switch status to create proper query string
        switch ($status) {
            case "all":
                break;
            case "pastDue":
                $query .= ' AND deadline < "' . $now . '" AND isComplete = 0';
                break;
            case "dueThisWeek":
                $query .= " AND deadline BETWEEN '" . $now . "' AND '" . $oneWeek . "' AND isComplete = 0";
                break;
            case "incomplete":
                $query .= ' AND isComplete = 0';
                break;
            case "complete":
                $query .= ' AND isComplete = 1';
                break;
        }
        
        //add priority to query
        if ($priority == "1") {
            $query .= ' AND isPriority = 1';
        }
        
        if ($status == "complete") {
            $query .= ' ORDER BY completionDate ASC'; //add order for completed tasks
        } else {
            $query .= ' ORDER BY deadline ASC'; //add default order
        }
        
        try {
                $stmt = $db->prepare($query);
                $stmt->bindParam(1, $this->projectId);
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
                    throw $e;
        }
    }   
    
    public function init($id) {
        global $db;
        if (gettype(intval($id)) == 'integer' && intval($id) != 0) {
            $query = 'SELECT * FROM projects WHERE projectId = ?';
        } else {
            $query = 'SELECT * FROM projects WHERE projectName = ?';
        }
        try {
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $id);
            $stmt->execute();
            $projectObj = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->projectId = $projectObj['projectId'];
            $this->projectName = $projectObj['projectName'];
            $this->projectDescription = $projectObj['projectDescription'];
            $this->latitude = $projectObj['latitude'];
            $this->longitude = $projectObj['longitude'];
            $this->street = $projectObj['streetAddress'];
            $this->city = $projectObj['city'];
            $this->state = $projectObj['state'];
            $this->zip = $projectObj['zip'];
            $this->uploadDate = $projectObj['uploadDate'];
            $this->isComplete = $projectObj['isComplete'];
            $this->deadline = $projectObj['deadline'];
            $this->projectManagerId = $projectObj['projectManagerId'];
            $this->clientName = $projectObj['clientName'];
        } catch (\Exception $e) {
            throw $e;
        }
        
        $this->getProjectTasks();
        
    }
    
    public function setProjectName($name) {
        $format = ucwords(strtolower($name));
        $this->projectName = $format;
    }
    
    public function addToolsToProject($toolIds) {
        global $db;
        
        foreach ($toolIds as $toolId) {
            try {
                $query = 'INSERT INTO projectTools (toolId, projectId) VALUES (:toolId, :projectId)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':toolId', $toolId);
                $stmt->bindParam(':projectId', $this->projectId);
                $stmt->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        }
    }
    
    public function getLink() {
        return $this->path . $this->projectId;
    }
    
    public function getClientName() {
        return $this->clientName;
    }
    
    public function getProjectName($return  = 'link') {
        if ($return == 'link') {
            return '<a href="' . $this->getLink() . '">' . $this->projectName . '</a>';
        }
        
        return $this->projectName;
        
    }
    
    public function getDescription() {
        return $this->projectDescription;
    }
    
    public function getStreet() {
        return $this->street;
    }
    
    public function getCity() {
        return $this->city;
    }
    
    public function getState() {
        return $this->state;
    }
    
    public function getZip() {
        return $this->zip;
    }
    public function setProjectManager($projectManagerId) {
        $this->projectManagerId = $projectManagerId;
    }
    
    public function getProjectId() {
        return $this->projectId;
    }
    
    public function getId() {
        return $this->projectId;
    }
    
    public function getDeadline() {
        global $db;

        try {
            $query = 'SELECT deadline FROM projects WHERE projectId = ?';
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $this->projectId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['deadline'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function setDeadline($deadline) {
        $this->deadline = $deadline;
    }
    
    public function getAddress($format = null) {
        $street = $this->street;
        $city = $this->city;
        $state = $this->state;
        $zip = $this->zip;
        $oneLine = "{$street},{$city}, {$state}, {$zip}";
        $twoLine = "{$street}<br>{$city}, {$state}, {$zip}";
        
        return $format == 'twoLine' ? $twoLine : $oneLine;
    }
    
    public function getCheckedInUsers() {
        global $db;
        $today = new DateTime('today');
        $tomorrow = new DateTime('tomorrow');
        $today = $today->format('Y-m-d H:i:s');
        $tomorrow = $tomorrow->format('Y-m-d H:i:s');
        try {
            $query = 'SELECT userId FROM check1 WHERE projectId = :projectId AND status = 0';
            //$query .=" AND timeIn BETWEEN :today AND :tomorrow'"
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $this->projectId);
            //$stmt->bindParam(':today', $today) ;
            //$stmt->bindParam(':tomorrow', $tomorrow);
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $users;
            
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getDailyNotes() {
        global $db;
        try {
            $query = 'SELECT userId, message, timeIn, timeOut FROM check1 WHERE projectId = :projectId';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->execute();
            $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $messages;
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function setState($state) {
        $this->state = $state;
    }
    
    //Deprecated 08/23/2018 use getCoordinates()
    public function setLocation($street, $city, $state, $zip) {
       $address = $street . " " . $city . " " . $state . " " . $zip;
       $address = str_replace (" ", "+", urlencode($address));
       $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck";

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $details_url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       $response = json_decode(curl_exec($ch), true);

       // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
       if ($response['status'] != 'OK') {
         return null;
       }

       $geometry = $response['results'][0]['geometry'];

        $longitude = $geometry['location']['lng'];
        $latitude = $geometry['location']['lat'];

        $array = array(
            'latitude' => $geometry['location']['lat'],
            'longitude' => $geometry['location']['lng'],
            'location_type' => $geometry['location_type'],
        );
        $this->latitude = $geometry['location']['lat'];
        $this->longitude = $geometry['location']['lng'];
        $this->street = $street;
        $this->city = $city;
        $this->state = $state;
        $this->zip = $zip;
    }
    
    public function getCoordinates($street, $city, $state, $zip) {
       $address = $street . " " . $city . " " . $state . " " . $zip;
       $address = str_replace (" ", "+", urlencode($address));
       $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&key=AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck";

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $details_url);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       $response = json_decode(curl_exec($ch), true);

       // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
       if ($response['status'] != 'OK') {
         return null;
       }

       $geometry = $response['results'][0]['geometry'];

        $longitude = $geometry['location']['lng'];
        $latitude = $geometry['location']['lat'];

        $array = array(
            'latitude' => $geometry['location']['lat'],
            'longitude' => $geometry['location']['lng'],
            'location_type' => $geometry['location_type'],
        );
        $latitude = $geometry['location']['lat'];
        $longitude = $geometry['location']['lng'];
        return ['lat'=>$latitude,'lng'=>$longitude];
    }
    
    public function getLongitude() {
        return $this->longitude;
    }
    
    public function getLatitude() {
        return $this->latitude;
    }
    
    public function getDeadlineFormat() {
        $deadline = $this->getDeadline();
        $isComplete = $this->isComplete;
        if ($isComplete) {
            return "COMPLETE";   
        } else {
            if ( $deadline < date("Y-m-d") ) {
                return "PAST DUE";
            } elseif ( $deadline == date('Y-m-d') ) {
                return "DUE TODAY";
            } elseif ( $deadline > date('Y-m-d') && $deadline < date('Y-m-d', strtotime("+1 week")) ) {
                return "Due " . date("l", strtotime( $deadline ));
            } else {
                return "DUE " . date("m/d", strtotime( $deadline ));
            } 
        }

    }
    
    public function getDeadlineClass() {
        $deadline = $this->getDeadline();
        $isComplete =$this->isComplete;
        if ($isComplete) {
            return "";
        } else {
            if ($deadline <= date('Y-m-d') ) {
                return "text-danger ";
            } elseif ( $deadline > date('Y-m-d') && $deadline < date('Y-m-d', strtotime("+1 week")) ) {
                return "text-warning ";
            } else {
                return "text-muted ";
            }  
        }

    }
    
    public function getProjectManagerId() {
        global $db;
        
        try {
            $query = 'SELECT projectManagerId FROM projects WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectId );
            $stmt->execute();
            $aVar = $stmt->fetch(PDO::FETCH_ASSOC);
            return $aVar["projectManagerId"];
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getProjectManagerName() {
        global $db;
        
        try {
            $query = 'SELECT firstName, lastName FROM users WHERE userId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectManagerId );
            $stmt->execute();
            $managerName = $stmt->fetch(PDO::FETCH_ASSOC);
            return $managerName['firstName'] . " " . $managerName['lastName'];
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getProjectCreatedOnFormat() {
        return date("M d, Y", strtotime($this->uploadDate));
    }
    
    public function getProjectDeadlineFormat() {
        return date("M d, Y", strtotime($this->deadline));
    }
    
    public function getFeatureImageId() {
        global $db;
        
        try {
            $query = 'SELECT featureImgId FROM projects WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam( ':id', $this->projectId );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC)['featureImgId'];
        } catch (\Exception $e) {
            return null;
        }
    }
    
    public function getFeatureImageObj() {
        global $db;
        
        $id = $this->getFeatureImageId();
        
        try {
            $query = 'SELECT * FROM images WHERE imgId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $id );
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function getAllImageObj() {
        global $db;
        
        try {
            $query = 'SELECT * FROM images WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    
    public function theMap() { 
        
        echo '<script> function initMap() { var uluru = {lat: ' . $this->getLatitude() . ', lng: '. $this->getLongitude() . ' var map-'. $this->getProjectId() . ' = new google.maps.Map(document.getElementById("map-' . $this->getProjectId() . '"), { zoom: 12, center: uluru }); var marker = new google.maps.Marker({ position: uluru, map: map }); }</script></div><script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck&callback=initMap"></script>';
    }
    
    public function getAllImages() {
        global $db;

        try {
            $query = 'SELECT * FROM images WHERE projectId = ?';
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $this->projectId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function setClient($name) {
        $format = ucwords(strtolower($name));
        $this->client = $format;
    }
    
    public function getClient() {
        return $this->client;
    }
    
    public function addTask($task) {
        array_push($this->tasks, $task);
    }
    
    public function getTasks() {
        return $this->tasks;
    }
    
    public function getAllTasks() {
        global $db;
        try{
            $query = 'SELECT * FROM tasks WHERE projectId = ? ORDER BY isComplete ASC';
            $stmt = $db->prepare($query);
            $stmt->bindParam(1, $this->projectId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw $e;
        }
    }
    
    public function theTasks() {
        $html = '<div class="task">';
        foreach ($this->tasks as $task) {
            $html .= '<span class="text-warning">9d </span>';
            $html .= '<p>' . $this->getTitle() . '</p>';
            $html .= '<p class="text-muted" style="font-size: .8rem;">1400 Hog Mountain Rd, Watkinsville, GA</p>';
            $html .= '<hr>';
    
            $html = "<div class='card my-3 p-3'>";
            $html .= "<h5></h5>";
            $html .= "</div>";
            
        }
        $html .= '</div>';
        echo $html;
    }
    
    public function addVendor($vendor) {
        array_push($this->vendors, $vendor);
    }
    
    public function getVendors() {
        return $this->vendors;
    }
    
    public function theVendors() {
        foreach ($this->vendors as $vendor) {
            $html = "<div class='card my-3 p-3'>";
            $html .= "<h5>" . $vendor->getName() . "</h5>";
            $html .= "<p>" . $vendor->getMainContact() . "</p>";
            $html .= "<p>" . $vendor->getPhone() . "</p>";
            foreach($vendor->getSupplies() as $supply) {
                $html .= "<p>" . $supply . "</p>";
            }
            $html .= "</div>";
            echo $html;
        }
    }
    
    public function updateFeatureImage($imgId) {
        global $db;

            try {
                $query = 'UPDATE projects SET featureImgId = :imgId WHERE projectId = :projectId';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':imgId', $imgId);
                $stmt->bindParam(':projectId', $this->projectId );
                $stmt->execute();
            } catch (\Exception $e) {
                return false;
            }

            return true;
    }
    
    public function addNewClientToProject($projectName, $id, $clientName) {
        global $db;

            try {
                $query = 'UPDATE projects SET clientId = :clientId, clientName = :clientName WHERE projectName = :projectName';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':clientId', $id);
                $stmt->bindParam(':clientName', $clientName );
                $stmt->bindParam(':projectName', $projectName);
                $stmt->execute();
            } catch (\Exception $e) {
                return false;
            }

            return true;
    }
    
    public function insertToDB($props) {
        global $db;
        global $session;
        
        $coordinates = $this->getCoordinates($props['street'],$props['city'],$props['state'],$props['zip']);
        $lat = $coordinates['lat'];
        $lng = $coordinates['lng'];
        
        //because nick has unecessarily repeating columns
        $client = initObject('Client',$props['clientId']);
        $clientName = $client->getName();

           try {
                $query = 'INSERT INTO projects (projectName, projectDescription, projectManagerId, latitude, longitude, streetAddress, city, state, zip, deadline, clientId, clientName) VALUES (:projectName, :projectDescription, :projectManagerId, :latitude, :longitude, :streetAddress, :city, :state, :zip, :deadline, :clientId, :clientName)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':projectName', $props['name']);
                $stmt->bindParam(':projectDescription', $props['description']);
                $stmt->bindParam(':projectManagerId', $props['ownerId']);
                $stmt->bindParam(':latitude', $lat);
                $stmt->bindParam(':longitude', $lng);
                $stmt->bindParam(':streetAddress', $props['street']);
                $stmt->bindParam(':city', $props['city']);
                $stmt->bindParam(':state', $props['state']);
                $stmt->bindParam(':zip', $props['zip']);
                $stmt->bindParam(':deadline', $props['deadline']);
                $stmt->bindParam(':clientId', $props['clientId']);
                $stmt->bindParam(':clientName',$clientName);
                $stmt->execute();
               
                //Pull back project from db by name to get pId
                $projectName = $props['name'];
                $pObj = initObject('Project',$projectName);
                $pId = $pObj->getId();
                $pmId = $pObj->getProjectManagerId();
                $event = 'add';
               $class = $props['class'];
               $classId = $pId;
               
                //add projectEvent
                addProjectEvent($pId, $pmId ,$event, $class, $classId);
               
                if($props['isNewClient'] == 'on') {
                    $session->getFlashBag()->add('success', 'Your new project "' . $props['name'] . '" has been added. Now enter your new client\'s information.');
                    redirect('/add.php?class=Client&pId=' . $pObj->getId());
                } else {
                    $session->getFlashBag()->add('success', 'Your new project "' . $props['name'] . '" has been added. Now add some tasks for the project!');
                    redirect('/add.php?class=Task&pId=' . $pObj->getId());
                }
                
            } catch (\Exception $e) {
                $session->getFlashBag()->add('error', 'Something happened, try adding the project again or contact Iteca Solutions. <br>' . $e);
            }
        
            
    } 
    
    public function markComplete($pId) {
        global $db;
        global $session;
        
        try {
            $query = 'UPDATE projects SET isComplete = 1 WHERE projectId = ?';
            $stmt = $db->prepare($query);
            $stmt->bindParam(1,$pId);
            $stmt->execute();
            $session->getFlashBag()->add('success','Project successfully marked as complete.');
            redirect('/project.php?projectId=' . $pId);
        } catch (\Exception $e) {
            $session->getFlashBag()->add('success','Something went wrong. Please Try again or contact Iteca Solutions.');
            redirect('/project.php?projectId=' . $pId);
        }
    }
    
    public function update($props) {
        global $db;
        
        $client = initObject('Client', $props['clientId']);
        $props['clientName'] = $client->getName();
        
        $setLocation = $this->setLocation($props['street'],$props['city'],$props['state'],$props['zip']);
        
        try {
            $query = 'UPDATE projects SET projectName = :projectName, projectDescription = :projectDescription, latitude = :latitude, longitude= :longitude, streetAddress = :streetAddress, city = :city, state = :state, zip = :zip, deadline = :deadline, clientId = :clientId, clientName = :clientName WHERE projectId = :projectId';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $props['pId']);
            $stmt->bindParam(':projectName', $props['name']);
            $stmt->bindParam(':projectDescription', $props['description']);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);
            $stmt->bindParam(':streetAddress', $props['street']);
            $stmt->bindParam(':city', $props['city']);
            $stmt->bindParam(':state', $props['state']);
            $stmt->bindParam(':zip', $props['zip']);
            $stmt->bindParam(':deadline', $props['deadline']);
            $stmt->bindParam(':clientId', $props['clientId']);
            $stmt->bindParam(':clientName', $props['clientName']);
            $stmt->execute();
            return 'success';
        } catch (\Exception $e) {
            return $e;
        }
    } 
    
    public function delete() {
        global $db;
        
        try{
            $query = 'DELETE FROM projects WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectId);
            $stmt->execute();
            $result = 'success';
        } catch (\Exception $e1) {
            $result = $e;
        }
        
        try {
            $query = 'DELETE FROM tasks WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectId);
            $stmt->execute();
            $result = 'success';
        } catch (\Exception $e2) {
            $result = $e;
        } 
        
        try {
            $query = 'DELETE FROM delivery WHERE projectId = :id';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':id', $this->projectId);
            $stmt->execute();
            $result = 'success';
        } catch (\Exception $e3) {
            $result = $e;
        }
        return $result;
    }
    
/******* DEPRECATED METHODS ********/
    
    //Deprecated 08/23/2018 use insertToDB()
    public function addNewProjectToDb() {
        global $db;
        
        if($this->clientId == null && $this->clientName == null) {
           try {
                $query = 'INSERT INTO projects (projectName, projectDescription, projectManagerId, latitude, longitude, streetAddress, city, state, zip, deadline) VALUES (:projectName, :projectDescription, :projectManagerId, :latitude, :longitude, :streetAddress, :city, :state, :zip, :deadline)';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':projectName', $this->projectName);
                $stmt->bindParam(':projectDescription', $this->projectDescription);
                $stmt->bindParam(':projectManagerId', $this->projectManagerId);
                $stmt->bindParam(':latitude', $this->latitude);
                $stmt->bindParam(':longitude', $this->longitude);
                $stmt->bindParam(':streetAddress', $this->street);
                $stmt->bindParam(':city', $this->city);
                $stmt->bindParam(':state', $this->state);
                $stmt->bindParam(':zip', $this->zip);
                $stmt->bindParam(':deadline', $this->deadline);
                $stmt->execute();
            } catch (\Exception $e) {
                throw $e;
            }
        } else {
            try {
            $query = 'INSERT INTO projects (projectName, projectDescription, projectManagerId, latitude, longitude, streetAddress, city, state, zip, deadline, clientId, clientName) VALUES (:projectName, :projectDescription, :projectManagerId, :latitude, :longitude, :streetAddress, :city, :state, :zip, :deadline, :clientId, :clientName)';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectName', $this->projectName);
            $stmt->bindParam(':projectDescription', $this->projectDescription);
            $stmt->bindParam(':projectManagerId', $this->projectManagerId);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);
            $stmt->bindParam(':streetAddress', $this->street);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':zip', $this->zip);
            $stmt->bindParam(':deadline', $this->deadline);
            $stmt->bindParam(':clientId', $this->clientId);
            $stmt->bindParam(':clientName', $this->clientName);
            $stmt->execute();
        } catch (\Exception $e) {
            throw $e;
        }
        }
    } 
    
    //Deprecated 08/23/2018 Use update();
    public function updateProject() {
        global $db;
            try {
            $query = 'UPDATE projects SET projectName = :projectName, description = :projectDescription, latitude = :latitude, longitude= :longitude, streetAddress = :streetAddress, city = :city, state = :state, zip = :zip, deadline = :deadline, clientId = :clientId, clientName = :clientName WHERE projectId = :projectId';
            $stmt = $db->prepare($query);
            $stmt->bindParam(':projectId', $this->projectId);
            $stmt->bindParam(':projectName', $this->projectName);
            $stmt->bindParam(':projectDescription', $this->projectDescription);
            $stmt->bindParam(':latitude', $this->latitude);
            $stmt->bindParam(':longitude', $this->longitude);
            $stmt->bindParam(':streetAddress', $this->street);
            $stmt->bindParam(':city', $this->city);
            $stmt->bindParam(':state', $this->state);
            $stmt->bindParam(':zip', $this->zip);
            $stmt->bindParam(':deadline', $this->deadline);
            $stmt->bindParam(':clientId', $this->clientId);
            $stmt->bindParam(':clientName', $this->clientName);
            $stmt->execute();
        } catch (\Exception $e) {
            throw $e;
        }
    } 

    
} //End Of Project Class