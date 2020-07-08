<?php
//workouts.php
//workout-move

$routine_slug = $parameters['routine'];

$where_clause = "WHERE slug = " . "'" . $routine_slug . "'";

$routine = Database::action('SELECT', 'routines', '*', $where_clause )[0];
$columns = ['moves.*' => null, 'routine_moves.routine_order' => null];
$where_clause = "LEFT JOIN moves ON routine_moves.move_id = moves.id WHERE routine_moves.routine_id = " . $routine['id'];

$moves = Database::action('SELECT', 'routine_moves', $columns, $where_clause);
for($i = 0;$i<count($moves);$i++) {
    if($moves[$i]['has_weight'] == 1) {
        $moves[$i]['weight'] = 0;
    }
    $moves[$i]['reps'] = 0;
}
$json = json_encode($moves);
//echo $json;
?>

<h1>Workout: <?php echo $routine['name']; ?></h1>

<div class="row" >
    <div class="col-sm-3 d-flex justify-content-center align-items-center">
        <button class="btn btn-info" v-on:click="position--" v-if="position>0">Go Back</button>
    </div>
    <div class="col-sm-6">
        <h3>{{ moves[position].name }}</h3>
        <div v-if="moves[position].has_weight==Number(1)">
            <label for="weight">Weight</label>
            <input id="weight" type="number" v-model="moves[position].weight">
        </div>
        <label for="reps">Reps</label>
        <input id="reps" type="number" v-model="moves[position].reps">
    </div>
    <div class="col-sm-3 d-flex justify-content-center align-items-center">
        <button class="btn btn-info" v-on:click="position++" v-if="position<(moves.length - 1)">Next Move</button>
        
    </div>
</div>



