<?php
//The purpose of the login class is to log a user into our site using a cookie. The class also validates, sanitizes, and authenticates our user.
class Login
{
    /*
    --------------------
    PROPERTIES
    --------------------
    */
    private $email;
    private $inputPassword;
    private $dbPassword;
    private $userId;
    private $isAdmin;
    private $jwt;
    private $cookie;
    private $expireTime;
    public $isAuthenticated = false;
    
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
    public static function authenticateWith($email, $password)
    {
        $instance = new self();
        
        //get db user info by email
        $user = $instance->findUserBy($email);
        
        //check if returned a user or not
        if( empty($user) ) {
            global $session;
            $session->getFlashBag()->add('error', 'Email not found. Try Again');
            redirect('/login');
        }
        
        //if we did, fill our object props
        //**jwt and cookie props will NOT be set here
        $instance->fillPropertiesWith($email, $password, $user);
        
        //verify the two passwords
        if (!password_verify($instance->inputPassword, $instance->dbPassword)) {
            global $session;
            $session->getFlashBag()->add('error', 'Password not correct. Try Again');
            redirect('/login');
        }
        
        //if passwords check out, then our user is authenticated
        $instance->isAuthenticated = true;
        
        //if user is authenticated, generate our JWT and cookie
        if($instance->isAuthenticated) {
            $instance->generateJWT();
            $instance->generateCookie();
        }
        
        return $instance;
    }
    
    //methods
    public function getCookie() {
        return $this->cookie;
    }
    
    private function fillPropertiesWith(String $email, String $password, Array $user) 
    {
        $this->email = $email;
        $this->inputPassword = $password;
        
        $this->userId = $user['userId'];
        $this->dbPassword = $user['password'];
        $this->isAdmin = $user['isAdmin'];
    }
    
    private function findUserBy($email)
    {
        global $db;
            try {
                $query = 'SELECT userId, password, isAdmin FROM users WHERE email = :email';
                $stmt = $db->prepare($query);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                return $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (\Exception $e) {
                throw $e;
            }
    }
    
    private function generateJWT() 
    {
        $expireTime = time() + 3600;
        $this->expireTime = $expireTime;
        var_dump($this->expireTime);
        $config = [
            'iss' => BASE_URL,
            'sub' => "{$this->userId}",
            'exp' => $this->expireTime,
            'iat' => time(),
            'nbf' => time(),
            'admin' => "{$this->isAdmin}"
            ];
        $this->jwt = \Firebase\JWT\JWT::encode($config, getenv("SECRET_KEY"), 'HS256');
    }
    
    private function generateCookie()
    {
        $accessToken = Symfony\Component\HttpFoundation\Cookie::create('access_token', $this->jwt, $this->expireTime, '/', getenv('COOKIE_DOMAIN'));
        $this->cookie = $accessToken;
    }
}