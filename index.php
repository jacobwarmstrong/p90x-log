<?php

//index.php
//require our bootstrap file to fire up our application
require('config/bootstrap.php');

use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;

//var_dump(Request::createFromGlobals());
$uri = Request::createFromGlobals()->server->get('REQUEST_URI');

function is_open_page($page) {
    switch ($page) {
        case 'login':
        case 'register':
        case 'landing':
            return true;
        default :
            return false;
    }
}


//match routes with incoming requests
$matcher = new UrlMatcher($routes, $context);
try {
    $parameters = $matcher->match($uri);
    if ( !is_open_page( $parameters['class'] ) ) {
        $authenticator->requireAuth();
    }
    var_dump($parameters);
    include($parameters['view']);
} catch (\Exception $e) {
    include('page.php');
}