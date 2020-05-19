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

<h1>Projects App</h1>
<nav>
    <ul>
        <li><a href="/projects">Projects</a></li>
        <li><a href="/maintenance">Maintenance</a></li>
        <li><a href="/tasks">Tasks</a></li>
        <li><a href="/users">Users</a></li>
        <li><a href="<?php echo $href; ?>"><?php echo $str; ?></a></li>
    </ul>
</nav>