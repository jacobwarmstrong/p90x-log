<?php
//testlogin.php
//testing login minus routing class

//configuration and autoload file of application
include("config/bootstrap.php");

var_dump($request->cookies->all());

//header.php
include("layout/header.php");

//render our content based off router data
//Render::theContentFrom($data);

include("layout/footer.php");

