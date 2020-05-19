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

<h1>P90X Log</h1>
<nav>
    <ul>
        <li><a href="/workouts">Workout History</a></li>
        <li><a href="/workout">Workout</a></li>
        <li><a href="/my-account">My Account</a></li>
        <li><a href="<?php echo $href; ?>"><?php echo $str; ?></a></li>
        <li><a href="/register">Register</a></li>
    </ul>
</nav>