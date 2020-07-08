<?php

//header.php
$authenticator = new Authenticator;
if($authenticator->isAuthenticated()) {
    $href = '/controllers/logoutUser.php';
    $str = 'Logout';
} else {
    $href = '/login';
    $str = 'Login';
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<div class="container" id="app">

<h3>P90X Log</h3>
<nav>
    <ul>
        <li><a href="/workouts">Workout History</a></li>
        <li><a href="/workout">Workout</a></li>
        <li><a href="/my-account">My Account</a></li>
        <li><a href="<?php echo $href; ?>"><?php echo $str; ?></a></li>
        <li><a href="/register">Register</a></li>
    </ul>
</nav>
    