<?php
//workouts.php
//testing map view on projects.php
$user = findUserByAccessToken();
?>

<h3>Dashboard</h3>
<p>Hello, <?php echo $user['email']; ?></p>
<p>Today is a new day, are you ready to BRING IT?</p>