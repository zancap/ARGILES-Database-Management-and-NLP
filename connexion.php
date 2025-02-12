<?php
// Database connection configuration
$servername = "localhost";
$username = "zancanap";
$password = "@Tutideze15";
$dbname = "argiles";

// Connect to MySQL database
try {
	$db = new PDO('mysql:host='.$servername.';dbname='.$dbname,$username,$password);
}
catch (Exception $e){
	die('Erreur : '.$e->getMessage());
	print "Accès impossible à la base <br/>\n";
}
?>