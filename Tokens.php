<?php

class Token { // api for Tokens

  public $database;           # database class
  public $debugger;           # debugger class
  public $library = "Tokens"; # librari name

  function __construct() {            # constructor
    $this->database = new DataBase(); # init database class
    $this->debugger = new Debugger(); # init debugger class
    $this->database->connect( "login", "pasword", "database_name" ); # connect to database
  }

  function generateToken( string $name, string $pass ) {  # generate token function

    $time = date("d/m/Y");  # get date

    $object = (object) array();
    $object->name  = "name";  # set name in new object for parameters
    $object->value = $name;   # set value in object

    $values = array();   # create values json array
    $params = array();   # create parameters jsom array
    $params[] = json_encode((array)$object);  # add json object to params array

    $request = $this->database->select( $values, "user_list", $params ); # get user object from SQL

    if ( $request["pass"] != hash( "sha256", $pass ) ) {  # compare heshed passwords
      $this->debugger->debug( $this->library, "Auth data wrong", 2 ); # debug a error
      return; # stop script
    }

    return base64_encode( $time . "*" . hash( "sha256", $pass ) . "*" . $name );  #generate token

  }

  function checkToken( obj $metadata ) {  # check user token

    # use metadata from getMetadata function

    $checkable = FALSE; # check variable
    $timestamp = date("d/m/Y");

    $object = (object) array();
    $object->name  = "name";            # set name in new object for parameters
    $object->value = $metadata["name"]; # set value in object

    $values = array();   # create values json array
    $params = array();   # create parameters jsom array
    $params[] = json_encode((array)$object);  # add json object to params array

    $request = $this->database->select( $values, "user_list", $params ); # get user object from SQL

    if ( $request["pass"] != $metadata["pass"] ) # compare heshed passwords
      $checkable = TRUE;                         # set to true

    if ( $metadata["time"] != $timestamp )       # check local and token time
      $checkable = TRUE;                         # set to true

    return $checkable; # return result

  }
}

?>
