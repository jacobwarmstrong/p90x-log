<?php
use
Firebase\JWT\JWT;
    
use
Symfony\Component\HttpFoundation\Cookie;
    
class Authenticator
{
    public function isAuthenticated() {
        global $request;
        if ($request->cookies->has('access_token')) {
            return true;
        }

        try {
            $this->decodeJwt();
            return true;
        } catch (\Exception $e) {
                return false;
        }
    }
    
    private function decodeJwt($prop = null) {
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



    public function requireAuth() {
        global $session;

        if(!$this->isAuthenticated()) {
            $accessToken = new \Symfony\Component\HttpFoundation\Cookie("access_token", "Expired", time()-3600, '/', getenv('COOKIE_DOMAIN'));
            $session->getFlashBag()->add('error', 'You must be logged in to view this page.');

            redirect('/login', ['cookies' => [$accessToken]]);
        } 
    }

    private function isAdmin() {
        if(!isAuthenticated()) {
            return false;
        }

        try {
            $isAdmin = decodeJwt('admin');
        } catch (\Exception $e) {
            return false;
        }

        return (boolean)$isAdmin;
    }

    private function requireAdmin() {
        global $session;
         if(!isAuthenticated()) {
            $accessToken = new \Symfony\Component\HttpFoundation\Cookie("access_token", "Expired", time()-3600, '/', getenv('COOKIE_DOMAIN'));
            redirect('/login.php', ['cookies' => [$accessToken]]);
        }

        try {
            if(! decodeJwt('admin')) {
             $session->getFlashBag()->add('error', 'You are not an admin, no access granted.');
             redirect('/');
            }
        } catch (\Exception $e) {
            $accessToken = new \Symfony\Component\HttpFoundation\Cookie("access_token", "Expired", time()-3600, '/', getenv('COOKIE_DOMAIN'));
            redirect('/login.php', ['cookies' => [$accessToken]]);
        }    
    }
}