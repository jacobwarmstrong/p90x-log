<?php
//workouts.php
//testing map view on projects.php

$workouts = Database::do('Select', 'routines', '*');
?>

<h3>Workouts</h3>
<p>Select a workout!</p>

<?php foreach($workouts as $workout) : ?>
    <a href="#"><?php echo $workout['name']; ?></a>
<?php endforeach; ?>
