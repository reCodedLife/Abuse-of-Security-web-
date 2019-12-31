<?php

require_once("init.php");   # init all requirements

$debugger = new Debugger(); # init debugger class
$database = new DataBase(); # init database class
$tokens = new Token();      # init taken class
$file = "GET-INFO";         # init file name variable
$array = array();           # create output array
$object = (object) array(); # create json object

$database->connect( "db_login", "db_password", "db_name" );  # connect to database

if ( !isset( $_GET["token"] ) )                     # check token exists
  $debugger->debug( $file, "Токен не указан", 2 );  # debug error
else {
  $metadata = $tokens->getMetadata( $_GET["token"] ); # setup token variable
  $tokens->checkToken( $metadata );                   # getting metadata from token
}

if ( isset( $_GET["allow_programms"] ) ) {  # searching for variable

  $reply = $database->select( [], "allow_programms", [] );  # get reply from database

  while ( ( $resource = mysqli_fetch_assoc( $reply ) ) ) {  # get all rows
    $object->type = $resource["type"];  # get type of programm
    $object->path = $resource["path"];  # get path of programm
    $object->status = $resource["status"];  # get user status
    $array[] = $object;                 # add json to array
    $object = (object) array();         # clear variable
  }

} else if ( isset( $_GET["allowed_domains"] ) ) { # searching for variable

  $reply = $database->select( [], "allow_domains", [] );    # get reply from database

  while ( ( $resource = mysqli_fetch_assoc( $reply ) ) ) {  # get all rows
    $object->domain = $resource["domain"];  # get allowed domain
    $object->status = $resource["status"];  # get users status
    $array[] = $object;                     # add json to array
    $object = (object) array();             # clear variable
  }

}

echo json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); # print result

?>
