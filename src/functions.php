<?php
    
//global scope functions. Should avoid these as much as possible and use classes.

function helloWorld() {
    return  'hello world';
}

//Redirects user
function redirect($path, $extra = []) 
{
    $response = \Symfony\Component\HttpFoundation\Response::create(null, \Symfony\Component\HTTPFoundation\Response::HTTP_FOUND, ['location' => $path]);
    if(key_exists('cookies', $extra)) {
        foreach($extra['cookies'] as $cookie)
        $response->headers->setCookie($cookie);
    }
    $response->send();
    exit;
}

//should NOT have HTML in controller, how can you do better?
function switchLoginButton() {
    if (isAuthenticated()) {
        $path = '/controllers/logoutUser.php';
        $str = 'Logout';
        
    } else {
        $path = '/login';
        $str = 'Login';
    }
    echo '<a href=' . $path . '>' . $str . '</a>';
}

function display_errors() {
    global $session;
    
    if(!$session->getFlashBag()->has('error')) {
        return;
    }
    
    $messages = $session->getFlashBag()->get('error');
    
    $response = '<div class="alert alert-warning alert-dismissible fade show" role="alert">';
    foreach($messages as $message) {
        $response .= "{$message}<br />";
    }
    $response .= '<button type="button" class="close" data-dismiss="alert" arialabel="Close"><span aria-hidden="true">&times;</span></button></div>';
    
    return $response;
}

function display_success() {
    global $session;
    
    if(!$session->getFlashBag()->has('success')) {
        return;
    }
    
    $messages = $session->getFlashBag()->get('success');
    
    $response = '<div class="alert alert-success alert-dismissible fade show" role="alert">';
    foreach($messages as $message) {
        $response .= "{$message}<br />";
    }
    $response .= '<button type="button" class="close" data-dismiss="alert" arialabel="Close"><span aria-hidden="true">&times;</span></button></div>';
    
    return $response;
}

//use this for form sanitation when user inputs new address
function getCoordinates($address) {
   //$address = $street . " " . $city . " " . $state . " " . $zip;
   $addressPrep = str_replace (" ", "+", urlencode($address));
   $details_url = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $addressPrep . "&key=AIzaSyCGC4HoMmLsKLZHnPBL_tg9XpENVLahAck";

    $geocode = file_get_contents($details_url);
    $response = json_decode($geocode);
    var_dump($response->results[0]->geometry);
    $coordinates = $response->results[0]->geometry->location;

   // If Status Code is ZERO_RESULTS, OVER_QUERY_LIMIT, REQUEST_DENIED or INVALID_REQUEST
   /*if ($response-> != 'OK') {
     echo 'yo';
     return null;
   }
   */
    return $coordinates;
}

function parsePhone($phone) {
    return str_replace('-','',$phone);
}

function createUser($email, $password) {
    global $db;
    
    try {
        $query = 'INSERT INTO users (email, password) VALUES (:email, :password)';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        return findUserByEmail($email);
    } catch (\Exception $e) {
        throw $e;
    }
}

function decodeJwt($prop = null) {
    global $request;
    \Firebase\JWT\JWT::$leeway = 1;
    $jwt = \Firebase\JWT\JWT::decode(
        $request->cookies->get('access_token'),
        getenv('SECRET_KEY'),
        ['HS256']
    );
    
    if ($prop === null) {
        return $jwt;
    }
    
    return $jwt->{$prop};
}

function findUserByEmail($email) {
    global $db;
    
    try {
        $query = 'SELECT * FROM users WHERE email = :email';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        throw $e;
    }
}

function findUserByAccessToken() {
    global $db;
    
    try {
       $userId = decodeJwt('sub'); 
    } catch(\Exception $e) {
        throw $e;
    }
    
    try {
        $query = 'SELECT * FROM users WHERE userId = :id';
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (\Exception $e) {
        throw $e;
    }
}

//check is parameter bag has content for page
function has_content($parameters) {
    if ( key_exists( 'content', $parameters ) ) {
        return true;
    } else {
        return false;
    }
}
