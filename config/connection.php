<?php
/*
connection.php
Makes connection to SQL Database in all situations
use global $db when writing query functions
*/

//Reference .env file found in inc folder for values. These environment values are kept out of repo for security
$dsn  = 'mysql:host=' . getenv("SQL_HOST") . ';dbname=' . getenv("SQL_DATABASE");
$username = getenv("SQL_USER");
$password = getenv("SQL_PASSWORD");

//Try to make connection to database, if cannot connect, let the user know with error message.
try {
  $db = new PDO($dsn, $username, $password);
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
  echo "Unable to connect. ";
  echo $e->getMessage(); 
  exit;
}
