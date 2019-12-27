<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("init.php");

$debugger = new Debugger();
$tokens = new Token();

if ( isset($_GET["name"]) )
  $name = $_GET["name"];
else {
  $debugger->debug( "access.php", "You must set name", 2 );
  return;
}

if ( isset($_GET["pass"]) )
  $pass = $_GET["pass"];
else {
  $debugger->debug( "access.php", "You must set password", 2 );
  return;
}

echo $tokens->generateToken( $name, $pass );

 ?>
