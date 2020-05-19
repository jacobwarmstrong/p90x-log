<?php
//registerUser.php
//This script registers our new user into the db
include( __DIR__ . '/../config/bootstrap.php');

$email = $request->get('email');
$password = $request->get('password');
$confirmPassword = $request->get('confirm_password');

if( $password != $confirmPassword ) {
    $session->getFlashBag()->add('error', 'Passwords Do Not Match, Please Try Again.');
    redirect('/register.php');
}

$user = findUserByEmail($email);
if(!empty($user)) {
    $session->getFlashBag()->add('error', 'Username not available. Try Something Else.');
    redirect('/register.php');
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$user = createUser($email, $hashed);

redirect('/');