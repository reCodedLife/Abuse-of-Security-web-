<?php

require ("security.php"); # import security php file

function debug( sting $from, string $message, int $type = 0 ) {  # main funcion

  $from     = getParsed( $from );     # get normal string from error string ( I'M_A_"echo "Hacker";-- | to IM_A_echo Hacker -- )
  $message  = getParsed( $message );  # get normal string from error string ( from messages )

  if ( !parse( $from ) || !parse( $message ) ) {  # checking message for denied sumbols
    echo "[Debbuger] error: exeption was occured!"; # just display message
    return; # stop script
  }

  switch ( $type ) {  # get type
    case 0: # plain message type
      echo "{Message}[".$from."] " . $message ;  # display data
      break;
    case 1: # warning
      echo "{Warning}[".$from."] " . $message ;  # display data
      break;
    case 2: # error
      echo "{Critical}[".$from."] " . $message ; # display data
      break;
    default:  # plain text
      echo "{Message}[".$from."] " . $message ;  # display data
      break;
  }

}

?>
