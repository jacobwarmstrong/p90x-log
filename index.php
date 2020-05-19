<?php
/*
index.php
All url requests redirect here and our Route class handles where to go from there.
*/

//configuration and autoload file of application
include("config/bootstrap.php");

//checking out cookie across pages for testing
var_dump($request->cookies->get('access_token'));

//get URI to route us to proper view
$uriString = $request->server->get('REQUEST_URI');
$data = Route::withURI($uriString);

var_dump($uriString);
var_dump($data);

//header.php
include("layout/header.php");

//render our content based off router data
Render::theContentFrom($data);

include("layout/footer.php");

