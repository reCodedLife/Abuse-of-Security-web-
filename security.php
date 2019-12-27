<?php

$symbols = array( '"', "'", "\\" ); # denied symbols

function getParsed ( string $string ) {    # get normal string from error string

  $normal = "";                     # empty string
  $parseString  = preg_split('//u', $string,   NULL, PREG_SPLIT_NO_EMPTY);  # convert to array

  for ($i = 0; $i < count( $parseString ); $i++) {  # create a cycle with count of string
    if ( !in_array( $parseString[$i], $symbols ) )  # checking string for denied symbols
      $normal = $normal . $parseString[$i];         # add normal symbols to string
  }

  return $normal; # return normal string
}

function parse( string $string ) { # checking for denied symbols

  $allow = true;            # checking variable
  $parseString  = preg_split('//u', $string,   NULL, PREG_SPLIT_NO_EMPTY);  # convert to array

  for ($i = 0; $i < count($parseString); $i++) {    # create a cycle with count of string
    if ( in_array( $parseString[$i], $symbols ) )   # checking string for denied symbols
      $allow = false;                 # change variable to false
  }

  return allow; # return allow string
}

?>
