<?php
//workouts.php
//testing map view on projects.php

$workouts = Database::action('SELECT', 'routines', '*');
?>

<h3>Workouts</h3>
<p>Select a workout!</p>

<?php foreach($workouts as $workout) : ?>
    <a href="#"><?php echo $workout['name']; ?></a>
<?php endforeach;

//select all moves in backs and biceps and all the columns related to moves
$columns = ['moves.*' => null, 'routine_moves.routine_order' => 0]; //this reads weird and shouldnt be this way
$workout_moves = Database::action('SELECT', 'routine_moves', $columns, "LEFT JOIN moves ON routine_moves.move_id = moves.id WHERE routine_moves.routine_id = 1");
var_dump($workout_moves);
