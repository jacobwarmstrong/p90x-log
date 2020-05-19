<?php
/*
bootstrap.php
Sets up our application configuration and connects us to our database.
https://www.binpress.com/php-bootstrapping-crash-course/
*/

//autoload dependencies AND iteca Classes from composer
//ref this article for adding native classes to composer autoload
//https://phpenthusiast.com/blog/how-to-autoload-with-composer
require_once __DIR__ . '/../vendor/autoload.php';

//load our environment variables from our .env in the inc folder. These sensitive variables are left out of our code and repo
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

//create base url CONSTANT variable for sitewide usage, change as necessary
define('BASE_URL', 'C:\wamp64\www\projects-app');

//load our global functions
require(BASE_URL . '/src/functions.php');

//create alias for httpFoundation request class
use
\Symfony\Component\HttpFoundation\Request as Request;

use
\Symfony\Component\HttpFoundation\Session\Session as Session;

//start a new session
$session = new Session();
$session->start();

//initialize httpFoundation request class
$request = Request::createFromGlobals();

//create authenticator
$authenticator = new Authenticator();

//connect to the sql database
require_once __DIR__ . '/connection.php';
