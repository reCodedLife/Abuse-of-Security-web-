<?php

/*
  TOKEN STRUCTURE:

  time*name*password

  time contains date of token
  name contains login of user
  password contains hashed user password
*/

class Token { // api for Tokens

  public $database;           # database class
  public $debugger;           # debugger class
  public $security;           # security class
  public $library = "Tokens"; # librari name

  function __construct() {            # constructor
    $this->database = new DataBase(); # init database class
    $this->debugger = new Debugger(); # init debugger class
    $this->security = new Security(); # init security class
    $this->database->connect( "db_login", "db_password", "db_name" );  # connect to database
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
    $request = mysqli_fetch_assoc( $request );  # updating querry

    if ( $request["pass"] != hash( "sha256", $pass ) ) {  # compare heshed passwords
      $this->debugger->debug( $this->library, "Auth data wrong", 2 ); # debug a error
      return; # stop script
    }

    return base64_encode( $time . "*" . $name . "*" . hash( "sha256", $pass ) );  #generate token

  }

  function checkToken( array $metadata ) {  # check user token

    # use metadata from getMetadata function

    $checkable = true;            # check variable
    $timestamp = date("d/m/Y");   # get date

    $params = $this->database->createObject( ["name"], [$metadata["name"]] ); # creating new object
    $request = $this->database->select( [], "user_list", $params ); # get user object from SQL
    $request = mysqli_fetch_assoc( $request );  # updating querry

    if ( $request["pass"] != $metadata["pass"] ) # compare heshed passwords
      $checkable = false;                        # set to false

    if ( $metadata["time"] != $timestamp )       # check local and token time
      $checkable = false;                        # set to false

    return $checkable; # return result

  }

  public function getMetadata( string $token ) { # get metadata from token

    $array = explode( "*", base64_decode($token) ); // decode token and split to array *WARNING password is heshed!

    if (  count( $array ) < 3 ||
          count( $array ) > 3 ||
          count( $array ) <= 0 )  # checking data
      $this->debugger->debug( $this->library, "Data is broken", 2 );  # debug a error

    if (  !is_string( $array[0] ) ||
          !is_string( $array[1] ) ||
          !is_string( $array[2] ) ) # checking data
      $this->debugger->debug( $this->library, "Data is broken", 2 );  # debug error

    if (  !mb_detect_encoding( $array[0], "utf-8", true ) ||
          !mb_detect_encoding( $array[1], "utf-8", true ) ||
          !mb_detect_encoding( $array[2], "utf-8", true ) )  # checking data
      $this->debugger->debug( $this->library, "Data is broken", 2 );  #debug error

    if (  !$this->security->parse( $array[0] ) ||
          !$this->security->parse( $array[1] ) ||
          !$this->security->parse( $array[2] ) )  # checking token for denied symbols
      $this->debugger->debug( $this->library, "Token contains denied symbols", 2 );  #debug error

    $metaData = (object) array(); # init json variable
    $metaData->time = $array[0];  # write timestamp to metadata
    $metaData->name = $array[1];  # write username to metadata
    $metaData->pass = $array[2];  # write password to metadata
    $metaData = json_encode( (array) $metaData );  # add json object to params array

    return json_decode( $metaData, true ); # just return metadata

  }
}

?>
