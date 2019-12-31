<?php

require_once("init.php");   # init all requirements

$debugger = new Debugger(); # init debugger class
$database = new DataBase(); # init database class
$tokens = new Token();      # init taken class
$file = "GET-INFO";         # init file name variable
$array = array();           # create output array
$object = (object) array(); # create json object

$database->connect( "id12056522_admin", "8895304025", "id12056522_corporate" ); # connect to database

if ( !isset( $_GET["token"] ) )                     # check token exists
  $debugger->debug( $file, "Токен не указан", 2 );  # debug error
else
  $metadata = $tokens->getMetadata( $_GET["token"] ); # setup token variable

if ( isset( $_GET["allow_programms"] ) ) {  # searching for variable

  if ( !$tokens->checkToken( $metadata ) )          # getting metadata from token
    $debugger->debug( $file, "Токен неверен", 2 );  # debug a error

  $reply = $database->select( [], "allow_programms", [] );  # get reply from database
  while ( ( $resource = mysqli_fetch_assoc( $reply ) ) ) {  # get all rows
    $object->type = $resource["type"];  # get type of programm
    $object->path = $resource["path"];  # get path of programm
    $object->status = $resource["status"];  # get user status
    $array[] = $object;                 # add json to array
    $object = null;                     # clear variable
  }


}

echo json_encode($array, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK); # print result

?>
