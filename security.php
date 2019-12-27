<?php

date_default_timezone_set( "Europe/Moscow" );
header('Content-Type: text/html; charset=utf-8');

class Security {

  public $symbols = array( '"', "'", '\\' ); # denied symbols
  public $debugger;
  public $library = "Security";

  function __construct() {
    $this->debugger = new Debugger();
  }

  public function getParsed ( string $string ) {     # get normal string from error string
    if ( $string != "" ) {

      $normal = "";                     # empty string
      $parseString  = preg_split('//u', $string,   NULL, PREG_SPLIT_NO_EMPTY);  # convert to array
      for ($i = 0; $i < count( $parseString ); $i++) {  # create a cycle with count of string
        if ( !in_array( $parseString[$i], $symbols ) )  # checking string for denied symbols
          $normal = $normal . $parseString[$i];         # add normal symbols to string
      }

      return $normal; # return normal string

    } else {
      $this->debugger->debug( $this->library, "String not given", 2 );
      return;
    }

  }

  public function parse( string $string ) { # checking for denied symbols

    $allow = true;            # checking variable
    $parseString  = preg_split('//u', $string,   NULL, PREG_SPLIT_NO_EMPTY);  # convert to array

    for ($i = 0; $i < count($parseString); $i++) {    # create a cycle with count of string
      if ( in_array( $parseString[$i], $this->symbols ) )   # checking string for denied symbols
        $allow = false;                 # change variable to false
    }

    return $allow; # return allow string
  }

  public function getMetadata( string $token ) { # get metadata from token
    
    $array = explode( "*", base64_decode($token) ); // decode token and split to array *WARNING password is heshed!

    if ( !$this->parse( $array[0] ) || !$this->parce( $array[1] ) || !$this->parse( $array[2] ) ) {  # checking token for denied symbols
      $this->debugger->debug( $this->library, "Token contains denied symbols", 2 );  #debug error
      return; # stop script
    }

    $metaData->time = $array[0];  # write timestamp to metadata
    $metaData->name = $array[1];  # write username to metadata
    $metaData->pass = $array[2];  # write password to metadata

    return $metaData; # just return metadata

  }
}

?>
