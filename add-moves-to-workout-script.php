<?php
//write script to add moves to workout
//get all moves
//add move to routine_moves
//in loop, add order with $i
$order = 0;
$moves = Database::action('SELECT', 'moves', ['name' => 0,'id' => 0]);
foreach($moves as $move) {
    $columns = ['move_id' => $move['id'], 'routine_id' => 1, 'routine_order' => $order];
    Database::action('INSERT', 'routine_moves', $columns);
    $order++;
}