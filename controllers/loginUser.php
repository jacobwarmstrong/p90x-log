<?php
//loginUser.php
//This script logs our user in using the Iteca\Projects-App\Login class
include( __DIR__ . '/../config/bootstrap.php');

$exptime = time() + 3600;
var_dump($exptime);

$email = $request->request->get('email');
$password = $request->request->get('password');

$loginInfo = Login::authenticateWith($email, $password);
var_dump($loginInfo->getCookie());

$extra = [ "cookies" => [$loginInfo->getCookie()] ];

if($loginInfo->isAuthenticated) {
    redirect('/projects', $extra);
}
